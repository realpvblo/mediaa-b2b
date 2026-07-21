<?php

$order = $controller->getOrder(
    $orderId
);

if (
    ! $order
    || ! $controller->canViewOrder($order)
) {

?>

    <p>

        Nie znaleziono zamówienia.

    </p>

<?php

    return;
}

?>

<div class="mediaa-order-details">

    <h2>

        Zamówienie
        #<?php echo esc_html(
                $order->get_order_number()
            ); ?>

    </h2>

    <p>

        <strong>Status:</strong>

        <?php

        echo esc_html(

            wc_get_order_status_name(

                $order->get_status()

            )

        );

        ?>

    </p>

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

    <hr>

    <h3>

        Produkty

    </h3>

    <ul>

        <?php foreach (
            $order->get_items()
            as $item
        ) : ?>

            <li>

                <?php

                echo esc_html(

                    $item->get_name()

                );

                ?>

                ×

                <?php

                echo esc_html(

                    $item->get_quantity()

                );

                ?>

            </li>

        <?php endforeach; ?>

    </ul>

    <hr>

    <strong>

        Łącznie:

    </strong>

    <?php

    echo wp_kses_post(

        $order->get_formatted_order_total()

    );

    ?>

    <p>

        <a href="<?php echo esc_url(

                        home_url('/b2b?tab=orders')

                    ); ?>">

            ← Powrót

        </a>

    </p>

</div>