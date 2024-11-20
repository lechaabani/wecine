<?php

namespace App\Controller;

use App\Serializer\MovieSerializer;
use App\Service\MovieDB\MovieDBClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    private MovieDBClient $movieDBClient;
    private MovieSerializer $movieSerializer;

    public function __construct(MovieDBClient $movieDBClient, MovieSerializer $movieSerializer)
    {
        $this->movieDBClient = $movieDBClient;
        $this->movieSerializer = $movieSerializer;
    }

    /**
     * Page d'accueil
     */
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $genres = $this->movieDBClient->getGenres();
        $bestMovie = $this->movieDBClient->getBestMovie();
        $movies = $this->movieDBClient->getPopularMovies();

        return $this->render('movie/index.html.twig', [
            'genres' => $genres,
            'bestMovie' => $bestMovie ? $this->movieSerializer->serialize($bestMovie) : null,
            'movies' => $this->movieSerializer->serializeCollection($movies),
        ]);
    }

    /**
     * Liste des films par genre
     */
    #[Route('/genre/{id}', name: 'app_genre_movies')]
    public function genreMovies(int $id): Response
    {
        $genres = $this->movieDBClient->getGenres();
        $movies = $this->movieDBClient->getMoviesByGenre($id);

        $currentGenre = array_filter($genres, fn($genre) => $genre['id'] === $id);

        return $this->render('movie/genre.html.twig', [
            'genres' => $genres,
            'movies' => $this->movieSerializer->serializeCollection($movies),
            'currentGenre' => $currentGenre ? array_values($currentGenre)[0] : null,
        ]);
    }

    /**
     * Recherche de films via une API
     */
    #[Route('/api/movies/search', name: 'app_movies_search', methods: ['GET'])]
    public function searchMovies(Request $request): JsonResponse
    {
        $query = $request->query->get('q');
        $movies = $this->movieDBClient->searchMovies($query);

        return new JsonResponse($this->movieSerializer->serializeCollection($movies));
    }

    /**
     * Liste des films populaires via l'API
     */
    #[Route('/api/movies/list', name: 'app_movies_list', methods: ['GET'])]
    public function getMoviesList(): JsonResponse
    {
        $movies = $this->movieDBClient->getPopularMovies();
        return new JsonResponse($this->movieSerializer->serializeCollection($movies));
    }

    /**
     * Liste des genres pour l'API
     */
    #[Route('/api/genre/movie/list', name: 'app_genres_list', methods: ['GET'])]
    public function getGenresList(): JsonResponse
    {
        $genres = $this->movieDBClient->getGenres();
        return new JsonResponse($genres);
    }

    /**
     * Films par genre via l'API
     */
    #[Route('/api/genre/{ids}/movies', name: 'app_api_genre_movies', methods: ['GET'])]
    public function apiGenreMovies(string $ids): JsonResponse
    {
        $genreIds = explode(',', $ids);
        $movies = $this->movieDBClient->getMoviesByGenre((int)$genreIds[0]);

        return new JsonResponse($this->movieSerializer->serializeCollection($movies));
    }

    /**
     * Détails d'un film
     */
    #[Route('/api/movie/{id}', name: 'app_movie_details', methods: ['GET'])]
    public function movieDetails(int $id): JsonResponse
    {
        $movie = $this->movieDBClient->getMovieDetails($id);

        return new JsonResponse($this->movieSerializer->serialize($movie));
    }

    /**
     * Notation d'un film
     */
    #[Route('/api/movie/{id}/rate', name: 'app_movie_rate', methods: ['POST'])]
    public function rateMovie(int $id, Request $request): JsonResponse
    {
        $rating = $request->request->get('rating');
        // Pour cet exercice, nous retournons une réponse simulée
        return new JsonResponse(['success' => true]);
    }
}
