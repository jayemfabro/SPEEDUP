<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        
        // Handle profile photo upload
        if ($request->hasFile('photo')) {
            try {
                // Delete old photo if exists
                if ($user->profile_photo_path && \Storage::disk('public')->exists($user->profile_photo_path)) {
                    \Storage::disk('public')->delete($user->profile_photo_path);
                }
                
                // Store the new photo
                $photoPath = $request->file('photo')->store('profile-photos', 'public');
                $user->profile_photo_path = $photoPath;
                
                // For debug, log the path
                \Log::info('Profile photo updated', [
                    'user' => $user->id,
                    'path' => $photoPath,
                    'url' => asset('storage/' . $photoPath)
                ]);
            } catch (\Exception $e) {
                \Log::error('Error updating profile photo', [
                    'user' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Parse the name into individual components
        $fullName = trim($validated['name']);
        $nameParts = explode(' ', $fullName);
        $nameParts = array_filter($nameParts); // Remove empty parts
        
        $firstName = '';
        $middleName = '';
        $lastName = '';
        
        if (count($nameParts) === 1) {
            // Only first name provided
            $firstName = $nameParts[0];
        } elseif (count($nameParts) === 2) {
            // First and last name
            $firstName = $nameParts[0];
            $lastName = $nameParts[1];
        } elseif (count($nameParts) >= 3) {
            // First, middle(s), and last name
            $firstName = array_shift($nameParts); // Remove and get first element
            $lastName = array_pop($nameParts);    // Remove and get last element
            $middleName = implode(' ', $nameParts); // Everything in between
        }
        
        // Fill all fields including parsed name components
        $user->fill([
            'name' => $validated['name'],
            'first_name' => $firstName,
            'middle_name' => $middleName ?: null,
            'last_name' => $lastName ?: null,
            'email' => $validated['email'],
            'birthdate' => $validated['birthdate'] ?? null,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
        
        \Log::info('Profile updated with parsed name', [
            'user_id' => $user->id,
            'original_name' => $validated['name'],
            'parsed_first_name' => $firstName,
            'parsed_middle_name' => $middleName,
            'parsed_last_name' => $lastName,
            'constructed_name' => trim($firstName . ' ' . $middleName . ' ' . $lastName)
        ]);

        return Redirect::route('profile.edit')->with('message', 'Profile updated successfully.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
