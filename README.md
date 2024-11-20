# WeCine

A Symfony-based movie website using TheMovieDB API.

## Prerequisites

- Docker & Docker Compose
- PHP 8.2+
- Composer
- Node.js & Yarn

## Installation

1. Clone the repository
```bash
git clone git@github.com:lechaabani/wecine.git
cd wecine
```

2. Copy the environment file and configure your TMDB API key
```bash
cp .env .env.local
# Edit .env.local and set your TMDB_API_KEY
```

3. Start the Docker environment
```bash
docker-compose up -d
```

4. Install dependencies
```bash
docker-compose exec php composer install
docker-compose exec php yarn install
```

5. Build assets
```bash
docker-compose exec php yarn build
```

## Development

- Start the development server: `docker-compose up -d`
- Watch assets: `docker-compose exec php yarn watch`
- Run tests: `docker-compose exec php bin/phpunit`

## API Documentation

This project uses TheMovieDB API v3. Documentation can be found at:
https://developers.themoviedb.org/3

## License

This project is licensed under the MIT License.