<?php require 'functions.php';
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
	 if(!login_check($link) && !login_check_cookie($link)) 
	{
	 header('Location:login.php ');
	 die();
	}
	
	$query="select `new_user` from `users` where `id` = ?";
	$result = exe_query($query,"i",$user_id,"","","","","");
	
	if(!$result || $result==0 || $result != 1){
		 header('Location:home.php ');
	     die();
	}
	
	    
        $all_tags=array();
        $result = mysqli_query($link,"SELECT `name` FROM `tags`");
         while( $row = mysqli_fetch_array( $result ) ){
             $all_tags[] = $row['name'];
         }
         $all_tags_without_yours=$all_tags;
		 $trend_tags=array();
		 //Recent tags_sorting
		 $all_tags_without_yours_sorted_trending=array();
		 foreach($all_tags_without_yours as $i){
			  if($i=="empty" || trim($i," ") == "") continue;
			  //trendying
			  $query="SELECT `postsid` FROM `tags` where `name` = '$i'";
              $result = "";			  
			  $result .= exe_query($query,"","","","","","","");
			  $temparr=array();
			  if($result != "" && trim($result))$temparr=explode(" ",$result);
			  $all_tags_without_yours_sorted_trending[$i]=count($temparr);
			  //trendying
			  
         }
		 arsort($all_tags_without_yours_sorted_trending);
		 
		 foreach($all_tags_without_yours_sorted_trending as $i => $i_value){
			  if($i=="empty" || trim($i," ") == "") continue;
			  $trend_tags[]= trim($i,'#');
         }
	     
	$final_next=array();
	$error="";
	if($_SERVER["REQUEST_METHOD"] == "POST" )
		{
			 if($_POST['submit']=="go")
			 {
				 if(!empty($_POST['tag'])) 
				 {
				 foreach($_POST['tag'] as $check) 
				{
				 $final_next[]="#".$check;
				}
				 
				 $final_res_st="";
				 $final_res_st.=implode(" ",$final_next);
				 
				 $query="update `users` set `new_user` = '0' where `id` = ?";
	             exe_query($query,"i",$user_id,"","","","","");
				 
				 if($final_res_st!=""&&trim($final_res_st)!=""){
				 $query="update `userstags` set `tagsid` = ? where `id_user` = ?";
	             exe_query($query,"si",trim($final_res_st),$user_id,"","","","");
				 }
				 header('Location:home.php ');
	             die();
				 }
				 else{
					 $error = '<div class="alert alert-danger" style="font-size:150%"role="alert"><p><strong>You should select one hashtag at least!</strong></p></div>';
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
<nav class="navbar navbar-white navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" style="font-size:300%;" href="#"><b>Trendyz</b></a>                                 
        </div>
      </div>
    </nav>
	 <div class="container page-content">
	 <h1 style="color:blue;">Select hashtag(s) to follow:<br></h1>
	 <div class="row">
			<div class="form-group col-lg-6" id="error"><? echo $error?>
			</div>
			</div>
		<form method="post" action="">
	 <div class="row">

	 <?php
	 					$limit=0;
			foreach($trend_tags as $i)
			{
					if($limit>14) break;
					$limit++;
					$tag=array();
						if($i==''||trim($i,' ')=='') continue;
						  echo "
				<div class='col-xs-4'>
					<div class='widget'>
						<div style='background-color:white;' class='widget-body bordered-top bordered-bottom bordered-right bordered-left bordered-sky'>
										  <div class='row'>
											<center><label style='font-size:300%;cursor:pointer;'><input type='checkbox' id='tagCheck' value='$i' name='tag[]' class='colored-blue'>
											<span class='text'>&nbsp;#$i</span></label></center>
										  </div>
						</div>
					</div>
				</div>";}?>
		
	
	</div>
			<div class="row">
		<button type="submit" id="submit" name ="submit" value ="go" class="btn btn-primary btn-lg pull-right">Go!</button>
		</div>
	</form>
	 
		
	 </div>
  <footer class="footer">
      <div class="container">
        <p class="text-muted"> Trendyz &copy; 2016 - All rights reserved <a href="contact.php" target="_blank"><strong>Contact us!</strong></a> </p>
      </div>
    </footer>
		
  </body>
</html>