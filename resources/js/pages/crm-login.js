/**
 * CRM Login JavaScript
 * Handles authentication with the API
 */

import apiService from '../services/api.js';

class CRMLogin {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupPasswordToggle();
        this.checkExistingAuth();
    }

    setupEventListeners() {
        const form = document.getElementById('crmLoginForm');
        form.addEventListener('submit', (e) => this.handleLogin(e));

        // Form validation
        const inputs = form.querySelectorAll('input[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearFieldError(input));
        });
    }

    setupPasswordToggle() {
        const passwordInput = document.getElementById('password');
        const toggleBtn = document.getElementById('password-addon');

        toggleBtn.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            const icon = toggleBtn.querySelector('i');
            icon.classList.toggle('ri-eye-fill');
            icon.classList.toggle('ri-eye-off-fill');
        });
    }

    checkExistingAuth() {
        // Check if user is already authenticated
        const token = localStorage.getItem('auth_token');
        if (token) {
            // Verify token is still valid
            apiService.getCurrentUser()
                .then(response => {
                    if (response.success) {
                        // Redirect to CRM dashboard
                        window.location.href = '/crm';
                    }
                })
                .catch(() => {
                    // Token is invalid, clear it
                    apiService.clearAuthToken();
                });
        }
    }

    async handleLogin(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        // Validate form
        if (!this.validateForm(form)) {
            return;
        }

        const email = formData.get('email');
        const password = formData.get('password');

        try {
            this.setLoading(true);

            const response = await apiService.login(email, password);

            if (response.success) {
                this.showAlert('Login successful! Redirecting...', 'success');

                // Store user data
                localStorage.setItem('user_data', JSON.stringify(response.data.user));

                // Redirect to CRM dashboard
                setTimeout(() => {
                    window.location.href = '/crm';
                }, 1000);
            } else {
                this.showAlert(response.message || 'Login failed', 'danger');
            }
        } catch (error) {
            console.error('Login error:', error);
            this.showAlert(error.message || 'Login failed. Please try again.', 'danger');
        } finally {
            this.setLoading(false);
        }
    }

    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required]');

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        return isValid;
    }

    validateField(input) {
        const value = input.value.trim();
        let isValid = true;
        let message = '';

        // Required field validation
        if (!value) {
            isValid = false;
            message = 'This field is required.';
        }
        // Email validation
        else if (input.type === 'email' && !this.isValidEmail(value)) {
            isValid = false;
            message = 'Please provide a valid email address.';
        }
        // Password validation
        else if (input.type === 'password' && value.length < 6) {
            isValid = false;
            message = 'Password must be at least 6 characters long.';
        }

        this.setFieldValidation(input, isValid, message);
        return isValid;
    }

    clearFieldError(input) {
        input.classList.remove('is-invalid');
        const feedback = input.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.textContent = '';
        }
    }

    setFieldValidation(input, isValid, message) {
        if (isValid) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
        } else {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');

            const feedback = input.parentNode.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.textContent = message;
            }
        }
    }

    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    setLoading(loading) {
        const loginBtn = document.getElementById('loginBtn');
        const spinner = document.getElementById('loginSpinner');

        if (loading) {
            loginBtn.disabled = true;
            spinner.classList.remove('d-none');
            loginBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Signing In...';
        } else {
            loginBtn.disabled = false;
            spinner.classList.add('d-none');
            loginBtn.innerHTML = 'Sign In';
        }
    }

    showAlert(message, type = 'info') {
        const alertContainer = document.getElementById('alertContainer');

        const alertId = 'alert-' + Date.now();
        const alertHtml = `
            <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="ri-${this.getAlertIcon(type)}-line me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        alertContainer.insertAdjacentHTML('beforeend', alertHtml);

        // Auto remove after 5 seconds
        setTimeout(() => {
            const alert = document.getElementById(alertId);
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }

    getAlertIcon(type) {
        const icons = {
            'success': 'check',
            'danger': 'close',
            'warning': 'alert',
            'info': 'information'
        };
        return icons[type] || 'information';
    }
}

// Initialize login when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    new CRMLogin();
});
