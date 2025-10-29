<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClassUpdateController extends Controller
{
    /**
     * Get the last update timestamp for class modifications
     */
    public function getLastUpdate(Request $request)
    {
        try {
            $since = $request->get('since', 0);
            
            // Get the last update timestamp from storage
            $updateFile = storage_path('app/class_updates.json');
            
            if (!file_exists($updateFile)) {
                // If no update file exists, create one with current timestamp
                $this->markUpdate();
                $lastUpdate = time();
            } else {
                $data = json_decode(file_get_contents($updateFile), true);
                $lastUpdate = $data['last_update'] ?? time();
            }
            
            return response()->json([
                'last_update' => $lastUpdate,
                'has_updates' => $lastUpdate > $since,
                'current_time' => time()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to check for updates',
                'message' => $e->getMessage(),
                'last_update' => time(),
                'has_updates' => false
            ], 500);
        }
    }
    
    /**
     * Mark that classes have been updated
     */
    public function markUpdate()
    {
        try {
            $updateFile = storage_path('app/class_updates.json');
            
            $data = [
                'last_update' => time(),
                'marked_at' => now()->toISOString()
            ];
            
            file_put_contents($updateFile, json_encode($data));
            
            return response()->json([
                'success' => true,
                'timestamp' => $data['last_update']
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to mark update',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}