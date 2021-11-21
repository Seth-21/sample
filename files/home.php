<style>
	.custom-menu {
        z-index: 1000;
	    position: absolute;
	    background-color: #ffffff;
	    border: 1px solid #0000001c;
	    border-radius: 5px;
	    padding: 8px;
	    min-width: 13vw;
}
a.custom-menu-list {
    width: 100%;
    display: flex;
    color: #4c4b4b;
    font-weight: 600;
    font-size: 1em;
    padding: 1px 11px;
}
	span.card-icon {
    position: absolute;
    font-size: 3em;
    bottom: .2em;
    color: #ffffff80;
}
.file-item{
	cursor: pointer;
}
a.custom-menu-list:hover,.file-item:hover,.file-item.active {
    background: #80808024;
}
table th,td{
	/*border-left:1px solid gray;*/
}
a.custom-menu-list span.icon{
		width:1em;
		margin-right: 5px
}
</style>
<nav aria-label="breadcrumb ">
  <ol class="breadcrumb">
  <li class="breadcrumb-item text-primary">Home</li>
  </ol>
</nav>
<div class="containe-fluid">
	<?php include('db_connect.php') ;
	$files = $conn->query("SELECT f.*,u.name as uname 
						   	FROM files f 
						   	LEFT JOIN users u on u.id = f.user_id 
						   	WHERE  (f.user_id ='$_SESSION[login_id]' OR f.is_public = 1) 
						   	AND file_status =0
							UNION
							SELECT f.*,u.name as uname 
							FROM file_sharing 
							LEFT JOIN files f ON f.id = file_sharing.file_id
							LEFT JOIN users u ON f.user_id = u.id
							WHERE share_to_id='$_SESSION[login_id]' AND f.is_public <> 1
						   order by date(date_updated) desc");

	?>
	<div class="row">
		<?php if($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 3): ?>
			<div class="col-lg-12">
				<div class="card col-md-4 offset-2 bg-primary float-left">
					<div class="card-body text-white">
						<h4><b>Users</b></h4>
						<hr>
						<span class="card-icon"><i class="fa fa-users"></i></span>
						<h3 class="text-right"><b><?php echo $conn->query('SELECT * FROM users')->num_rows ?></b></h3>
					</div>
				</div>
				<div class="card col-md-4 offset-2 bg-primary ml-4 float-left">
					<div class="card-body text-white">
						<h4><b>Files</b></h4>
						<hr>
						<span class="card-icon"><i class="fa fa-file"></i></span>
						<h3 class="text-right"><b><?php echo $conn->query('SELECT * FROM files')->num_rows ?></b></h3>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<div class="row mt-3 ml-3 mr-3">
			<div class="card col-md-12">
				<div class="card-body">
					<table width="100%">
						<tr>
							<th width="20%" class="">Uploader</th>
							<th width="30%" class="">Filename</th>
							<th width="20%" class="">Date</th>
							<th width="30%" class="">Description</th>
							<th width="5px" class=""></th>
						</tr>
						<?php 
					while($row=$files->fetch_assoc()):
						$name = explode(' ||',$row['name']);
						$name = isset($name[1]) ? $name[0] ." (".$name[1].").".$row['file_type'] : $name[0] .".".$row['file_type'];
						$img_arr = array('png','jpg','jpeg','gif','psd','tif');
						$doc_arr =array('doc','docx');
						$pdf_arr =array('pdf','ps','eps','prn');
						$icon ='fa-file';
						if(in_array(strtolower($row['file_type']),$img_arr))
							$icon ='fa-image';
						if(in_array(strtolower($row['file_type']),$doc_arr))
							$icon ='fa-file-word';
						if(in_array(strtolower($row['file_type']),$pdf_arr))
							$icon ='fa-file-pdf';
						if(in_array(strtolower($row['file_type']),['xlsx','xls','xlsm','xlsb','xltm','xlt','xla','xlr']))
							$icon ='fa-file-excel';
						if(in_array(strtolower($row['file_type']),['zip','rar','tar']))
							$icon ='fa-file-archive';

					?>
						<tr class='file-item' data-id="<?php echo $row['id'] ?>" data-name="<?php echo $name ?>" data-path="<?php echo $row['file_path'] ?>">
							<td><i><?php echo ucwords($row['uname']) ?></i></td>
							<td><large><span><i class="fa <?php echo $icon ?>"></i></span><b> <?php echo $name ?></b></large>
							<input type="text" class="rename_file" value="<?php echo $row['name'] ?>" data-id="<?php echo $row['id'] ?>" data-type="<?php echo $row['file_type'] ?>" style="display: none">

							</td>
							<td><i><?php echo date('Y/m/d h:i A',strtotime($row['date_updated'])) ?></i></td>
							<td><i><?php echo $row['description'] ?></i></td>
							<?php
								$button="";
								if($row['user_id'] == $_SESSION['login_id']){
									$button='<button class="btn btn-sm btn-danger btnDelete" id="'.$row['id'].'"><i class="fa fa-trash"></i></button>';
								}
							?>
							<td><?= $button ?></td>
						</tr>
							
					<?php endwhile; ?>
					</table>
					
				</div>
			</div>
			
		</div>
	</div>

</div>
<div id="menu-file-clone" style="display: none;">
	<a href="javascript:void(0)" class="custom-menu-list file-option shareFile"><span><i class="fa fa-share"></i> </span> Share</a>
	<a href="javascript:void(0)" class="custom-menu-list file-option download"><span><i class="fa fa-download"></i> </span> Download</a>
</div>
<script>
	//FILE
	$('.file-item').bind("contextmenu", function(event) { 
    event.preventDefault();

    $('.file-item').removeClass('active')
    $(this).addClass('active')
    $("div.custom-menu").hide();
    var custom =$("<div class='custom-menu file'></div>")
	custom.append($('#menu-file-clone').html())
	custom.find('.download').attr('data-id',$(this).attr('data-id'))
	custom.find('.shareFile').attr('data-id',$(this).attr('data-id'))
	custom.find('.download').attr('data-name',$(this).attr('data-name'))
	custom.find('.download').attr('data-path',$(this).attr('data-path'))
    custom.appendTo("body")
	custom.css({top: event.pageY + "px", left: event.pageX + "px"});

	
	$("div.file.custom-menu .download").click(function(e){
		e.preventDefault()
		let name = $(this).attr('data-name');
		let path = $(this).attr('data-path');
		let ext = name.substr(name.length - 3);

		if(ext.toLowerCase() == "png" || ext == ext.toLowerCase() == "jpg"){
			window.open('assets/uploads/'+path,'_blank');
		}else{
			window.open('download.php?id='+$(this).attr('data-id'));
		}
	})

	$("div.file.custom-menu .shareFile").click(function(e){
		e.preventDefault()
		uni_modal('','manage_share.php?fid='+$(this).attr('data-id'))
	})

})
	$(document).bind("click", function(event) {
    $("div.custom-menu").hide();
    $('#file-item').removeClass('active')

});
	$(document).keyup(function(e){

    if(e.keyCode === 27){
        $("div.custom-menu").hide();
    $('#file-item').removeClass('active')

    }
})

	$(".btnDelete").on('click', function(){

		let c = confirm("Are you sure you want to remove this file?");

		if(c){
			$.ajax({
				url:'ajax.php?action=file_remove',
				method:'POST',
				data:{id:$(this).attr('id')},
				success:function(resp){
					alert("File moved to trash.");
					location.reload();
				}
			})
		}
	})
</script>