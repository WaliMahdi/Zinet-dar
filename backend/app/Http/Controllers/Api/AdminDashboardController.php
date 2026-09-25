<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Get dashboard statistics.
     */
    public function index(Request $request): JsonResponse
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Orders Stats
        $commandesStats = [
            'total'          => Commande::count(),
            'en_attente'     => Commande::where('statut', 'en_attente')->count(),
            'confirmee'      => Commande::where('statut', 'confirmee')->count(),
            'livree'         => Commande::where('statut', 'livree')->count(),
            'annulee'        => Commande::where('statut', 'annulee')->count(),
            'today_count'    => Commande::whereDate('created_at', $today)->count(),
        ];

        // Revenue
        // Assuming 'montant_total' is stored in the orders table and 'livree' means completed payment (for cash on delivery)
        // Alternatively, we can calculate total confirmed revenue. Let's provide both or standard CA.
        $chiffreAffairesTotal = Commande::whereNotIn('statut', ['annulee'])->sum('montant_total');
        $chiffreAffairesMois  = Commande::whereNotIn('statut', ['annulee'])
                                        ->where('created_at', '>=', $startOfMonth)
                                        ->sum('montant_total');

        // Products Stats
        $produitsStats = [
            'total'      => Produit::count(),
            'actifs'     => Produit::where('actif', true)->count(),
            'rupture'    => Produit::where('quantite_stock', '<=', 0)->count(),
            'stock_bas'  => Produit::where('quantite_stock', '>', 0)->where('quantite_stock', '<=', 5)->count(),
        ];

        // Clients Stats
        $clientsStats = [
            'total' => User::where('email', '!=', 'sedielectro@gmail.com')->count(),
        ];

        // Recent Orders
        $recentCommandes = Commande::with('user')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // Low Stock Products Alert
        $alertesStock = Produit::where('quantite_stock', '<=', 5)
            ->orderBy('quantite_stock', 'asc')
            ->take(10)
            ->get(['id', 'nom', 'reference', 'quantite_stock']);

        return response()->json([
            'success' => true,
            'message' => 'Statistiques récupérées avec succès',
            'data'    => [
                'commandes'       => $commandesStats,
                'chiffre_affaires' => [
                    'total' => $chiffreAffairesTotal,
                    'mois'  => $chiffreAffairesMois,
                ],
                'produits'        => $produitsStats,
                'clients'         => $clientsStats,
                'recent_commandes'=> $recentCommandes,
                'alertes_stock'   => $alertesStock,
            ]
        ]);
    }
}
