<?php

defined('ABSPATH') || exit;

$rootPage = get_page_by_path(
    'strefa-partnera'
);

if (! $rootPage) {

?>

<div class="mediaa-company-card">

    <h2>Materiały</h2>

    <p>

        Nie znaleziono strony "Strefa Partnera".

    </p>

</div>

<?php

return;

}

$materialSlug = sanitize_title(
    $_GET['material'] ?? ''
);

if ($materialSlug !== '') {

    $page = get_page_by_path(
        'strefa-partnera/' . $materialSlug
    );

} else {

    $page = $rootPage;

}

if (! $page) {

?>

<div class="mediaa-company-card">

    <p>

        Nie znaleziono materiałów.

    </p>

</div>

<?php

return;

}

?>

<div class="mediaa-company-card">

<?php if ($materialSlug !== '') : ?>

    <p class="mediaa-back-link">

        <a href="<?php echo esc_url(
            home_url('/b2b?tab=downloads')
        ); ?>">

            ← Powrót do listy materiałów

        </a>

    </p>

<?php endif; ?>

    <h2>

        Materiały

    </h2>

    <?php

    $content = apply_filters(
        'the_content',
        $page->post_content
    );

    $content = str_replace(
        home_url('/strefa-partnera/'),
        home_url('/b2b?tab=downloads&material='),
        $content
    );

    echo $content;
    ?>

</div>