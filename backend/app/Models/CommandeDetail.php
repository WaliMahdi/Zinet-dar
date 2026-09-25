<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommandeDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'commande_id',
        'produit_id',
        'nom_produit',
        'reference_produit',
        'prix_unitaire',
        'quantite',
        'montant',
    ];

    protected $casts = [
        'commande_id'   => 'integer',
        'produit_id'    => 'integer',
        'prix_unitaire' => 'float',
        'quantite'      => 'integer',
        'montant'       => 'float',
    ];

    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class);
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class);
    }
}
