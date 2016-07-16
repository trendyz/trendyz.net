<?php
require 'functions.php';
	$link = mysqli_connect("localhost","cl51-users-nr3","D.Uyckj4N","cl51-users-nr3");
$id = $_GET['id'];
$userid = $_GET['userid'];
$id=test_input($link,$id);
$userid=test_input($link,$userid);
$res=delete_post($id,$userid);
?>