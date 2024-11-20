<?php

namespace App\Tests\Unit\Service\MovieDB;

use App\Dto\MovieDto;
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

    public function testGetPopularMovies(): void
    {
        $expectedData = [
            'results' => [
                [
                    'id' => 1,
                    'title' => 'Test Movie',
                    'overview' => 'Test Overview',
                    'poster_path' => '/test.jpg',
                    'release_date' => '2024-01-01',
                    'vote_average' => 8.0,
                    'vote_count' => 100,
                    'genre_ids' => [1, 2],
                ]
            ]
        ];

        $this->response->method('toArray')
            ->willReturn($expectedData);

        $this->httpClient->method('request')
            ->willReturn($this->response);

        $movies = $this->movieDBClient->getPopularMovies();

        $this->assertCount(1, $movies);
        $this->assertInstanceOf(MovieDto::class, $movies[0]);
        $this->assertEquals('Test Movie', $movies[0]->getTitle());
    }

    public function testGetMoviesByGenre(): void
    {
        $expectedData = [
            'results' => [
                [
                    'id' => 1,
                    'title' => 'Genre Movie',
                    'overview' => 'Genre Overview',
                    'poster_path' => '/genre.jpg',
                    'release_date' => '2024-01-01',
                    'vote_average' => 7.5,
                    'vote_count' => 50,
                    'genre_ids' => [1],
                ]
            ]
        ];

        $this->response->method('toArray')
            ->willReturn($expectedData);

        $this->httpClient->method('request')
            ->willReturn($this->response);

        $movies = $this->movieDBClient->getMoviesByGenre(1);

        $this->assertCount(1, $movies);
        $this->assertInstanceOf(MovieDto::class, $movies[0]);
        $this->assertEquals('Genre Movie', $movies[0]->getTitle());
    }

    public function testGetMovieDetails(): void
    {
        $expectedMovieData = [
            'id' => 1,
            'title' => 'Detailed Movie',
            'overview' => 'Detailed Overview',
            'poster_path' => '/details.jpg',
            'release_date' => '2024-01-01',
            'vote_average' => 9.0,
            'vote_count' => 200,
            'genres' => [['id' => 1, 'name' => 'Action']],
        ];

        $this->response->method('toArray')
            ->willReturn($expectedMovieData);

        $this->httpClient->method('request')
            ->willReturnCallback(function ($method, $url) {
                if (str_contains($url, '/videos')) {
                    $mockResponse = $this->createMock(ResponseInterface::class);
                    $mockResponse->method('toArray')->willReturn([
                        'results' => [
                            [
                                'type' => 'Trailer',
                                'site' => 'YouTube',
                                'key' => 'trailer123'
                            ]
                        ]
                    ]);
                    return $mockResponse;
                }

                return $this->response;
            });

        $movie = $this->movieDBClient->getMovieDetails(1);

        $this->assertInstanceOf(MovieDto::class, $movie);
        $this->assertEquals('Detailed Movie', $movie->getTitle());
        $this->assertEquals('trailer123', $movie->getVideoKey());
    }

}
