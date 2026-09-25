<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\Catalog\CategorieController;
use App\Http\Controllers\Api\Catalog\ProduitController;
use App\Http\Controllers\Api\Catalog\ImageProduitController;
use App\Http\Controllers\Api\CommandeController;
use App\Http\Controllers\Api\PanierController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AdminClientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — SEDI Electro
|--------------------------------------------------------------------------
|
| Routes are organised into two groups:
|   1. Public  — registration, login, email verification
|   2. Private — requires a valid Sanctum token (auth:sanctum middleware)
|
*/

// ── Public authentication routes ─────────────────────────────────────────────
Route::prefix('auth')->name('auth.')->group(function () {

    Route::post('register',             [AuthController::class, 'register'])
         ->name('register');

    Route::post('verify-email',         [AuthController::class, 'verifyEmail'])
         ->name('verify-email');

    Route::post('resend-verification',  [AuthController::class, 'resendVerification'])
         ->name('resend-verification');

    Route::post('login',                [AuthController::class, 'login'])
         ->name('login');

    // ── Protected routes (Sanctum token required) ─────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('logout',   [AuthController::class, 'logout'])
             ->name('logout');

        Route::get('profile',   [AuthController::class, 'profile'])
             ->name('profile');

        Route::match(['put', 'patch'], 'profile', [AuthController::class, 'updateProfile'])
             ->name('profile.update');
        
    });
});

Route::get('/settings/logo', [\App\Http\Controllers\SettingController::class, 'logo']);
Route::get('/bannieres', [\App\Http\Controllers\BanniereController::class, 'index']);
Route::get('/bannieres/section/{section}', [\App\Http\Controllers\BanniereController::class, 'getBySection']);
Route::get('/homepage-images', [\App\Http\Controllers\HomepageImageController::class, 'index']);

// ── Public Catalog Routes (GET) ──────────────────────────────────────────────
Route::get('categories', [CategorieController::class, 'index']);
Route::get('categories/{categorie}', [CategorieController::class, 'show']);
Route::get('categories/{categorie}/produits', [CategorieController::class, 'produits']);
Route::get('produits', [ProduitController::class, 'index']);
Route::get('produits/{produit}', [ProduitController::class, 'show']);

// ── Protected Catalog Routes (POST/PUT/PATCH/DELETE) ─────────────────────────
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Bannières & Paramètres
    Route::post('/settings/logo', [\App\Http\Controllers\SettingController::class, 'updateLogo']);
    Route::post('/bannieres', [\App\Http\Controllers\BanniereController::class, 'store']);
    Route::put('/bannieres/{banniere}', [\App\Http\Controllers\BanniereController::class, 'update']);
    Route::patch('/bannieres/{banniere}/status', [\App\Http\Controllers\BanniereController::class, 'updateStatus']);
    Route::delete('/bannieres/{banniere}', [\App\Http\Controllers\BanniereController::class, 'destroy']);
    Route::post('/homepage-images', [\App\Http\Controllers\HomepageImageController::class, 'store']);
    Route::delete('/homepage-images/{homepage_image}', [\App\Http\Controllers\HomepageImageController::class, 'destroy']);

    // Categories
    Route::post('categories', [CategorieController::class, 'store']);
    Route::put('categories/{categorie}', [CategorieController::class, 'update']);
    Route::delete('categories/{categorie}', [CategorieController::class, 'destroy']);

    // Produits
    Route::post('produits', [ProduitController::class, 'store']);
    Route::put('produits/{produit}', [ProduitController::class, 'update']);
    Route::delete('produits/{produit}', [ProduitController::class, 'destroy']);

    // Actions spécifiques Produits
    Route::patch('produits/{produit}/stock', [ProduitController::class, 'stock']);
    Route::patch('produits/{produit}/remise', [ProduitController::class, 'remise']);
    Route::patch('produits/{produit}/statut', [ProduitController::class, 'statut']);

    // Images
    Route::post('produits/{produit}/images', [ImageProduitController::class, 'store']);
    Route::delete('produits/{produit}/images/{image}', [ImageProduitController::class, 'destroy']);
    Route::patch('produits/{produit}/images/{image}/principale', [ImageProduitController::class, 'principale']);
});

// ── Panier & Commandes — Client Routes (auth:sanctum) ────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    // Panier
    Route::get('panier', [PanierController::class, 'index']);
    Route::post('panier/ajouter', [PanierController::class, 'ajouter']);
    Route::patch('panier/{produit}/quantite', [PanierController::class, 'updateQuantite']);
    Route::delete('panier/vider', [PanierController::class, 'vider']); // Must be before {produit}
    Route::delete('panier/{produit}', [PanierController::class, 'retirer']);
    
    // Checkout
    Route::post('panier/commander', [PanierController::class, 'commander']);

    // Mes Commandes
    Route::get('mes-commandes', [CommandeController::class, 'mesCommandes']);
    Route::get('mes-commandes/{id}', [CommandeController::class, 'showMine']);
});

// ── Commandes — Admin Routes (auth:sanctum + admin) ──────────────────────────
Route::middleware(['auth:sanctum', 'admin'])
    ->prefix('admin')
    ->group(function () {
        Route::get('commandes', [CommandeController::class, 'indexAdmin']);
        Route::get('commandes/{id}', [CommandeController::class, 'showAdmin']);
        Route::patch('commandes/{id}/statut', [CommandeController::class, 'updateStatut']);
        Route::patch('commandes/{id}/note', [CommandeController::class, 'updateNote']);
        Route::delete('commandes/{id}', [CommandeController::class, 'destroy']);

        // Dashboard
        Route::get('dashboard', [AdminDashboardController::class, 'index']);

        // Clients
        Route::get('clients', [AdminClientController::class, 'index']);
        Route::get('clients/{id}', [AdminClientController::class, 'show']);
        Route::delete('clients/{id}', [AdminClientController::class, 'destroy']);
    });

// ── Legacy route — preserved to avoid breaking any existing integrations ─────
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
