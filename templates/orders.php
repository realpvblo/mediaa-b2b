<?php

defined('ABSPATH') || exit;

use MediaaB2B\OrdersController;

$controller = new OrdersController();

$orders = $controller->getOrders();

$orderId = absint(
    $_GET['order'] ?? 0
);

if ($orderId > 0) {

    require MEDIAA_B2B_PATH
        . 'templates/order-details.php';

    return;
}

?>

<div class="mediaa-orders">

    <h2 style="margin-top: 1rem;">Moje zamówienia</h2>

    <?php if (empty($orders)) : ?>

        <p>Nie posiadasz jeszcze żadnych zamówień.</p>

    <?php else : ?>

        <div class="mediaa-orders-list">

            <?php foreach ($orders as $order) : ?>

                <article class="mediaa-order-card">

                    <div class="mediaa-order-card__header">

                        <h3>
                            Zamówienie #<?php echo esc_html($order->get_order_number()); ?>
                        </h3>

                        <span class="mediaa-order-status">
                            <?php
                            echo esc_html(
                                wc_get_order_status_name(
                                    $order->get_status()
                                )
                            );
                            ?>
                        </span>

                    </div>

                    <div class="mediaa-order-card__body">

                        <p>
                            <strong>Data:</strong>
                            <?php
                            echo esc_html(
                                wc_format_datetime(
                                    $order->get_date_created()
                                )
                            );
                            ?>
                        </p>

                        <p>
                            <strong>Kwota:</strong>
                            <?php
                            echo wp_kses_post(
                                $order->get_formatted_order_total()
                            );
                            ?>
                        </p>

                    </div>

                    <div class="mediaa-order-card__footer">

                        <a href="<?php

                                    echo esc_url(

                                        home_url(

                                            '/b2b?tab=orders&order='
                                                . $order->get_id()

                                        )

                                    );

                                    ?>">
                            Szczegóły
                        </a>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>