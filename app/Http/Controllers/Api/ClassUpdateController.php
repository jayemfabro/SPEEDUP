<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ClassUpdateController extends Controller
{
    /**
     * Get the timestamp of the most recent class update
     */
    public function getLastUpdate(Request $request)
    {
        try {
            // Get the most recently updated class
            $lastUpdatedClass = ClassModel::orderBy('updated_at', 'desc')->first();
            
            if (!$lastUpdatedClass) {
                return response()->json([
                    'last_update' => null,
                    'has_updates' => false
                ]);
            }

            $lastUpdateTime = $lastUpdatedClass->updated_at->timestamp;
            
            // Check if there are updates since the client's last check
            $clientLastCheck = $request->query('since');
            $hasUpdates = false;
            
            if ($clientLastCheck) {
                $hasUpdates = $lastUpdateTime > (int)$clientLastCheck;
            }

            return response()->json([
                'last_update' => $lastUpdateTime,
                'has_updates' => $hasUpdates,
                'updated_at_iso' => $lastUpdatedClass->updated_at->toISOString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to check for updates',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark that an update has occurred (called after class updates)
     */
    public function markUpdate(Request $request)
    {
        try {
            // Store the current timestamp in cache
            $timestamp = now()->timestamp;
            Cache::put('last_class_update', $timestamp, 300); // Keep for 5 minutes
            
            return response()->json([
                'success' => true,
                'timestamp' => $timestamp
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to mark update',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}