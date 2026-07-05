<div class="mediaa-b2b-portal">

    <h2>

        Witaj
        <?php echo esc_html(Auth::user()->display_name); ?>

    </h2>

    <p>

        Jesteś zalogowany jako Partner B2B.

    </p>

    <a href="<?php echo esc_url(wp_logout_url(home_url('/b2b'))); ?>">

        Wyloguj

    </a>

</div>