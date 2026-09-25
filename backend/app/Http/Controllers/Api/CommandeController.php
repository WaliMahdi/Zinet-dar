<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommandeRequest;
use App\Http\Requests\UpdateCommandeNoteRequest;
use App\Http\Requests\UpdateCommandeStatutRequest;
use App\Http\Resources\CommandeResource;
use App\Models\Commande;
use App\Models\Produit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommandeController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // CLIENT ENDPOINTS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * CLIENT: List the authenticated user's own orders.
     */
    public function mesCommandes(Request $request): JsonResponse
    {
        $user     = $request->user();
        $commandes = Commande::with('details.produit.images')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate((int) $request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Vos commandes récupérées avec succès.',
            'data'    => CommandeResource::collection($commandes)->response()->getData(true),
        ]);
    }

    /**
     * CLIENT: Show a specific order belonging to the authenticated user.
     */
    public function showMine(Request $request, $id): JsonResponse
    {
        $user     = $request->user();
        $commande = Commande::with('details.produit.images')->findOrFail($id);

        if ($commande->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Détails de la commande récupérés avec succès.',
            'data'    => new CommandeResource($commande),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ADMIN ENDPOINTS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * ADMIN: List all orders with filters.
     */
    public function indexAdmin(Request $request): JsonResponse
    {
        $query = Commande::with(['details.produit.images', 'user']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('produit_id')) {
            $query->whereHas('details', function ($q) use ($request) {
                $q->where('produit_id', $request->produit_id);
            });
        }

        if ($request->filled('nom_client')) {
            $query->where('nom_client', 'like', '%' . $request->nom_client . '%');
        }

        if ($request->filled('telephone')) {
            $query->where('telephone', 'like', '%' . $request->telephone . '%');
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $commandes = $query->orderByDesc('created_at')
            ->paginate((int) $request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Toutes les commandes récupérées avec succès.',
            'data'    => CommandeResource::collection($commandes)->response()->getData(true),
        ]);
    }

    /**
     * ADMIN: Show a specific order.
     */
    public function showAdmin($id): JsonResponse
    {
        $commande = Commande::with(['details.produit.images', 'user'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Détails de la commande récupérés avec succès.',
            'data'    => new CommandeResource($commande),
        ]);
    }

    /**
     * ADMIN: Update the order status.
     *
     * Stock management (multi-product):
     * - Stock is already reduced at checkout.
     * - When status transitions TO 'annulee' → restore stock.
     * - When status transitions FROM 'annulee' to any other status → decrement stock again.
     */
    public function updateStatut(UpdateCommandeStatutRequest $request, $id): JsonResponse
    {
        $data     = $request->validated();
        $commande = Commande::with('details.produit.images')->findOrFail($id);

        $ancienStatut  = $commande->statut;
        $nouveauStatut = $data['statut'];

        DB::transaction(function () use ($commande, $data, $ancienStatut, $nouveauStatut) {
            // Stock: restore when cancelling
            if ($nouveauStatut === 'annulee' && $ancienStatut !== 'annulee') {
                foreach ($commande->details as $detail) {
                    $produit = $detail->produit;
                    if ($produit) {
                        $produit->increment('quantite_stock', $detail->quantite);
                    }
                }
            }

            // Stock: decrement again if un-cancelling
            if ($ancienStatut === 'annulee' && $nouveauStatut !== 'annulee') {
                foreach ($commande->details as $detail) {
                    $produit = $detail->produit;
                    if ($produit) {
                        $produit->decrement('quantite_stock', $detail->quantite);
                    }
                }
            }

            $updateData = [
                'statut'          => $nouveauStatut,
                'date_traitement' => now(),
            ];

            if (isset($data['note_admin'])) {
                $updateData['note_admin'] = $data['note_admin'];
            }

            $commande->update($updateData);
        });

        return response()->json([
            'success' => true,
            'message' => 'Statut de la commande mis à jour avec succès.',
            'data'    => new CommandeResource($commande->fresh(['details.produit.images', 'user'])),
        ]);
    }

    /**
     * ADMIN: Update the admin note only.
     */
    public function updateNote(UpdateCommandeNoteRequest $request, $id): JsonResponse
    {
        $data     = $request->validated();
        $commande = Commande::findOrFail($id);

        $commande->update([
            'note_admin' => $data['note_admin'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Note admin mise à jour avec succès.',
            'data'    => new CommandeResource($commande->fresh(['details.produit.images', 'user'])),
        ]);
    }

    /**
     * ADMIN: Soft-delete an order.
     */
    public function destroy($id): JsonResponse
    {
        $commande = Commande::findOrFail($id);
        $commande->delete();

        return response()->json([
            'success' => true,
            'message' => 'Commande supprimée avec succès.',
            'data'    => null,
        ]);
    }
}
