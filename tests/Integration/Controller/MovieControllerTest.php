<?php
// tests/Integration/Controller/MovieControllerTest.php

namespace App\Tests\Integration\Controller;

use App\Service\MovieDB\MovieDBClient;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class MovieControllerTest extends WebTestCase
{
    private $client;
    private $movieDBClientMock;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        // Mock du service MovieDBClient
        $this->movieDBClientMock = $this->createMock(MovieDBClient::class);

        // Configuration du mock avec des données de test
        $this->movieDBClientMock
            ->method('getGenres')
            ->willReturn([
                ['id' => 1, 'name' => 'Action'],
                ['id' => 2, 'name' => 'Comedy']
            ]);

        $this->movieDBClientMock
            ->method('getPopularMovies')
            ->willReturn([
                'results' => [
                    [
                        'id' => 1,
                        'title' => 'Test Movie',
                        'overview' => 'Test Overview',
                        'poster_path' => '/test.jpg'
                    ]
                ]
            ]);

        // Remplacer le service réel par notre mock
        self::getContainer()->set('App\Service\MovieDB\MovieDBClient', $this->movieDBClientMock);
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1'); // Vérifie qu'il y a un h1
    }

    public function testGenreList(): void
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.genre-list');
        $this->assertSelectorTextContains('.genre-list', 'Action'); // Vérifie qu'on trouve le genre "Action"
    }

    public function testMovieList(): void
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.movie-list');
        $this->assertSelectorTextContains('.movie-list', 'Test Movie'); // Vérifie qu'on trouve le titre du film test
    }

    public function testInvalidRoute(): void
    {
        $this->client->request('GET', '/invalid-route');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Nettoyage après chaque test
        $this->client = null;
        $this->movieDBClientMock = null;
    }
}