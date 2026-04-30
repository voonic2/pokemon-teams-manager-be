# Pokémon Project — Backend

## 1. Main purpose of this repo

This repository is the **Laravel** backend for a Pokémon team builder. It exposes a **GraphQL** API so clients can create and manage named teams, add or remove Pokémon (up to six per team), and resolve live Pokémon data (name, types, stats, artwork) from the public **PokéAPI**. Team definitions are persisted in **PostgreSQL** as JSON on each team row.

## 2. Installation

**Prerequisites:** PHP 8.2+, Composer, Node.js (for optional Vite assets), and Docker if you use the bundled stack.

### Option A — Docker (app + PostgreSQL + MySQL)

From the `BE` directory:

```bash
docker compose up --build
```

- App: `http://localhost:8000` (override with `APP_PORT`)
- PostgreSQL: host port `54320` → container `5432` (see `docker-compose.yml`)
- MySQL: host port `33060` → container `3306`

Copy environment variables and generate the app key if you are not relying entirely on Compose env:

```bash
cp .env.example .env
php artisan key:generate
```

Run migrations (teams live on PostgreSQL; migrations target the `pgsql` connection):

```bash
php artisan migrate
```

### Option B — Local PHP

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Point `PGSQL_*` (and optionally `MYSQL_*`) in `.env` at your databases, then:

```bash
php artisan migrate
php artisan serve --host=0.0.0.0 --port=8000
```

The Composer `setup` script can bootstrap a minimal local install: `composer run setup`.

## 3. Important components used

| Component | Role |
|-----------|------|
| **Laravel 12** | HTTP application framework, configuration, Eloquent. |
| **Nuwave Lighthouse** | GraphQL schema, directives, and resolvers mapped from `graphql/`. |
| **webonyx/graphql-php** | GraphQL execution engine (pulled in by Lighthouse). |
| **mll-lab/laravel-graphiql** | Browser IDE for ad-hoc queries and mutations (development). |
| **PostgreSQL** | Primary persistence for `teams` (name + JSON `pokemon` payload). |
| **MySQL** | Defined in `docker-compose.yml` and `.env.example` for planned logging; not used by application models today. |
| **PokéAPI** | External REST source for species data (`POKEMON_API_URL` in `.env`). |

## 4. Summary of the endpoints with examples

The public API is **GraphQL** over HTTP, not a large set of REST routes.

| URL / method | Description |
|--------------|-------------|
| `POST /graphql` | Execute GraphQL queries and mutations (JSON body with `query` and optional `variables`). |
| `GET /graphiql` | GraphiQL UI (local/dev) for exploring the schema and running examples. |
| `GET /up` | Laravel health check. |

### Query: list teams (optional filter by `id`)

```graphql
query Teams {
  pokemonTeam {
    id
    name
    pokemon {
      id
      uuid
      name
      types
      stats {
        name
        value
      }
    }
    createdAt
    updatedAt
  }
}
```

### Query: fetch one Pokémon by name (PokéAPI)

```graphql
query OnePokemon {
  getPokemon(name: "pikachu") {
    id
    name
    image
    types
    stats {
      name
      value
    }
  }
}
```

### Mutation: create a team

```graphql
mutation CreateTeam {
  createPokemonTeam(input: { name: "Kanto squad" }) {
    id
    name
    createdAt
  }
}
```

### Mutation: add or remove a Pokémon on a team

`action` must be `add` or `remove`. For `remove`, supply `pokemonUuid` from the stored team member. For `add`, supply `pokemonId` (PokéAPI species id).

```graphql
mutation AddMember {
  updatePokemonTeam(
    input: { id: 1, action: "add", pokemonId: 25 }
  ) {
    id
    name
    pokemon {
      uuid
      name
    }
  }
}
```

```graphql
mutation RemoveMember {
  updatePokemonTeam(
    input: {
      id: 1
      action: "remove"
      pokemonId: 25
      pokemonUuid: "00000000-0000-0000-0000-000000000000"
    }
  ) {
    id
    name
  }
}
```

### Mutation: delete a team

```graphql
mutation DeleteTeam {
  deletePokemonTeam(id: 1) {
    id
    name
  }
}
```

**Example `curl` against `/graphql`:**

```bash
curl -s -X POST http://localhost:8000/graphql \
  -H "Content-Type: application/json" \
  -d '{"query":"query { getPokemon(name: \"eevee\") { name types stats { name value } } }"}'
```

(Adjust the host/port to match `APP_URL` or your Docker mapping.)

## 5. Future improvements

- **User action logging on MySQL** — Persist an audit trail of mutations and important reads (who changed which team, when) using the MySQL service already provisioned in Docker and `MYSQL_*` environment variables.
- **Team analysis endpoint** — New GraphQL query that returns a **full team analysis** derived from member **stats** and **types** (coverage, weaknesses, bulk scores, etc.) so clients can display strategic insights without reimplementing the rules.
