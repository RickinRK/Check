<?php include 'db_connect.php'; ?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">

<?php
// Dados para o gráfico de rosca (PieChart) - Vendas por categoria
$dataCategories = [["Category", "Sold Per Day"]];
$query = $conn->query("
    SELECT c.name AS category, COUNT(oi.id) AS total
    FROM order_items oi
    JOIN products p ON p.id = oi.product_id
    JOIN categories c ON c.id = p.category_id
    GROUP BY c.name
");
while ($row = $query->fetch_assoc()) {
    $dataCategories[] = [$row['category'], (int)$row['total']];
}
$jsonDataCategories = json_encode($dataCategories);

// Dados para o gráfico de área (AreaChart) - Itens pagos e não pagos
$dataOrders = [["Status", "Quantidade"]];
$paidOrders = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE amount_tendered > 0")->fetch_assoc()['total'] ?? 0;
$unpaidOrders = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE amount_tendered = 0")->fetch_assoc()['total'] ?? 0;
$dataOrders[] = ["Pagos", (int)$paidOrders];
$dataOrders[] = ["Não pagos", (int)$unpaidOrders];
$jsonDataOrders = json_encode($dataOrders);

//Categorias para os hub superiores
$totalCategorias = $conn->query("SELECT COUNT(*) AS total FROM categories")->fetch_assoc()['total'] ?? 0;
$totalProdutos = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'] ?? 0;
$totalPedidos = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'] ?? 0;
$totalUsuarios = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'] ?? 0;
?>

google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawCharts);

function drawCharts() {
    // Gráfico de rosca (PieChart)
    var dataCategories = google.visualization.arrayToDataTable(<?php echo $jsonDataCategories; ?>);
    var optionsCategories = {
        title: 'Vendas Recentes por Categoria',
        pieHole: 0.4
    };
    var chartCategories = new google.visualization.PieChart(document.getElementById('piechart'));
    chartCategories.draw(dataCategories, optionsCategories);

    // Gráfico de área (AreaChart)
    var dataOrders = google.visualization.arrayToDataTable([
        ['Status', 'Quantidade'],
        ['Pagos', <?php echo $paidOrders; ?>],
        ['Não pagos', <?php echo $unpaidOrders; ?>]
    ]);
    var optionsOrders = {
        title: 'Pedidos Pagos e Não Pagos',
        hAxis: {title: 'Status', titleTextStyle: {color: '#333'}},
        vAxis: {minValue: 0},
        legend: {position: 'top'}
    };
    var chartOrders = new google.visualization.AreaChart(document.getElementById('areachart'));
    chartOrders.draw(dataOrders, optionsOrders);
}
</script>

<div class="container-fluid">
    <div class="row mt-3 ml-3 mr-3">
        <!-- Estatísticas do Painel -->
        <div class="col-md-3 mb-3">
            <div class="card bg-white border-0">
                <div class="card-body">
                    <h4 class="text-dark">Categorias</h4>
                    <h3><?php echo $totalCategorias; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-white border-0">
                <div class="card-body">
                    <h4 class="text-dark">Pedidos</h4>
                    <h3><?php echo $totalPedidos; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-white border-0">
                <div class="card-body">
                    <h4 class="text-dark">Produtos</h4>
                    <h3><?php echo $totalProdutos; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-white border-0">
                <div class="card-body">
                    <h4 class="text-dark">Usuários</h4>
                    <h3><?php echo $totalUsuarios; ?></h3>
                </div>
            </div>
        </div>
    </div>
    

    <!-- Gráfico de Vendas por Categoria (Pizza/Donut) -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card border-0">
                <div class="card-body">
                    <div id="piechart" style="width: 100%; height: 350px;"></div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Área (Itens Pagos e Não Pagos) -->
        <div class="col-md-6 mb-3">
            <div class="card border-0">
                <div class="card-body">
                    <div id="areachart" style="width: 100%; height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Pedidos -->
    <div class="row">
        <div class="col-md-12 mb-5">
            <div class="card">
                <div class="card-header">
                    <b>Lista de Pedidos</b>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Data</th>
                                <th>Nota Fiscal</th>
                                <th>Número do Pedido</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $i = 1;
                            $orders = $conn->query("SELECT * FROM orders ORDER BY unix_timestamp(date_created) DESC");
                            while ($row = $orders->fetch_assoc()):
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $i++; ?></td>
                                <td><?php echo date("M d, Y", strtotime($row['date_created'])); ?></td>
                                <td><?php echo $row['amount_tendered'] > 0 ? $row['ref_no'] : 'N/A'; ?></td>
                                <td><?php echo $row['order_number']; ?></td>
                                <td class="text-right"><?php echo number_format($row['total_amount'], 2); ?></td>
                                <td class="text-center">
                                    <?php if ($row['amount_tendered'] > 0): ?>
                                        <span class="badge badge-success">Pago</span>
                                    <?php else: ?>
                                        <span class="badge badge-primary">Não pago</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
