<?php

namespace MediaaB2B;

class OrdersController
{
    /**
     * Get all orders for current user.
     */
    public function getOrders(): array
    {
        return \wc_get_orders(
            [
                'customer_id' => \get_current_user_id(),
                'limit'       => -1,
                'orderby'     => 'date',
                'order'       => 'DESC',
            ]
        );
    }

    /**
     * Get single order.
     */
    public function getOrder(
        int $orderId
    ): ?\WC_Order {

        $order = \wc_get_order($orderId);

        if (! $order instanceof \WC_Order) {
            return null;
        }

        return $order;
    }

    /**
     * Check permission.
     */
    public function canViewOrder(
        \WC_Order $order
    ): bool {

        $user = \wp_get_current_user();

        if (Roles::isAdministrator($user)) {
            return true;
        }

        return (int) $order->get_customer_id()
            === \get_current_user_id();
    }
}
