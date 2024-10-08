<?php 
include 'db_connect.php';
$order = $conn->query("SELECT * FROM orders where id = {$_GET['id']}");
foreach($order->fetch_array() as $k => $v){
	$$k= $v;
}
$items = $conn->query("SELECT o.*,p.name FROM order_items o inner join products p on p.id = o.product_id where o.order_id = $id ");
?>

<style>
	.flex{
		display: inline-flex;
		width: 100%;
	}
	.w-50{
		width: 50%;
	}
	.text-center{
		text-align:center;
	}
	.text-right{
		text-align:right;
	}
	table.wborder{
		width: 100%;
		border-collapse: collapse;
	}
	table.wborder>tbody>tr, table.wborder>tbody>tr>td{
		border:1px solid;
	}
	p{
		margin:unset;
	}

</style>
<div class="container-fluid">
	<img src="assets/uploads/logo.png" alt="Logo CHECK" width="80" height="40">
	<p class="text-center"><b><?php echo $amount_tendered > 0 ? "Nota Fiscal" : "Bill" ?></b></p>
	<hr>
	<div class="flex">
		<div class="w-100">
			<?php if($amount_tendered > 0): ?>
			<p>N.Nota: <b><?php echo $ref_no ?></b></p>
		<?php endif; ?>
			<p>Data: <b><?php echo date("M d, Y",strtotime($date_created)) ?></b></p>
		</div>
	</div>
	<hr>
	<p><b>Lista de Pedidos</b></p>
	<table width="100%">
		<thead>
			<tr>
				<td><b>QTD</b></td>
				<td><b>Pedido</b></td>
				<td class="text-right"><b>Total</b></td>
			</tr>
		</thead>
		<tbody>
			<?php 
			while($row = $items->fetch_assoc()):
			?>
			<tr>
				<td><?php echo $row['qty'] ?></td>
				<td><p><?php echo $row['name'] ?></p><?php if($row['qty'] > 0): ?><small>(<?php echo number_format($row['price'],2) ?>)</small> <?php endif; ?></td>
				<td class="text-right"><?php echo number_format($row['amount'],2) ?></td>
			</tr>
			<?php endwhile; ?>
		</tbody>
	</table>
	<hr>
	<table width="100%">
		<tbody>
			<tr>
				<td><b>Valor Total</b></td>
				<td class="text-right"><b><?php echo number_format($total_amount,2) ?></b></td>
			</tr>
			<?php if($amount_tendered > 0): ?>


			<tr>
				<td><b>Valor Pago</b></td>
				<td class="text-right"><b><?php echo number_format($amount_tendered,2) ?></b></td>
			</tr>
			<tr>
				<td><b>A pagar</b></td>
				<td class="text-right"><b><?php echo number_format($amount_tendered - $total_amount,2) ?></b></td>
			</tr>
		<?php endif; ?>
			
		</tbody>
	</table>
	<hr>
	<p class="text-center"><b>N.Pedido</b></p>
	<h4 class="text-center"><b><?php echo $order_number ?></b></h4>
</div>