<?php

namespace App\Serializer;

use App\Dto\MovieDto;

class MovieSerializer
{
    /**
     * Sérialise un seul MovieDto en tableau
     */
    public function serialize(MovieDto $movie): array
    {
        return [
            'id' => $movie->getId(),
            'title' => $movie->getTitle(),
            'overview' => $movie->getOverview(),
            'poster_path' => $movie->getPosterPath(),
            'poster_url' => $movie->getPosterUrl(),
            'backdrop_path' => $movie->getBackdropPath(),
            'backdrop_url' => $movie->getBackdropUrl(),
            'vote_average' => $movie->getVoteAverage(),
            'vote_count' => $movie->getVoteCount(),
            'genres' => $movie->getGenres(),
            'release_date' => $movie->getReleaseDate()->format('Y-m-d'),
            'video_key' => $movie->getVideoKey(),
        ];
    }

    /**
     * Sérialise une collection de MovieDto
     */
    public function serializeCollection(array $movies): array
    {
        return array_map([$this, 'serialize'], $movies);
    }
}

