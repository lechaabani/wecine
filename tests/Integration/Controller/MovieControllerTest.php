<?php

namespace App\Tests\Integration\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Service\MovieDB\MovieDBClient;
use App\Dto\MovieDto;

class MovieControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        // Exemple de données pour un film
        $movieData = [
            'id' => 1,
            'title' => 'Test Movie',
            'overview' => 'This is a test overview.',
            'poster_path' => '/test.jpg',
            'poster_url' => 'https://image.tmdb.org/t/p/w500/test.jpg',
            'release_date' => '2024-01-01',
            'vote_average' => 8.0,
            'vote_count' => 100,
            'genres' => ['Action'],
        ];

        // Mock des méthodes avec des objets MovieDto
        $mockMovieDBClient = $this->createMock(MovieDBClient::class);

        $mockMovieDBClient->method('getGenres')->willReturn([
            ['id' => 1, 'name' => 'Action'],
            ['id' => 2, 'name' => 'Comedy']
        ]);

        $mockMovieDBClient->method('getPopularMovies')->willReturn([
            new MovieDto($movieData),
        ]);

        $mockMovieDBClient->method('getMovieDetails')->willReturn(
            new MovieDto($movieData)
        );

        $mockMovieDBClient->method('searchMovies')->willReturn([
            new MovieDto($movieData),
        ]);

        $mockMovieDBClient->method('getMoviesByGenre')->willReturn([
            new MovieDto($movieData),
        ]);

        // Injecter le mock dans le conteneur
        self::getContainer()->set(MovieDBClient::class, $mockMovieDBClient);
    }

    public function testGetMoviesList(): void
    {
        $this->client->request('GET', '/api/movies/list');

        $this->assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
        $this->assertEquals('Test Movie', $response[0]['title']);
    }

    public function testGetMovieDetails(): void
    {
        $this->client->request('GET', '/api/movie/1');

        $this->assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertIsArray($response);
        $this->assertEquals('Test Movie', $response['title']);
        $this->assertEquals(8.0, $response['vote_average']);
    }

    public function testGetGenresList(): void
    {
        $this->client->request('GET', '/api/genre/movie/list');

        $this->assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertIsArray($response);
        $this->assertCount(2, $response);
        $this->assertEquals('Action', $response[0]['name']);
    }

    public function testSearchMovies(): void
    {
        $this->client->request('GET', '/api/movies/search?q=Test');

        $this->assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
        $this->assertEquals('Test Movie', $response[0]['title']);
    }

    public function testGenreMovies(): void
    {
        $this->client->request('GET', '/genre/1');

        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse()->getContent();

        $this->assertStringContainsString('Test Movie', $response);
    }

    public function testApiGenreMovies(): void
    {
        $this->client->request('GET', '/api/genre/1/movies');

        $this->assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
        $this->assertEquals('Test Movie', $response[0]['title']);
    }

    public function testRateMovie(): void
    {
        $this->client->request('POST', '/api/movie/1/rate', [
            'rating' => 5,
        ]);

        $this->assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertIsArray($response);
        $this->assertTrue($response['success']);
    }
}
