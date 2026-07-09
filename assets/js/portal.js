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

document
    .querySelectorAll('.mediaa-b2b-form input')
    .forEach((input) => {

        input.addEventListener('invalid', () => {

            input.classList.add('is-invalid');

        });

        input.addEventListener('input', () => {

            if (input.checkValidity()) {

                input.classList.remove('is-invalid');

            }

        });

    });