<?php

namespace App\GraphQL\Mutations;

use App\Models\PokemonTeam;
use App\Helpers\PokemonApiHelper;
use Illuminate\Support\Str;

class UpdatePokemonTeam
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args): PokemonTeam
    {
        try {
            $pokemonTeam = PokemonTeam::find($args['id']);

            if (! $pokemonTeam) {
                throw new \Exception('Pokemon team not found');
            }

            $teamPokemon = $pokemonTeam->pokemon ?? [];

            if ($args['action'] === 'add') {
                if (count($teamPokemon) >= 6) {
                    throw new \Exception('Pokemon team is full');
                }
            } else {
                if (!isset($args['pokemonUuid'])) {
                    throw new \Exception('Pokemon uuid is required');
                } else if (!isset($teamPokemon[$args['pokemonUuid']])) {
                    throw new \Exception('Pokemon not in team');
                }
            }

            if ($args['action'] === 'add') {
                $newTeamMemberPokemon = PokemonApiHelper::getPokemon($args['pokemonId'], true);
                $newTeamMemberPokemon['uuid'] = Str::uuid()->toString();
                $teamPokemon[$newTeamMemberPokemon['uuid']] = $newTeamMemberPokemon;
            } elseif ($args['action'] === 'remove') {
                unset($teamPokemon[$args['pokemonUuid']]);
            }
            $pokemonTeam->pokemon = $teamPokemon;
            $pokemonTeam->save();

            return $pokemonTeam->fresh() ?? $pokemonTeam;
        } catch (\Exception $e) {
            throw new \Exception('Failed to update pokemon team: ' . $e->getMessage());
        }
    }
}
