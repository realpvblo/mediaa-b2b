<?php

namespace MediaaB2B;

class ProductManager
{
    private const META_B2B_PRICE = '_mediaa_b2b_price';

    private const META_ONLY_B2B = '_mediaa_only_b2b';

    public function register(): void
    {
        \add_action(
            'woocommerce_product_options_pricing',
            [$this, 'renderB2BPriceField']
        );

        \add_action(
            'woocommerce_admin_process_product_object',
            [$this, 'saveB2BPriceField']
        );

        \add_filter(
            'woocommerce_product_get_price',
            [$this, 'overrideProductPrice'],
            10,
            2
        );

        \add_filter(
            'woocommerce_product_variation_get_price',
            [$this, 'overrideProductPrice'],
            10,
            2
        );

        \add_action(
            'woocommerce_product_options_general_product_data',
            [$this, 'renderOnlyB2BField']
        );

        \add_action(
            'woocommerce_admin_process_product_object',
            [$this, 'saveOnlyB2BField']
        );

        \add_filter(
            'woocommerce_product_is_visible',
            [$this, 'filterProductVisibility'],
            10,
            2
        );

        \add_action(
            'template_redirect',
            [$this, 'protectOnlyB2BProducts']
        );
    }

    public function renderB2BPriceField(): void
    {
        \woocommerce_wp_text_input(
            [
                'id' => self::META_B2B_PRICE,

                'label' => __('Cena B2B', 'mediaa-b2b'),

                'desc_tip' => true,

                'type' => 'price',

                'description' => __(
                    'Cena widoczna tylko dla klientów B2B.',
                    'mediaa-b2b'
                ),
            ]
        );
    }

    public function saveB2BPriceField(
        \WC_Product $product
    ): void {

        $price = sanitize_text_field(

            \wp_unslash(
                $_POST[self::META_B2B_PRICE] ?? ''
            )

        );

        $product->update_meta_data(
            self::META_B2B_PRICE,
            $price
        );
    }

    private function shouldUseB2BPrice(): bool
    {
        if (! \is_user_logged_in()) {
            return false;
        }

        $user = \wp_get_current_user();

        return Roles::canAccessPortal($user);
    }

    private function getB2BPrice(
        \WC_Product $product
    ): string {

        return (string) $product->get_meta(
            self::META_B2B_PRICE,
            true
        );
    }

    public function overrideProductPrice(
        $price,
        \WC_Product $product
    ) {

        if (! $this->shouldUseB2BPrice()) {
            return $price;
        }

        $b2bPrice = $this->getB2BPrice($product);

        if ($b2bPrice === '') {
            return $price;
        }

        return $b2bPrice;
    }

    public function renderOnlyB2BField(): void
    {
        \woocommerce_wp_checkbox(
            [
                'id' => self::META_ONLY_B2B,

                'label' => __(
                    'Produkt tylko dla B2B',
                    'mediaa-b2b'
                ),

                'description' => __(
                    'Produkt będzie widoczny wyłącznie dla partnerów B2B.',
                    'mediaa-b2b'
                ),

                'desc_tip' => true,
            ]
        );
    }

    public function saveOnlyB2BField(
        \WC_Product $product
    ): void {

        $enabled = isset(
            $_POST[self::META_ONLY_B2B]
        )
            ? 'yes'
            : 'no';

        $product->update_meta_data(
            self::META_ONLY_B2B,
            $enabled
        );
    }

    public function filterProductVisibility(
        bool $visible,
        int $productId
    ): bool {

        if ($this->shouldUseB2BPrice()) {
            return $visible;
        }

        $onlyB2B = get_post_meta(
            $productId,
            self::META_ONLY_B2B,
            true
        );

        if ($onlyB2B === 'yes') {
            return false;
        }

        return $visible;
    }

    public function protectOnlyB2BProducts(): void
    {
        if (! \is_product()) {
            return;
        }

        if ($this->shouldUseB2BPrice()) {
            return;
        }

        $productId = get_the_ID();

        $onlyB2B = get_post_meta(
            $productId,
            self::META_ONLY_B2B,
            true
        );

        if ($onlyB2B !== 'yes') {
            return;
        }

        global $wp_query;

        $wp_query->set_404();

        status_header(404);

        nocache_headers();

        include get_query_template('404');

        exit;
    }
}
