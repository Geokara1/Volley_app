// signup_validation.js
// Τρέχει όταν φορτωθεί πλήρως το HTML

document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('signupForm');

    // ─── ASYNC USERNAME CHECK ───────────────────────────────────────────
    // Ελέγχει αν το username υπάρχει ήδη στη DB όταν ο χρήστης φεύγει από το πεδίο
    document.getElementById('username').addEventListener('blur', function () {
        const username = this.value.trim();
        if (username.length === 0) return;

        if (username.includes(' ')) {
            showError('username', 'Το username δεν επιτρέπεται να περιέχει κενά.');
            return;
        }

        // AJAX call στο check_username.php
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

    // ─── FORM SUBMIT ────────────────────────────────────────────────────
    form.addEventListener('submit', function (e) {
        e.preventDefault(); // σταματά την υποβολή
        clearAllErrors();

        let isValid = true;

        // 1. Όνομα — μόνο γράμματα (ελληνικά + λατινικά)
        const firstName = document.getElementById('firstName').value.trim();
        if (!validateName(firstName)) {
            showError('firstName', 'Το όνομα πρέπει να περιέχει μόνο γράμματα.');
            isValid = false;
        }

        // 2. Επίθετο — μόνο γράμματα
        const lastName = document.getElementById('lastName').value.trim();
        if (!validateName(lastName)) {
            showError('lastName', 'Το επίθετο πρέπει να περιέχει μόνο γράμματα.');
            isValid = false;
        }

        // 3. Ρόλος — υποχρεωτική επιλογή
        const role = document.getElementById('role').value;
        if (!role) {
            showError('role', 'Παρακαλώ επιλέξτε ρόλο.');
            isValid = false;
        }

        // 4. Τηλέφωνο — ακριβώς 10 ψηφία
        const phone = document.getElementById('phone').value.trim();
        if (!validatePhone(phone)) {
            showError('phone', 'Το τηλέφωνο πρέπει να αποτελείται από ακριβώς 10 ψηφία.');
            isValid = false;
        }

        // 5. Email — έγκυρη μορφή
        const email = document.getElementById('email').value.trim();
        if (!validateEmail(email)) {
            showError('email', 'Παρακαλώ εισάγετε έγκυρη διεύθυνση email.');
            isValid = false;
        }

        // 6. Username — χωρίς κενά (η async έλεγξε ύπαρξη στη DB)
        const username = document.getElementById('username').value.trim();
        if (!validateUsername(username)) {
            showError('username', 'Το username δεν μπορεί να περιέχει κενά ή να είναι κενό.');
            isValid = false;
        }
        // Αν η async έλεγξε και βρήκε ότι υπάρχει ήδη, μην προχωρήσεις
        const usernameErrEl = document.getElementById('usernameError');
        if (usernameErrEl && usernameErrEl.style.display === 'block') {
            isValid = false;
        }

        // 7. Password — min 5 χαρακτήρες + τουλάχιστον 1 σύμβολο
        const password = document.getElementById('password').value;
        if (!validatePassword(password)) {
            showError('password', 'Ο κωδικός χρειάζεται τουλάχιστον 5 χαρακτήρες και 1 σύμβολο (π.χ. !@#$).');
            isValid = false;
        }

        // 8. Confirm password — πρέπει να ταιριάζει
        const confirmPassword = document.getElementById('confirmPassword').value;
        if (password !== confirmPassword) {
            showError('confirmPassword', 'Οι κωδικοί δεν ταιριάζουν.');
            isValid = false;
        }

        // Αν όλα είναι σωστά, υποβολή στο signup_handler.php
        if (isValid) {
            form.submit();
        }
    });

});

// ─── VALIDATION FUNCTIONS ────────────────────────────────────────────────────

// Δέχεται μόνο γράμματα (ελληνικά + λατινικά) και κενά/παύλες
function validateName(value) {
    const nameRegex = /^[a-zA-ZΑ-Ωα-ωΆΈΉΊΌΎΏάέήίόύώ\s\-]+$/;
    return value.length > 0 && nameRegex.test(value);
}

// Ακριβώς 10 ψηφία, τίποτα άλλο
function validatePhone(value) {
    return /^\d{10}$/.test(value);
}

// Απλός έλεγχος email format (x@x.xx)
function validateEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
}

// Min 5 χαρακτήρες ΚΑΙ τουλάχιστον 1 ειδικός χαρακτήρας
function validatePassword(value) {
    const hasSpecialChar = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]/.test(value);
    return value.length >= 5 && hasSpecialChar;
}

// Χωρίς κενά, δεν μπορεί να είναι κενό
function validateUsername(value) {
    return value.length > 0 && !/\s/.test(value);
}

// ─── ERROR HELPERS ───────────────────────────────────────────────────────────

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