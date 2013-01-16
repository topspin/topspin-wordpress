<?php

global $tsProduct;

if($tsProduct) {

?>

<table>
	<tr>
		<th>In Stock Quantity:</th>
		<td><?php echo $tsProduct->meta->product_in_stock_quantity; ?></td>
	</tr>
	<tr>
		<th>Factory SKU:</th>
		<td><?php echo $tsProduct->meta->product_factory_sku; ?></td>
	</tr>
	<tr>
		<th>Max Backorder Quantity:</th>
		<td><?php echo $tsProduct->meta->product_max_backorder_quantity; ?></td>
	</tr>
	<tr>
		<th>Reserve Quantity:</th>
		<td><?php echo $tsProduct->meta->product_reserve_quantity; ?></td>
	</tr>
	<tr>
		<th>Available:</th>
		<td><?php echo ($tsProduct->meta->product_available) ? 'Yes' : 'No'; ?></td>
	</tr>
	<tr>
		<th>Sold/Shipped Quantity:</th>
		<td><?php echo $tsProduct->meta->product_sold_shipped_quantity; ?></td>
	</tr>
</table>


<?php

}

?>