<?php

namespace App\GraphQL\Mutations;

use App\Models\PokemonTeam;

class CreatePokemonTeam
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args): PokemonTeam
    {
        try {
            $newPokemonTeam = PokemonTeam::create([
                'name' => $args['name'],
                'pokemon' => [],
            ]);

            return $newPokemonTeam;
        } catch (\Exception $e) {
            throw new \Exception('Failed to create pokemon team: ' . $e->getMessage());
        }
    }
}
