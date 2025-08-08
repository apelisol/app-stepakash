import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Add CSRF token to all requests
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Set up axios interceptors for better error handling
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 419) {
            // CSRF token mismatch
            window.showToast('Session expired. Please refresh the page.', 'error');
        } else if (error.response?.status === 429) {
            // Rate limiting
            window.showToast('Too many requests. Please wait a moment.', 'warning');
        } else if (error.response?.status >= 500) {
            // Server errors
            window.showToast('Server error. Please try again later.', 'error');
        }
        return Promise.reject(error);
    }
);