<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PokemonTeam extends Model
{
    protected $connection = 'pgsql';

    protected $table = 'teams';

    protected $fillable = [
        'name',
        'pokemon',
    ];

    protected function casts(): array
    {
        return [
            'pokemon' => 'array',
            'created_at' => 'date',
            'updated_at' => 'date',
        ];
    }
}
