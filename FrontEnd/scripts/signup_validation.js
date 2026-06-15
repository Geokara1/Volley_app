document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('signupForm');

    document.getElementById('username').addEventListener('blur', function () {
        const username = this.value.trim();
        if (username.length === 0) return;

        if (username.includes(' ')) {
            showError('username', 'Το username δεν επιτρέπεται να περιέχει κενά.');
            return;
        }
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/Volley_app/BackEnd/check_username.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (!response.available) {
                    showError('username', 'Το username χρησιμοποιείται ήδη.');
                } else {
                    clearError('username');
                }
            }
        };
        xhr.send('username=' + encodeURIComponent(username));
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault(); 
        clearAllErrors();

        let isValid = true;

        const firstName = document.getElementById('firstName').value.trim();
        if (!validateName(firstName)) {
            showError('firstName', 'Το όνομα πρέπει να περιέχει μόνο γράμματα.');
            isValid = false;
        }

        const lastName = document.getElementById('lastName').value.trim();
        if (!validateName(lastName)) {
            showError('lastName', 'Το επίθετο πρέπει να περιέχει μόνο γράμματα.');
            isValid = false;
        }

        const role = document.getElementById('role').value;
        if (!role) {
            showError('role', 'Παρακαλώ επιλέξτε ρόλο.');
            isValid = false;
        }

        const phone = document.getElementById('phone').value.trim();
        if (!validatePhone(phone)) {
            showError('phone', 'Το τηλέφωνο πρέπει να αποτελείται από ακριβώς 10 ψηφία.');
            isValid = false;
        }

        const email = document.getElementById('email').value.trim();
        if (!validateEmail(email)) {
            showError('email', 'Παρακαλώ εισάγετε έγκυρη διεύθυνση email.');
            isValid = false;
        }

        const username = document.getElementById('username').value.trim();
        if (!validateUsername(username)) {
            showError('username', 'Το username δεν μπορεί να περιέχει κενά ή να είναι κενό.');
            isValid = false;
        }

        const usernameErrEl = document.getElementById('usernameError');
        if (usernameErrEl && usernameErrEl.style.display === 'block') {
            isValid = false;
        }

        const password = document.getElementById('password').value;
        if (!validatePassword(password)) {
            showError('password', 'Ο κωδικός χρειάζεται τουλάχιστον 5 χαρακτήρες και 1 σύμβολο (π.χ. !@#$).');
            isValid = false;
        }
        
        const confirmPassword = document.getElementById('confirmPassword').value;
        if (password !== confirmPassword) {
            showError('confirmPassword', 'Οι κωδικοί δεν ταιριάζουν.');
            isValid = false;
        }

        if (isValid) {
            form.submit();
        }
    });

});

function validateName(value) {
    const nameRegex = /^[a-zA-ZΑ-Ωα-ωΆΈΉΊΌΎΏάέήίόύώ\s\-]+$/;
    return value.length > 0 && nameRegex.test(value);
}

function validatePhone(value) {
    return /^\d{10}$/.test(value);
}

function validateEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
}

function validatePassword(value) {
    const hasSpecialChar = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]/.test(value);
    return value.length >= 5 && hasSpecialChar;
}

function validateUsername(value) {
    return value.length > 0 && !/\s/.test(value);
}

function showError(fieldId, message) {
    const errorEl = document.getElementById(fieldId + 'Error');
    if (errorEl) {
        errorEl.textContent = message;
        errorEl.style.display = 'block';
    }
    const field = document.getElementById(fieldId);
    if (field) field.classList.add('input-error');
}

function clearError(fieldId) {
    const errorEl = document.getElementById(fieldId + 'Error');
    if (errorEl) {
        errorEl.textContent = '';
        errorEl.style.display = 'none';
    }
    const field = document.getElementById(fieldId);
    if (field) field.classList.remove('input-error');
}

function clearAllErrors() {
    document.querySelectorAll('.error-msg').forEach(el => {
        el.textContent = '';
        el.style.display = 'none';
    });
    document.querySelectorAll('.input-error').forEach(el => {
        el.classList.remove('input-error');
    });
}