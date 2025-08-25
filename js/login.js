
document.addEventListener('DOMContentLoaded', () => {
    if (sessionStorage.getItem('alreadyAnimated')) {
        document.querySelector('.background-wrapper')?.classList.remove('animate');
    } else {
        sessionStorage.setItem('alreadyAnimated', 'yes');
    }
});



document.addEventListener('DOMContentLoaded', () => {

    const form = document.querySelector('form');
    const toggle = document.querySelector('.password-toggle');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const emailErrorP = document.getElementById('emailError');
    const passwordErrorP = document.getElementById('passwordError');

    if (form) {
        form.addEventListener('submit', function (e) {
            let valid = true;

            // Reset errors
            emailErrorP.style.display = 'none';
            passwordErrorP.style.display = 'none';
            emailInput.classList.remove('input-error');
            passwordInput.classList.remove('input-error');

            const email = emailInput.value.trim();
            const password = passwordInput.value;

            // Validation email
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email === '') {
                emailErrorP.textContent = "Veuillez saisir votre adresse e-mail.";
                emailErrorP.style.display = 'block';
                emailInput.classList.add('input-error');
                valid = false;
            } else if (!emailPattern.test(email)) {
                emailErrorP.textContent = "L'adresse e-mail n'est pas valide.";
                emailErrorP.style.display = 'block';
                emailInput.classList.add('input-error');
                valid = false;
            }

            // Validation password
            const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
            if (password === '') {
                passwordErrorP.textContent = "Veuillez saisir votre mot de passe.";
                passwordErrorP.style.display = 'block';
                passwordInput.classList.add('input-error');
                valid = false;
            } else if (!passwordPattern.test(password)) {
                passwordErrorP.textContent = "Le mot de passe doit contenir au moins 8 caractères, incluant une majuscule, une minuscule, un chiffre et un symbole.";
                passwordErrorP.style.display = 'block';
                passwordInput.classList.add('input-error');
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    }

    // 👁️ Toggle password visibility
    if (toggle && passwordInput) {
        toggle.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            toggle.textContent = isPassword ? '🙈' : '👁️';
        });
    }

});
