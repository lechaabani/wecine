// assets/app.js
import './styles/app.scss';
import { initializeMovieFunctions } from './js/movie-functions';

document.addEventListener('DOMContentLoaded', () => {
    // Initialisation des fonctionnalités des films
    const { loadMovieDetails } = initializeMovieFunctions();  // On récupère la fonction loadMovieDetails

    // Gestion de la recherche
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    let searchTimeout;

    if (searchInput && searchResults) {
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();

            if (query.length < 2) {
                searchResults.innerHTML = '';
                searchResults.classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(async () => {
                try {
                    const response = await fetch(`/api/movies/search?q=${encodeURIComponent(query)}`);
                    const movies = await response.json();

                    if (movies.length > 0) {
                        searchResults.innerHTML = movies.slice(0, 5).map(movie => `
                            <div class="search-result p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-none"
                                 data-movie-id="${movie.id}">
                                <div class="flex items-center space-x-3">
                                    <img src="${movie.poster_url}" 
                                         alt="${movie.title}" 
                                         class="w-12 h-16 object-cover rounded">
                                    <div>
                                        <div class="font-medium text-gray-900">${movie.title}</div>
                                        <div class="text-sm text-gray-500">
                                            ${new Date(movie.release_date).getFullYear()}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `).join('');
                        searchResults.classList.remove('hidden');
                    } else {
                        searchResults.innerHTML = `
                            <div class="p-4 text-gray-500">
                                Aucun résultat trouvé pour "${query}"
                            </div>
                        `;
                        searchResults.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Erreur de recherche:', error);
                    searchResults.innerHTML = `
                        <div class="p-4 text-red-500">
                            Une erreur est survenue lors de la recherche
                        </div>
                    `;
                    searchResults.classList.remove('hidden');
                }
            }, 300);
        });

        // Gestion des clics sur les résultats de recherche
        searchResults.addEventListener('click', (e) => {
            const searchResult = e.target.closest('.search-result');
            if (searchResult) {
                const movieId = searchResult.dataset.movieId;
                loadMovieDetails(movieId);  // Utilisation de la fonction importée
                searchInput.value = '';
                searchResults.classList.add('hidden');
            }
        });

        // Fermer les résultats quand on clique ailleurs
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });
    }

    // Gestion des filtres de genre
    document.querySelectorAll('.genre-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', async () => {
            const selectedGenres = Array.from(document.querySelectorAll('.genre-checkbox:checked'))
                .map(cb => cb.dataset.genreId);

            try {
                let response;
                if (selectedGenres.length) {
                    // Si des genres sont sélectionnés
                    response = await fetch(`/api/genre/${selectedGenres.join(',')}/movies`);
                } else {
                    // Si aucun genre n'est sélectionné, on charge les films populaires
                    response = await fetch('/api/movies/list');
                }

                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }

                const movies = await response.json();

                // Petit debug pour vérifier ce qu'on reçoit
                console.log('Films reçus:', movies);

                updateMoviesGrid(movies);
            } catch (error) {
                console.error('Erreur de filtrage:', error);
                showNotification('Erreur lors du filtrage des films', 'error');
            }
        });
    });
});

// Fonction de mise à jour de la grille des films
function updateMoviesGrid(movies) {
    const moviesGrid = document.getElementById('moviesGrid');
    if (!moviesGrid) return;

    moviesGrid.innerHTML = movies.map(movie => `
        <article class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="aspect-[2/3]">
                <img src="${movie.poster_url}"
                     alt="${movie.title}"
                     class="w-full h-full object-cover rounded-t-lg" />
            </div>
            <div class="p-4">
                <h3 class="text-lg font-medium text-gray-900 line-clamp-1">${movie.title}</h3>
                <div class="flex items-center space-x-2 mt-2">
                    <div class="flex space-x-0.5">
                        ${Array(5).fill().map((_, i) => `
                            <svg class="w-4 h-4 ${i < Math.round(movie.vote_average / 2) ? 'text-yellow-400' : 'text-gray-300'}"
                                 fill="currentColor"
                                 viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        `).join('')}
                    </div>
                    <span class="text-xs text-gray-500">(${movie.vote_count})</span>
                </div>
                <p class="text-sm text-gray-500 mt-2">${movie.overview.length > 100 ? movie.overview.slice(0, 100) + '...' : movie.overview}</p>
                <button class="view-details mt-4 w-full bg-blue-600 text-white text-sm font-medium py-2 px-4 rounded-md hover:bg-blue-700 transition-colors"
                        data-movie-id="${movie.id}">
                    Lire le détail
                </button>
            </div>
        </article>
    `).join('');

    // Réinitialiser les écouteurs d'événements
    initializeMovieFunctions();
}