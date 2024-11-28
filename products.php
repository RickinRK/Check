<?php include('db_connect.php'); ?>

<div class="container-fluid">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <b>Lista de Produtos</b>
                        <span class="float-right">
                            <button class="btn btn-primary btn-sm" id="new_product">
                                <i class="fa fa-plus"></i> Novo Produto
                            </button>
                        </span>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Categoria</th>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th>Preço</th>
                                    <th>Status</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                $categories = [];
                                $qry = $conn->query("SELECT * FROM categories ORDER BY name ASC");
                                while ($row = $qry->fetch_assoc()) {
                                    $categories[$row['id']] = ucwords($row['name']);
                                }
                                $products = $conn->query("SELECT * FROM products ORDER BY id ASC");
                                while ($row = $products->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++ ?></td>
                                    <td><?php echo $categories[$row['category_id']] ?></td>
                                    <td><?php echo $row['name'] ?></td>
                                    <td><?php echo $row['description'] ?></td>
                                    <td><?php echo number_format($row['price'], 2) ?></td>
                                    <td><?php echo $row['status'] == 1 ? "Disponível" : "Indisponível" ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-primary btn-sm edit_product" 
                                            data-id="<?php echo $row['id'] ?>" 
                                            data-name="<?php echo $row['name'] ?>" 
                                            data-description="<?php echo $row['description'] ?>" 
                                            data-price="<?php echo $row['price'] ?>" 
                                            data-category_id="<?php echo $row['category_id'] ?>" 
                                            data-status="<?php echo $row['status'] ?>">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm delete_product" 
                                            data-id="<?php echo $row['id'] ?>">
                                            <i class="fa fa-trash"></i>
                                        </button>
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
</div>

<!-- Modal -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Novo Produto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="manage-product">
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="form-group">
                        <label for="category_id">Categoria</label>
                        <select name="category_id" id="category_id" class="custom-select select2" required>
                            <option value=""></option>
                            <?php foreach ($categories as $id => $name): ?>
                            <option value="<?php echo $id ?>"><?php echo $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Nome</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Descrição</label>
                        <textarea name="description" id="description" cols="30" rows="4" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="price">Preço</label>
                        <input type="number" class="form-control text-right" name="price" required>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="status" name="status" checked value="1">
                            <label class="custom-control-label" for="status">Disponível</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializa o modal para adicionar produto
    $('#new_product').click(function() {
        $('#productModalLabel').text('Novo Produto');
        $('#manage-product')[0].reset();
        $('#manage-product').find('[name="id"]').val('');
        $('#productModal').modal('show');
    });

    // Abrir modal para editar produto
    $('.edit_product').click(function() {
        $('#productModalLabel').text('Editar Produto');
        let form = $('#manage-product');
        form[0].reset();
        form.find('[name="id"]').val($(this).data('id'));
        form.find('[name="category_id"]').val($(this).data('category_id')).trigger('change');
        form.find('[name="name"]').val($(this).data('name'));
        form.find('[name="description"]').val($(this).data('description'));
        form.find('[name="price"]').val($(this).data('price'));
        $('#status').prop('checked', $(this).data('status') == 1);
        $('#productModal').modal('show');
    });

    // Salvar produto
    $('#manage-product').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: 'ajax.php?action=save_product',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            success: function(resp) {
                if (resp == 1) {
                    alert('Produto adicionado com sucesso!');
                } else if (resp == 2) {
                    alert('Produto atualizado com sucesso!');
                }
                $('#productModal').modal('hide');
                location.reload();
            }
        });
    });

    // Excluir produto
    $('.delete_product').click(function() {
        if (confirm('Tem certeza de que deseja excluir este produto?')) {
            $.ajax({
                url: 'ajax.php?action=delete_product',
                method: 'POST',
                data: { id: $(this).data('id') },
                success: function(resp) {
                    if (resp == 1) {
                        alert('Produto excluído com sucesso!');
                        location.reload();
                    }
                }
            });
        }
    });
});
</script>
