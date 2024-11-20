<?php include 'db_connect.php'; ?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">

<?php
// Consulta para o gráfico de vendas por categoria
$data = [["Category", "Sold Per Day"]]; // Títulos das colunas
$query = $conn->query("
    SELECT c.name AS category, COUNT(oi.id) AS total
    FROM order_items oi
    JOIN products p ON p.id = oi.product_id
    JOIN categories c ON c.id = p.category_id
    GROUP BY c.name
");
while ($row = $query->fetch_assoc()) {
    $data[] = [$row['category'], (int)$row['total']];
}
$jsonData = json_encode($data); // Dados no formato JSON para uso no JavaScript

// Consultas para estatísticas do painel
$totalCategorias = $conn->query("SELECT COUNT(*) AS total FROM categories")->fetch_assoc()['total'] ?? 0;
$totalProdutos = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'] ?? 0;
$totalPedidos = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'] ?? 0;
$totalUsuarios = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'] ?? 0;
?>

google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
    // Usando os dados vindos do PHP
    var data = google.visualization.arrayToDataTable(<?php echo $jsonData; ?>);

    var options = {
        title: 'Vendas Recentes',
        pieHole: 0.4 // Gráfico de rosca
    };

    var chart = new google.visualization.PieChart(document.getElementById('piechart'));

    chart.draw(data, options);
}
</script>

<style>
   span.float-right.summary_icon {
    font-size: 3rem;
    position: absolute;
    right: 1rem;
    top: 0;
}
.imgs{
		margin: .5em;
		max-width: calc(100%);
		max-height: calc(100%);
	}
	.imgs img{
		max-width: calc(100%);
		max-height: calc(100%);
		cursor: pointer;
	}
	#imagesCarousel,#imagesCarousel .carousel-inner,#imagesCarousel .carousel-item{
		height: 60vh !important;background: black;
	}
	#imagesCarousel .carousel-item.active{
		display: flex !important;
	}
	#imagesCarousel .carousel-item-next{
		display: flex !important;
	}
	#imagesCarousel .carousel-item img{
		margin: auto;
	}
    	#imagesCarousel img{ 
		width: auto!important;
		height: auto!important;
		max-height: calc(100%)!important;
		max-width: calc(100%)!important;
	}
    
</style>

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

    <!-- Gráfico de Vendas -->
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card border-0">
                <div class="card-body">
                    <div id="piechart" style="width: 100%; height: 350px;"></div>
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

 
<script>
	$('#manage-records').submit(function(e){
        e.preventDefault()
        start_load()
        $.ajax({
            url:'ajax.php?action=save_track',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success:function(resp){
                resp=JSON.parse(resp)
                if(resp.status==1){
                    alert_toast("Salvo",'success')
                    setTimeout(function(){
                        location.reload()
                    },800)

                }
                
            }
        })
    })
    $('#tracking_id').on('keypress',function(e){
        if(e.which == 13){
            get_person()
        }
    })
    $('#check').on('click',function(e){
            get_person()
    })
    function get_person(){
            start_load()
        $.ajax({
                url:'ajax.php?action=get_pdetails',
                method:"POST",
                data:{tracking_id : $('#tracking_id').val()},
                success:function(resp){
                    if(resp){
                        resp = JSON.parse(resp)
                        if(resp.status == 1){
                            $('#name').html(resp.name)
                            $('#address').html(resp.address)
                            $('[name="person_id"]').val(resp.id)
                            $('#details').show()
                            end_load()

                        }else if(resp.status == 2){
                            alert_toast("Tracking ID desconhecido.",'danger');
                            end_load();
                        }
                    }
                }
            })
    }
</script>