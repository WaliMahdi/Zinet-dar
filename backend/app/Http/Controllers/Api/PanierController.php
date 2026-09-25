<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Panier\AjouterPanierRequest;
use App\Http\Requests\Panier\CheckoutRequest;
use App\Http\Resources\CommandeResource;
use App\Http\Resources\PanierResource;
use App\Models\Commande;
use App\Models\CommandeDetail;
use App\Models\Panier;
use App\Models\PanierDetail;
use App\Models\Produit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PanierController extends Controller
{
    private function getCartForUser($userId)
    {
        return Panier::firstOrCreate(['user_id' => $userId]);
    }

    public function index(Request $request): JsonResponse
    {
        $panier = $this->getCartForUser($request->user()->id);
        $panier->load('details.produit.images');

        return response()->json([
            'success' => true,
            'message' => 'Panier récupéré avec succès.',
            'panier'  => new PanierResource($panier),
        ]);
    }

    public function ajouter(AjouterPanierRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();
        
        $panier = $this->getCartForUser($user->id);
        $produit = Produit::findOrFail($data['produit_id']);

        if (!$produit->actif) {
            return response()->json([
                'success' => false,
                'message' => 'Ce produit n\'est pas disponible à la commande.',
            ], 422);
        }

        $detail = PanierDetail::where('panier_id', $panier->id)
                              ->where('produit_id', $produit->id)
                              ->first();

        $newQuantity = $data['quantite'];
        if ($detail) {
            $newQuantity += $detail->quantite;
        }

        if ($produit->quantite_stock < $newQuantity) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuffisant. Il ne reste que ' . $produit->quantite_stock . ' article(s) en stock.',
            ], 422);
        }

        if ($detail) {
            $detail->update(['quantite' => $newQuantity]);
        } else {
            PanierDetail::create([
                'panier_id'  => $panier->id,
                'produit_id' => $produit->id,
                'quantite'   => $newQuantity,
            ]);
        }

        $panier->load('details.produit.images');

        return response()->json([
            'success' => true,
            'message' => 'Produit ajouté au panier.',
            'panier'  => new PanierResource($panier),
        ]);
    }

    public function updateQuantite(Request $request, $produitId): JsonResponse
    {
        $data = $request->validate([
            'quantite' => ['required', 'integer', 'min:1'],
        ]);
        
        $user = $request->user();
        $panier = $this->getCartForUser($user->id);
        
        $detail = PanierDetail::where('panier_id', $panier->id)
                              ->where('produit_id', $produitId)
                              ->firstOrFail();

        $produit = Produit::findOrFail($produitId);

        if ($produit->quantite_stock < $data['quantite']) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuffisant. Il ne reste que ' . $produit->quantite_stock . ' article(s) en stock.',
            ], 422);
        }

        $detail->update(['quantite' => $data['quantite']]);
        
        $panier->load('details.produit.images');

        return response()->json([
            'success' => true,
            'message' => 'Quantité mise à jour.',
            'panier'  => new PanierResource($panier),
        ]);
    }

    public function retirer(Request $request, $produitId): JsonResponse
    {
        $user = $request->user();
        $panier = $this->getCartForUser($user->id);
        
        $detail = PanierDetail::where('panier_id', $panier->id)
                              ->where('produit_id', $produitId)
                              ->firstOrFail();

        $detail->delete();
        
        $panier->load('details.produit.images');

        return response()->json([
            'success' => true,
            'message' => 'Produit retiré du panier.',
            'panier'  => new PanierResource($panier),
        ]);
    }

    public function vider(Request $request): JsonResponse
    {
        $user = $request->user();
        $panier = $this->getCartForUser($user->id);
        
        PanierDetail::where('panier_id', $panier->id)->delete();
        
        $panier->load('details.produit.images');

        return response()->json([
            'success' => true,
            'message' => 'Panier vidé.',
            'panier'  => new PanierResource($panier),
        ]);
    }

    public function commander(CheckoutRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();
        $panier = $this->getCartForUser($user->id);
        $panier->load('details');

        if ($panier->details->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Votre panier est vide.',
            ], 422);
        }

        try {
            $commande = DB::transaction(function () use ($data, $user, $panier) {
                $sousTotal = 0.0;
                $commandeDetailsData = [];
                $produitsToUpdate = [];

                // Re-verify stock and prepare data
                foreach ($panier->details as $detail) {
                    $produit = Produit::where('id', $detail->produit_id)->lockForUpdate()->first();

                    if (!$produit || !$produit->actif) {
                        throw new \Exception('Le produit ID ' . $detail->produit_id . ' n\'est plus disponible.');
                    }

                    if ($produit->quantite_stock < $detail->quantite) {
                        throw new \Exception('Stock insuffisant pour le produit: ' . $produit->nom . '.');
                    }

                    $prixUnitaire = (float) ($produit->prix_apres_remise ?? $produit->prix);
                    $montant = $prixUnitaire * $detail->quantite;
                    $sousTotal += $montant;

                    $commandeDetailsData[] = [
                        'produit_id'        => $produit->id,
                        'nom_produit'       => $produit->nom,
                        'reference_produit' => $produit->reference,
                        'prix_unitaire'     => $prixUnitaire,
                        'quantite'          => $detail->quantite,
                        'montant'           => $montant,
                    ];

                    $produitsToUpdate[] = [
                        'model' => $produit,
                        'deduct' => $detail->quantite,
                    ];
                }

                // Create Commande
                $commande = Commande::create([
                    'user_id'       => $user->id,
                    'nom_client'    => $data['nom_client'],
                    'telephone'     => $data['telephone'],
                    'adresse'       => $data['adresse'],
                    'note_client'   => $data['note_client'] ?? null,
                    'sous_total'    => $sousTotal,
                    'montant_total' => $sousTotal,
                    'mode_paiement' => 'espece',
                    'statut'        => 'en_attente',
                ]);

                // Insert details and reduce stock
                foreach ($commandeDetailsData as $detailData) {
                    $detailData['commande_id'] = $commande->id;
                    CommandeDetail::create($detailData);
                }

                foreach ($produitsToUpdate as $updateItem) {
                    $updateItem['model']->decrement('quantite_stock', $updateItem['deduct']);
                }

                // Empty cart
                PanierDetail::where('panier_id', $panier->id)->delete();

                return $commande;
            });

            return response()->json([
                'success' => true,
                'message' => 'Commande passée avec succès.',
                'data'    => new CommandeResource($commande->load(['details.produit', 'user'])),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
