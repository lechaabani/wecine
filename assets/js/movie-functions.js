// assets/js/movie-functions.js

export function initializeMovieFunctions() {
    // Gestion des détails des films
    const loadMovieDetails = async (movieId) => {
        try {
            const response = await fetch(`/api/movie/${movieId}`);
            const movie = await response.json();

            // Mise à jour du contenu de la modal
            document.getElementById('movieTitle').textContent = movie.title;
            document.getElementById('moviePoster').src = movie.poster_url;
            document.getElementById('movieOverview').textContent = movie.overview;

            // Reset des étoiles
            document.querySelectorAll('.star').forEach(star => {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            });

            // Stockage de l'ID du film
            document.getElementById('movieModal').dataset.movieId = movieId;

            // Affichage de la modal
            showModal('movieModal');
        } catch (error) {
            console.error('Erreur lors du chargement des détails:', error);
        }
    };

    // Gestion des modales
    const showModal = (modalId) => {
        const modal = document.getElementById(modalId);
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    const hideModal = (modalId) => {
        const modal = document.getElementById(modalId);
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };

    // Gestionnaire de notation
    const handleRating = async (rating) => {
        const movieId = document.getElementById('movieModal').dataset.movieId;
        try {
            const response = await fetch(`/api/movie/${movieId}/rate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ rating })
            });

            if (response.ok) {
                // Mise à jour visuelle des étoiles
                updateStars(rating);
                showNotification(`Film noté ${rating} étoiles !`);
            }
        } catch (error) {
            console.error('Erreur de notation:', error);
            showNotification("Erreur lors de l'enregistrement de la note", 'error');
        }
    };

    // Mise à jour visuelle des étoiles
    const updateStars = (rating) => {
        document.querySelectorAll('.star').forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
    };

    // Notification
    const showNotification = (message, type = 'success') => {
        const notification = document.createElement('div');
        notification.className = `fixed bottom-4 right-4 p-4 rounded-lg shadow-lg transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white`;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    };

    // Event Listeners
    // Pour les boutons "voir les détails"
    document.querySelectorAll('.view-details').forEach(button => {
        button.addEventListener('click', () => {
            loadMovieDetails(button.dataset.movieId);
        });
    });

    // Pour les étoiles
    document.querySelectorAll('.star').forEach(star => {
        star.addEventListener('click', () => {
            handleRating(parseInt(star.dataset.star));
        });

        // Effet de survol
        star.addEventListener('mouseenter', () => {
            const rating = parseInt(star.dataset.star);
            updateStars(rating);
        });

        star.addEventListener('mouseleave', () => {
            const currentRating = parseInt(document.getElementById('movieModal').dataset.currentRating || '0');
            updateStars(currentRating);
        });
    });

    // Pour fermer les modales
    window.closeMovieModal = () => hideModal('movieModal');
    window.closeVideoModal = () => {
        hideModal('videoModal');
        const videoFrame = document.getElementById('videoFrame');
        if (videoFrame) {
            videoFrame.src = '';
        }
    };

    // Fermeture avec Echap
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeMovieModal();
            closeVideoModal();
        }
    });

    // Pour la bande-annonce
    window.playTrailer = (videoKey) => {
        if (!videoKey) {
            showNotification('Aucune bande-annonce disponible', 'error');
            return;
        }
        const videoFrame = document.getElementById('videoFrame');
        if (videoFrame) {
            videoFrame.src = `https://www.youtube.com/embed/${videoKey}?autoplay=1`;
            showModal('videoModal');
        }
    };

    return {
        loadMovieDetails
    };
}