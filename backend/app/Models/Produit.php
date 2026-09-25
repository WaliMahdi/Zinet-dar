<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'categorie_id',
        'nom',
        'marque',
        'reference',
        'description',
        'caracteristiques',
        'prix',
        'remise',
        'prix_apres_remise',
        'quantite_stock',
        'garantie',
        'image',
        'actif',
        'vedette',
        'nouveau',
    ];

    protected $casts = [
        'prix'              => 'float',
        'remise'            => 'float',
        'prix_apres_remise' => 'float',
        'quantite_stock'    => 'integer',
        'actif'             => 'boolean',
        'vedette'           => 'boolean',
        'nouveau'           => 'boolean',
    ];

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ImageProduit::class);
    }

    public function scopeActifs($query)
    {
        return $query->where('actif', true);
    }
}
