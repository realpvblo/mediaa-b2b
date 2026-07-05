document.addEventListener('DOMContentLoaded', () => {

    const button = document.getElementById(
        'mediaa-show-register'
    );

    const form = document.getElementById(
        'mediaa-register-form'
    );

    if (!button || !form) {
        return;
    }

    button.addEventListener('click', () => {

        form.style.display =
            form.style.display === 'none'
                ? 'block'
                : 'none';

    });

});