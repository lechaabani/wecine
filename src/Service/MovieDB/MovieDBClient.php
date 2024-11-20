<?php
// src/Service/MovieDB/MovieDBClient.php

namespace App\Service\MovieDB;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class MovieDBClient
{
    private string $apiKey;
    private string $baseUrl;
    private string $imageBaseUrl;
    private FilesystemAdapter $cache;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly ParameterBagInterface $params
    ) {
        $this->apiKey = $this->params->get('moviedb.api_key');
        $this->baseUrl = $this->params->get('moviedb.base_url');
        $this->imageBaseUrl = $this->params->get('moviedb.image_base_url');
        $this->cache = new FilesystemAdapter();
    }

    public function getGenres(): array
    {
        return $this->cache->get('movie_genres', function (ItemInterface $item) {
            $item->expiresAfter(3600); // Cache for 1 hour

            $response = $this->httpClient->request('GET', "{$this->baseUrl}/genre/movie/list", [
                'query' => [
                    'api_key' => $this->apiKey,
                    'language' => 'en-US'
                ]
            ]);

            return $response->toArray()['genres'];
        });
    }

    public function getPopularMovies(int $page = 1): array
    {
        $cacheKey = sprintf('popular_movies_page_%d', $page);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($page) {
            $item->expiresAfter(1800); // Cache for 30 minutes

            $response = $this->httpClient->request('GET', "{$this->baseUrl}/movie/popular", [
                'query' => [
                    'api_key' => $this->apiKey,
                    'language' => 'en-US',
                    'page' => $page
                ]
            ]);

            return $response->toArray();
        });
    }

    public function getMoviesByGenre(int $genreId, int $page = 1): array
    {
        $cacheKey = sprintf('movies_genre_%d_page_%d', $genreId, $page);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($genreId, $page) {
            $item->expiresAfter(1800);

            $response = $this->httpClient->request('GET', "{$this->baseUrl}/discover/movie", [
                'query' => [
                    'api_key' => $this->apiKey,
                    'with_genres' => $genreId,
                    'language' => 'en-US',
                    'page' => $page
                ]
            ]);

            return $response->toArray();
        });
    }

    public function getImageUrl(string $path, string $size = 'w500'): string
    {
        return $this->imageBaseUrl . $size . $path;
    }
}