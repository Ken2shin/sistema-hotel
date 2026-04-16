<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingRequest;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(): JsonResponse
    {
        try {
            $settings = Setting::all()
                ->map(fn($s) => [
                    'id' => $s->id,
                    'key' => $s->key,
                    'value' => $s->is_encrypted ? '[ENCRYPTED]' : $s->getValue(),
                    'type' => $s->type,
                    'description' => $s->description,
                    'is_encrypted' => $s->is_encrypted,
                ])
                ->groupBy('key');

            return response()->json(['data' => $settings]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching settings'], 500);
        }
    }

    public function show(string $key): JsonResponse
    {
        try {
            $setting = Setting::where('key', $key)->first();

            if (!$setting) {
                return response()->json(['error' => 'Setting not found'], 404);
            }

            return response()->json([
                'data' => [
                    'key' => $setting->key,
                    'value' => $setting->is_encrypted ? '[ENCRYPTED]' : $setting->getValue(),
                    'type' => $setting->type,
                    'description' => $setting->description,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching setting'], 500);
        }
    }

    public function update(UpdateSettingRequest $request, string $key): JsonResponse
    {
        try {
            $validated = $request->validated();

            $setting = Setting::setSetting(
                $key,
                $validated['value'],
                $validated['type'] ?? 'string',
                $validated['is_encrypted'] ?? false
            );

            if (isset($validated['description'])) {
                $setting->update(['description' => $validated['description']]);
            }

            Log::info('Setting updated', ['key' => $key, 'user_id' => auth()->id()]);

            return response()->json([
                'data' => $setting,
                'message' => 'Setting updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Setting update failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getSetting(string $key): JsonResponse
    {
        try {
            $value = Setting::getSetting($key);

            if ($value === null) {
                return response()->json(['error' => 'Setting not found'], 404);
            }

            return response()->json(['data' => ['value' => $value]]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching setting'], 500);
        }
    }

    public function bulk(): JsonResponse
    {
        try {
            $keys = request()->input('keys', []);

            if (empty($keys)) {
                return response()->json(['error' => 'No keys provided'], 400);
            }

            $settings = Setting::whereIn('key', $keys)->get();
            $data = [];

            foreach ($settings as $setting) {
                $data[$setting->key] = $setting->getValue();
            }

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching settings'], 500);
        }
    }

    public function reset(string $key): JsonResponse
    {
        try {
            Setting::where('key', $key)->delete();

            Log::warning('Setting reset', ['key' => $key, 'user_id' => auth()->id()]);

            return response()->json(['message' => 'Setting reset to default']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error resetting setting'], 500);
        }
    }
}
