<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Argo Almacenadora Login</title>

        <!-- CSS -->
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
        <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">
		    <link rel="stylesheet" href="assets/css/form-elements.css">
        <link rel="stylesheet" href="assets/css/style.css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- Favicon and touch icons -->
        <link rel="shortcut icon" href="assets/ico/favicon.png">
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
        <link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">

    </head>

    <body>
<style type="text/css" media="screen">

</style>
        <!-- Top content -->
        <div class="top-content">

            <div class="inner-bg">


                <div class="container">
                    <div class="row">
                    <!-- INICIA HEADER -->
                    <div class="breadcrumb_header">
                        <img alt="argo" src="assets/img/backgrounds/header1.png">
                    </div>

                        <div class="col-sm-8 col-sm-offset-2 text">
                            <!-- <h1 style="display: inline"><strong>Argo</strong> Almacenadora</h1>
                            <img style="display:inline;" alt="argo" src="assets/img/backgrounds/logo2.png">                           -->
                            <div class="description">
                            	<!--<p>
	                            	This is a free responsive login form made with Bootstrap.
	                            	Download it on <a href="http://azmind.com"><strong>AZMIND</strong></a>, customize and use it as you like!
                            	</p>-->
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-sm-offset-3 form-box">
                        	<div class="form-top">
                        		<div class="form-top-left">
                        			<!--  <h3>Accede a nuestro sitio</h3>-->
                        			<img style="display:inline;" alt="argo" src="assets/img/backgrounds/logo2.png">
                              		<h3 style="display: inline"><strong>Argo</strong> Almacenadora</h3>


                                    <div id="msgSubmit1" style="color: white;" class="hidden" >Acceso no permitido, usuario y/o clave incorrectos.</div>
                                    <div id="msgSubmit2" style="color: white;" class="hidden" >Debe introducir usuario y clave.</div>
                                    <div id="msgSubmit3" style="color: white;" class="hidden" >Debe introducir un usuario valido.</div>
                                    <div id="msgSubmit4" style="color: white;" class="hidden" >Debe introducir una clave valida.</div>

                        		</div>
                        		<div class="form-top-right">
                        			<i class="fa fa-lock"></i>
                        		</div>
                            </div>
                            <div class="form-bottom">
			                    <!--  <form if="loginArgo" role="form" action="sesion/login.php" method="post" class="login-form">-->
			                    <form id="loginArgo" role="form"  action="sesion/login.php" method="post" class="login-form">
			                    	<div class="form-group">
			                    		<label class="sr-only" for="formusername">Usuario</label>
			                        	<input type="text" name="formusername" placeholder="Usuario..." class="form-username form-control" id="formusername" style="text-transform:lowercase;">
			                        </div>
			                        <div class="form-group">
			                        	<label class="sr-only" for="formpassword">Clave</label>
			                        	<input type="password" name="formpassword" placeholder="Clave..." class="form-password form-control" id="formpassword" style="text-transform:lowercase;">
			                        </div>
			                        <button type="submit" class="btn">Iniciar sesión</button>
			                    </form>
		                    </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-sm-offset-3 social-login">
                        	<strong><font size="2" color="navy">Copyright © <?= date('Y') ?></font> <a style="color:#FFFFFF" href="http://www.argoalmacenadora.com.mx/index.php"> Argo Almacenadora.</a> </strong> <font size="2"  color="navy">Todos los derechos reservados.</font>
                        	<div class="social-login-buttons">

	                        	<!-- <a class="btn btn-link-2" href="#">
	                        		<i class="fa fa-facebook"></i> Facebook
	                        	</a> -->
	                        	<!-- <a class="btn btn-link-2" href="#">
	                        		<i class="fa fa-twitter"></i> Twitter
	                        	</a>
	                        	<a class="btn btn-link-2" href="#">
	                        		<i class="fa fa-google-plus"></i> Google Plus
	                        	</a> -->
                        	</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- Javascript -->
        <script src="assets/js/jquery-1.11.1.min.js"></script>
        <script src="assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="assets/js/jquery.backstretch.min.js"></script>
        <script src="assets/js/scripts.js"></script>

        <!--[if lt IE 10]>
            <script src="assets/js/placeholder.js"></script>
        <![endif]-->

        <script type="text/javascript">
        	$("#loginArgo").submit(function(event){
        		event.preventDefault();
        		var usuario = $("#formusername").val();
        		var password = $("#formpassword").val();
            password = password.toLowerCase();
            usuario = usuario.toLowerCase();
            console.log(password);
        		if(usuario == '' && password == ''){
        			$("#msgSubmit1").addClass("hidden");
        			$("#msgSubmit2").removeClass("hidden");
        			$("#msgSubmit3").addClass("hidden");
        			$("#msgSubmit4").addClass("hidden");
        			return;
        		}

        		if(usuario == ''){
        			$("#msgSubmit1").addClass("hidden");
        			$("#msgSubmit2").addClass("hidden");
        			$("#msgSubmit3").removeClass("hidden");
        			$("#msgSubmit4").addClass("hidden");
        			return;
        		}

        		if(password == ''){

        			$("#msgSubmit1").addClass("hidden");
        			$("#msgSubmit2").addClass("hidden");
        			$("#msgSubmit3").addClass("hidden");
        			$("#msgSubmit4").removeClass("hidden");
        			return;
        		}

        		$.ajax({
        			type: "POST",
        			url: "sesion/login.php",
        			data: "formusername=" + usuario + "&formpassword=" + encodeURIComponent(password),
        			success: function(text){
                console.log("formusername=" + usuario + "&formpassword=" + encodeURIComponent(password));
                console.log(text);
        				if(text == "error"){
        					    $("#msgSubmit1").removeClass("hidden");
                			$("#msgSubmit2").addClass("hidden");
                			$("#msgSubmit3").addClass("hidden");
                			$("#msgSubmit4").addClass("hidden");
        				}
                if (text == "error3") {
                      alert("La vigencia de tu contraseña ha terminado actualizarla en el exodo.");
                }

                if (text == "errornegativo"){
        					$("#msgSubmit1").removeClass("hidden");
                			$("#msgSubmit2").addClass("hidden");
                			$("#msgSubmit3").addClass("hidden");
                			$("#msgSubmit4").addClass("hidden");
        				}


        				if(text == "success"){
        					window.location="seccion/index.php";
        				}

        			}
        		});
        	});
        </script>

    </body>

</html>
