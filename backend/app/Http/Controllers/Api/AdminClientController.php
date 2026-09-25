<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminClientController extends Controller
{
    /**
     * Get a list of all clients (excluding admin).
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::where('email', '!=', 'sedielectro@gmail.com');

        if ($request->filled('nom')) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->nom . '%')
                  ->orWhere('last_name', 'like', '%' . $request->nom . '%')
                  ->orWhere('name', 'like', '%' . $request->nom . '%');
            });
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('telephone')) {
            // Note: Users table doesn't have telephone right now in the backend, it's in order. 
            // We'll leave the parameter but skip querying if telephone is not on User.
            // If needed, we can join with orders.
        }

        // We include aggregate statistics for orders if we want, or do it on the detail page.
        // Easiest is to eager load orders count and total amount on the fly.
        $query->withCount('commandes')
              ->withSum('commandes', 'montant_total');

        $clients = $query->orderByDesc('created_at')->paginate((int) $request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Clients récupérés avec succès',
            'data'    => $clients
        ]);
    }

    /**
     * Show details for a specific client.
     */
    public function show($id): JsonResponse
    {
        $client = User::with(['commandes' => function($q) {
            $q->orderByDesc('created_at');
        }])
        ->withCount('commandes')
        ->withSum('commandes', 'montant_total')
        ->findOrFail($id);

        if ($client->email === 'sedielectro@gmail.com') {
            return response()->json(['success' => false, 'message' => 'Action non autorisée'], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Détails client récupérés avec succès',
            'data'    => $client
        ]);
    }

    /**
     * Delete a client.
     */
    public function destroy($id): JsonResponse
    {
        $client = User::findOrFail($id);
        
        if ($client->email === 'sedielectro@gmail.com') {
            return response()->json(['success' => false, 'message' => 'Impossible de supprimer l\'administrateur'], 403);
        }

        $client->delete();

        return response()->json([
            'success' => true,
            'message' => 'Client supprimé avec succès'
        ]);
    }
}
