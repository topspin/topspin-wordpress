<?php

/*
 *
 *	Last Modified:		September 22, 2011
 *
 *	--------------------------------------
 *	Change Log
 *	--------------------------------------
 *	2011-09-22
 		- Created page
 *
 */

global $store;

$ordersList = $store->orders_get_list();

?>

<div class="wrap">
	<h2>Orders</h2>

    <table class="topspin-orders-list wp-list-table widefat fixed" cellspacing="0">
        <thead>
            <tr>
            	<th scope="col" id="id" class="id-column">ID</th>
            	<th scope="col" id="shipping" class="shipping-column">Shipping Info</th>
            	<th scope="col" id="phone" class="phone-column">Phone</th>
            	<th scope="col" id="fan" class="fan-column">Fan</th>
            	<th scope="col" id="details" class="details-column">Details</th>
            	<th scope="col" id="subtotal" class="subtotal-column">Subtotal</th>
            </tr>
        </thead>
        <?php if(count($ordersList)) : ?>
        <tbody id="the-list">
        	<?php foreach($ordersList as $order) : ?>
            <tr id="id-<?php echo $order->id;?>" valign="top">
            	<td class="order-id"><?php echo $order->id; ?></td>
            	<td class="order-shipping">
            		<strong><?php echo $order->shipping_address_firstname; ?> <?php echo $order->shipping_address_lastname; ?></strong><br/>
            		<?php echo $order->shipping_address_address1; ?> <?php echo $order->shipping_address_address2; ?><br/>
            		<?php echo $order->shipping_address_city; ?>, <?php echo $order->shipping_address_state; ?> <?php echo $order->shipping_address_postal_code; ?>, <?php echo $order->shipping_address_country; ?>
            	</td>
            	<td class="order-phone"><?php echo $order->phone; ?></td>
            	<td class="order-fan"><a href="mailto:<?php echo $order->fan; ?>"><?php echo $order->fan; ?></a></td>
            	<td class="order-details"><a href="<?php echo $order->details_url; ?>" target="_blank"><?php echo $order->details_url; ?></a></td>
            	<td class="order-subtotal"><?php echo $order->symbol; ?><?php echo money_format('%i',$order->subtotal); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <?php else : ?>
        <tbody id="the-list">
        	<tr class="no-items"><td colspan="5">There are no orders cached yet.</td></tr>
        </tbody>
        <?php endif; ?>
        <tfoot>
            <tr>
            	<th scope="col" id="id" class="id-column">ID</th>
            	<th scope="col" id="shipping" class="shipping-column">Shipping Info</th>
            	<th scope="col" id="phone" class="phone-column">Phone</th>
            	<th scope="col" id="fan" class="fan-column">Fan</th>
            	<th scope="col" id="details" class="details-column">Details</th>
            	<th scope="col" id="subtotal" class="subtotal-column">Subtotal</th>
            </tr>
        </tfoot>
    </table>
</div>