<div class="mediaa-b2b-portal mediaa-b2b-b2b">

    <?php if (function_exists('wc_print_notices')) : ?>
        <?php wc_print_notices(); ?>
    <?php endif; ?>

    <h2>Partner B2B</h2>

    <p>
        Zaloguj się do strefy partnera.
    </p>

    <?php require MEDIAA_B2B_PATH . 'templates/partials/login-form.php'; ?>

    <hr>

    <h3>Zostań partnerem B2B</h3>

    <p>
        Nie masz jeszcze konta? Wypełnij formularz i poczekaj
        na akceptację administratora.
    </p>

    <?php require MEDIAA_B2B_PATH . 'templates/partials/register-form.php'; ?>

</div>