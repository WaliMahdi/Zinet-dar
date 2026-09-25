<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commande extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'commandes';

    protected $fillable = [
        'user_id',
        'nom_client',
        'telephone',
        'adresse',
        'sous_total',
        'montant_total',
        'mode_paiement',
        'statut',
        'note_client',
        'note_admin',
        'date_traitement',
    ];

    protected function casts(): array
    {
        return [
            'user_id'         => 'integer',
            'sous_total'      => 'float',
            'montant_total'   => 'float',
            'date_traitement' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CommandeDetail::class);
    }
}
