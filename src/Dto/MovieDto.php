<?php

namespace App\Dto;

class MovieDto implements \JsonSerializable
{
    private int $id;
    private string $title;
    private string $overview;
    private ?string $posterPath;
    private ?string $posterUrl;
    private ?string $backdropPath;
    private ?string $backdropUrl;
    private float $voteAverage;
    private int $voteCount;
    private array $genres;
    private \DateTimeImmutable $releaseDate;
    private ?string $videoKey;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->title = $data['title'];
        $this->overview = $data['overview'];
        $this->posterPath = $data['poster_path'] ?? null;
        $this->posterUrl = $data['poster_url'] ?? null;
        $this->backdropPath = $data['backdrop_path'] ?? null;
        $this->backdropUrl = $data['backdrop_url'] ?? null;
        $this->voteAverage = $data['vote_average'];
        $this->voteCount = $data['vote_count'];
        $this->genres = $data['genre_ids'] ?? $data['genres'] ?? [];
        $this->releaseDate = new \DateTimeImmutable($data['release_date']);
        $this->videoKey = $data['video_key'] ?? null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getOverview(): string
    {
        return $this->overview;
    }

    public function getPosterPath(): ?string
    {
        return $this->posterPath;
    }

    public function getPosterUrl(): ?string
    {
        return $this->posterUrl;
    }

    public function getBackdropPath(): ?string
    {
        return $this->backdropPath;
    }

    public function getBackdropUrl(): ?string
    {
        return $this->backdropUrl;
    }

    public function getVoteAverage(): float
    {
        return $this->voteAverage;
    }

    public function getVoteCount(): int
    {
        return $this->voteCount;
    }

    public function getGenres(): array
    {
        return $this->genres;
    }

    public function getReleaseDate(): \DateTimeImmutable
    {
        return $this->releaseDate;
    }

    public function getVideoKey(): ?string
    {
        return $this->videoKey;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'overview' => $this->overview,
            'poster_path' => $this->posterPath,
            'poster_url' => $this->posterUrl,
            'backdrop_path' => $this->backdropPath,
            'backdrop_url' => $this->backdropUrl,
            'vote_average' => $this->voteAverage,
            'vote_count' => $this->voteCount,
            'genres' => $this->genres,
            'release_date' => $this->releaseDate->format('Y-m-d'),
            'video_key' => $this->videoKey
        ];
    }
}