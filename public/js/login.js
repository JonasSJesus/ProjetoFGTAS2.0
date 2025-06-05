// Mostrar ou ocultar a senha
document.addEventListener('DOMContentLoaded', function () {
    const toggleSenha = document.getElementById('toggleSenha');
    const senhaInput = document.getElementById('senha');
    const iconSenha = document.getElementById('iconSenha');

    toggleSenha.addEventListener('click', function () {
        if (senhaInput.type === 'password') {
            senhaInput.type = 'text';
            iconSenha.classList.remove('bi-eye');
            iconSenha.classList.add('bi-eye-slash');
        } else {
            senhaInput.type = 'password';
            iconSenha.classList.remove('bi-eye-slash');
            iconSenha.classList.add('bi-eye');
        }
    });


});