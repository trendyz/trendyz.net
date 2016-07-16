<?php

	require 'functions.php';
	date_default_timezone_set('Africa/Cairo');
	 	$link = mysqli_connect("localhost","cl51-users-nr3","D.Uyckj4N","cl51-users-nr3");
	  session_start();
	  $username = $_SESSION['username'];
	  $user_id = $_SESSION['user_id'];
	  $posty = $postyErr = "";
	  	$error = "";
	$successMsg = "";
	if ($_POST)
	{
		if(!$_POST["email"])
		{
			$error .= "*E-mail address is required.<br>";
		}
		if(!$_POST["subject"])
		{
			$error .= "*Subject is required.<br>";
		}
		if(!$_POST["content"])
		{
			$error .= "*Content field could not be left blank.<br>";
		}
		
		if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) == false && $_POST["email"])
		{
			$error .= "*Invalid email address.<br>";
		}
		
		if ($error != "")
		{
		
			$error = '<div class="alert alert-danger" role="alert"><p><strong>There were error(s) in your form</strong></p>' .$error.'</div>';
		}
		else
		{
			$emailTo ="ahmadmuhamo@gmail.com";
			$subject =$_POST["subject"];
			$content =$_POST["content"];
			$headers ="From: ".$_POST["email"];
			
			if (mail($emailTo,$subject,$content,$headers))
			{
				$successMsg = '<div class="alert alert-success" role="alert"><p><strong>Your message was sent, we\'ll contact you ASAP!</strong></p></div>';
			}
			else{
				$error = '<div class="alert alert-danger" role="alert"><p><strong>Your message could not be sent, please try again</strong></p></div>';
			}
		}
			if($_POST["submit"]=='logout!')
			{
				$_SESSION = array();
				session_destroy();
				setcookie("user_id","",time() - 60*60*24*30);
				setcookie("username","",time() - 60*60*24*30);
				setcookie("login_string","",time() - 60*60*24*30);
				 header('Location:login.php ');
				 die();
			}
	}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <link rel="icon" href="img/favicon.png">
    <title>Trendyz</title>
    <!-- Bootstrap core CSS -->
    <link href="bootstrap.3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome.4.6.1/css/font-awesome.min.css" rel="stylesheet">
    <link href="assets/css/animate.min.css" rel="stylesheet">
    <link href="assets/css/timeline.css" rel="stylesheet">
    <link href="assets/css/cover.css" rel="stylesheet">
    <link href="assets/css/forms.css" rel="stylesheet">
    <link href="assets/css/buttons.css" rel="stylesheet">
    <script src="assets/js/jquery.1.11.1.min.js"></script>
    <script src="bootstrap.3.3.6/js/bootstrap.min.js"></script>
    <script src="assets/js/custom.js"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  
  <body>
  
    <!-- Fixed navbar -->
    <nav class="navbar navbar-white navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" style="font-size:300%;" href="index.php"><b>Trendyz</b></a>                                 
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav navbar-right">
			<li><a style="color:#4895E6; font-size:150%; font-weight: bold;" href="profile.php"><?echo $username;?></a></li>
            <li><a href="home.php">Home</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                Account <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li><a href="forgot_pass.php">Recover password</a></li>
				<li><a href="contact.php">Contact support</a></li>
              </ul>
            </li>
			<li>
			<form class="form-inline pull-xs-right" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
				<button style="margin-top: 20px;" type="submit" name="submit" class="btn btn-info" value="logout!">log out</button>
			</form>
			</li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container page-content ">
			<h1>Get in touch!<br></h1>
			<div class="row">
			<div class="form-group col-lg-6" id="error"><? echo $error.$successMsg;?>
			</div>
			</div>
    
	<form method="post">
			<div class="row">
			<fieldset class="form-group col-lg-4">
				<label for="email">Email address</label>
				<div class="input-group">
				<span class="input-group-addon bg-blue bordered-blue"><i class="fa fa-envelope-o"></i></span>
				<input type="email" name="email" class="form-control" id="email" placeholder="email@example.com">
				</div>
				<small class="text-muted">We'll never share your email with anyone else.</small>
			</fieldset>
			</div>
			<div class="row">
			  <fieldset class="form-group col-lg-4">
				<label for="subject">Subject</label>
				<input type="text" name="subject" class="form-control" id="subject">
			  </fieldset>
			</div>
		  <div class="row">
		   <fieldset class="form-group col-lg-6">
			<label for="content">What would you like to ask us?</label>
			<textarea class="form-control" name="content" id="content" placeholder="Message content.. " rows="3"></textarea>
		  </fieldset>
		  </div>
		  
		  <button type="submit" id="submit" class="btn btn-primary">Submit</button>
		</form>
	</div>
    <footer class="footer">
      <div class="container">
        <p class="text-muted"> Trendyz &copy; 2016 - All rights reserved <a href="contact.php" target="_blank"><strong>Contact us!</strong></a> </p>
      </div>
    </footer>
	
	 <!-- jQuery first, then Bootstrap JS. -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	<script type="text/javascript">
		$("form").submit(function (e)
		{
			e.preventDefault();
			
				
		var error = "";
		if ($("#email").val() == "")
		{
			error += "The email is required.<br>";
		}
		if ($("#subject").val() == "")
		{
			error += "The subject field is required.<br>";
		}
		if ($("#content").val() == "")
		{
			error += "The content field is required.";
		}
		if (error != "")
		{
			$("#error").html('<div class="alert alert-danger" role="alert"><p><strong>There were error(s) in your form</strong></p>' + error +'</div>');
		}
		else()
		{
			$("form").unbind("submit").submit();
		}
		});

	</script>
  </body>
</html>