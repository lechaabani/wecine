<?php
// tests/Unit/Service/MovieDB/MovieDBClientTest.php

namespace App\Tests\Unit\Service\MovieDB;

use App\Service\MovieDB\MovieDBClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MovieDBClientTest extends TestCase
{
    private $httpClient;
    private $parameterBag;
    private $movieDBClient;
    private $response;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->parameterBag = $this->createMock(ParameterBagInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);

        // Configure parameter bag
        $this->parameterBag->method('get')
            ->willReturnMap([
                ['moviedb.api_key', 'test_api_key'],
                ['moviedb.base_url', 'https://api.themoviedb.org/3'],
                ['moviedb.image_base_url', 'https://image.tmdb.org/t/p/']
            ]);

        $this->movieDBClient = new MovieDBClient(
            $this->httpClient,
            $this->parameterBag
        );
    }

    public function testGetGenres(): void
    {
        $expectedData = [
            'genres' => [
                ['id' => 1, 'name' => 'Action'],
                ['id' => 2, 'name' => 'Comedy']
            ]
        ];

        $this->response->method('toArray')
            ->willReturn($expectedData);

        $this->httpClient->method('request')
            ->willReturn($this->response);

        $genres = $this->movieDBClient->getGenres();

        $this->assertEquals($expectedData['genres'], $genres);
    }

    public function testGetPopularMovies(): void
    {
        $expectedData = [
            'results' => [
                [
                    'id' => 1,
                    'title' => 'Test Movie',
                    'overview' => 'Test Overview',
                    'poster_path' => '/test.jpg'
                ]
            ]
        ];

        $this->response->method('toArray')
            ->willReturn($expectedData);

        $this->httpClient->method('request')
            ->willReturn($this->response);

        $movies = $this->movieDBClient->getPopularMovies();

        $this->assertEquals($expectedData, $movies);
    }

    public function testGetMoviesByGenre(): void
    {
        $expectedData = [
            'results' => [
                [
                    'id' => 1,
                    'title' => 'Test Movie',
                    'genre_ids' => [1, 2]
                ]
            ]
        ];

        $this->response->method('toArray')
            ->willReturn($expectedData);

        $this->httpClient->method('request')
            ->willReturn($this->response);

        $movies = $this->movieDBClient->getMoviesByGenre(1);

        $this->assertEquals($expectedData, $movies);
    }

    public function testGetImageUrl(): void
    {
        $path = '/test.jpg';
        $size = 'w500';
        $expectedUrl = 'https://image.tmdb.org/t/p/w500/test.jpg';

        $imageUrl = $this->movieDBClient->getImageUrl($path, $size);

        $this->assertEquals($expectedUrl, $imageUrl);
    }
}