<?php
// src/Dto/MovieDto.php

namespace App\Dto;

class MovieDto
{
    private int $id;
    private string $title;
    private string $overview;
    private ?string $posterPath;
    private float $voteAverage;
    private array $genres;
    private \DateTimeImmutable $releaseDate;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->title = $data['title'];
        $this->overview = $data['overview'];
        $this->posterPath = $data['poster_path'] ?? null;
        $this->voteAverage = $data['vote_average'];
        $this->genres = $data['genre_ids'] ?? [];
        $this->releaseDate = new \DateTimeImmutable($data['release_date']);
    }

    // Getters
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

    public function getVoteAverage(): float
    {
        return $this->voteAverage;
    }

    public function getGenres(): array
    {
        return $this->genres;
    }

    public function getReleaseDate(): \DateTimeImmutable
    {
        return $this->releaseDate;
    }
}