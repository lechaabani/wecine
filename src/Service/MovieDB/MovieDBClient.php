<?php

namespace App\Service\MovieDB;

use App\Dto\MovieDto;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MovieDBClient
{
    private string $apiKey;
    private string $baseUrl;
    private string $imageBaseUrl;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly ParameterBagInterface $params
    ) {
        $this->apiKey = $this->params->get('moviedb.api_key');
        $this->baseUrl = $this->params->get('moviedb.base_url');
        $this->imageBaseUrl = $this->params->get('moviedb.image_base_url');
    }

    public function getPopularMovies(int $page = 1): array
    {
        $data = $this->fetchData('/movie/popular', ['page' => $page]);
        return array_map(
            fn(array $movieData) => $this->createMovieDto($movieData),
            $data['results']
        );
    }

    public function getBestMovie(): ?MovieDto
    {
        $data = $this->fetchData('/movie/top_rated', ['page' => 1]);
        if (empty($data['results'])) {
            return null;
        }

        $bestMovie = $data['results'][0];
        $bestMovie['video_key'] = $this->getMovieTrailer($bestMovie['id']);
        return $this->createMovieDto($bestMovie);
    }

    public function getGenres(): array
    {
        $data = $this->fetchData('/genre/movie/list');
        return $data['genres'];
    }

    public function getMoviesByGenre(int $genreId, int $page = 1): array
    {
        $data = $this->fetchData('/discover/movie', [
            'with_genres' => $genreId,
            'page' => $page
        ]);

        return array_map(
            fn(array $movieData) => $this->createMovieDto($movieData),
            $data['results']
        );
    }

    public function searchMovies(string $query): array
    {
        $data = $this->fetchData('/search/movie', ['query' => $query]);
        return array_map(
            fn(array $movieData) => $this->createMovieDto($movieData),
            $data['results']
        );
    }

    public function getMovieDetails(int $id): MovieDto
    {
        $data = $this->fetchData("/movie/{$id}");
        $data['video_key'] = $this->getMovieTrailer($id) ?? null;

        return $this->createMovieDto($data);
    }

    private function getMovieTrailer(int $movieId): ?string
    {
        $data = $this->fetchData("/movie/{$movieId}/videos");
        foreach ($data['results'] as $video) {
            if ($video['type'] === 'Trailer' && $video['site'] === 'YouTube') {
                return $video['key'];
            }
        }
        return null;
    }

    private function createMovieDto(array $data): MovieDto
    {
        if (isset($data['poster_path'])) {
            $data['poster_url'] = $this->imageBaseUrl . 'w500' . $data['poster_path'];
        }
        if (isset($data['backdrop_path'])) {
            $data['backdrop_url'] = $this->imageBaseUrl . 'original' . $data['backdrop_path'];
        }

        return new MovieDto($data);
    }

    private function fetchData(string $endpoint, array $queryParams = []): array
    {
        $response = $this->httpClient->request('GET', "{$this->baseUrl}{$endpoint}", [
            'query' => array_merge([
                'api_key' => $this->apiKey,
                'language' => 'fr-FR'
            ], $queryParams)
        ]);

        return $response->toArray();
    }
}