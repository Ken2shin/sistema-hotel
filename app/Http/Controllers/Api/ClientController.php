<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $clients = Client::with(['reservations'])
                ->paginate(20);

            return response()->json([
                'data' => $clients->items(),
                'pagination' => [
                    'current_page' => $clients->currentPage(),
                    'total' => $clients->total(),
                    'per_page' => $clients->perPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching clients'], 500);
        }
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        try {
            $client = Client::create($request->validated());

            Log::info('Client created', ['client_id' => $client->id]);

            return response()->json([
                'data' => $client,
                'message' => 'Client created successfully'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Client creation failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function show(Client $client): JsonResponse
    {
        try {
            $client->load(['reservations']);

            return response()->json(['data' => $client]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Client not found'], 404);
        }
    }

    public function update(UpdateClientRequest $request, Client $client): JsonResponse
    {
        try {
            $client->update($request->validated());

            Log::info('Client updated', ['client_id' => $client->id]);

            return response()->json(['data' => $client, 'message' => 'Client updated']);
        } catch (\Exception $e) {
            Log::error('Client update failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroy(Client $client): JsonResponse
    {
        try {
            $client->delete();

            Log::info('Client deleted', ['client_id' => $client->id]);

            return response()->json(['message' => 'Client deleted']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting client'], 500);
        }
    }

    public function reservations(Client $client): JsonResponse
    {
        try {
            $reservations = $client->reservations()
                ->with(['room', 'payments'])
                ->orderBy('check_in', 'desc')
                ->paginate(10);

            return response()->json([
                'data' => $reservations->items(),
                'pagination' => [
                    'current_page' => $reservations->currentPage(),
                    'total' => $reservations->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching reservations'], 500);
        }
    }
}
