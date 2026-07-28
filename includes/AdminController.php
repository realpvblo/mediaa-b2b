<?php

namespace MediaaB2B;

class AdminController
{
    public function register(): void
    {
        \add_action(
            'admin_menu',
            [$this, 'registerMenu']
        );

        \add_action(
            'admin_post_mediaa_b2b_approve_user',
            [$this, 'approveUser']
        );

        // The following action is commented out because the savePartner method is not implemented yet. It will be implemented in the next step.
        // \add_action(
        //     'admin_post_mediaa_b2b_save_partner',
        //     [$this, 'savePartner']
        // );
    }

    public function registerMenu(): void
    {
        $pendingCount = \count($this->getPendingUsers());

        $menuTitle = __('Mediaa B2B', 'mediaa-b2b');

        if ($pendingCount > 0) {
            $menuTitle .= sprintf(
                ' <span class="awaiting-mod">%d</span>',
                $pendingCount
            );
        }

        \add_menu_page(
            $menuTitle,
            $menuTitle,
            'manage_options',
            'mediaa-b2b',
            [$this, 'renderPendingUsersPage'],
            'dashicons-groups',
            56
        );
    }

    private function getPendingUsers(): array
    {
        return \get_users(
            [
                'role'    => Roles::ROLE_B2B_PENDING,
                'orderby' => 'registered',
                'order'   => 'ASC',
            ]
        );
    }

    private function getActiveUsers(): array
    {
        return \get_users(
            [
                'role'    => Roles::ROLE_B2B_CUSTOMER,
                'orderby' => 'registered',
                'order'   => 'ASC',
            ]
        );
    }

    public function approveUser(): void
    {
        if (! current_user_can('manage_options')) {
            wp_die('Brak uprawnień.');
        }

        if (
            ! isset($_POST['mediaa_b2b_nonce'])
            || ! \wp_verify_nonce(
                \sanitize_text_field(
                    \wp_unslash($_POST['mediaa_b2b_nonce'])
                ),
                'mediaa_b2b_approve_user'
            )
        ) {
            \wp_safe_redirect(
                \admin_url('admin.php?page=mediaa-b2b&status=error')
            );

            exit;
        }

        if (! isset($_POST['user_id'])) {
            wp_safe_redirect(
                admin_url('admin.php?page=mediaa-b2b&status=error')
            );

            exit;
        }

        $user = get_user_by(
            'id',
            absint($_POST['user_id'])
        );

        if (! $user instanceof \WP_User) {

            wp_safe_redirect(
                admin_url('admin.php?page=mediaa-b2b&status=error')
            );

            exit;
        }

        $user->set_role(
            Roles::ROLE_B2B_CUSTOMER
        );

        wp_safe_redirect(
            admin_url('admin.php?page=mediaa-b2b&status=approved')
        );

        exit;
    }

    private function renderUsersTable(
        array $users,
        bool $showActions
    ): void
    {
        if (empty($users)) {

            echo '<p>Brak użytkowników.</p>';

            return;
        }

        ?>

        <table class="widefat striped">

            <thead>

                <tr>

                    <th>Firma</th>

                    <th>Osoba kontaktowa</th>

                    <th>E-mail</th>

                    <th>Telefon</th>

                    <th>NIP</th>

                    <th>Data rejestracji</th>

                    <?php if (! $showActions) : ?>

                        <th>Kod partnera</th>

                        <th>Prowizja</th>

                        <th>Partner</th>

                    <?php endif; ?>

                    <th>Akcja</th>

                </tr>

            </thead>

            <tbody>

            <?php foreach ($users as $user) :

                $company = \get_user_meta(
                    $user->ID,
                    'billing_company',
                    true
                );

                $firstName = \get_user_meta(
                    $user->ID,
                    'billing_first_name',
                    true
                );

                $lastName = \get_user_meta(
                    $user->ID,
                    'billing_last_name',
                    true
                );

                $phone = \get_user_meta(
                    $user->ID,
                    'billing_phone',
                    true
                );

                $nip = \get_user_meta(
                    $user->ID,
                    'billing_nip',
                    true
                );

                $contactPerson = \trim(
                    $firstName . ' ' . $lastName
                );

                $partnerCode = PartnerManager::getCode($user->ID);

                $partnerRate = PartnerManager::getRate($user->ID);

                $isPartner = PartnerManager::isPartner($user->ID);

            ?>

                <tr>

                    <td><?php echo esc_html($company); ?></td>

                    <td><?php echo esc_html($contactPerson); ?></td>

                    <td><?php echo esc_html($user->user_email); ?></td>

                    <td><?php echo esc_html($phone); ?></td>

                    <td><?php echo esc_html($nip); ?></td>

                    <td><?php echo esc_html($user->user_registered); ?></td>

                    <?php if (! $showActions) : ?>

                        <td>
                            <?php echo esc_html($partnerCode ?: '—'); ?>
                        </td>

                        <td>
                            <?php echo $isPartner ? esc_html($partnerRate . '%') : '—'; ?>
                        </td>

                        <td>
                            <?php if ($isPartner) : ?>
                                <span class="mediaa-status is-paid">Partner</span>
                            <?php else : ?>
                                <span class="mediaa-status is-pending">Nie</span>
                            <?php endif; ?>
                        </td>

                    <?php endif; ?>

                    <?php if ($showActions) : ?>
                        <td>
                            <form
                                method="post"
                                action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                                <input
                                    type="hidden"
                                    name="action"
                                    value="mediaa_b2b_approve_user">
                                <input
                                    type="hidden"
                                    name="user_id"
                                    value="<?php echo esc_attr($user->ID); ?>">
                                <?php
                                wp_nonce_field(
                                    'mediaa_b2b_approve_user',
                                    'mediaa_b2b_nonce'
                                );
                                ?>
                                <button class="button button-primary">
                                    Akceptuj
                                </button>
                            </form>
                        </td>
                    <?php else: ?>
                        <td>
                            <a
                                href="<?php echo esc_url(admin_url('admin.php?page=mediaa-b2b-edit-partner&user_id=' . $user->ID)); ?>"
                                class="button button-secondary">
                                👤 Edytuj partnera
                            </a>
                        </td>
                    <?php endif; ?>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

        <?php
    }

    public function renderPendingUsersPage(): void
    {
        $pendingUsers = $this->getPendingUsers();
        $activeUsers = $this->getActiveUsers();

        $status = \sanitize_text_field(
            \wp_unslash($_GET['status'] ?? '')
        );

?>

        <div class="wrap">

            <h1>Mediaa B2B</h1>

            <?php if ($status === 'approved') : ?>

                <div class="notice notice-success is-dismissible">
                    <p>Konto zostało aktywowane.</p>
                </div>

            <?php elseif ($status === 'error') : ?>

                <div class="notice notice-error">
                    <p>Nie udało się aktywować konta.</p>
                </div>

            <?php endif; ?>

            <h2>
                Oczekujące konta (<?php echo count($pendingUsers); ?>)
            </h2>

            <?php $this->renderUsersTable($pendingUsers, true); ?>

            <h2 style="margin-top:40px;">
                Aktywni klienci B2B (<?php echo count($activeUsers); ?>)
            </h2>

            <?php $this->renderUsersTable($activeUsers, false); ?>

        </div>

<?php
    }
}
