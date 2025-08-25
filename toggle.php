<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<title>Test Toggle Password</title>
<style>
  .password-toggle {
    cursor: pointer;
    user-select: none;
    margin-left: 8px;
  }
</style>
</head>
<body>

<input type="password" id="password" placeholder="Password">
<span class="password-toggle">👁️</span>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('.password-toggle');
    const passwordInput = document.getElementById('password');

    if(toggle && passwordInput) {
        toggle.addEventListener('click', () => {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggle.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                toggle.textContent = '👁️';
            }
        });
    }
});
</script>

</body>
</html>
