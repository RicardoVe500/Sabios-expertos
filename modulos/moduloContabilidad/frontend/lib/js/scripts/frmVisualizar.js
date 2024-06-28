document.getElementById('togglePassword').addEventListener('click', function () {
    var passwordField = document.getElementById('clave');
    var type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);
    this.textContent = type === 'password' ? '👁️‍🗨️' : '👁️';
});