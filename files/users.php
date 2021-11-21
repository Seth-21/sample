
<nav aria-label="breadcrumb ">
  <ol class="breadcrumb">
  <li class="breadcrumb-item text-primary">Home</li>
  </ol>
</nav>
<div class="container-fluid">
	
	<div class="row">
	<div class="col-lg-12">
		<?php
			if($_SESSION['login_type']==1){
				?><button class="btn btn-primary float-right btn-md" id="new_user"><i class="fa fa-plus"></i> New user</button><?php
			}
		?>
	</div>
	</div>
	<br>
	<div class="row">
		<div class="card col-lg-12">
			<div class="card-body">
				<table class="table-striped table-bordered col-md-12">
			<thead>
				<tr>
					<th class="text-center">#</th>
					<th class="text-center">Name</th>
					<th class="text-center">BU Email</th>
					<th class="text-center">User Type</th>
					<?php
						if($_SESSION['login_type']==1){
							?><th class="text-center">Action</th><?php
						}
					?>
				</tr>
			</thead>
			<tbody>
				<?php
 					include 'db_connect.php';
 					$users = $conn->query("SELECT *,
					 							CASE 
												 	WHEN `type` = '1' THEN 'Administrator'
												 	WHEN `type` = '2' THEN 'Standard User'
													ELSE 'Faculty'
												END as user_type
					 					   FROM users 
										   order by name asc");
 					$i = 1;
 					while($row= $users->fetch_assoc()):
				 ?>
				 <tr>
				 	<td>
				 		<?php echo $i++ ?>
				 	</td>
				 	<td>
				 		<?php echo $row['name'] ?>
				 	</td>
				 	<td>
				 		<?php echo $row['username'] ?>
				 	</td>
					<td><?php echo $row['user_type'] ?></td>
					<?php
						if($_SESSION['login_type']==1){
							?>				 	
								<td>
									<center>
										<div class="btn-group">
											<button type="button" class="btn btn-danger">Action</button>
											<button type="button" class="btn btn-danger dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<span class="sr-only">Toggle Dropdown</span>
											</button>
											<div class="dropdown-menu">
											<a class="dropdown-item edit_user" href="javascript:void(0)" data-id = '<?php echo $row['id'] ?>'>Edit</a>
											<div class="dropdown-divider"></div>
											<a class="dropdown-item delete_user" href="javascript:void(0)" data-id = '<?php echo $row['id'] ?>'>Delete</a>
											</div>
										</div>
									</center>
								</td>
							<?php
						}
					?>

				 </tr>
				<?php endwhile; ?>
			</tbody>
		</table>
			</div>
		</div>
	</div>

</div>
<script>
	
$('#new_user').click(function(){
	uni_modal('New User','manage_user.php')
})
$('.edit_user').click(function(){
	uni_modal('Edit User','manage_user.php?id='+$(this).attr('data-id'))
})

</script>