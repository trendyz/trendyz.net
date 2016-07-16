	<?php
	require 'functions.php';
	$link = mysqli_connect("localhost","cl51-users-nr3","D.Uyckj4N","cl51-users-nr3");
	if(mysqli_connect_error()){
	 echo "Database Connection Error, Please try again later..";
	 die();
 }
 session_start();
 if(login_check($link)) {
	 header('Location:index.php ');
	 die();
 }
 if(login_check_cookie($link)){
	 $_SESSION['user_id'] =  $_COOKIE['user_id'];											
                    $_SESSION['username'] = $_COOKIE['username'];
                    $_SESSION['login_string']=$_COOKIE['login_string'];
					$newURL="index.php";
	 header('Location:index.php ');
	 die();
 }
// define variables and set to empty values
$usernameErr = $emailErr = $genderErr = $passwordErr = $password2Err = $checkErr = "";
$username = $email = $gender = $password = $password2 = $check = "";
$userlog = $passlog = $keepme = "";
$userlogErr = $passlogErr = $userpassErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
	if($_POST["submit"]=='Sign up'){
  if (empty($_POST["username"])) {
    $usernameErr = "*Username is required";
  } else {
    $username = test_input($link,$_POST["username"]);
    if (!preg_match("/^[a-zA-Z0-9 ]/",$username)) {
      $usernameErr = "*Only letters and numbers are allowed"; 
    }
  }
 
  if (empty($_POST["email"])) {
    $emailErr = "*E-mail is required";
  } else {
    $email = test_input($link,$_POST["email"]);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailErr = "*Invalid email format"; 
    }
  }
    
	if (empty($_POST["password"])) {
    $passwordErr = "*Password is required";
  } else {
    $password = test_input_password($_POST["password"]);
    if(!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#)($%]{8,50}$/', $password)) {
      $passwordErr = "*Invalid password format"; 
    }
  }
  
  if (empty($_POST["password2"])) {
    $password2Err = "*Password confirmation is required";
  } else {
    $password2 = test_input_password($_POST["password2"]);
    if($_POST['password']!= $_POST["password2"]) {
      $password2Err = "*Passwords do not match"; 
    }
  }

  if (empty($_POST["check"])) {
    $checkErr = "*You must confirm your age";
  } else {
    $check = test_input($link,$_POST["check"]);
  }

  if (empty($_POST["gender"])) {
    $genderErr = "*Please choose your gender";
  } else {
    $gender = test_input($link,$_POST["gender"]);
  }
	}
	
  //log-in
  else if($_POST["submit"]=='Login'){if (empty($_POST["userlog"])) {
    $userlogErr = "   *Username is required";
  } else {
    $userlog = test_input($link,$_POST["userlog"]);
    if (!preg_match("/^[a-zA-Z0-9 ]/",$userlog)) {
      $userlogErr = "*invalid username"; 
    }
  }
  
  if (empty($_POST["passlog"])) {
    $passlogErr = "*Password is required";
  } else {
    $passlog = test_input_password($_POST["passlog"]);
  }
  
   if (!empty($_POST["keepme"])) {
    $keepme = test_input($link,$_POST["keepme"]);
  }
  
  }
}

if($userlog != "" && $passlog != "" && $userlogErr == "" && $passlogErr == "")
{
	
	$userlogexist = $passlogexist = $user_id = "";
	
	$stmt = $link->prepare("select `username` , `password` , `id` from `users` where `username` = ?  LIMIT 1");
		if(!$stmt){
			echo "Error at Database, Please try again later";
	        die();
		}
		$stmt->bind_param("s", $userlog);
		if(!$stmt){
			echo "Error at Database, Please try again later";
	        die();
		}
		
		$stmt->execute();
		$stmt->bind_result($userlogexist,$passlogexist,$user_id);
        $stmt->fetch();
		$stmt->close();
		
		if($userlogexist == $userlog && $passlogexist == md5(md5($userlogexist).md5($passlog).$userlogexist) ){
			//echo "Successfully logged in!";
			        // Get the user-agent string of the user.
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    // XSS protection as we might print this value
                    $userlogexist = preg_replace("/[^a-zA-Z0-9_\-]+/","",$userlogexist);
					$user_id = preg_replace("/[^0-9]+/", "", $user_id);
                    $_SESSION['user_id'] = $user_id;											
                    $_SESSION['username'] = $userlogexist;
                    $_SESSION['login_string'] = hash('sha512', 
                    $passlogexist . $user_browser);
                    // Login successful.
					
					
					if($keepme!="")
					{
						setcookie("user_id",$_SESSION['user_id'],time() + 60*60*24*30);
						setcookie("username",$_SESSION['username'],time() + 60*60*24*30);
						setcookie("login_string",$_SESSION['login_string'],time() + 60*60*24*30);
					}
					
	 header('Location: index.php');
	 die();
		
		}
	else{
		$userpassErr = "*Wrong username or password";
		
		
	}
	$link->close();
}

if(isset($_POST['g-recaptcha-response'])&& $_POST['g-recaptcha-response'])
{
	// var_dump($_POST);
	$secret="6LeZzCMTAAAAAPumymkMP54R8MXXsk94qtekXygD";
	$ip= $_SERVER['REMOTE_ADDR'];
	$captcha = $_POST['g-recaptcha-response'];
	$rspns= file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha&remoteip$ip");
	//var_dump($rspns);
	$validate = json_decode($rspns,TRUE);
	if($validate['success'])
	{

		if($usernameErr == "" && $emailErr == "" && $passwordErr == "" && $password2Err == "" && $genderErr == "" && $checkErr == ""
		&& $username != "" && $email != "" && $password != "" && $gender != "" && $check != "")
		{
			
			$shouldDie=false;
			$userexist = $emailexist = "";
			
			$stmt = $link->prepare("select `username` from `users` where `username` = ?");
				if(!$stmt){
					echo "Error at Database, Try again";
					die();
				}
				$stmt->bind_param("s", $username);
				if(!$stmt){
					echo "Error at Database, Try again";
					die();
				}
				$stmt->execute();
				$stmt->bind_result($userexist);
				$stmt->fetch();
				$stmt->close();

			if($userexist != "")
			{
				$usernameErr="This username already exists</br>";
				$shouldDie=true;
			}
			
			$stmt = $link->prepare("select `email` from `users` where `email` = ?");
				if(!$stmt){
					echo "Error at Database, Try again1";
					die();
				}
				$stmt->bind_param("s", $email);
				if(!$stmt){
					echo "Error at Database, Try again2";
					die();
				}
				$stmt->execute();
				$stmt->bind_result($emailexist);
				$stmt->fetch();
			
			if($emailexist != ""){
				$emailErr= "This E-mail already exists";
				$shouldDie=true;
			}
			$stmt->close();
			
			if(!$shouldDie)
			{
			
				$stmt = $link->prepare("INSERT INTO `users` (`username`,`email`,`password`,`gender`) VALUES (?,?,?,?)");
				if(!$stmt){
					echo "Error at Database, Try again";
					die();
				}
				$stmt->bind_param("ssss", $username,$email,md5(md5($username).md5($password).$username),$gender);
				if(!$stmt){
					echo "Error at Database, Try again";
					die();
				}
				$stmt->execute();
				$stmt->close();
				
				
				$stmt = $link->prepare("select `username` , `password` , `id` from `users` where `username` = ?  LIMIT 1");
				if(!$stmt){
					echo "Error at Database, Please try again later";
					die();
				}
				$stmt->bind_param("s", $username);
				if(!$stmt){
					echo "Error at Database, Please try again later";
					die();
				}
				
				$stmt->execute();
				$stmt->bind_result($usernamelogin,$passwordlogin,$user_id_login);
				$stmt->fetch();
				$stmt->close();
				
				$stmt = $link->prepare("INSERT INTO `userstags` (`id_user`,`username`) VALUES (?,?)");
				if(!$stmt){
					echo "Error at Database, Try again";
					die();
				}
				$stmt->bind_param("is",$user_id_login,$usernamelogin);
				if(!$stmt){
					echo "Error at Database, Try again";
					die();
				}
				$stmt->execute();
				$stmt->close();
				
				//---------------------------------------------------------------
				$stmt = $link->prepare("INSERT INTO `notification` (`user_id`) VALUES (?)");
				if(!$stmt){
					echo "Error at Database, Try again";
					die();
				}
				$stmt->bind_param("i",$user_id_login);
				if(!$stmt){
					echo "Error at Database, Try again";
					die();
				}
				$stmt->execute();
				$stmt->close();
				//echo "you have been successfully signed up!";
				mail($email,"Welcome to Trendyz"," ","FROM: support@trendyz.net");
				 // Get the user-agent string of the user.
							$user_browser = $_SERVER['HTTP_USER_AGENT'];
							// XSS protection as we might print this value
							$usernamelogin = preg_replace("/[^a-zA-Z0-9_\-]+/","",$usernamelogin);
							$user_id_login = preg_replace("/[^0-9]+/", "", $user_id_login);
							$_SESSION['user_id'] = $user_id_login;											
							$_SESSION['username'] = $usernamelogin;
							$_SESSION['login_string'] = hash('sha512', 
							$passwordlogin . $user_browser);
							// Sign up successful.
							header('Location: tags_select.php');
			 die();
			}
		}
	}
	else
		{
			echo "<script>alert('Please prove that you are not a robot!')</script>";
		}
}

?>

<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Trendyz</title>

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
		<script src='https://www.google.com/recaptcha/api.js'></script>
		
    </head>

    <body>

        <!-- Top content -->
        <div class="top-content">
        	
            <div class="inner-bg">
                <div class="container">
                	
                    <div class="row">
                        <div class="col-sm-8 col-sm-offset-2 text">
                            <h1><strong><font style="font-size:150%;" color="#6D6D6D"> Trendyz</font></strong> live it simple!</h1>
                            <div class="description">                            	<!--<p>

	                            	This is a free responsive <strong>"login and register forms"</strong> template made with Bootstrap. 
	                            	Download it on <a href="http://azmind.com" target="_blank"><strong>AZMIND</strong></a>, 
	                            	customize and use it as you like!

                            </div>                            	</p>!-->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-5">
						<?php if($passwordErr == "*Invalid password format") 
						   {
							echo '<div class="alert alert-danger" role="alert"><span style="color:#6E0202;text-align:center;">Password must be 8 or more characters and contains at least one letter and one number:</br>
						   *Allowed special characters are: )!@#$%( </br></span></div>';
						   }
						?>
                        	<div class="form-box">
	                        	<div class="form-top">
	                        		<div class="form-top-left">
	                        			<h3>Login</h3>
	                            		<p>Enter username and password to log on:</p>
	                        		</div>
	                        		<div class="form-top-right">
	                        			<i class="fa fa-lock"></i>
	                        		</div>
	                            </div>
	                            <div class="form-bottom">
				                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" class="login-form">
				                    	<div class="form-group">
											<strong><span style="color:#6E0202;"><?php echo $userpassErr;?></span><strong>
				                    		<label class="sr-only" for="form-username">Username</label>
				                        	<input type="text" name="userlog" placeholder="Username..." class="form-username form-control" id="form-username" value="<?php echo $userlog;?>">
				                        </div>
										
				                        <div class="form-group">
				                        	<label class="sr-only" for="form-password">Password</label>
				                        	<input type="password" name="passlog" placeholder="Password..." class="form-password form-control" id="form-password" value= "<?php echo $passlog;?>">
				                        </div>
										<div class="form-group">
										<label><input id="persist_box" type="checkbox" name="keepme" value="keepme" <?php if (isset($keepme) && $keepme=="keepme") echo "checked";?>/><span style="color:#ccc;">Keep me logged in</span></label>
										<label>&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;</label><label><a href="forgot_pass.php" style="color:#ccc; text-decoration:none"><u>forgot password?</u></a></label>
										</div>
				                        <button  type="submit" name ="submit" value="Login" class="btn">Sign in!</button>
				                    </form>
			                    </div>
		                    </div>

	                        
                        </div>
                        
                        <div class="col-sm-1 middle-border"></div>
                        <div class="col-sm-1"></div>
                        	
                        <div class="col-sm-5">
                        	
                        	<div class="form-box">
                        		<div class="form-top">
	                        		<div class="form-top-left">
	                        			<h3>Sign up now</h3>
	                            		<p>Fill in the form below to get instant access:</p>
	                        		</div>
	                        		<div class="form-top-right">
	                        			<i class="fa fa-pencil"></i>
	                        		</div>
	                            </div>
	                            <div class="form-bottom">
				                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"  class="registration-form">
				                    	<div class="form-group">
											<span style="color:#6E0202;" id="sp" class="error"> <?php echo $usernameErr;?></span>
				                    		<label class="sr-only" for="username">Username</label>
				                        	<input type="text" name="username" placeholder="Username..." class="form-first-name form-control" id="form-first-name" value="<?php echo $username;?>"">
				                        </div>
				                        <div class="form-group">
										<span id="sp" style="color:#6E0202;" class="error"> <?php echo $emailErr;?></span>
				                        	<label class="sr-only" for="email">Email</label>
				                        	<input type="text" name="email" placeholder="Email..." class="form-email form-control" id="form-email" class="radius" value="<?php echo $email;?>">
				                        </div>
										<div class="form-group">
											<span id="sp" style="color:#6E0202;" class="error"> <?php echo $passwordErr;?></span>
				                    		<label class="sr-only" for="password">Password</label>
				                        	<input type="password" name="password" placeholder="Password" class="form-first-name form-control" id="form-first-name" value="<?php echo $password;?>">
				                        </div>
										<div class="form-group">
											<span id="sp" style="color:#6E0202;" color="red" class="error"> <?php echo $password2Err;?></span>
				                    		<label class="sr-only" for="password2">Confirm password</label>
				                        	<input type="password" name="password2" placeholder="Confirm password" class="form-first-name form-control" id="form-first-name">
				                        </div>
				                        <div class="form-group">
										<label class="col-xs-3 control-label"><font color="#ccc">Gender</font></label>
											<div class="col-xs-9">
											<div class="btn-group" data-toggle="buttons">
											<label class="btn btn-primary">
											<input type="radio" name="gender" id="Male" <?php if (isset($gender) && $gender=="male") echo "checked";?> value="male" > Male
											</label>
											<label class="btn btn-primary">
											<input type="radio" name="gender" id="Female" <?php if (isset($gender) && $gender=="female") echo "checked";?> value="female"> Female
											</label>
											</div>
											<div class="btn-group" data-toggle="buttons">
											<label class="btn btn-primary"><input type="checkbox" checked autocomplete="off" name="check" <?php if (isset($check) && $check=="above") echo "checked";?> value="above">I'm over 13 years old</label>
											</div>
											</div>
											<br>
										</div>
										<div class="form-group">
											<center><div class="g-recaptcha" data-sitekey="6LeZzCMTAAAAAH6GMUSIc9NNrLDq8uws5HW_GSws"></div></center>
										</div>
										<button type="submit" class="btn" name="submit" value="Sign up">Sign me up!</button>
				                    </form>
			                    </div>
                        	</div>
                        	
                        </div>
                    </div>
                    
                </div>
            </div>
            
        </div>

        <!-- Footer -->
        <footer>
        	<div class="container">
        		<div class="row">
        			
        			<div class="col-sm-8 col-sm-offset-2">
        				<div class="footer-border"></div>
        				<p>Trendyz &copy; 2016 &nbsp;<a href="contact.php" target="_blank"><strong>Contact us!</strong></a> 
						<i class="fa fa-smile-o"></i></p>
        			</div>
        			
        		</div>
        	</div>
        </footer>

        <!-- Javascript -->
        <script src="assets/js/jquery-1.11.1.min.js"></script>
        <script src="assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="assets/js/jquery.backstretch.min.js"></script>
        <script src="assets/js/scripts.js"></script>
        
		
 
        <!--[if lt IE 10]>
            <script src="assets/js/placeholder.js"></script>
        <![endif]-->

    </body>

</html>