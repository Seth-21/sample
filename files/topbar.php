<style>
	.logo {
    margin: auto;
    font-size: 20px;
    background: white;
    padding: 5px 11px;
    border-radius: 30% 30%;
    background-color: white;
}
img{
  height: 45px;
  width: 25%;
}
</style>

<nav class="navbar navbar-dark bg-primary fixed-top " style="padding:0;">
  <div class="container-fluid mt-2 mb-2">
  	<div class="col-lg-12">
  		<div class="col-md-10 float-left" style="display: flex;">
  			<img  src="assets/banner.png">
  		</div>
	  	<div class="col-md-2 mt-2 float-right">
	  		<a class="text-light" href="ajax.php?action=logout"><?php echo ucwords($_SESSION['login_name']) ?> <i class="fa fa-power-off"></i></a>
	    </div>
    </div>
  </div>
  
</nav>