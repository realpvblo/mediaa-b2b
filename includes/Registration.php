<?php

namespace MediaaB2B;

class Registration
{
    public function register(): void
    {
        \add_action(
            'woocommerce_register_form',
            [$this, 'renderCompanyFields']
        );
    }

    /**
     * Render additional B2B fields.
     */
    public function renderCompanyFields(): void
    {
?>
        <p class="form-row form-row-wide">
            <label for="billing_company">
                Company Name <span class="required">*</span>
            </label>

            <input
                type="text"
                class="input-text"
                name="billing_company"
                id="billing_company"
                value="<?php echo esc_attr($_POST['billing_company'] ?? ''); ?>">
        </p>

        <p class="form-row form-row-wide">
            <label for="billing_nip">
                VAT / NIP <span class="required">*</span>
            </label>

            <input
                type="text"
                class="input-text"
                name="billing_nip"
                id="billing_nip"
                value="<?php echo esc_attr($_POST['billing_nip'] ?? ''); ?>">
        </p>
<?php
    }
}
