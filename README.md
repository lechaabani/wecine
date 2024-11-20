
# WeCine 🎥

A Symfony-based movie website powered by TheMovieDB API. Explore popular movies, top-rated films, and search by genre, all wrapped in an intuitive user interface.

---

## Table of Contents

- [Features](#features)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Development](#development)
- [Testing](#testing)
- [Usage](#usage)
- [API Documentation](#api-documentation)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)

---

## Features

- **Movie Listings:** Browse popular and top-rated movies.
- **Search:** Find movies using a powerful search engine.
- **Genres:** Filter movies by genre.
- **Responsive Design:** Optimized for desktop and mobile.
- **Built with Symfony:** Modern PHP framework for scalability and maintainability.
- **Integration with TMDB API:** Access movie data from a rich and up-to-date database.

---

## Prerequisites

Make sure the following tools are installed on your system:

- [Docker](https://www.docker.com/) & [Docker Compose](https://docs.docker.com/compose/)
- [PHP 8.2+](https://www.php.net/releases/8.2/)
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) & [Yarn](https://yarnpkg.com/)

---

## Installation

Follow these steps to set up the project locally:

1. **Clone the repository**
   ```bash
   git clone git@github.com:lechaabani/wecine.git
   cd wecine
   ```

2. **Configure environment variables**
   - Copy the sample environment file and edit it:
     ```bash
     cp .env .env.local
     ```
   - Add your TMDB API key in `.env.local`:
     ```
     TMDB_API_KEY=your_api_key_here
     TMDB_BASE_URL=https://api.themoviedb.org/3
     TMDB_IMAGE_BASE_URL=https://image.tmdb.org/t/p/
     ```

3. **Start Docker**
   ```bash
   docker-compose up -d
   ```

4. **Install backend and frontend dependencies**
   ```bash
   docker-compose exec php composer install
   docker-compose exec php yarn install
   ```

5. **Build frontend assets**
   ```bash
   docker-compose exec php yarn build
   ```

6. **Verify installation**
   - Open the application in your browser at: [http://localhost:8080](http://localhost:8080).

---

## Development

### Start the development server
```bash
docker-compose up -d
```

### Watch for asset changes
```bash
docker-compose exec php yarn watch
```

### Run unit and integration tests
```bash
docker-compose exec php bin/phpunit
```

### Useful commands
- **Clear cache:**
  ```bash
  docker-compose exec php bin/console cache:clear
  ```
- **Database migrations:**
  ```bash
  docker-compose exec php bin/console doctrine:migrations:migrate
  ```
- **Static code analysis:**
  ```bash
  docker-compose exec php vendor/bin/phpstan analyse
  ```

---

## Testing

This project includes a test suite to ensure code quality and functionality.

### Run all tests
```bash
docker-compose exec php bin/phpunit
```

### Run a specific test
```bash
docker-compose exec php bin/phpunit tests/Unit/Service/MovieDBClientTest.php
```

---

## Usage

### Browse Movies
Visit [http://localhost:8080](http://localhost:8080) to explore the app. Features include:
- **Homepage:** Display popular and top-rated movies.
- **Genres:** Filter movies by genre using the sidebar.
- **Search:** Use the search bar to find specific movies.

### API Endpoints
You can interact with the application programmatically using the following endpoints:
- `GET /api/movies/list`: Get popular movies.
- `GET /api/movies/search?q={query}`: Search for movies.
- `GET /api/genre/movie/list`: Get the list of genres.
- `GET /api/genre/{id}/movies`: Get movies by genre.
- `GET /api/movie/{id}`: Get movie details.

---

## API Documentation

WeCine uses TheMovieDB API v3 to fetch movie data. Full documentation for the API can be found here:
[TMDB API Documentation](https://developers.themoviedb.org/3)

---

## Troubleshooting

### Common Issues
1. **Invalid API Key:**
   - Ensure your TMDB API key is correctly set in `.env.local`.

2. **Docker fails to start:**
   - Verify Docker is installed and running.
   - Check for conflicting ports (`8080` for the web server).

3. **Assets not loading:**
   - Run `docker-compose exec php yarn build` to rebuild frontend assets.

4. **Tests fail:**
   - Ensure all dependencies are installed using `composer install` and `yarn install`.

### Where to get help
If you encounter issues, please open an issue in the GitHub repository or refer to the Symfony and TMDB documentation.

---

## Contributing

We welcome contributions! To get started:
1. Fork the repository.
2. Create a new branch (`git checkout -b feature/my-feature`).
3. Commit your changes (`git commit -m "Add my feature"`).
4. Push to the branch (`git push origin feature/my-feature`).
5. Open a pull request.

---

## License

This project is licensed under the [MIT License](LICENSE).

Feel free to use, modify, and distribute it as per the terms of the license.
