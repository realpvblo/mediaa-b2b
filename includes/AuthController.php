<?php

namespace MediaaB2B;

class AuthController
{
    public function register(): void
    {
        \add_action(
            'init',
            [$this, 'handleAuthentication']
        );
    }

    public function handleAuthentication(): void
    {
        if ($this->isLoginRequest()) {
            $this->login();

            return;
        }

        if ($this->isRegisterRequest()) {
            $this->registerUser();

            return;
        }

        if ($this->isLogoutRequest()) {
            $this->logout();
        }
    }

    private function login(): void
    {

        if (! \wp_verify_nonce(
            sanitize_text_field(
                \wp_unslash($_POST['mediaa_b2b_nonce'])
            ),
            'mediaa_b2b_login'
        )) {
            return;
        }

        $data = $this->getLoginData();

        $user = \wp_signon(
            [
                'user_login'    => $data['email'],
                'user_password' => $data['password'],
                'remember'      => true,
            ],
            false
        );

        if (\is_wp_error($user)) {

            \wp_safe_redirect(
                \add_query_arg(
                    'login',
                    'failed',
                    \home_url('/b2b')
                )
            );

            exit;
        }

        \wp_safe_redirect(\home_url('/b2b'));

        exit;
    }

    private function registerUser(): void
    {
        if (! $this->validateRegistration()) {
            return;
        }

        $data = $this->getRegistrationData();

        $userId = $this->createUser($data);

        if (! $userId) {
            return;
        }

        $this->saveCompanyData($userId, $data);

        $this->redirectAfterRegistration();
    }

    private function validateRegistration(): bool
    {
        if (! isset($_POST['mediaa_b2b_register_nonce'])) {
            return false;
        }

        if (
            ! \wp_verify_nonce(
                sanitize_text_field(
                    \wp_unslash($_POST['mediaa_b2b_register_nonce'])
                ),
                'mediaa_b2b_register'
            )
        ) {
            return false;
        }

        $data = $this->getRegistrationData();

        if (empty($data['email'])) {
            return false;
        }

        if (empty($data['password'])) {
            return false;
        }

        if (empty($data['company'])) {
            return false;
        }

        return true;
    }

    private function getRegistrationData(): array
    {
        return [

            'first_name' => sanitize_text_field(
                \wp_unslash($_POST['mediaa_register_first_name'] ?? '')
            ),

            'last_name' => sanitize_text_field(
                \wp_unslash($_POST['mediaa_register_last_name'] ?? '')
            ),

            'company' => sanitize_text_field(
                \wp_unslash($_POST['mediaa_register_company'] ?? '')
            ),

            'nip' => sanitize_text_field(
                \wp_unslash($_POST['mediaa_register_nip'] ?? '')
            ),

            'phone' => sanitize_text_field(
                \wp_unslash($_POST['mediaa_register_phone'] ?? '')
            ),

            'email' => sanitize_email(
                \wp_unslash($_POST['mediaa_register_email'] ?? '')
            ),

            'password' => \wp_unslash(
                $_POST['mediaa_register_password'] ?? ''
            ),

        ];
    }

    private function getLoginData(): array
    {
        return [

            'email' => sanitize_email(
                \wp_unslash($_POST['mediaa_email'] ?? '')
            ),

            'password' => \wp_unslash(
                $_POST['mediaa_password'] ?? ''
            ),

        ];
    }

    private function createUser(array $data): int|false
    {
        $userId = \wp_insert_user([
            'user_login' => sanitize_user(
                $data['email'],
                true
            ),
            'user_email' => $data['email'],
            'user_pass' => $data['password'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'role' => Roles::ROLE_B2B_PENDING,
        ]);

        if (\is_wp_error($userId)) {
            return false;
        }

        return $userId;
    }

    private function saveCompanyData(
        int $userId,
        array $data
    ): void {

        \update_user_meta(
            $userId,
            'billing_first_name',
            $data['first_name']
        );

        \update_user_meta(
            $userId,
            'billing_last_name',
            $data['last_name']
        );

        \update_user_meta(
            $userId,
            'billing_company',
            $data['company']
        );

        \update_user_meta(
            $userId,
            'billing_phone',
            $data['phone']
        );

        \update_user_meta(
            $userId,
            'billing_nip',
            $data['nip']
        );
    }

    private function redirectAfterRegistration(): void
    {
        \wp_safe_redirect(

            \add_query_arg(
                'registered',
                'success',
                \home_url('/b2b')
            )

        );

        exit;
    }

    private function logout(): void
    {
        //
    }

    private function isLoginRequest(): bool
    {
        return isset($_POST['mediaa_b2b_nonce']);
    }

    private function isRegisterRequest(): bool
    {
        return isset($_POST['mediaa_b2b_register_nonce']);
    }

    private function isLogoutRequest(): bool
    {
        return isset($_GET['mediaa_logout']);
    }
}
