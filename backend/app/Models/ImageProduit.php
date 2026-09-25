<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImageProduit extends Model
{
    use HasFactory;

    protected $table = 'images_produits';

    protected $fillable = [
        'produit_id',
        'url',
        'public_id',
        'alt_text',
        'is_principale',
        'ordre',
    ];

    protected $casts = [
        'is_principale' => 'boolean',
        'ordre' => 'integer',
    ];

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class);
    }
}
