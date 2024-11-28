<?php 

?>

<div class="container-fluid">
	
	<div class="row">
	<div class="col-lg-12">
			<button class="btn btn-primary float-right btn-sm" id="new_user"><i class="fa fa-plus"></i> Novo Usuário</button>
	</div>
	</div>
	<br>
	<div class="col-lg-12">
		<div class="card ">
			<div class="card-header"><b>Lista de Usuarios</b></div>
			<div class="card-body">
				<table class="table table-hover">
			<thead>
				<tr>
					<th>#</th>
					<th>Nome</th>
					<th>Username</th>
					<th>Tipo</th>
					<th>Ação</th>
				</tr>
			</thead>
			<!--  Orginal Author Name: Mayuri K. 
 for any PHP, Codeignitor, Laravel OR Python work contact me at mayuri.infospace@gmail.com  
 Visit website : www.mayurik.com -->  
			<tbody>
				<?php
 					include 'db_connect.php';
 					$type = array("","Admin","Staff","Alumnus/Alumna");
 					$users = $conn->query("SELECT * FROM users order by name asc");
 					$i = 1;
 					while($row= $users->fetch_assoc()):
				 ?>
				 <tr>
				 	<td class="text-center">
				 		<?php echo $i++ ?>
				 	</td>
				 	<td>
				 		<?php echo ucwords($row['name']) ?>
				 	</td>
				 	
				 	<td>
				 		<?php echo $row['username'] ?>
				 	</td>
				 	<td>
				 		<?php echo $type[$row['type']] ?>
				 	</td>
				 	<td>
				 		<center>
							<div class="btn-group">
							  <a type="button" class=" dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							    <i class="fa fa-ellipsis-h"></i>
							  </a>
							  <div class="dropdown-menu">
							    <a class="dropdown-item edit_user" href="javascript:void(0)" data-id = '<?php echo $row['id'] ?>'>Editar</a>
							    <div class="dropdown-divider"></div>
							    <a class="dropdown-item delete_user" href="javascript:void(0)" data-id = '<?php echo $row['id'] ?>'>Deletar</a>
							  </div>
							</div>
						</center>
				 	</td>
				 </tr>
				<?php endwhile; ?>
			</tbody>
		</table>
			</div>
		</div>
	</div>

</div>
<script>
	$('table').dataTable();
$('#new_user').click(function(){
	uni_modal('Novo usuario','manage_user.php')
})
$('.edit_user').click(function(){
	uni_modal('Editar usuario','manage_user.php?id='+$(this).attr('data-id'))
})
$('.delete_user').click(function(){
		_conf("Tem certeza de que deseja excluir este usuário?","delete_user",[$(this).attr('data-id')])
	})
	function delete_user($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_user',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Deletado",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>
 