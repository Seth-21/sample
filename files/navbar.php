<style>
	#myProgress {
	width: 100%;
	background-color: #ddd;
	}

	#myBar {
	width: 100%;
	height: 5px;
	background-color: #04AA6D;
	text-align: center;
	line-height: 30px;
	color: white;
	}
</style>

<nav id="sidebar" class='mx-lt-5 bg-dark' >
		
	<div class="sidebar-list">

			<a href="index.php?page=home" class="nav-item nav-home"><span class='icon-field'><i class="fa fa-home"></i></span> Home</a>
			<a href="index.php?page=files" class="nav-item nav-files"><span class='icon-field'><i class="fa fa-file"></i></span> Files</a>
			<a href="index.php?page=trash" class="nav-item nav-trash"><span class='icon-field'><i class="fa fa-trash"></i></span> Trash</a>
			<?php if($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 3): ?>
				<a href="index.php?page=activity" class="nav-item nav-history"><span class='icon-history'><i class="fa fa-history"></i></span> Recent Activity</a>
				<a href="index.php?page=users" class="nav-item nav-users"><span class='icon-field'><i class="fa fa-users"></i></span> Users</a>
			<?php endif; ?>
	</div>
	<hr>
	<div class="col-md-12">
		Storage
		<div id="myProgress">
			<div id="myBar">10%</div>
			<input type="hidden" id="disk_space" value ="<?php echo disk_total_space("C:") ?>">
			<input type="hidden" id="disk_free" value ="<?php echo disk_free_space("C:") ?>">
		</div>
	</div>
</nav>
<script>
	$('.nav-<?php echo isset($_GET['page']) ? $_GET['page'] : '' ?>').addClass('active')

	show_disk()
	function show_disk(){
		let elem = document.getElementById("myBar");

		let disk_space = $("#disk_space").val() / 1073741824;
		let disk_free = $("#disk_free").val() / 1073741824;

		width =  disk_free / disk_space * 100;

		width_free = 

		elem.style.width = width + "%";
        elem.innerHTML = "<small>"+disk_free.toFixed() +"GB of "+disk_space.toFixed() + "GB</small>";
	}
</script>