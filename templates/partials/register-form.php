<form method="post" class="mediaa-b2b-form">

    <div class="mediaa-form-row">
        <label for="mediaa_register_first_name">Imię</label>

        <input
            id="mediaa_register_first_name"
            type="text"
            name="mediaa_register_first_name"
            required>
    </div>

    <div class="mediaa-form-row">
        <label for="mediaa_register_last_name">Nazwisko</label>

        <input
            id="mediaa_register_last_name"
            type="text"
            name="mediaa_register_last_name"
            required>
    </div>

    <div class="mediaa-form-row">
        <label for="mediaa_register_company">Firma</label>

        <input
            id="mediaa_register_company"
            type="text"
            name="mediaa_register_company"
            required>
    </div>

    <div class="mediaa-form-row">
        <label for="mediaa_register_nip">NIP</label>

        <input
            id="mediaa_register_nip"
            type="text"
            name="mediaa_register_nip"
            required>
    </div>

    <div class="mediaa-form-row">
        <label for="mediaa_register_phone">Telefon</label>

        <input
            id="mediaa_register_phone"
            type="text"
            name="mediaa_register_phone"
            required>
    </div>

    <div class="mediaa-form-row">
        <label for="mediaa_register_email">Adres e-mail</label>

        <input
            id="mediaa_register_email"
            type="email"
            name="mediaa_register_email"
            required>
    </div>

    <div class="mediaa-form-row">
        <label for="mediaa_register_password">Hasło</label>

        <input
            id="mediaa_register_password"
            type="password"
            name="mediaa_register_password"
            required>
    </div>

    <?php
    wp_nonce_field(
        'mediaa_b2b_register',
        'mediaa_b2b_register_nonce'
    );
    ?>

    <button
        type="submit"
        class="mediaa-button">
        Wyślij zgłoszenie
    </button>

</form>