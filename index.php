<?php
  require 'functions.php';
  	$link = mysqli_connect("localhost","cl51-users-nr3","D.Uyckj4N","cl51-users-nr3");
  session_start();
  if(!login_check($link) && !login_check_cookie($link)) 
	{
	 header('Location:login.php ');
	 die();
	}
	else
	{
		header('Location:home.php ');
	}
 if(!login_check($link) && login_check_cookie($link)){
	 $_SESSION['user_id'] =  $_COOKIE['user_id'];											
     $_SESSION['username'] = $_COOKIE['username'];
     $_SESSION['login_string']=$_COOKIE['login_string'];
 }
 $query="select `new_user` from `users` where `id` = ?";
$result = exe_query($query,"i",$user_id,"","","","","");
if($result != 0 || $result == 1){
     header('Location:tags_select.php ');
        die();
}  
 
?>