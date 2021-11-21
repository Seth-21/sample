<?php 
include 'db_connect.php';
$folder_parent = isset($_GET['fid'])? $_GET['fid'] : 0;
$folder_filter = $_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 3 ? "" : "AND user_id = '".$_SESSION['login_id']."'";
$folders = $conn->query("SELECT * FROM folders where parent_id = $folder_parent $folder_filter  order by name asc");

// echo "SELECT * FROM files where folder_id = $folder_parent and user_id = '".$_SESSION['login_id']."'  order by name asc";
$file_filter = $_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 3 ? "" : "AND user_id = '".$_SESSION['login_id']."'";
$files = $conn->query("SELECT * 
					   FROM files 
					   WHERE folder_id = $folder_parent $file_filter
					   		AND (files.user_id ='$_SESSION[login_id]' OR files.is_public = 1) 
							AND file_status = 0
					   UNION
					   SELECT f.*
					   FROM file_sharing 
					   LEFT JOIN files f ON f.id = file_sharing.file_id
					   WHERE share_to_id='$_SESSION[login_id]' AND folder_id = $folder_parent");

?>
<style>
	.folder-item{
		cursor: pointer;
	}
	.folder-item:hover{
		background: #eaeaea;
	    color: black;
	    box-shadow: 3px 3px #0000000f;
	}
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
  
<?php 
	$id=$folder_parent;
	while($id > 0)
	{ 

	$path = $conn->query("SELECT * FROM folders where id = $id  order by name asc")->fetch_array(); 
?>
	<li class="breadcrumb-item text-primary"><?php echo $path['name']; ?></li>
<?php
	$id = $path['parent_id'];	
	} 
?> 
	<li class="breadcrumb-item"><a class="text-primary" href="index.php?page=files">Files</a></li>
  </ol>
</nav>
<div class="container-fluid">
	<div class="col-lg-12">

		<div class="row">
			<div class="col-md-8">
				<button class="btn btn-primary btn-sm" id="new_folder"><i class="fa fa-plus"></i> New Folder</button>
				<button class="btn btn-primary btn-sm ml-4" id="new_file"><i class="fa fa-upload"></i> Upload File</button>
			</div>
			<div class="col-md-4">
				<div class="col-md-12 input-group offset-12">
					
					<input type="text" class="form-control" id="search" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
					<div class="input-group-append">
						<span class="input-group-text" id="inputGroup-sizing-sm"><i class="fa fa-search"></i></span>
					</div>
				</div>
			</div>
		</div>
		<div id="div_search">
			<hr> 
			<div class="row">
				<div class="col-md-12"><h4><b>Folders</b></h4></div>
			</div>
			<hr>
			<div class="row">
				<?php 
				while($row=$folders->fetch_assoc()):
				?>
					<div class="card col-md-3 mt-2 ml-2 mr-2 mb-2 folder-item" data-id="<?php echo $row['id'] ?>">
						<div class="card-body">
								<large><span><i class="fa fa-folder"></i></span><b class="to_folder"> <?php echo $row['name'] ?></b></large>
						</div>
					</div>
				<?php endwhile; ?>
			</div>	
			<hr>
			<div class="row">
				<div class="card col-md-12">
					<div class="card-body">
						<table width="100%">
							<tr>
								<th width="40%" class="">Filename</th>
								<th width="20%" class="">Date</th>
								<th width="40%" class="">Description</th>
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
								<td><large><span><i class="fa <?php echo $icon ?>"></i></span><b class="to_file"> <?php echo $name ?></b></large>
								<input type="text" class="rename_file" value="<?php echo $row['name'] ?>" data-id="<?php echo $row['id'] ?>" data-type="<?php echo $row['file_type'] ?>" style="display: none">

								</td>
								<td><i class="to_file"><?php echo date('Y/m/d h:i A',strtotime($row['date_updated'])) ?></i></td>
								<td><i class="to_file"><?php echo $row['description'] ?></i></td>
							</tr>
								
						<?php endwhile; ?>
						</table>
						
					</div>
				</div>
				
			</div>							
		</div>

		

	</div>
</div>
<div id="menu-folder-clone" style="display: none;">
	<a href="javascript:void(0)" class="custom-menu-list file-option edit">Rename</a>
	<a href="javascript:void(0)" class="custom-menu-list file-option delete">Delete</a>
</div>
<div id="menu-file-clone" style="display: none;">
	<a href="javascript:void(0)" class="custom-menu-list file-option shareFile"><span><i class="fa fa-share"></i> </span> Share</a>
	<a href="javascript:void(0)" class="custom-menu-list file-option edit"><span><i class="fa fa-edit"></i> </span>Rename</a>
	<a href="javascript:void(0)" class="custom-menu-list file-option download"><span><i class="fa fa-download"></i> </span>Download</a>
	<a href="javascript:void(0)" class="custom-menu-list file-option delete"><span><i class="fa fa-trash"></i> </span>Delete</a>
</div>

<script>
	
	$('#new_folder').click(function(){
		uni_modal('','manage_folder.php?fid=<?php echo $folder_parent ?>')
	})
	$('#new_file').click(function(){
		uni_modal('','manage_files.php?fid=<?php echo $folder_parent ?>')
	})
	$('.folder-item').dblclick(function(){
		location.href = 'index.php?page=files&fid='+$(this).attr('data-id')
	})
	$('.folder-item').bind("contextmenu", function(event) { 
    event.preventDefault();
    $("div.custom-menu").hide();
    var custom =$("<div class='custom-menu'></div>")
        custom.append($('#menu-folder-clone').html())
        custom.find('.edit').attr('data-id',$(this).attr('data-id'))
        custom.find('.delete').attr('data-id',$(this).attr('data-id'))
		custom.find('.shareFile').attr('data-id',$(this).attr('data-id'))
    custom.appendTo("body")
	custom.css({top: event.pageY + "px", left: event.pageX + "px"});

	$("div.custom-menu .edit").click(function(e){
		e.preventDefault()
		uni_modal('Rename Folder','manage_folder.php?fid=<?php echo $folder_parent ?>&id='+$(this).attr('data-id') )
	})
	$("div.custom-menu .delete").click(function(e){
		e.preventDefault()
		
		let c = confirm("Are you sure you want to remove this file?");

		if(c){
			$.ajax({
				url:'ajax.php?action=file_remove',
				method:'POST',
				data:{id:$(this).attr('data-id')},
				success:function(resp){
					if(typeof resp != undefined){
						resp = JSON.parse(resp);
						if(resp.status== 1){
							alert("File moved to trash.");
							location.reload();
						}else{
							alert("Cannot delete. You do not owned this file.");
							location.reload();
						}
					}

				}
			})
		}
	})
})

	//FILE
	$('.file-item').bind("contextmenu", function(event) { 
    event.preventDefault();

    $('.file-item').removeClass('active')
    $(this).addClass('active')
    $("div.custom-menu").hide();
    var custom =$("<div class='custom-menu file'></div>")
        custom.append($('#menu-file-clone').html())
        custom.find('.edit').attr('data-id',$(this).attr('data-id'))
        custom.find('.delete').attr('data-id',$(this).attr('data-id'))
        custom.find('.download').attr('data-id',$(this).attr('data-id'))
        custom.find('.download').attr('data-name',$(this).attr('data-name'))
        custom.find('.download').attr('data-path',$(this).attr('data-path'))
		custom.find('.shareFile').attr('data-id',$(this).attr('data-id'))
    custom.appendTo("body")
	custom.css({top: event.pageY + "px", left: event.pageX + "px"});
	console.log($(this));
	$("div.file.custom-menu .edit").click(function(e){
		e.preventDefault()
		$('.rename_file[data-id="'+$(this).attr('data-id')+'"]').siblings('large').hide();
		$('.rename_file[data-id="'+$(this).attr('data-id')+'"]').show();
	})
	$("div.file.custom-menu .delete").click(function(e){
		e.preventDefault()
		_conf("Are you sure to delete this file?",'delete_file',[$(this).attr('data-id')])
	})
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

	$('.rename_file').keypress(function(e){
		var _this = $(this)
		if(e.which == 13){
			start_load()
			$.ajax({
				url:'ajax.php?action=file_rename',
				method:'POST',
				data:{id:$(this).attr('data-id'),name:$(this).val(),type:$(this).attr('data-type'),folder_id:'<?php echo $folder_parent ?>'},
				success:function(resp){
					if(typeof resp != undefined){
						resp = JSON.parse(resp);
						if(resp.status== 1){
								_this.siblings('large').find('b').html(resp.new_name);
								end_load();
								_this.hide()
								_this.siblings('large').show()
						}else{
							alert("Cannot rename. You do not owned this file.");
							location.reload();
						}
					}
				}
			})
		}
	})

})
//FILE


	$('.file-item').click(function(){
		if($(this).find('input.rename_file').is(':visible') == true)
    	return false;
		uni_modal($(this).attr('data-name'),'manage_files.php?<?php echo $folder_parent ?>&id='+$(this).attr('data-id'))
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

});
	$(document).ready(function(){
		$('#search').keyup(function(){
			$("#div_search").load('search.php?file='+$(this).val());
		})
	})
	function delete_folder($id){
		start_load();
		$.ajax({
			url:'ajax.php?action=delete_folder',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp == 1){
					alert_toast("Folder successfully deleted.",'success')
						setTimeout(function(){
							location.reload()
						},1500)
				}
			}
		})
	}
	function delete_file($id){
		start_load();
		$.ajax({
			url:'ajax.php?action=file_remove',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp == 1){
					alert_toast("File moved to trash.",'success')
						setTimeout(function(){
							location.reload()
						},1500)
				}else{
					alert_toast("Cannot delete. You do not owned this file.",'danger')
						setTimeout(function(){
							location.reload()
						},1500)					
				}
			}
		})
		// start_load();
		// $.ajax({
		// 	url:'ajax.php?action=delete_file',
		// 	method:'POST',
		// 	data:{id:$id},
		// 	success:function(resp){
		// 		if(resp == 1){
		// 			alert_toast("Folder successfully deleted.",'success')
		// 				setTimeout(function(){
		// 					location.reload()
		// 				},1500)
		// 		}
		// 	}
		// })
	}

</script>