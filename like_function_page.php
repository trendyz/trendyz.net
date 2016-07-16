<?php
require 'functions.php';
	$link = mysqli_connect("localhost","cl51-users-nr3","D.Uyckj4N","cl51-users-nr3");
	$id = $_GET['id'];
$userid = $_GET['userid'];
$id=test_input($link,$id);
$userid=test_input($link,$userid);
$color="";
if(islike($id,$userid)==true) {
	$res=removelike($id,$userid);
	$color="w";
}
else{
 if(isdislike($id,$userid)==true) {
	 removedislike($id,$userid);
	  $color="d";
 }
 else {
	 $color="b";
 }
  $res=like($id,$userid,true);
  
}
$res.=$color;
echo $res;
?>