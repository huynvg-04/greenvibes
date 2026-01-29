if (history.scrollRestoration) {
    history.scrollRestoration = 'manual'; 
}

window.addEventListener('onbeforeunload', function () {
    window.scrollTo(0, 0); 
});

document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector(".register-form");
    const btn = document.getElementById("btnRegister");
    const btnText = btn.querySelector(".btn-text");
    const btnLoader = btn.querySelector(".btn-loading");

    const passwordField = document.getElementById("password");
    const confirmField = document.getElementById("password-confirm");
    const strengthBar = document.getElementById("strength-bar");
    const strengthText = document.getElementById("strength-text");
    const matchText = document.getElementById("match-text");

    if (form) {
        form.addEventListener("submit", function(e) {
            btn.disabled = true;
            btnText.classList.add('d-none');
            btnLoader.classList.remove('d-none');
        });
    }

    window.addEventListener("pageshow", function() {
        if (btn) {
            btn.disabled = false;
            btnText.classList.remove('d-none');
            btnLoader.classList.add('d-none');
        }
    });

    if (passwordField) {
        passwordField.addEventListener("input", function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);

            strengthBar.className = 'strength-bar ' + strength.class;
            strengthText.textContent = strength.text;
            strengthText.style.color = strength.color;

            checkPasswordMatch();
        });
    }

    if (confirmField) {
        confirmField.addEventListener("input", checkPasswordMatch);
    }

    function checkPasswordStrength(password) {
        if (password.length === 0) {
            return {
                class: '',
                text: 'Độ mạnh mật khẩu',
                color: '#7F8C8D'
            };
        }

        let score = 0;
        if (password.length >= 8) score++;
        if (/[a-z]/.test(password)) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^a-zA-Z0-9]/.test(password)) score++;

        switch (score) {
            case 0:
            case 1:
                return { class: 'weak', text: 'Rất yếu', color: 'var(--color-primary)' };
            case 2:
                return { class: 'fair', text: 'Yếu', color: 'var(--color-primary)' };
            case 3:
                return { class: 'good', text: 'Tốt', color: 'var(--color-primary)' };
            case 4:
            case 5:
                return { class: 'strong', text: 'Mạnh', color: 'var(--color-primary)' };
            default:
                return { class: '', text: 'Độ mạnh mật khẩu', color: 'var(--color-primary)' };
        }
    }

    function checkPasswordMatch() {
        // Kiểm tra xem element có tồn tại không trước khi lấy value
        if (!passwordField || !confirmField) return;

        const password = passwordField.value;
        const confirm = confirmField.value;

        if (confirm.length === 0) {
            matchText.textContent = '';
            matchText.className = 'match-text';
            return;
        }

        if (password === confirm) {
            matchText.textContent = 'Mật khẩu khớp';
            matchText.className = 'match-text match';
        } else {
            matchText.textContent = 'Mật khẩu không khớp';
            matchText.className = 'match-text no-match';
        }
    }

    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function() {
            this.parentElement.parentElement.classList.remove('focused');
        });
    });
});

// Định nghĩa hàm togglePassword toàn cục để HTML onclick có thể gọi được
window.togglePassword = function(fieldId) {
    const field = document.getElementById(fieldId);
    const eye = document.getElementById(fieldId + '-eye');

    if (field.type === 'password') {
        field.type = 'text';
        eye.innerHTML = "<i class='far fa-eye-slash'></i>";
    } else {
        field.type = 'password';
        eye.innerHTML = "<i class='far fa-eye'></i>";
    }
};

const link = document.createElement('link');
link.href = 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&family=Source+Sans+Pro:wght@400;600&display=swap';
link.rel = 'stylesheet';
document.head.appendChild(link);