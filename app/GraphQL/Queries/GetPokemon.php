<?php

namespace App\GraphQL\Queries;

use App\Helpers\PokemonApiHelper;

class GetPokemon
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args): array
    {
        try {
            $foundPokemon = PokemonApiHelper::getPokemon($args['name']);

            return $foundPokemon;
        } catch (\Exception $e) {
            throw new \Exception('Failed to get pokemon: ' . $e->getMessage());
        }
    }
}
