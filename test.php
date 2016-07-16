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
 if ($_SERVER["REQUEST_METHOD"] == "POST") 
 {
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
	else if($_POST["submit"]=='post!'){
		if(empty($_POST["userpost"])){
			$postyErr = "write something!";
		}
		else{
			$posty=nl2br(htmlspecialchars($_POST["userpost"]));
			 if(!$arr=gethashtags($posty)){
				$postyErr = "You must include a hashtag in your post!";
			}
			  else{  $tagwords="";
				$i=0;
				foreach($arr as $i){
					$tagwords=$tagwords." ".$i;
				}
				 
				 $stmt = $link->prepare("INSERT INTO `posts` (`username`,`post`,`tags`, `id_writer`) VALUES (?,?,?,?) ");
				 
		if(!$stmt){
			echo "Error at Database, Try again";
	        die();
		}
		$stmt->bind_param("sssi", $username,$posty,$tagwords,$user_id);
		if(!$stmt){
			echo "Error at Database, Try again";
	        die();
		}
		$stmt->execute();
		$lastId = $link->insert_id;
		$stmt->close();
		
		//----------------------------------------------------------------------------------
		$id_post_now = mysqli_insert_id($link);
        update_posts_from_followers($id_post_now,$_SESSION['user_id']);
		
		foreach($arr as $i){
			$currenttag = $oldposts = "";
		$stmt = $link->prepare("select `name`,`postsid` from `tags` where `name` = ?");
		if(!$stmt){
			echo "Error at Database, Try again";
	        die();
		}
		$stmt->bind_param("s", $i);
		if(!$stmt){
			echo "Error at Database, Try again";
	        die();
		}
		$stmt->execute();
		$stmt->bind_result($currenttag,$oldposts);
        $stmt->fetch();
		$stmt->close();
		if($currenttag!=""){
			$oldposts=$oldposts." ".$lastId;
			$stmt = $link->prepare(" update `tags` set `postsid` = '$oldposts' where `name` = ?");
		if(!$stmt){
			echo "Error at Database, Try again";
	        die();
		}
		$stmt->bind_param("s", $i);
		if(!$stmt){
			echo "Error at Database, Try again";
	        die();
		}
		$stmt->execute();
		$stmt->close();
		}
		else{
	    $stmt = $link->prepare("INSERT INTO `tags` (`name`,`postsid`) VALUES (?,?) ");
		if(!$stmt){
			echo "Error at Database, Try again";
	        die();
		}
		$stmt->bind_param("ss", $i,$lastId);
		if(!$stmt){
			echo "Error at Database, Try again";
	        die();
		}
		$stmt->execute();
		$stmt->close();
		}
		
		}
		}
		header("refresh:0");
		}
		
	}
	}
//Followed tags
		$usertags="";
		$query="select `tagsid` from `userstags` where `username` = ?";
		$types="s";
		$result = exe_query($query,$types,$username,"","","","","");
		$usertagsarr = explode(" ", $result);
		$fol_tags=array();
		$fol_tags_name=array();
		$usertagsarr=array_reverse($usertagsarr);
		foreach( $usertagsarr as $i )
		{
			if($i=="empty" || trim($i," ") == "") continue;
			$redirect="hashtag.php?q=".trim($i,'#');
			$fol_tags[] = $redirect;
			$fol_tags_name[]=trim($i,'#');
        }
//NewsFeed Posts
		home_posts();
		$query="select `posts_home_id` from `userstags` where `username` = ?";
		$types="s";
		$result = exe_query($query,$types,$username,"","","","","");
		$usertagsarr = explode(" ", $result);
		//if(hamo is new) arsort($usertagsarr);
		$postRes=array();
		$dateRes=array();
		$tagRes=array();
		$userRes=array();
		$postRep=array();
		$postComment=array();
		$post_id_for_buttons = array();
        foreach($usertagsarr as $postid  )
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
		//user
			$post_id_for_buttons[] = $postid;
		}
//Trending Tags
        $usertags="";
        $query="select `tagsid` from `userstags` where `username` = ?";
        $result = exe_query($query,"s",$username,"","","","","");
        $usertagsarr = explode(" ", $result);
        
        $all_tags=array();
        $result = mysqli_query($link,"SELECT `name` FROM `tags`");
         while( $row = mysqli_fetch_array( $result ) ){
             $all_tags[] = $row['name'];
         }
         $all_tags_without_yours=array_diff($all_tags,$usertagsarr);
		 $allTags=array();
		 $nameTags=array();
		 //Recent tags_sorting
		 $all_tags_without_yours_sorted_recent=array();
		 $all_tags_without_yours_sorted_trending=array();
		 $all_tags_without_yours_sorted_recent_tags=array();
		 $all_tags_without_yours_sorted_recent_name=array();
		 foreach($all_tags_without_yours as $i){
			  if($i=="empty" || trim($i," ") == "") continue;
			  //recent
			  $query="SELECT `id` FROM `tags` where `name` = '$i'";	
			  $result = exe_query($query,"","","","","","","");
			  $all_tags_without_yours_sorted_recent[$i]=$result;
			  //recent
			  //trendying
			  $query="SELECT `postsid` FROM `tags` where `name` = '$i'";
              $result = "";			  
			  $result .= exe_query($query,"","","","","","","");
			  $temparr=array();
			  if($result != "" && trim($result))$temparr=explode(" ",$result);
			  $all_tags_without_yours_sorted_trending[$i]=count($temparr);
			  //trendying
			  
         }
		 arsort($all_tags_without_yours_sorted_recent);
		 arsort($all_tags_without_yours_sorted_trending);
		 foreach($all_tags_without_yours_sorted_recent as $i => $i_value){
			  if($i=="empty" || trim($i," ") == "") continue;
              $redirect="hashtag.php?q=".trim($i,'#');
			  $all_tags_without_yours_sorted_recent_tags[] = $redirect;
			  $all_tags_without_yours_sorted_recent_name[]= trim($i,'#');
         }
		 //End of Recent tags_sorting
		 
		 foreach($all_tags_without_yours_sorted_trending as $i => $i_value){
			  if($i=="empty" || trim($i," ") == "") continue;
              $redirect="hashtag.php?q=".trim($i,'#');
			  $allTags[] = $redirect;
			  $nameTags[]= trim($i,'#');
         }
		 
		 
		 
		 
		 
		 
		 
//Comments
			
			
			$comment="";
	if($_SERVER["REQUEST_METHOD"] == "POST" )
		{	if($_POST['submit']=="search")
					{
				        $words=$_POST["search_text"];
						$words=trim($words);
						$words_arr=search_for_words($words);
						if($words=="" || trim($words)==""){
						}
						else{$get_data="search_results.php?q=";
						$k=0;
						foreach($words_arr as $i){
							if($k==0) {
								$get_data.="$i";
						}
						else{
							$get_data.="_$i";
						}
						$k++;
						}
					header('Location:'.$get_data);
						}
			        }
					
			foreach($post_id_for_buttons as $i )
				{
			        if($_POST['submit']==$i."delete")
					{
				        delete_post($i,$username);
			        }
					else if($_POST['submit']==$i."share")
					{
				        share($i,$user_id);
					}
					else if($_POST['submit']==$i."follow")
					{
				        
						follow($i,$user_id);
						
					}
					else if($_POST['submit']==$i."comment")
					{
						insert_comment($username,$_POST['comment_msg'],$i);
						//if($_POST['comment_msg']!="") $comment=$_POST['comment_msg'];
						header("Location:home.php#$i");
					}
					else if($_POST['submit']==$i."like")
					{
						if(isdislike($i,$user_id)) removedislike($i,$user_id);
						like($i,$user_id,true);
						header("Location:home.php#$i");
					}
					else if($_POST['submit']==$i."dislike")
					{
						if(islike($i,$user_id)) removelike($i,$user_id);
						dislike($i,$user_id,true);
						header("Location:home.php#$i");
					}
					else if($_POST['submit']==$i."removelike")
					{
						removelike($i,$user_id);
						header("Location:home.php#$i");
					}
					else if($_POST['submit']==$i."removedislike")
					{
						removedislike($i,$user_id);
						header("Location:home.php#$i");
					}
		        } 
              
         foreach($nameTags as $i)
			{
			 if($_POST['submit']==$i)
				{
				follow_tag("#".$i,$username,$user_id);
				}
			}	
		 foreach($fol_tags_name as $i)
			{
			 if($_POST['submit']==$i)
				{
				unfollow_tag("#".$i,$username,$user_id);
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
	<style>
	
		div #recent, #followed{
			position:fixed; 
			clear:both; 
			top:2;
			left:5;
			width:14%;

			
		}

		div #trending{
			position:fixed; 
			clear:both; 
			bottom:0;
			right:5;
			width:14%;
			
		}
		
	</style>
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
			<li>
			<div style="margin-top:20px; width:200%;" class="form-group">
			<form id="search_form" name="search" action="" method="post">
				<span class="input-icon inverted">
				<input type="text" name="search_text" placeholder="Search for something.." class="form-control input-md">
				<i class="glyphicon glyphicon-search bg-blue"></i>
				</span>
				<input type="submit" name="submit" value="search" style="display: none;" />
				</form>
			</div>
			</li>
			<li>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			</li>
			</form>
			<li><a style="color:#4895E6; font-size:150%; font-weight: bold;" href="profile.php"><?echo $username;?></a></li>
            <li><a class="actives" href="home.php">Home</a></li>
			<li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">  Notifications 
					<? 
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
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                Account <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
         <!--   <li><a href="profile2.html">Profile 2</a></li>
                <li><a href="profile3.html">Profile 3</a></li>
                <li><a href="profile4.html">Profile 4</a></li>
                <li><a href="sidebar_profile.html">Sidebar profile</a></li>
                <li><a href="user_detail.html">User detail</a></li>
                <li><a href="edit_profile.html">Edit profile</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="friends.html">Friends</a></li>
                <li><a href="friends2.html">Friends 2</a></li>
                <li><a href="profile_wall.html">Profile wall</a></li>
                <li><a href="photos1.html">Photos 1</a></li>
                <li><a href="photos2.html">Photos 2</a></li>
                <li><a href="view_photo.html">View photo</a></li>
                <li><a href="messages1.html">Messages 1</a></li>
                <li><a href="messages2.html">Messages 2</a></li>
                <li><a href="group.html">Group</a></li>
                <li><a href="list_users.html">List users</a></li>
                <li><a href="file_manager.html">File manager</a></li>
                <li><a href="people_directory.html">People directory</a></li>
                <li><a href="list_posts.html">List posts</a></li>
                <li><a href="grid_posts.html">Grid posts</a></li>
                <li><a href="forms.html">Forms</a></li>
                <li><a href="buttons.html">Buttons</a></li>-->
                <li><a href="#">Messages</a></li>
                <li><a href="notification.php">Notifications</a></li> 
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

    <!-- Begin page content -->
    <div id='test' class="container page-content" >
      <div class="row">
        <!-- left links -->
        <div class="col-md-3">
          <div class="profile-nav">
          <div id="recent" class="widget">
            <div class="widget-header">
              <h3 style ="font-size:110%" class="widget-caption"><strong>Recent tags</strong></h3>
            </div>
            <div class="widget-body bordered-top bordered-sky">
              <div class="card">
                  <div class="content">
                      <ul class="list-unstyled team-members">
					  <?php
					  
					  /*
					   $all_tags_without_yours_sorted_recent_tags[] = $redirect;
			  $all_tags_without_yours_sorted_recent_name[]= trim($i,'#');
					  
					  */
				     for($i=0;$i<count($all_tags_without_yours_sorted_recent_tags)&&$i<10&&count($all_tags_without_yours_sorted_recent_tags)>0;$i++)
					 { 
				                if($all_tags_without_yours_sorted_recent_tags[$i]==""||trim($all_tags_without_yours_sorted_recent_tags[$i]," ")=="") continue;
				 echo "
                          <li>
                              <div class='row'>
                                  <div class='col-xs-3'>
                                      <div class='avatar'>
                                          <img style='width:75%; height:75%;' src='img/Friends/hash.jpg' alt='Circle Image' class='img-circle img-no-padding img-responsive'>
                                      </div>
                                  </div>
                                  <div class='col-xs-6'>
                                     <strong><a style=' font-size:150%; 'href='$all_tags_without_yours_sorted_recent_tags[$i]'>{$all_tags_without_yours_sorted_recent_name[$i]}</a></strong>
                                  </div>
                                  <div class='col-xs-3 text-right'>
								  <form method='post' action=''>
                                  <button name='submit' type='submit' value='$all_tags_without_yours_sorted_recent_name[$i]' class='btn btn-sm btn-palegreen pull-right'>
                                  <i class='fa fa-plus '></i></button>
								  </form>
                                  </div>
                              </div>
					 </li>";}?>
                      </ul>
                  </div>
              </div>          
            </div>
          </div>

            <!--<div class="widget">
              <div class="widget-body">
                <ul class="nav nav-pills nav-stacked">
                  <li><a href="#"> <i class="fa fa-globe"></i> Pages</a></li>
                  <li><a href="#"> <i class="fa fa-gamepad"></i> Games</a></li>
                  <li><a href="#"> <i class="fa fa-puzzle-piece"></i> Ads</a></li>
                  <li><a href="#"> <i class="fa fa-home"></i> Markerplace</a></li>
                  <li><a href="#"> <i class="fa fa-users"></i> Groups</a></li>
                </ul>
              </div>
            </div>-->
          </div>
        </div><!-- end left links -->


        <!-- center posts -->
        <div class="col-md-6">
          <div class="row">
            <!-- left posts-->
            <div class="col-md-12">
              <div class="row">
                <div class="col-md-12">
                <!-- post state form -->
                  <div class="box profile-info n-border-top">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                        <textarea class="form-control input-lg p-text-area" style="resize:none;"name="userpost" rows="4" placeholder="Post something... "></textarea>
						<div class="box-footer box-form">
                        <button type="submit" name="submit" value="post!" class="btn btn-azure pull-right">Post</button>
                        <ul class="nav nav-pills">
                        </ul>
						</div>
						<strong><center><span style="color:red;"><?php echo $postyErr; ?></span><center><strong>
					</form>
                  </div><!-- end post state form -->

                  <!--  posts -->
				   <?php
				     
					 
					//echo "<a href='notification.php'>GO TO Notification</a>"; 
					 
				    
					for($i=0;$i<count($postRes);$i++)
					 {
						 
				     $like_btn_value=$dislike_btn_value="";
					 $share_btn_value=$post_id_for_buttons[$i]."share";
					 $delete_btn_value=$post_id_for_buttons[$i]."delete";
					 $follow_btn_value=$post_id_for_buttons[$i]."follow";
					 $comment_btn_value=$post_id_for_buttons[$i]."comment";
					 if(islike($post_id_for_buttons[$i],$user_id)) $like_btn_value=$post_id_for_buttons[$i]."removelike";
					 else $like_btn_value=$post_id_for_buttons[$i]."like";
					 if(isdislike($post_id_for_buttons[$i],$user_id)) $dislike_btn_value=$post_id_for_buttons[$i]."removedislike";
				     else $dislike_btn_value=$post_id_for_buttons[$i]."dislike";
					 
					
					 if($postRes[$i]==""||trim($postRes[$i]," ")=="") continue;
				      echo" 
                  <div id='{$postRes[$i]}'  class='box box-widget'>
                    <div class='box-header with-border'>
                      <div class='user-block'>
                        <img class='img-circle' src='img/Profile/default-avatar.png' alt='User Image'>
						<div>
						<form method='post' action=''>
							<button name='submit' class='btn btn-sm btn-palegreen pull-left' type='submit' value='{$follow_btn_value}' >
							<i class='fa fa-plus '></i>Follow</button>
							<button type='submit' value='{$delete_btn_value}' name='submit' class='btn btn-danger btn-xs pull-right' >delete / hide</button>
							</li>
							
						</form>
						</div>
					 <span class='description'> &nbsp; {$dateRes[$i]} </span> <!--date-->
                      </div>
                    </div>

                    <div id='$post_id_for_buttons[$i]' class='box-body' style='display: block;'>
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
					
					$rep_value = $postRep[$i];
					if($rep_value > 0){
						$rep_value="+".$rep_value;
					}
				     
                     echo "
					 </b> <!--hashTag-->
						<br>
						<form method='post' action=''>
													
							<button type='submit' value='{$like_btn_value}' 	name='submit' class='btn btn-default btn-xs pull-left'><i class='fa fa-thumbs-o-up'></i></button>
							
							<span class='text-muted pull-left'> <font style='color:purple;'> &nbsp; {$rep_value} &nbsp;</font></span>
							<button type='submit' value='{$dislike_btn_value}'	name='submit' class='btn btn-default btn-xs pull-left'><i class='fa fa-thumbs-o-down'></i></button>
							<button type='submit' value='{$share_btn_value}' 	name='submit' class='btn btn-default btn-xs pull-left'><i class='fa fa-share'></i> Share</button>
							
							<span class='pull-right text-muted'>{$number_of_comments} comments</span><br>
						</form>
                      
                    </div>";
					
					
					
					
					foreach($postComment_arr_final as $postcommentval)
					{
						if($postcommentval==""||trim($postcommentval," ")=="") continue;
						
						$query="SELECT `comment` FROM `comments` where `id` = ? ";	
			            $comment_value = exe_query($query,"i",$postcommentval,"","","","","");
						
						$query="SELECT `time` FROM `comments` where `id` = ? ";	
			            $comment_time_database = exe_query($query,"i",$postcommentval,"","","","","");
						
						if($comment_value==""||trim($comment_value," ")=="") continue;
						
						$comment_time=date("g:ia - l jS F Y", strtotime($comment_time_database)+ 3600);
			            if(date("Y:m:d", strtotime($comment_time_database)+ 3600)==date("Y:m:d",(time()+3600))){
				        $comment_time=date("g:ia", strtotime($comment_time_database)+ 3600)." - Today";
			             }
						
						echo "
                    <div class='box-footer box-comments' style='display: block;'>
                      <div class='box-comment'>
                         <img class='img-circle img-sm' src='img/Profile/default-avatar.png' alt='User Image'>
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
                      <form id='comment_form' action='#' method='post'>
                        <img class='img-responsive img-circle img-sm' src='img/Profile/default-avatar.png' alt='Alt Text'>
                        <div class='img-push'>
						  <input type='text' class='form-control input-sm' style='resize:none;' name='comment_msg' rows='3' placeholder='post comment'></textarea>
						  
						  <input type='submit' name='submit' value='$comment_btn_value' style='display: none;' />
                        </div>
                      </form>
                    </div>
					 </div>";}?><!--  end posts -->
                </div>
              </div>
            </div><!-- end left posts-->
          </div>
        </div><!-- end  center posts -->




        <!-- right posts -->
        <div class="col-md-3">
          <!-- Friends activity -->
          <div id="followed" class="widget">
            <div class="widget-header">
              <h3 style ="font-size:110%" class="widget-caption"><strong>Followed tags</strong></h3>
            </div>
            <div class="widget-body bordered-top bordered-sky">
              <div class="card">
                <div class="content">
                   <ul class="list-unstyled team-members">
				   <?php
				     for($i=0;$i<count($fol_tags)&&$i<10&&count($fol_tags)>0;$i++){
						 
						 if($fol_tags[$i]==""||trim($fol_tags[$i]," ")=="") continue;
						 
				      echo " <li>
                      <div class='row'>
					  <div class='col-xs-3'>
                                      <div class='avatar'>
                                          <img style='width:75%; height:75%;' src='img/Friends/hash.jpg' alt='Circle Image' class='img-circle img-no-padding img-responsive'>
                                      </div>
									  </div>
                        <div class='col-xs-6'>
                                     <strong><a style=' font-size:150%; 'href='$fol_tags[$i]'>{$fol_tags_name[$i]}</a></strong>
                                  </div>
                        <div class='col-xs-3 text-right'>
								  <form method='post' action=''>
                                  <button name='submit' type='submit' value='$fol_tags_name[$i]' class='btn btn-sm btn-darkorange pull-rights'>
                                  <i class='fa fa-times '></i></button>
								  </form>
                                  </div>
                      </div>
                    </li>";
					 }?>
					 </ul>         
                </div>
              </div>
            </div>
          </div><!-- End followed tags -->

          <!-- People You May Know -->
          <div id="trending" class="widget">
            <div class="widget-header">
              <h3 style ="font-size:110%" class="widget-caption"><strong>Trending tags</strong></h3>
            </div>
            <div class="widget-body bordered-top bordered-sky">
              <div class="card">
                  <div class="content">
                      <ul class="list-unstyled team-members">
					  <?php
				     for($i=0;$i<count($allTags)&&$i<10&&count($allTags)>0;$i++)
					 { 
				                if($allTags[$i]==""||trim($allTags[$i]," ")=="") continue;
				 echo "
                          <li>
                              <div class='row'>
                                  <div class='col-xs-3'>
                                      <div class='avatar'>
                                          <img style='width:75%; height:75%;' src='img/Friends/hash.jpg' alt='Circle Image' class='img-circle img-no-padding img-responsive'>
                                      </div>
                                  </div>
                                  <div class='col-xs-6'>
                                     <strong><a style=' font-size:150%; 'href='$allTags[$i]'>{$nameTags[$i]}</a></strong>
                                  </div>
                                  <div class='col-xs-3 text-right'>
								  <form method='post' action=''>
                                  <button name='submit' type='submit' value='$nameTags[$i]' class='btn btn-sm btn-palegreen pull-right'>
                                  <i class='fa fa-plus '></i></button>
								  </form>
                                  </div>
                              </div>
					 </li>";}?>
                      </ul>
                  </div>
              </div>          
            </div>
          </div><!-- End trending tags --> 
        </div><!-- end right posts -->
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
  </body>
</html>
