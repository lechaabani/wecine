<?php

namespace App\Tests\Unit\Serializer;

use App\Dto\MovieDto;
use App\Serializer\MovieSerializer;
use PHPUnit\Framework\TestCase;

class MovieSerializerTest extends TestCase
{
    private $movieSerializer;

    protected function setUp(): void
    {
        $this->movieSerializer = new MovieSerializer();
    }

    public function testSerialize(): void
    {
        $movieData = [
            'id' => 1,
            'title' => 'Test Movie',
            'overview' => 'This is a test overview.',
            'poster_path' => '/test.jpg',
            'poster_url' => 'https://image.tmdb.org/t/p/w500/test.jpg',
            'backdrop_path' => '/backdrop.jpg',
            'backdrop_url' => 'https://image.tmdb.org/t/p/original/backdrop.jpg',
            'vote_average' => 8.5,
            'vote_count' => 100,
            'genres' => ['Action', 'Comedy'],
            'release_date' => '2024-01-01',
            'video_key' => 'trailer123',
        ];

        $movie = new MovieDto($movieData);
        $serialized = $this->movieSerializer->serialize($movie);

        $this->assertEquals($movieData, $serialized);
    }

    public function testSerializeCollection(): void
    {
        $movieData1 = [
            'id' => 1,
            'title' => 'Test Movie 1',
            'overview' => 'Overview 1',
            'poster_path' => '/test1.jpg',
            'poster_url' => 'https://image.tmdb.org/t/p/w500/test1.jpg',
            'backdrop_path' => '/backdrop1.jpg',
            'backdrop_url' => 'https://image.tmdb.org/t/p/original/backdrop1.jpg',
            'vote_average' => 7.5,
            'vote_count' => 50,
            'genres' => ['Action'],
            'release_date' => '2024-01-01',
            'video_key' => 'trailer1',
        ];

        $movieData2 = [
            'id' => 2,
            'title' => 'Test Movie 2',
            'overview' => 'Overview 2',
            'poster_path' => '/test2.jpg',
            'poster_url' => 'https://image.tmdb.org/t/p/w500/test2.jpg',
            'backdrop_path' => '/backdrop2.jpg',
            'backdrop_url' => 'https://image.tmdb.org/t/p/original/backdrop2.jpg',
            'vote_average' => 8.0,
            'vote_count' => 75,
            'genres' => ['Comedy'],
            'release_date' => '2024-02-01',
            'video_key' => 'trailer2',
        ];

        $movies = [
            new MovieDto($movieData1),
            new MovieDto($movieData2),
        ];

        $serialized = $this->movieSerializer->serializeCollection($movies);

        $this->assertCount(2, $serialized);
        $this->assertEquals([$movieData1, $movieData2], $serialized);
    }
}
