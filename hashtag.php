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
	
	 if(!login_check($link) && !login_check_cookie($link)) 
	{
	 header('Location:login.php ');
	 die();
	}
	$query="select `new_user` from `users` where `id` = ?";
$result = exe_query($query,"i",$user_id,"","","","","");
if($result != 0 || $result == 1){
     header('Location:tags_select.php ');
        die();
}

$query="select `gender` from `users` where `id` = ?";
$gender = exe_query($query,"i",$user_id,"","","","","");

  //echo "welcome $username </br></br><hr>";
  if(isset($_GET["q"])){
  $tagname = "#".htmlspecialchars($_GET["q"]);
  }
  else{
	  if(isset($_POST["q"])){
		   $tagname = "#".htmlspecialchars($_POST["q"]);
	  }
	   else {
		   header('Location: no_results.php'); 
			 die();
	   }
  }

  
  $stmt = $link->prepare("select `postsid`, `id` from `tags` where `name` = ?");
		if(!$stmt){
			echo "Error at Database, Try again";
	        die();
		}
		$stmt->bind_param("s", $tagname);
		if(!$stmt){
			echo "Error at Database, Try again";
	        die();
		}
		$tagid="";
		$stmt->execute();
		$stmt->bind_result($postsid,$tagid);
        $stmt->fetch();
		$stmt->close();
		$arr = explode(" ", $postsid);
		 
		 if($tagid!=""&&!$postsid){
			 header('Location: posts_removed.php'); 
			 die();
		 }
		 
		 if(!$postsid){
			 header('Location: no_results.php'); 
			 die();
		 }
		$tagg=trim($tagname,'#');
		   
		$postsid="";
		//--button-----------------------------------------------------------------------------------------------
		
		$usertags="";
		 $stmt = $link->prepare("select `tagsid` from `userstags` where `username` = ?");
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
		$stmt->bind_result($usertags);
        $stmt->fetch();
		$stmt->close();

		$usertagsarr = explode(" ", $usertags);
		$key = array_search($tagname, $usertagsarr); 
if(in_array($tagname, $usertagsarr)){
	$likebuttonvalue="unfollow";
}
else{
	$likebuttonvalue= "follow";
}
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if($_POST['submit']=='logout!')
	{
		$_SESSION = array();
		session_destroy();
		setcookie("user_id","",time() - 60*60*24*30);
		setcookie("username","",time() - 60*60*24*30);
		setcookie("login_string","",time() - 60*60*24*30);
	 header("Refresh:0");
	 die();
	}
	if($_POST['submit']=='btn'){
		if($likebuttonvalue=="follow"){
			if($usertags!="" && str_replace(' ', '', $usertags)!= ""){
				if($usertags=="empty"){
			$usertags=$tagname;
				}
				else{
					$usertags=$usertags." ".$tagname;
				}
			$stmt = $link->prepare(" update `userstags` set `tagsid` = '$usertags' where `username` = ?");
		if(!$stmt){
			echo "Error at Database, Try again93";
	        die();
		}
		$stmt->bind_param("s", $username);
		if(!$stmt){
			echo "Error at Database, Try again98";
	        die();
		}
		$stmt->execute();
		$stmt->close();
			
			}
			else{
				$usertags=$tagname;
				$stmt = $link->prepare(" INSERT INTO `userstags` (`username`,`tagsid`) VALUES (?,?) ");
		if(!$stmt){
			echo "Error at Database, Try again109";
	        die();
		}
		$stmt->bind_param("ss", $username,$usertags);
		if(!$stmt){
			echo "Error at Database, Try again114";
	        die();
		}
		$stmt->execute();
		$stmt->close();		
			}	
		}
		else{
			
		    array_splice($usertagsarr, $key, 1);
			if(empty($usertagsarr)){
				$usertags="empty";
			}
			else{
			$usertags=implode(" ",$usertagsarr);
			}
			
			$stmt = $link->prepare(" update `userstags` set `tagsid` = '$usertags' where `username` = ?");
		if(!$stmt){
			echo "Error at Database, Try again133";
	        die();
		}
		$stmt->bind_param("s", $username);
		if(!$stmt){
			echo "Error at Database, Try again138";
	        die();
		}
		$stmt->execute();
		$stmt->close();
		}
		  header("Refresh:0");
		
	}
		}
		$postRes=array();
		$dateRes=array();
		$tagRes=array();
		$userRes=array();
		$postRep=array();
		$post_gender=array();
		$postComment=array();
		$post_id_for_buttons = array();
		$post_writer_id = array();
 foreach($arr as $postid  )
		{
			if($postid=="empty" || trim($postid," ") == "") continue;
			$query="SELECT `post` FROM `posts` where `id` = ? ";
			$types="i";
			$res1 = exe_query($query,$types,$postid,"","","","","");
			$query="SELECT `time` FROM `posts` where `id` = ? ";
			$types="i";
			$res2 = exe_query($query,$types,$postid,"","","","","");
			$query="SELECT `tags` FROM `posts` where `id` = ? ";
			$types="i";
			$res3 = exe_query($query,$types,$postid,"","","","","");
			$query="SELECT `username` FROM `posts` where `id` = ? ";
			$types="i";
			$res4 = exe_query($query,$types,$postid,"","","","","");
			$query="SELECT `prio` FROM `posts` where `id` = ? ";
			$types="i";
			$res5 = exe_query($query,$types,$postid,"","","","","");
			$query="SELECT `id_comments` FROM `posts` where `id` = ? ";
			$types="i";
			$res6 = exe_query($query,$types,$postid,"","","","","");
			$query="SELECT `gender` FROM `users` where `username` = ? ";
			$types="s";
			$res7 = exe_query($query,$types,$res4,"","","","","");
			$query="SELECT `id_writer` FROM `posts` where `id` = ? ";
			$types="i";
			$res104 = exe_query($query,$types,$postid,"","","","","");
			$postRes[] = $res1; //post
			$datepost=date("g:ia - l jS F Y", strtotime($res2)+ 3600);
			if(date("Y:m:d", strtotime($res2)+ 3600)==date("Y:m:d",(time()+3600))){
				$datepost=date("g:ia", strtotime($res2)+ 3600)." - Today";
			}
			$dateRes[] = $datepost; //date
			$tagRes[] = $res3; //tag
			$userRes[]= $res4;
			$postRep[]=$res5;
			$postComment[] = $res6;
			$post_gender[] = $res7;
			$post_writer_id[] = $res104;
		//user
			$post_id_for_buttons[] = $postid;
		}
		 	$comment="";
	if($_SERVER["REQUEST_METHOD"] == "POST" )
		{
           
			foreach($post_id_for_buttons as $i )
				{
					if($_POST['submit']==$i."comment")
					{
						insert_comment($username,$_POST['comment_msg'],$i);
						//if($_POST['comment_msg']!="") $comment=$_POST['comment_msg'];
						$kkk=$i."nav";
						$temp_tagname=trim($tagname,"#");
						header("Location:hashtag.php?q=$temp_tagname#$kkk");
					}
		        } 
              
        
		}
		//###############################################################################################
		
		$notify_new_arr=array();
		$notify_new_arr=notify($user_id);
		$notify_new="";
		$notify_new.=implode(" ",$notify_new_arr);
		$notification_count=0;	
		$query="SELECT `notify` FROM `notification` where `user_id` = '$user_id'";
        $notify_old = "";			  
		$notify_old .= exe_query($query,"","","","","","","");
		$notify_old_arr=array();
		$notify_old_arr=explode(" ",$notify_old);
		if($notify_new!=""&&trim($notify_new)!=""&&count($notify_new_arr)>0){
			$x=count($notify_new_arr);
			$notification_count=$x;
			$res="";
			foreach($notify_old_arr as $i){
				if($x>=10) break;
				if($i=="" || trim($i)== "") continue;
				$notify_new_arr[]=trim($i);
				$x++;
			}
			$notify_old_arr=array();
			$notify_old_arr=$notify_new_arr;
			$notify_old_arr = array_unique($notify_old_arr);
			$notify_update_database="";
			$notify_update_database.=implode(" ",$notify_old_arr);
			$query="update `notification` set `notify` = '$notify_update_database' where `user_id` = '$user_id'";
    		exe_query($query,"","","","","","","");
		}
		$notification_word=array();
		$notification_link=array();
      foreach($notify_old_arr as $i){
	    if($i==""||trim($i)=="") continue;
	    $arr=explode("#",$i);
	    if($arr[0]=="follow"){
		  $notification_word[]="You have got new $arr[1] follower(s)";
		  $notification_link[]="profile.php";
	   }
	   else {
		  $notification_word[]= "Someone $arr[0] on your post";
	      $notification_link[]= $arr[1];
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
    <title><?php echo $tagname;?> </title>
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

			
			<li><a style="color:#4895E6; font-size:150%; font-weight: bold;" href="profile.php"><?php echo $username;?></a></li>
            <li><a class="actives" href="home.php">Home</a></li>
			<li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">  Notifications 
					<?php 
					if ($notification_count > 0)
					{
						echo "
                    <span class='label label-info pull-right r-activity'>$notification_count</span>";
					}
					echo "
                    </a>
					<ul class='dropdown-menu'>";
					for($i=0;$i<count($notification_word)&&$i<10;$i++){
						if($notification_word[$i] == ""||trim($notification_word[$i]) == "") continue;
						echo "
					<li><a href='$notification_link[$i]'>$notification_word[$i]</a></li>";
					}
					echo "</ul>";
					?>
            </li>
           
			<li>
			<form class="form-inline pull-xs-right" method="post" action="">
				<button style="margin-top: 20px;" type="submit" name="submit" class="btn btn-info" value="logout!">log out</button>
			</form>
			</li>
          </ul>
        </div>
      </div>
    </nav>

<div class="container page-content" >
<div class="col-xs-2">
</div>
	 <div class="col-md-9">
	<div class="row">

		<form method="post" action=""> 
					<?php
					if($likebuttonvalue == "follow")
					{
						echo"
						<center><span>
						<strong><a style='font-size:300%;'href='hashtag.php?q=$tagg'>{$tagname}</a><strong>
						<button style ='font-size:130%; weight:bold; margin-bottom:20px; margin-left:20px;' name='submit' class='btn btn-azure shiny btn-toggle-following' value='btn' type='submit'> {$likebuttonvalue}</button>
						</span></center>";
					}
					else
					{
						echo"
						<center><span>
					<strong><a style='font-size:300%;' href='hashtag.php?q=$tagg'>{$tagname}</a><strong>
						<button  style ='font-size:130%; weight:bold; margin-bottom:20px; margin-left:20px;' name='submit' class='btn btn-warning btn-toggle-following' value='btn' type='submit'>{$likebuttonvalue}</button>
						<i class='fa fa-checkmark-round'></i>
						</span></center>";
					}

				?>
		</form>
		</div>

<!--  posts -->
				   <?php
				     
					 
					
					for($i=0;$i<count($postRes);$i++)
					 {
						 
						 
				     $like_btn_value=$dislike_btn_value="";
					 $share_btn_value=$post_id_for_buttons[$i]."share";
					 $delete_btn_value=$post_id_for_buttons[$i]."delete";
					 $follow_btn_value=$post_id_for_buttons[$i]."follow";
					 $comment_btn_value=$post_id_for_buttons[$i]."comment";
					 $id_of_post_writer=$post_writer_id[$i];
					 
					$id_for_post_div=$post_id_for_buttons[$i]."bigdiv";
					 if($postRes[$i]==""||trim($postRes[$i]," ")=="") continue;
					 
					 $id_for_post_div_above=$post_id_for_buttons[$i]."nav";
					 
					 echo"<div id='{$id_for_post_div_above}'></div>";
				      echo" 
                  <div id='{$id_for_post_div}' class='box box-widget'>
                    <div class='box-header with-border'>";
					if($post_gender[$i]== "male")
					{
					echo"
                      <div class='user-block'>
                        <img class='img-circle' src='img/Profile/default_male_avatar.png' alt='User Image'>
					";}
					else
					{
					echo"
                      <div class='user-block'>
                        <img class='img-circle' src='img/Profile/default_female_avatar.png' alt='User Image'>
					";}
					
					$posts_of_follower_person_arr=array();
					for($kk=0;$kk<count($postRes);$kk++)
					 {
						 if($postRes[$kk]==""||trim($postRes[$kk]," ")=="") continue;
						 if($post_writer_id[$kk]==$id_of_post_writer)
							 $posts_of_follower_person_arr[]=$post_id_for_buttons[$kk];
						 
					 }
					 $posts_of_follower_person_arr_string="";
					 $posts_of_follower_person_arr_string.=implode("~",$posts_of_follower_person_arr);
					
					echo"
						<div>
						<form>";
						    $delete_val_new="delete";
							if(!isfollowself($post_id_for_buttons[$i],$user_id)){
								$temp_temp_id=$post_id_for_buttons[$i]."followdiv";
								$delete_val_new="hide";
							if(isfollowbutton($post_id_for_buttons[$i],$user_id)){
								
								echo "<button  id='{$temp_temp_id}' class='btn btn-sm btn-warning pull-left fa fa-minus' type='button' onclick='ajaxfollow({$post_id_for_buttons[$i]},{$user_id},\"<?php echo $posts_of_follower_person_arr_string; ?>\")' >&nbsp;Unfollow</button>";
							}
								else{
							echo "<button id='{$temp_temp_id}'  class='btn btn-sm btn-palegreen pull-left fa fa-plus' type='button' onclick='ajaxfollow({$post_id_for_buttons[$i]},{$user_id},\"<?php echo $posts_of_follower_person_arr_string; ?>\")' >&nbsp;Follow</button>";
								}
							
							}	
							
						echo "</form>
						</div>
					 <span class='description'> &nbsp; {$dateRes[$i]} </span> <!--date-->
                      </div>
                    </div>

                    <div class='box-body' style='display: block;'>
					<p><br>{$postRes[$i]}<p> <!--post-->
					<br>
					 <b>
					 ";
					 $arr_tags_names_links= explode(" ",$tagRes[$i]);
					 foreach($arr_tags_names_links as $tag_name)
					 {
						 $tag_link="hashtag.php?q=".trim($tag_name,'#');
					        echo "<a href='{$tag_link}'>{$tag_name} </a>";							
					 }
					 $postComment_arr_final= explode(" ",$postComment[$i]);
					 $number_of_comments=0;
					 asort($postComment_arr_final);
					 
					 foreach($postComment_arr_final as $postcommentval)
					{
						if($postcommentval==""||trim($postcommentval," ")=="") continue;
						$number_of_comments++;
					}
					
					//###########################new
					$hamocolor="red";
					$rep_value = $postRep[$i];
					if($rep_value==0){
						$hamocolor="black";
					}
					if($rep_value > 0){
						$rep_value="+".$rep_value;
						$hamocolor="purple";
					}
				   
					 $color_like=$color_dislike="default";
					 if(islike($post_id_for_buttons[$i],$user_id)){
						 $color_like="azure";
					 }
					 if(isdislike($post_id_for_buttons[$i],$user_id)){
						 $color_dislike="maroon";
					 }
					 $id_for_like_color=$post_id_for_buttons[$i]."colorlike";
					 $id_for_dislike_color=$post_id_for_buttons[$i]."colordislike";
                     echo "
					 </b> <!--hashTag-->
						<br>
						<form>
													
					        <button type='button' id='$id_for_like_color' onclick='ajaxlike({$post_id_for_buttons[$i]},{$user_id})' class='btn btn-{$color_like} btn-xs pull-left'><i class='fa fa-thumbs-o-up'></i></button>
							<span id='{$post_id_for_buttons[$i]}' class='text-muted pull-left'> <font style='color:{$hamocolor};'>&nbsp;{$rep_value}&nbsp;</font></span>
							<button type='button' id='$id_for_dislike_color' onclick='ajaxdislike({$post_id_for_buttons[$i]},{$user_id})'  class='btn btn-{$color_dislike} btn-xs pull-left'><i class='fa fa-thumbs-o-down'></i></button>
							<button type='button' onclick='ajaxshare({$post_id_for_buttons[$i]},{$user_id})' class='btn btn-default btn-xs pull-left'><i class='fa fa-share'></i> Share</button>
							
							<span class='pull-right text-muted'>{$number_of_comments} comments</span><br>
						</form>
                      
                    </div>";
					
					//###########################new
					
					
					
					foreach($postComment_arr_final as $postcommentval)
					{
						if($postcommentval==""||trim($postcommentval," ")=="") continue;
						
						$query="SELECT `comment` FROM `comments` where `id` = ? ";	
			            $comment_value = exe_query($query,"i",$postcommentval,"","","","","");
						
						$query="SELECT `time` FROM `comments` where `id` = ? ";	
			            $comment_time_database = exe_query($query,"i",$postcommentval,"","","","","");
						
						$query="SELECT `name_writer` FROM `comments` where `id` = ? ";	
			            $comment_writer = exe_query($query,"i",$postcommentval,"","","","","");
						$query="SELECT `gender` FROM `users` where `username` = ? ";	
			            $comment_gender = exe_query($query,"s",$comment_writer,"","","","","");
						
						
						if($comment_value==""||trim($comment_value," ")=="") continue;
						
						$comment_time=date("g:ia - l jS F Y", strtotime($comment_time_database)+ 3600);
			            if(date("Y:m:d", strtotime($comment_time_database)+ 3600)==date("Y:m:d",(time()+3600))){
				        $comment_time=date("g:ia", strtotime($comment_time_database)+ 3600)." - Today";
			             }
						
						echo "
                    <div class='box-footer box-comments' style='display: block;'>
                      <div class='box-comment'>";
					  
					  if($comment_gender == "male")
					{
					echo"
                         <img class='img-circle img-sm' src='img/Profile/default_male_avatar.png' alt='User Image'>
					";}
					else{
						echo "
						<img class='img-circle img-sm' src='img/Profile/default_female_avatar.png' alt='User Image'>
						";
					}
					echo "
                        <div class='comment-text'>
                          <span class='username'>
						  <br>
                          <span class='text-muted pull-right'>$comment_time</span>
                          </span>
						 $comment_value
                        </div>
                      </div>
                     </div>";
					
				    }
					
					
					
					echo "
                    <div class='box-footer' style='display: block;'>
                      <form id='comment_form' action='#' method='post'>";
					  if ($gender == "male")
					  {echo"
                        <img class='img-responsive img-circle img-sm' src='img/Profile/default_male_avatar.png' alt='Alt Text'>
					  ";}
					  else{
						  echo"
						   <img class='img-responsive img-circle img-sm' src='img/Profile/default_female_avatar.png' alt='Alt Text'>
						   ";
					  }
					  echo"
                        <div class='img-push'>
						  <input type='text' class='form-control input-sm' style='resize:none;' name='comment_msg' rows='3' placeholder='post comment'></textarea>
						  <input type='submit' name='submit' value='$comment_btn_value' style='display: none;' />
                        </div>
                      </form>
                    </div>
					</div>";}?><!--  end posts -->
</div>
					</div>
<footer class="footer">
      <div class="container">
        <p class="text-muted"> Trendyz &copy; 2016 - All rights reserved <a href="contact.php" target="_blank"><strong>Contact us!</strong></a> </p>
      </div>
    </footer>
	<script type="text/javascript">
// search

$(function() {
    $('#search_form').each(function() {
        $(this).find('input').keypress(function(e) {
            // Enter pressed?
            if(e.which == 10 || e.which == 13) {
                this.form.submit();
            }
        });

        $(this).find('input[type=submit]').hide();
    });
});
</script>
<script type="text/javascript">
// search

$(function() {
    $('#comment_form').each(function() {
        $(this).find('input').keypress(function(e) {
            // Enter pressed?
            if(e.which == 10 || e.which == 13) {
                this.form.submit();
            }
        });

        $(this).find('input[type=submit]').hide();
    });
});
</script>
<script language="javascript" type="text/javascript">
function ajaxlike(){
	var id=arguments[0];
	var userid=arguments[1];
   var ajaxRequest;
   try{
      ajaxRequest = new XMLHttpRequest();
   }catch (e){
      try{
         ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
      }catch (e) {
         try{
            ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
         }catch (e){
            alert("Your browser broke!");
            return false;
         }
      }
   }
   ajaxRequest.onreadystatechange = function(){
      if(ajaxRequest.readyState == 4){
         var ajaxDisplay = document.getElementById(id);
		 var resultall=ajaxRequest.responseText;
		 var result=resultall.slice(0,-1);
		 var resultcolor=resultall.slice(-1);
		 var str="<font style='color:red;'>"+"&nbsp;"+ result+"&nbsp;" +"</font>";
		 if(result==0){
			 var str="<font style='color:black;'>"+"&nbsp;"+ result+"&nbsp;" +"</font>";
		 }
					if(result > 0){
						result="+"+result;
						var str="<font style='color:#800080;'>"+"&nbsp;"+ result+"&nbsp;" +"</font>";
					}
		 
		  //$id_for_like_color=$post_id_for_buttons[$i]."colorlike";
			//		 $id_for_dislike_color=$post_id_for_buttons[$i]."colordislike";
			var idforcolor=id+"colorlike";
			var idforcolordis=id+"colordislike";
			if(resultcolor=="w"){
				document.getElementById(idforcolor).className="btn btn-default btn-xs pull-left ";
			}
			else if(resultcolor=="b"){
				document.getElementById(idforcolor).className="btn btn-azure btn-xs pull-left ";
			}
			else {
				document.getElementById(idforcolor).className="btn btn-azure btn-xs pull-left ";
				document.getElementById(idforcolordis).className="btn btn-default btn-xs pull-left ";
			}
		 ajaxDisplay.innerHTML=str;
      }
   }
   var queryString = "?id=" + id ;
   queryString +=  "&userid=" + userid;
   ajaxRequest.open("GET", "like_function_page.php" + queryString, true);
   ajaxRequest.send(null); 
}
</script>
<script language="javascript" type="text/javascript">
function ajaxdislike(){
	var id=arguments[0];
	var userid=arguments[1];
   var ajaxRequest;
   try{
      ajaxRequest = new XMLHttpRequest();
   }catch (e){
      try{
         ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
      }catch (e) {
         try{
            ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
         }catch (e){
            alert("Your browser broke!");
            return false;
         }
      }
   }
   ajaxRequest.onreadystatechange = function(){
      if(ajaxRequest.readyState == 4){
         var ajaxDisplay = document.getElementById(id);
		 var resultall=ajaxRequest.responseText;
		 var result=resultall.slice(0,-1);
		 var resultcolor=resultall.slice(-1);
					var str="<font style='color:red;'>"+"&nbsp;"+ result+"&nbsp;" +"</font>";
		 if(result==0){
			 var str="<font style='color:black;'>"+"&nbsp;"+ result+"&nbsp;" +"</font>";
		 }
					if(result > 0){
						result="+"+result;
						var str="<font style='color:#800080;'>"+"&nbsp;"+ result+"&nbsp;" +"</font>";
					}
		 var idforcolor=id+"colorlike";
			var idforcolordis=id+"colordislike";
			if(resultcolor=="w"){
				document.getElementById(idforcolordis).className="btn btn-default btn-xs pull-left ";
			}
			else if(resultcolor=="b"){
				document.getElementById(idforcolordis).className="btn btn-maroon btn-xs pull-left ";
			}
			else {
				document.getElementById(idforcolordis).className="btn btn-maroon btn-xs pull-left ";
				document.getElementById(idforcolor).className="btn btn-default btn-xs pull-left ";
			}
		 ajaxDisplay.innerHTML=str;
      }
   }
   var queryString = "?id=" + id ;
   queryString +=  "&userid=" + userid;
   ajaxRequest.open("GET", "dislike_function_page.php" + queryString, true);
   ajaxRequest.send(null); 
}
</script>
<script language="javascript" type="text/javascript">
function ajaxfollow(){
	
	var id=arguments[0];
	var userid=arguments[1];
	
	var arr = new Array();
	var testy=arguments[2].slice(11,-4);
	arr=testy.split('~');
   var ajaxRequest;
   try{
      ajaxRequest = new XMLHttpRequest();
   }catch (e){
      try{
         ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
      }catch (e) {
         try{
            ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
         }catch (e){
            alert("Your browser broke!");
            return false;
         }
      }
   }
   
   ajaxRequest.onreadystatechange = function(){
      if(ajaxRequest.readyState == 4){
		  var result=ajaxRequest.responseText;
		  
		  for(var i=0;i<arr.length;i++){
			var temp_id_temp=arr[i]+"followdiv";
		  var ajaxDisplay = document.getElementById(temp_id_temp);
		  if(result=="follow"){
			ajaxDisplay.className="btn btn-sm btn-warning pull-left fa fa-minus";
			ajaxDisplay.innerHTML=" Unfollow";
		  }
		  else{
			ajaxDisplay.className="btn btn-sm btn-palegreen pull-left fa fa-plus";
			ajaxDisplay.innerHTML=" Follow";
		  }
		  } 
      }
   }
   var queryString = "?id=" + id ;
   queryString +=  "&userid=" + userid;
   ajaxRequest.open("GET", "follow_function_page.php" + queryString, true);
   ajaxRequest.send(null); 
}
</script>
<script language="javascript" type="text/javascript">
function ajaxshare(){
	var id=arguments[0];
	var userid=arguments[1];
   var ajaxRequest;
   try{
      ajaxRequest = new XMLHttpRequest();
   }catch (e){
      try{
         ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
      }catch (e) {
         try{
            ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
         }catch (e){
            alert("Your browser broke!");
            return false;
         }
      }
   }
   ajaxRequest.onreadystatechange = function(){
      if(ajaxRequest.readyState == 4){
      }
   }
   var queryString = "?id=" + id ;
   queryString +=  "&userid=" + userid;
   ajaxRequest.open("GET", "share_function_page.php" + queryString, true);
   ajaxRequest.send(null); 
}
</script>

  </body>
</html>