
document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");

    form.addEventListener("submit", function (e) {
        let hasError = false;

        // حذف الرسائل القديمة و borders
        document.querySelectorAll(".message.error").forEach(el => el.remove());
        document.querySelectorAll(".input-error").forEach(el => el.classList.remove("input-error"));

        // التقاط القيم
        const username = document.getElementById("username");
        const email = document.getElementById("email");
        const password = document.getElementById("password");

        // regex نفس لي ف PHP
        const usernameRegex = /^[a-zA-Z0-9_]{3,20}$/;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

        // التحقق من الاسم
        if (!usernameRegex.test(username.value.trim())) {
            showError(username, "Nom d'utilisateur invalide");
            hasError = true;
        }

        // التحقق من البريد
        if (!emailRegex.test(email.value.trim())) {
            showError(email, "Email invalide");
            hasError = true;
        }

        // التحقق من كلمة السر
        if (!passwordRegex.test(password.value.trim())) {
            showError(password, "Mot de passe faible. Il doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre");
            hasError = true;
        }

        if (hasError) {
            e.preventDefault(); // منع إرسال الفورم إذا كاين خطأ
        }
    });

    function showError(inputElement, message) {
        inputElement.classList.add("input-error");
        const error = document.createElement("p");
        error.className = "message error";
        error.textContent = message;
        inputElement.insertAdjacentElement("afterend", error);
    }
});


document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('.password-toggle');
    const passwordInput = document.getElementById('password');

    toggle.addEventListener('click', () => {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggle.textContent = '🙈';  // باش تبان وجه العين مسدودة كي تشير إلى الإخفاء
        } else {
            passwordInput.type = 'password';
            toggle.textContent = '👁️';  // العين مفتوحة للإظهار
        }
    });
});
