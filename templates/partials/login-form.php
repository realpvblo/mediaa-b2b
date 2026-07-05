<form method="post" class="mediaa-b2b-form">

    <div class="mediaa-form-row">

        <label for="mediaa_email">

            Adres e-mail

        </label>

        <input
            id="mediaa_email"
            type="email"
            name="mediaa_email"
            required>

    </div>

    <div class="mediaa-form-row">

        <label for="mediaa_password">

            Hasło

        </label>

        <input
            id="mediaa_password"
            type="password"
            name="mediaa_password"
            required>

    </div>

    <?php
    wp_nonce_field(
        'mediaa_b2b_login',
        'mediaa_b2b_nonce'
    );
    ?>

    <button
        type="submit"
        class="mediaa-button">
        Zaloguj się
    </button>

</form>