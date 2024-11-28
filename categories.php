<?php include('db_connect.php'); ?>

<div class="container-fluid">
    <div class="col-lg-12">
        <div class="row">
            <!-- Botão para abrir o Modal -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <b>Lista de Categorias</b>
                        <span class="float:right">
                            <button class="btn btn-primary btn-sm col-sm-2 float-right" id="new_category">
                                <i class="fa fa-plus"></i> Adicionar Categorias
                            </button>
                        </span>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                $category = $conn->query("SELECT * FROM categories ORDER BY id ASC");
                                while($row = $category->fetch_assoc()):
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++; ?></td>
                                    <td><?php echo $row['name']; ?></td>
                                    <td><?php echo $row['description']; ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary edit_category" type="button" 
                                                data-id="<?php echo $row['id']; ?>" 
                                                data-name="<?php echo $row['name']; ?>" 
                                                data-description="<?php echo $row['description']; ?>">
                                            <i class="fa fa-edit"></i> Editar
                                        </button>
                                        <button class="btn btn-sm btn-danger delete_category" type="button" 
                                                data-id="<?php echo $row['id']; ?>">
                                            <i class="fa fa-trash-alt"></i> Deletar
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
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="" id="manage-category">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Formulario de Categoria</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="form-group">
                        <label class="control-label">Nome</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Descrição</label>
                        <textarea name="description" id="description" cols="30" rows="4" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // Botão para abrir o modal de novo
    $('#new_category').click(function() {
        $('#categoryModal').modal('show');
        $('#categoryModalLabel').text('Adicionar Categoria');
        $('#manage-category').get(0).reset();
        $('input[name="id"]').val('');
    });

    // Botão para editar
    $('.edit_category').click(function() {
        $('#categoryModal').modal('show');
        $('#categoryModalLabel').text('Editar Categoria');
        var cat = $('#manage-category');
        cat.get(0).reset();
        cat.find("[name='id']").val($(this).attr('data-id'));
        cat.find("[name='name']").val($(this).attr('data-name'));
        cat.find("[name='description']").val($(this).attr('data-description'));
    });

    // Submissão do formulário
    $('#manage-category').submit(function(e) {
        e.preventDefault();
        start_load(); // Adicione sua função de carregamento aqui
        $.ajax({
            url: 'ajax.php?action=save_category',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Categoria adicionada com sucesso!", 'success');
                } else if (resp == 2) {
                    alert_toast("Categoria atualizada com sucesso!", 'success');
                }
                setTimeout(function() {
                    location.reload();
                }, 1500);
                $('#categoryModal').modal('hide');
            }
        });
    });

    // Botão de deletar
    $('.delete_category').click(function() {
        _conf("Você quer mesmo deletar essa categoria?", "delete_category", [$(this).attr('data-id')]);
    });

    // Função para deletar
    function delete_category(id) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_category',
            method: 'POST',
            data: { id: id },
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Categoria deletada com sucesso!", 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                }
            }
        });
    }
});
</script>

<style>
td {
    vertical-align: middle !important;
}
td p {
    margin: unset;
}
</style>
