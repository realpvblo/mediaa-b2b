<?php

defined('ABSPATH') || exit;

$user = wp_get_current_user();

$edit = isset($_GET['edit']);

?>

<?php

if ($edit) :

    require MEDIAA_B2B_PATH

        . 'templates/company-edit.php';

else :

?>

<div class="mediaa-company-card">

    <h2 style="margin-top: 1rem;">Dane firmy</h2>

    <div class="mediaa-company-grid">

        <div class="mediaa-company-item">

            <span>Nazwa firmy</span>

            <strong>

                <?php
                echo esc_html(
                    get_user_meta(
                        $user->ID,
                        'billing_company',
                        true
                    )
                );
                ?>

            </strong>

        </div>

        <div class="mediaa-company-item">

            <span>NIP</span>

            <strong>

                <?php
                echo esc_html(
                    get_user_meta(
                        $user->ID,
                        'billing_nip',
                        true
                    )
                );
                ?>

            </strong>

        </div>

        <div class="mediaa-company-item">

            <span>Imię i nazwisko</span>

            <strong>

                <?php

                echo esc_html(

                    trim(

                        $user->first_name .
                            ' ' .
                            $user->last_name

                    )

                );

                ?>

            </strong>

        </div>

        <div class="mediaa-company-item">

            <span>Telefon</span>

            <strong>

                <?php

                echo esc_html(

                    get_user_meta(

                        $user->ID,

                        'billing_phone',

                        true

                    )

                );

                ?>

            </strong>

        </div>

        <div class="mediaa-company-item">

            <span>Adres e-mail</span>

            <strong>

                <?php
                echo esc_html(
                    $user->user_email
                );
                ?>

            </strong>

        </div>

    </div>

    <div class="mediaa-company-actions">

        <a
            href="<?php echo esc_url(
            home_url('/b2b?tab=company&edit=1')
            ); ?>"
            class="mediaa-button">

            Edytuj dane

        </a>

    </div>

</div>

<?php endif; ?>
