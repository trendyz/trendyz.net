<?php
			require 'functions.php';
			date_default_timezone_set('Africa/Cairo');
				$link = mysqli_connect("localhost","cl51-users-nr3","D.Uyckj4N","cl51-users-nr3");
			if(mysqli_connect_error())
			{
			 echo "Database Connection Error, Please try again later..";
			 die();
			}
			$st=$email=$username=$passToken=$expiryDate= "";
			$error=$successMsg="";

			
	if ($_POST)
	{
		if(!$_POST["email"])
		{
			$error .= "*E-mail address is required.<br>";
		}
		if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) == false && $_POST["email"])
		{
			$error .= "*Invalid email address.<br>";
		}
		if ($error != "")
		{
		
			$error = '<div class="alert alert-danger" role="alert"><p><strong>There was an error in your form</strong></p>' .$error.'</div>';
		}
		else
		{
			$query="SELECT `email` FROM `users` WHERE `email` = ?";
			$st=exe_query($query,"s",$_POST['email'],"","","","","");
					
			if($_POST["email"] == $st && $_POST["email"] !="")
			{
					$email=$_POST["email"];
					$PasswordResetToken= getToken();
					$PasswordResetExpiration= date("Y-m-d H:i:s", strtotime('+30 minutes'));
					$passToken = mysqli_real_escape_string($link, $PasswordResetToken);
					$expiryDate = mysqli_real_escape_string($link, $PasswordResetExpiration);
					$usrEmail = mysqli_real_escape_string($link, $email);
					setcookie("mailpw", $email);
					$query="UPDATE `users` SET PasswordResetToken = '$passToken', PasswordResetExpiration ='$expiryDate' WHERE email = '$usrEmail'";
					mysqli_query($link , $query);
					$pwrurl = "http://trendyz.net/pass_reset.php?token=".$passToken.$email;
					//mail($email,"Password Reset","Click on the link to reset your password\n $pwrurl \n \n Please note that the link will expire after 30 minutes","From:support@trendyz.net");
					if (mail($email,"Password Reset","Click on the link to reset your password\n $pwrurl \n \n Please note that the link will expire after 30 minutes","From:support@trendyz.net"))
					{	
					$successMsg = '<div class="alert alert-success" role="alert"><p><strong>An email was sent, please check your inbox!</strong></p></div>';
					}
					else
					{
						$error = '<div class="alert alert-danger" role="alert"><p><strong>There was an error, please try again</strong></p></div>';
					} 
			}			
			else if ($_POST["email"] !="")
			{
				$error = '<div class="alert alert-danger" role="alert"><p><strong>Email was not found</strong></p></div>';
			}
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
    <title>Password Recovery</title>
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
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
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


<div class="container page-content ">
	<div class="row">
		<div style ="font-size:150%;"class="form-group" id="error"><? echo $error.$successMsg;?>
		</div>
	</div>
<div class="loginbox radius">
<h2 style="color:#141823; text-align:center;">Reset your password</h2>
	<div class="loginboxinner radius">
    	<div class="loginheader">
    		<h4 class="title">You will receive an e-mail with the password reset link</h4>
    	</div><!--loginheader-->
        
        <div class="loginform">
<form id="login" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
  <input id="email" type="text" name="email" placeholder= "E-mail" value="" class="radius" />
  </p>
 
	<p>
  <button  class="btn btn-info shiny btn-lg" name="submit" value="send"> Send E-mail </button>
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

	<script type="text/javascript">
		$("form").submit(function (e)
		{
			e.preventDefault();
			
				
		var error = "";
		if ($("#email").val() == "")
		{
			error += "Please enter your email";
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