<?php 
include('db_connect.php');
?>
<div class="container-fluid">
	<form action="" id="manage-share">
		<input type="hidden" name="file_id" value="<?php echo isset($_GET['fid']) ? $_GET['fid'] :'' ?>">
		<div class="form-group">
			<label for="name" class="control-label">Share to:</label>
			<input type="text" placeholder="Enter username you wish to share this file..." name="name" id="name" class="form-control" required>
		</div>
		<div class="form-group" id="msg"></div>

	</form>
	<hr>
	<div class="row">
		<label><strong>File shared to:</strong></label>
		
		<table width="100%">
			<?php
				$share = $conn->query("SELECT id,file_sharing.file_id,users.name as share_to
											FROM file_sharing 
											LEFT JOIN users ON file_sharing.share_to_id = users.id
											WHERE `file_id`='$_GET[fid]'");
				while($row = $share->fetch_array()){
					?>
						<tr id="tr<?php echo $row['id'] ?>">
							<td width="90%" class=""><?php echo $row['share_to'] ?></td>
							<td width="10%" class=""><button class="btn btn-sm btn-danger btnRemoveShare" data-id="<?php echo $row['id'] ?>" data-file="<?php echo $row['file_id'] ?>" data-name="<?php echo $row['share_to'] ?>"><i class="fa fa-times"></i></button></td>
						</tr>
					<?php
				}
			?>

		</table>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('#manage-share').submit(function(e){
			e.preventDefault()
			start_load();
			$('#msg').html('')
			$.ajax({
				url:'ajax.php?action=share_file',
				method:'POST',
				data:$(this).serialize(),
				success:function(resp){
					if(typeof resp != undefined){
						resp = JSON.parse(resp);
						if(resp.status == 1){
							alert_toast("File successfully shared.",'success')
							setTimeout(function(){
								location.reload()
							},1500)
						}else{
							$('#msg').html('<div class="alert alert-danger">'+resp.msg+'</div>')
							end_load()
						}
					}
				}
			})
		})

		$(".btnRemoveShare").on('click',function(){
			let c = confirm("Remove sharing for this user?");
			let id = $(this).attr('data-id');
			let name = $(this).attr('data-name');
			let file_id = $(this).attr('data-file');
			if(c){
				$.ajax({
					url:'ajax.php?action=share_remove',
					method:'POST',
					data:{id,
						  name,
						  file_id},
					success:function(resp){
						$('#tr'+id).remove();
						alert_toast("Sharing revoked successfully.",'success')
					}
				})
			}
		})
	})
</script>