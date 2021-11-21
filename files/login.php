<link href="assets/login/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="assets/login/css/bootstrap.min.js"></script>
<script src="assets/login/css/jquery.min.js"></script>

<!DOCTYPE html>
<?php include('./header.php'); ?>
<?php 
    session_start();
    if(isset($_SESSION['login_id']))
    header("location:index.php?page=home");
?>
<html>
    <head>
    <title>Admin | Blog Site</title>
        <link rel="stylesheet" href="assets/bootstrap.min.css" 
        <link rel="stylesheet" href="assets/all.css" >
        <link rel="stylesheet" type="text/css" href="assets/login/css/styles.css">
    </head>
    <body>
        <div class="container">
            <div class="d-flex justify-content-center h-100">
                <div class="card">
                    <div class="card-header">
                        <h3>Sign In</h3>
                    </div>
                    <div class="card-body">
                        <form id="login-form" >
                            <div class="input-group form-group">
                                <input type="email" id="username" name="username"  class="form-control" placeholder="BU E-mail" required>
                                
                            </div>
                            <div class="input-group form-group">
                                <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                            </div>
                            <div class="form-group">
                                <input type="submit" value="Login" class="btn float-right login_btn">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <script>
        $('#login-form').submit(function(e){
            e.preventDefault()
            $('#login-form button[type="button"]').attr('disabled',true).html('Logging in...');
            if($(this).find('.alert-danger').length > 0 )
                $(this).find('.alert-danger').remove();
            $.ajax({
                url:'ajax.php?action=login',
                method:'POST',
                data:$(this).serialize(),
                error:err=>{
                    console.log(err)
            $('#login-form button[type="button"]').removeAttr('disabled').html('Login');

                },
                success:function(resp){
                    if(resp == 1){
                        location.reload('index.php?page=home');
                    }else{
                        $('#login-form').prepend('<div class="alert alert-danger">Username or password is incorrect.</div>')
                        $('#login-form button[type="button"]').removeAttr('disabled').html('Login');
                    }
                }
            })
        })
    </script>	
</html>