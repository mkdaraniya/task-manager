import './bootstrap';
import './echo';  // Import Echo
import Swal from 'sweetalert2';

// Global AJAX error handling
$.ajaxSetup({
    error: function(xhr) {
        if (xhr.status === 422) {
            const errors = xhr.responseJSON.errors;
            let msg = '';
            for (let field in errors) {
                msg += errors[field][0] + ' ';
            }
            Swal.fire('Validation Error', msg, 'error');
        } else if (xhr.status === 403) {
            Swal.fire('Forbidden', 'You do not have permission.', 'error');
        } else {
            Swal.fire('Error', 'Something went wrong!', 'error');
        }
    }
});

// Dark mode toggle (Metronic has it)
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('kt_theme_mode');
    if (toggle) {
        toggle.addEventListener('click', function() {
            // Toggle class on body
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
        });
    }
});
