<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppVersion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class AppVersionController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->filled('package_name')) {
                // Get all versions for a specific package
                $versions = AppVersion::where('package_name', $request->package_name)
                                    ->pluck('version')->first();
                
                if ($versions) {
                    return response()->json([
                        'success' => true,
                        'message' => 'App versions retrieved successfully for ' . $request->package_name,
                        'package_name' => $request->package_name,
                        'version' => $versions
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'No app versions found for ' . $request->package_name,
                        'package_name' => $request->package_name,
                        'data' => []
                    ], 500); // Changed from 500 to 404 (not found)
                }
            }
            
            // Get all package names with their versions (grouped)
            $query = AppVersion::query();
            $versions = $query->pluck('version','package_name');
            
            
            return response()->json([
                'success' => true,
                'message' => 'All app versions retrieved successfully',
                'data' => $versions
            ], 200);
        
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve app versions',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    public function store(Request $request){
        try {
            $validated = $request->validate([
                'package_name' => 'required',
                'version' => 'required|integer'
            ]);

            // Check if this exact combination already exists
            $existingVersion = AppVersion::where('package_name', $validated['package_name'])
                                       ->first();

            if ($existingVersion) {
                return response()->json([
                    'success' => false,
                    'message' => 'This package version already exists'
                ], 500);
            }

            $appVersion = AppVersion::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'App version created successfully',
                'data' => $appVersion
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create app version'
            ], 500);
        }
    }
    
    public function update(Request $request, $package) 
    {
        try {
            // Find the app version by package name
            $appVersion = AppVersion::where('package_name', $package)->first();
            if (!$appVersion) {
                return response()->json([
                    'success' => false,
                    'message' => 'App version not found'
                ], 404);
            }
    
            // Validate the request
            $validated = $request->validate([
                'package_name' => 'required|string',
                'version' => 'required|integer' // Changed to string if version can be like "dfgh"
            ]);
    
            // Check for duplicate combination (excluding current record)
            $existingVersion = AppVersion::where('package_name', $validated['package_name'])
                ->where('version', $validated['version'])
                ->where('id', '!=', $appVersion->id) // Exclude current record
                ->first();
    
            if ($existingVersion) {
                return response()->json([
                    'success' => false,
                    'message' => 'This package name and version combination already exists'
                ], 409);
            }
    
            // Update the app version
            $appVersion->update(['version' => $validated['version']]);
            $appVersion->refresh();
    
            return response()->json([
                'success' => true,
                'message' => 'App version updated successfully',
                'data' => $appVersion
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update app version',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
}