<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class PokemonApiHelper
{

    const LUCKY_NUMBERS = [1, 6, 12, 15, 18, 21, 42, 44, 67, 77];
    
    public static function getPokemon(string $identifier, bool $canBeShiny = false): array
    {
        $baseUrl = rtrim((string) config('app.pokemon_api_url'), '/');
        $pokemon = Http::get($baseUrl.'/'.$identifier)->throw()->json();

        if (empty($pokemon)) {
            throw new \Exception('Pokemon not found');
        }

        // Get types images
        $types = collect($pokemon['types'] ?? [])->pluck('type.name')->filter()->values()->all();
        $typesImages = [];
        foreach (config('pokemonTypes') as $type => $image) {
            if (in_array($type, $types)) {
                $typesImages[] = $image;
            }
        }

        // Get stats
        $stats = collect($pokemon['stats'] ?? [])->map(function ($stat) {
            return [
                'name' => $stat['stat']['name'],
                'value' => $stat['base_stat'],
            ];
        })->filter()->values()->all();

        // Get pokemon image
        $pokemonImage = $pokemon['sprites']['front_default'] ?? null;
        if ($canBeShiny) {
            $isShiny = in_array(rand(0, 100), self::LUCKY_NUMBERS); // 5% chance of being shiny
            if ($isShiny) {
                $pokemonImage = $pokemon['sprites']['front_shiny'] ?? null;
            }
        }

        $pokemonData = [
            'id' => $pokemon['id'],
            'name' => $pokemon['name'],
            'image' => $pokemonImage,
            'types' => $typesImages,
            'stats' => $stats,
        ];

        return $pokemonData;
    }
}
