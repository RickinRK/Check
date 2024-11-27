
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conectar com o banco de dados
include 'db_connect.php';

if ($_GET['action'] == 'save_order') {
    // Obter os dados do pedido
    $order_number = $_POST['order_number'];
    $total_amount = $_POST['total_amount'];
    $total_tendered = $_POST['total_tendered'];

    // Validação básica
    if (!$order_number || !$total_amount || !$total_tendered) {
        echo "Erro: Dados inválidos.";
        exit;
    }

    // Inserir o pedido na tabela `orders`
    $sql = "INSERT INTO orders (order_number, total_amount, total_tendered, date_created) 
            VALUES ('$order_number', '$total_amount', '$total_tendered', NOW())";
    
    if ($conn->query($sql)) {
        $order_id = $conn->insert_id;  // Obtém o ID do pedido inserido

        // Inserir os itens do pedido na tabela `order_items`
        if (isset($_POST['item_id'])) {
            $item_ids = $_POST['item_id'];
            $qtys = $_POST['qty'];
            $prices = $_POST['price'];
            $amounts = $_POST['amount'];

            foreach ($item_ids as $index => $item_id) {
                $qty = $qtys[$index];
                $price = $prices[$index];
                $amount = $amounts[$index];

                // Inserir cada item do pedido
                $conn->query("INSERT INTO order_items (order_id, product_id, qty, price, amount) 
                              VALUES ('$order_id', '$item_id', '$qty', '$price', '$amount')");
            }
        }

        echo $order_id;  // Retorna o ID do pedido salvo
    } else {
        echo "Erro ao salvar pedido: " . $conn->error;
    }
}
?>
