<?php
			require 'functions.php';
	date_default_timezone_set('Africa/Cairo');
		$link = mysqli_connect("localhost","cl51-users-nr3","D.Uyckj4N","cl51-users-nr3");
	session_start();
	
	 if(!login_check($link) && login_check_cookie($link))
	 {
	 $_SESSION['user_id'] =  $_COOKIE['user_id'];											
     $_SESSION['username'] = $_COOKIE['username'];
     $_SESSION['login_string']=$_COOKIE['login_string'];
	}
	$username = $_SESSION['username'];
	$user_id = $_SESSION['user_id'];
	$posty = $postyErr = "";

			if(mysqli_connect_error())
			{
			 echo "Database Connection Error, Please try again later..";
			 die();
			}
			$passwordErr=$password2Err=$username=$passErr="";

		$error=$successMsg="";
	if ($_POST)
	{
	 
		if(!$_POST["password"])
		{
			$error .= "*Enter your new password<br>";
		}
		if(!$_POST["password2"])
		{
			$error .= "*Confirm your new password<br>";
		}
		if($_POST['password']!= $_POST["password2"] && $_POST['password'] !="")
		{
			  $error .= "*Passwords do not match"; 
		}
		if ($error != "")
		{
		
			$error = '<div class="alert alert-danger" role="alert"><p><strong>There were error(s) in your form</strong></p>' .$error.'</div>';
		}	
		else 
		{
				$mail=mysqli_real_escape_string($link, $_COOKIE["mailpw"]);
				$query = "SELECT `username` From `users` where `email` = '$mail'";
				$res= mysqli_query($link,$query);
				$row = mysqli_fetch_assoc($res);
				$username = $row["username"];
				

				$stmt = $link->prepare("UPDATE `users` SET password = ? WHERE `email` = '$mail' ");
				if(!$stmt)
				{
					echo "Error at Database1, Try again";
					die();
				}
				$stmt->bind_param("s",md5(md5($username).md5($_POST["password"]).$username));
				if(!$stmt)
				{
					echo "Error at Database2, Try again";
					die();
				}
				$stmt->execute();
				$stmt->close();
				//mail($mail,"Password changed","You have successfully reset your password! \n you can now re-login with your new password and continue posting!","From:it-dep@trendyz.com");
				if (mail($mail,"Password changed","You have successfully reset your password! \n you can now re-login with your new password and continue posting!","From:it-dep@trendyz.com"))
			{	$successMsg = '<div class="alert alert-success" role="alert"><p><strong>Password has been changed successfully</strong></p></div>';
			}
			else
			{
				$error = '<div class="alert alert-danger" role="alert"><p><strong>There was an error, please try again</strong></p></div>';
			}
		}
			
			
	}
		

?>
<html>
	<head>
	    <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<meta name="description" content="">
		<meta name="keywords" content="">
		<meta name="author" content="">
		<link rel="icon" href="img/favicon.png">
		<!-- Bootstrap core CSS -->
		<link rel="stylesheet" href="style.css" type="text/css" />
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
		<title>Password Reset</title>
		<link rel="stylesheet" href="style.css" type="text/css" />
	</head>
	<body class="login">
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
			<li><a style ="font-weight: bold; font-size:150%; color:blue;" href="login.php">Login</a></li>
          </ul>
        </div>
      </div>
    </nav>
	
	<body class="login">
		
<div class="container page-content ">
			<div class="row">
			<div style ="font-size:150%;"class="form-group" id="error"><? echo $error.$successMsg;?>
			</div>
			</div>
	<div class="loginbox radius">
	<h2 style="color:#141823; text-align:center;">Reset Password</h2>

        
        <div class="loginform">

			<form id="login" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
			  <p>
			  <input id="password" type="password"  name="password" placeholder="Password" value=""/> 
			  </p>
			  <p>
			  <input id="password_confirm" type="password" name="password2" placeholder="Confirm Password" value=""/>
			  </p>
			  <button  class="btn btn-info shiny btn-lg"  name="submit" value="reset"> Reset! </button>
			  </p>
			</form>
        </div><!--loginform-->
    </div><!--loginboxinner-->
</div><!--loginbox-->
</div>
    <footer class="footer">
      <div class="container">
        <p class="text-muted"> Trendyz &copy; 2016 - All rights reserved <a href="contact.php" target="_blank"><strong>Contact us!</strong></a> </p>
      </div>
    </footer>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	<script type="text/javascript">
		$("form").submit(function (e)
		{
			e.preventDefault();
			
				
		var error = "";
		if ($("#password").val() == "")
		{
			error += "Enter your new password";
		}
		if ($("#password_confirm").val() == "")
		{
			error += "Confirm your new password";
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