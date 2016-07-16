<?php
require 'functions.php';
	$link = mysqli_connect("localhost","cl51-users-nr3","D.Uyckj4N","cl51-users-nr3");
$id = $_GET['id'];
$userid = $_GET['userid'];
$id=test_input($link,$id);
$userid=test_input($link,$userid);
$color="";
if(isdislike($id,$userid)==true) {
	$res=removedislike($id,$userid);
	$color="w";
}
else{
 if(islike($id,$userid)==true) {
	 removelike($id,$userid);
	 $color="d";
 }
 else{
	 $color="b";
 }
  $res=dislike($id,$userid,true);
  
}
$res.=$color;
echo $res;
?>