<?php
// src/Controller/MovieController.php

namespace App\Controller;

use App\Service\MovieDB\MovieDBClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MovieController extends AbstractController
{
    public function __construct(
        private readonly MovieDBClient $movieDBClient
    ) {}

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $genres = $this->movieDBClient->getGenres();
        $popularMovies = $this->movieDBClient->getPopularMovies();

        return $this->render('movie/index.html.twig', [
            'genres' => $genres,
            'movies' => $popularMovies['results'],
            'page_title' => 'WeCine - Movie Discovery'
        ]);
    }
}