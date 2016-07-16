<?php
date_default_timezone_set('Africa/Cairo');
function sec_session_start() {
    $session_name = 'sec_session_id';   // Set a custom session name
    $secure = true;
    // This stops JavaScript being able to access the session id.
    $httponly = true;
    // Forces sessions to only use cookies.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
        exit();
    }
    // Gets current cookies params.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"],
        $cookieParams["path"], 
        $cookieParams["domain"], 
        $secure,
        $httponly);
    // Sets the session name to the one set above.
    session_name($session_name);
    session_start();            // Start the PHP session 
    session_regenerate_id(true);    // regenerated the session, delete the old one. 
}
function login_check($mysqli) {
    // Check if all session variables are set 
    if (isset($_SESSION['user_id'], 
                        $_SESSION['username'], 
                        $_SESSION['login_string'])) {
 
        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        $username = $_SESSION['username'];
 
        // Get the user-agent string of the user.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
 
        if ($stmt = $mysqli->prepare("SELECT password 
                                      FROM `users` 
                                      WHERE id = ? LIMIT 1")) {
            // Bind "$user_id" to parameter. 
            $stmt->bind_param('i', $user_id);
            $stmt->execute();   // Execute the prepared query.
            $stmt->store_result();
 
            if ($stmt->num_rows == 1) {
                // If the user exists get variables from result.
                $stmt->bind_result($password);
                $stmt->fetch();
                $login_check = hash('sha512', $password . $user_browser);
 
                if ($login_check == $login_string ){
                    // Logged In!!!! 
                    return true;
                } else {
                    // Not logged in 
                    return false;
                }
            } else {
                // Not logged in 
                return false;
            }
        } else {
            // Not logged in 
            return false;
        }
    } else {
        // Not logged in 
        return false;
    }
}
function login_check_cookie($mysqli) {
    // Check if all session variables are set 
    if (isset($_COOKIE["user_id"], 
                        $_COOKIE["username"], 
                        $_COOKIE["login_string"]))
	{
 
        $user_id = $_COOKIE['user_id'];
        $login_string = $_COOKIE['login_string'];
        $username = $_COOKIE['username'];
 
        // Get the user-agent string of the user.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
 
        if ($stmt = $mysqli->prepare("SELECT password 
                                      FROM `users` 
                                      WHERE id = ? LIMIT 1")) 
		{
            // Bind "$user_id" to parameter. 
            $stmt->bind_param('i', $user_id);
            $stmt->execute();   // Execute the prepared query.
            $stmt->store_result();
 
            if ($stmt->num_rows == 1) 
		{
                // If the user exists get variables from result.
                $stmt->bind_result($password);
                $stmt->fetch();
                $login_check = hash('sha512', $password . $user_browser);
 
                if ($login_check == $login_string )
		{
                    // Logged In!!!! 
                    return true;
        
		} else 
		{
                    // Not logged in 
                    return false;
         }
            } else 
			{
                // Not logged in 
                return false;
            }
        } else {
            // Not logged in 
            return false;
        }
    } 
	else 
	{
        // Not logged in 
        return false;
    }
}
function test_input($link,$data='') {
			$data=trim($data);
			$data=mysqli_real_escape_string($link,$data);
			$data=htmlentities($data);
			$data=nl2br($data);
			return $data;
		}
function test_input_password($data) {
		  $data = htmlspecialchars($data);
		  return $data;
		}
function crypto_rand_secure($min, $max) {
			$range = $max - $min;
			if ($range < 0) return $min; // not so random...
			$log = log($range, 2);
			$bytes = (int) ($log / 8) + 1; // length in bytes
			$bits = (int) $log + 1; // length in bits
			$filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do 
		{
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } 
			while ($rnd >= $range);
			return $min + $rnd;
}
function getToken($length=32){
			$token = "";
			$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
			$codeAlphabet.= "0123456789";
			for($i=0;$i<$length;$i++){
			$token .= $codeAlphabet[crypto_rand_secure(0,strlen($codeAlphabet))];
		}
		return $token;
}
function gethashtags($string) {  
    $hashtags= FALSE;  
    preg_match_all("/(#[a-zA-Z0-9_زوةىرؤءئشسيبلاتنمكطدجحخهعغفقثصض]+)/u", $string, $matches);  
    if ($matches) {
        $hashtagsArray = array_count_values($matches[0]);
        $hashtags = array_keys($hashtagsArray);
    }
    return $hashtags;
}
function exe_query($query,$types,$v1,$v2,$v3,$v4,$v5,$v6){
		$link = mysqli_connect("localhost","cl51-users-nr3","D.Uyckj4N","cl51-users-nr3");
	 $stmt = $link->prepare($query);
		if(!$stmt){
			echo "Error at Database, Try again 1";
	        die();
		}
		$val="";
		if($v6!="") $stmt->bind_param($types,$v1,$v2,$v3,$v4,$v5,$v6);
		else if($v5!="") $stmt->bind_param($types,$v1,$v2,$v3,$v4,$v5);
		else if($v4!="") $stmt->bind_param($types,$v1,$v2,$v3,$v4);
		else if($v3!="") $stmt->bind_param($types,$v1,$v2,$v3);
		else if($v2!="") $stmt->bind_param($types,$v1,$v2);
		else if($v1!="") $stmt->bind_param($types,$v1);
		if(!$stmt){
			echo "Error at Database, Try again 2";
	        die();
		}
		$ok=false;
		$arr=explode(' ', strtolower(trim($query)));
		if($arr[0]=="select")
		{
			$ok=true;
		}
		$stmt->execute();
		if($ok){$stmt->bind_result($res);
        $stmt->fetch();
		$stmt->close();
		return $res;}
		else{
			$stmt->close();
		}
}
function binary_search($arr,$val){
        $left=0;
        $right=count($arr);
        while($left<$right){
            $mid=($left+$right)/2;
            if($arr[$mid]==$val) return true;
            if($arr[$mid]>$val) $left= $mid+1;
            else $right =$mid-1;
        }
	if($left<count($arr)&&$arr[$left]==$val) return true;
        return false;
}
function remove_tag_posts_with_friends_posts($arr1,$arr2){
	arsort($arr2);
	$res=array();
	foreach($arr2 as $i){
		if(!binary_search($arr1,$i))
			$res[]=$i;
	}
	return $res;
}
function home_posts(){
	$query=" SELECT `tagsid` FROM `userstags` WHERE `username` = ? ";
	$st=exe_query($query,"s",$_SESSION['username'],"","","","","");
    $tags_arr=explode(' ',$st);
	$tags_arr=array_reverse($tags_arr);
	$home_posts="";
	$num_posts_per_tag=100/count($tags_arr); //2
	$rem_posts=0;
	$ans="";
	foreach($tags_arr as $tag){
		$query="SELECT `postsid` FROM `tags` WHERE `name`= ? ";
		$st=exe_query($query,"s",$tag,"","","","","");
		$posts_arr=explode(' ',$st);
		$x=0;
		while($x<count($posts_arr)&& $x<$rem_posts+$num_posts_per_tag){
			$ans=$ans." ".$posts_arr[$x];
			
			$x++;
		}
		if($x<$num_posts_per_tag) $rem_posts=$rem_posts+($num_posts_per_tag-$x);
	}
	$query=" SELECT `id_posts_dislike` FROM `userstags` WHERE `username` = ? ";
	$st=exe_query($query,"s",$_SESSION['username'],"","","","","");
    $arr2=explode(' ',$st);
	if($num_posts_per_tag!=0){
	$arr1=explode(' ',$ans);
	if($st!=""&&str_replace(" ","",$st)!="") $arr1=array_diff($arr1,$arr2);//posts from tags
	//$ans=trim(implode(" ",$arr1));
              }
    $query=" SELECT `posts_from_friends` FROM `userstags` WHERE `username` = ? ";
	$friends_posts=exe_query($query,"s",$_SESSION['username'],"","","","","");
	$arr_friends_posts=explode(' ',$friends_posts);
	if($st!=""&&str_replace(" ","",$st)!=""&&count($arr_friends_posts)>0) $arr_friends_posts=array_diff($arr_friends_posts,$arr2);
	$ans=trim(implode(" ",$arr_friends_posts));
	$arr1=remove_tag_posts_with_friends_posts($arr_friends_posts,$arr1);
	$ans=$ans." ".trim(implode(" ",$arr1));
	$ans=trim($ans);
	$arr_final_res_final=explode(' ',$ans);
	$arr_final_res_final = array_unique($arr_final_res_final);
	$ans="";
	$ans.=implode(" ",$arr_final_res_final);
	$ans=trim($ans);
    $query="UPDATE `userstags` SET `posts_home_id`='$ans' WHERE `username`=? ";
	$st=exe_query($query,"s",$_SESSION['username'],"","","","","");
}
function delete_post ($postid,$user_id){
	$posttags=exe_query("SELECT `tags` FROM `posts` WHERE `id`= ?","i",$postid,"","","","","");
	$postwriter=exe_query("SELECT `id_writer` FROM `posts` WHERE `id`= ?","i",$postid,"","","","","");
		if($user_id==$postwriter){
			delete_all_comments($postid);
			$arr=explode(' ',$posttags);
			foreach($arr as $i){
				    $res=exe_query("SELECT `postsid` FROM `tags` WHERE `name`= ?","s",$i,"","","","","");
                  	if(empty($res)) continue;
			        $arrres=explode(' ',$res);
					$key = array_search($postid, $arrres); 
					array_splice($arrres, $key, 1);
					$finalres=trim(implode(" ",$arrres));
					exe_query("UPDATE `tags` SET `postsid` = ? WHERE `name` = ?","ss",$finalres,$i,"","","","");
			}
		}
		else{
			 $unlike=exe_query("SELECT `id_posts_dislike` FROM `userstags` WHERE `id_user`= ?","i",$user_id,"","","","","");
			 $unlike=trim($unlike." ".$postid);
			 exe_query("UPDATE `userstags` SET `id_posts_dislike` = ? WHERE `id_user` = ?","si",$unlike,$user_id,"","","","");
		}
}
function follow($id_post,$id_person){
        $query="SELECT `id_writer` FROM `posts` WHERE `id`= ? ";
        $id_writer=exe_query($query,"i",$id_post,"","","","","");
		if($id_writer!=$id_person){
        $query="SELECT `people_follow_you` FROM `userstags` WHERE `id_user`= ? ";
        $st=exe_query($query,"i",$id_writer,"","","","","");
		if(empty($st)) $st="";
		if(isfollow($id_writer,$id_person,$st)){
			unfollow($id_writer,$id_person,$st);
			return "unfollow";
		}
        else {  $st=trim($id_person." ".$st);		
        $query="UPDATE `userstags` SET `people_follow_you`='$st' WHERE `id_user`=? ";
        exe_query($query,"i",$id_writer,"","","","","");
		return "follow";
		    }
		}
		else {
			return false;
		}
}
function isfollow($id_writer,$id_person,$people_follow_you){
	if($people_follow_you==""||trim($people_follow_you)=="") return false;
	$arr=explode(" ",$people_follow_you);
	return in_array($id_person,$arr);
}
function isfollowbutton($id_post,$id_person){
	$query="SELECT `id_writer` FROM `posts` WHERE `id`= ? ";
    $id_writer=exe_query($query,"i",$id_post,"","","","","");
	$query="SELECT `people_follow_you` FROM `userstags` WHERE `id_user`= ? ";
    $people_follow_you=exe_query($query,"i",$id_writer,"","","","","");
	if(empty($people_follow_you)) $people_follow_you="";
	if($people_follow_you==""||trim($people_follow_you)=="") return false;
	$arr=explode(" ",$people_follow_you);
	return in_array($id_person,$arr);
}
function isfollowself($id_post,$id_person){
	$query="SELECT `id_writer` FROM `posts` WHERE `id`= ? ";
    $id_writer=exe_query($query,"i",$id_post,"","","","","");
	if($id_writer==$id_person) return true;
	else return false;
}
function unfollow($id_writer,$id_person,$people_follow_you){
	    $follower_arr=explode(" ",$people_follow_you);
	    $key = array_search($id_person, $follower_arr); 
		array_splice($follower_arr, $key, 1);
		$st="";
		$st=implode(" ",$follower_arr);
		if(empty($follower_arr)){
				$st="";
			}
		$st=trim($st);
		$query="UPDATE `userstags` SET `people_follow_you`='$st' WHERE `id_user`=? ";
        exe_query($query,"i",$id_writer,"","","","","");
}
function update_posts_from_followers($id_post ,$id_writer){
        $st="";
		$query="SELECT `people_follow_you` FROM `userstags` WHERE `id_user`= ? ";
        $st.=exe_query($query,"i",$id_writer,"","","","","");
        $arr=explode(' ',$st);
        foreach($arr as $id_person){
           $st="";
		   $query="SELECT `posts_from_friends` FROM `userstags` WHERE `id_user`= ? ";
		   $st.=exe_query($query,"i",$id_person,"","","","","");
           $st=trim($id_post." ".$st); 
		   $arr_res = explode(" ", $st);
		   $arr_res = array_unique($arr_res);
		   $st = implode(" ",$arr_res);
           $query="UPDATE `userstags` SET `posts_from_friends`='$st' WHERE `id_user`=?";
           $st=exe_query($query,"s",$id_person,"","","","","");
        }
    }
function share($id_post,$id_person_who_share){
         $query ="SELECT `people_follow_you` FROM `userstags` WHERE `id_user` =?";
         $st="";
         $st.=exe_query($query,"i",$id_person_who_share,"","","","","");
         $arr=explode(' ',$st);
        foreach($arr as $id_person){
            $query="SELECT `posts_from_friends` FROM `userstags` WHERE `id_user`= ? ";
            $st=exe_query($query,"s",$id_person,"","","","","");
            $st=$id_post." ".$st;
            $query="UPDATE `userstags` SET `posts_from_friends`='$st' WHERE `id_user`=?";
            $st=exe_query($query,"s",$id_person,"","","","","");
        }
        
    }
function follow_tag($tagname,$username,$user_id){
	    $usertags="";
			$link = mysqli_connect("localhost","cl51-users-nr3","D.Uyckj4N","cl51-users-nr3");
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
				//----------------------------------------------------------------------------------------------yasser
	               $stmt = $link->prepare(" update `userstags` set `tagsid` = '$usertags' where `username` = ?");	
		if(!$stmt){
			echo "Error at Database, Try again109";
	        die();
		}
		$stmt->bind_param("i", $username);
			//----------------------------------------------------------------------------------------------yasser
		if(!$stmt){
			echo "Error at Database, Try again114";
	        die();
		}
		$stmt->execute();
		$stmt->close();		
			}	
		  header("Refresh:0");
		
	}
function unfollow_tag($tagname,$username,$user_id){
	    $usertags="";
		 	$link = mysqli_connect("localhost","cl51-users-nr3","D.Uyckj4N","cl51-users-nr3");
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
		  header("Refresh:0");
		
	}
function insert_comment($name_writer="",$comment="",$id_post){
			$link = mysqli_connect("localhost","cl51-users-nr3","D.Uyckj4N","cl51-users-nr3");
		$query ="INSERT INTO `comments` (`name_writer`,`comment`,`id_post`) VALUES(?,?,?)";
        $st=exe_query($query,"ssi",$name_writer,$comment,$id_post,"","","");
        $query="SELECT `id` FROM `comments` ORDER BY id DESC LIMIT 1";
		$res=mysqli_query($link,$query);
	    $row=mysqli_fetch_array($res);
	    $id_comment_now=$row['id'];                 // $id_comment_now = mysqli_insert_id($link);  not working if I use the new exe and working with old exe //
        $query=" SELECT `id_comments` FROM `posts` WHERE `id`= ? ";
        $st="";
        $st.=exe_query($query,"i",$id_post,"","","","","");
        $st=$id_comment_now." ".$st;
		$st=trim($st);
        $query="UPDATE `posts` SET `id_comments`='$st' WHERE `id`=?";
        $st=exe_query($query,"i",$id_post,"","","","","");
    }
function like($id_post,$user_id,$update){
        $query="SELECT `prio` FROM `posts` WHERE `id`= ? ";
        $prio=exe_query($query,"i",$id_post,"","","","","");
        $prio++;
        $query="UPDATE `posts` SET `prio`='$prio' WHERE `id`=? ";
        $st=exe_query($query,"i",$id_post,"","","","","");
		if($update){ $query="select `like_posts` from `userstags` WHERE `id_user`=? ";
        $st="";
		$st.=exe_query($query,"i",$user_id,"","","","","");
		$st=$id_post." ".$st;
		$st=trim($st);
		$query="UPDATE `userstags` SET `like_posts`='$st' WHERE `id_user`=?";
        $st=exe_query($query,"i",$user_id,"","","","","");
		}
		return $prio;
    }
function dislike($id_post,$user_id,$update){
        $query="SELECT `prio` FROM `posts` WHERE `id`= ? ";
        $prio=exe_query($query,"i",$id_post,"","","","","");
        $prio=$prio-1;
        if($prio>-10){
        $query="UPDATE `posts` SET `prio`='$prio' WHERE `id`=? ";
        $st=exe_query($query,"i",$id_post,"","","","","");
		if($update){$query="select `dislike` from `userstags` WHERE `id_user`=? ";
        $st="";
		$st.=exe_query($query,"i",$user_id,"","","","","");
		$st=$id_post." ".$st;
		$st=trim($st);
		$query="UPDATE `userstags` SET `dislike`='$st' WHERE `id_user`=?";
        $st=exe_query($query,"i",$user_id,"","","","","");
		}
        }
        else{
            delete_all_comments($id_post);
        }
		return $prio;
    }
function islike($id_post,$user_id){
	    $query="select `like_posts` from `userstags` WHERE `id_user`=? ";
        $st="";
		$st.=exe_query($query,"i",$user_id,"","","","","");
		$likearr=explode(" ",trim($st));
		return in_array($id_post,$likearr);
}
function isdislike($id_post,$user_id){
	 $query="select `dislike` from `userstags` WHERE `id_user`=? ";
        $st="";
		$st.=exe_query($query,"i",$user_id,"","","","","");
		$dislikearr=explode(" ",trim($st));
		return in_array($id_post,$dislikearr);
}
function removelike($id_post,$user_id){
	    $query="select `like_posts` from `userstags` WHERE `id_user`=? ";
        $st="";
		$st.=exe_query($query,"i",$user_id,"","","","","");
		$likearr=explode(" ",trim($st));
		$key = array_search($id_post, $likearr); 
		array_splice($likearr, $key, 1);
		$st="";
		$st=implode(" ",$likearr);
		if(empty($likearr)){
				$st="";
			}
		$st=trim($st);
		$query="UPDATE `userstags` SET `like_posts`='$st' WHERE `id_user`=?";
        $st=exe_query($query,"i",$user_id,"","","","","");
		return dislike($id_post,$user_id,false);
}
function removedislike($id_post,$user_id){
	 $query="select `dislike` from `userstags` WHERE `id_user`=? ";
        $st="";
		$st.=exe_query($query,"i",$user_id,"","","","","");
		$dislikearr=explode(" ",trim($st));
		$key = array_search($id_post, $dislikearr); 
		array_splice($dislikearr, $key, 1);
		$st="";
		$st=implode(" ",$dislikearr);
		if(empty($dislikearr)){
				$st="";
			}
		$st=trim($st);
		$query="UPDATE `userstags` SET `dislike`='$st' WHERE `id_user`=?";
        $st=exe_query($query,"i",$user_id,"","","","","");
		return like($id_post,$user_id,false);
}
function delete_all_comments($id_post){
        $query="SELECT `id_comments` FROM `posts` WHERE `id`= ? ";
        $st="";
        $st.=exe_query($query,"i",$id_post,"","","","","");
		if($st!=""){
        $arr=explode(' ',$st);
        foreach($arr as $id_comment){
        $query ="DELETE  FROM `comments` WHERE `id` =?";
        $st=exe_query($query,"i",$id_comment,"","","","","");    
        }
		}
        $query ="DELETE  FROM `posts` WHERE `id` =?";
        $st=exe_query($query,"i",$id_post,"","","","","");
    }
function notify($user_id){
	 $result=array();
	 $query="SELECT `people_follow_you` FROM `userstags` WHERE `id_user`= ? ";
     $stnew="";
     $stnew.=exe_query($query,"i",$user_id,"","","","","");
	 $new_arr=explode(" ",$stnew);
	 $query="SELECT `old_people_follow_you` FROM `userstags` WHERE `id_user`= ? ";
     $st="";
     $st.=exe_query($query,"i",$user_id,"","","","","");
	 $old_arr=explode(" ",$st);
	 $arr_temp=array();
	 $arr_temp=array_diff($new_arr,$old_arr);
	 $test=implode(" ",$arr_temp);
	 $test=trim($test);
	 if(count($arr_temp)>0&&$test!=""&&!empty($test)) $result[]="follow"."#".count($arr_temp);
	 $query="UPDATE `userstags` SET `old_people_follow_you`='$stnew' WHERE `id_user`=?";
	 exe_query($query,"i",$user_id,"","","","","");
	 	$link = mysqli_connect("localhost","cl51-users-nr3","D.Uyckj4N","cl51-users-nr3");
	 $query="SELECT `prio`,`oldprio`,`id`,`id_comments`,`old_id_comments` FROM `posts` WHERE `id_writer`= '$user_id' ";
	 $res=mysqli_query($link,$query);
	 $red="viewpost.php?q=";
	 while($row = mysqli_fetch_array($res))
  {
	if($row['prio']!=$row['oldprio']){
		$result[]="reacted"."#".$red.$row['id'];
		$val=$row['prio'];
		 $query="UPDATE `posts` SET `oldprio`='$val' WHERE `id`=?";
			 exe_query($query,"i",$row['id'],"","","","","");
    }
    else if($row['id_comments']!=$row['old_id_comments']){
		$result[]="commented"."#".$red.$row['id'];
		$val=$row['id_comments'];
		 $query="UPDATE `posts` SET `old_id_comments`='$val' WHERE `id`=?";
			 exe_query($query,"i",$row['id'],"","","","","");
    }
		
  }
	 return $result;
}
function no_of_followers($user_id){
	 $query="SELECT `people_follow_you` FROM `userstags` WHERE `id_user`= ? ";
     $st="";
	 $st.=exe_query($query,"i",$user_id,"","","","","");
	 $arr=array();
	 $st=trim($st);
	 if(!empty($st)&&$st!="") $arr=explode(" ",$st);
	 return count($arr);
}
function clear_old_database($user_id){
	$query="SELECT `id_posts_dislike` FROM `userstags` WHERE `id_user`= ? ";
    $id_posts_dislike="";
	$id_posts_dislike.=exe_query($query,"i",$user_id,"","","","","");
	
	$query="SELECT `like_posts` FROM `userstags` WHERE `id_user`= ? ";
    $like="";
	$like.=exe_query($query,"i",$user_id,"","","","","");
	
	$query="SELECT `dislike` FROM `userstags` WHERE `id_user`= ? ";
    $dislike="";
	$dislike.=exe_query($query,"i",$user_id,"","","","","");
	
	$query="SELECT `posts_from_friends` FROM `userstags` WHERE `id_user`= ? ";
    $posts_from_friends="";
	$posts_from_friends.=exe_query($query,"i",$user_id,"","","","","");
	//######################################################################
	if($id_posts_dislike!=""&&trim($id_posts_dislike)!=""){
		$id_posts_dislike_arr=explode(" ",$id_posts_dislike);
		$id_posts_dislike_arr_final=array();
		foreach($id_posts_dislike_arr as $i){
			$query="SELECT `id` FROM `posts` WHERE `id`= ? ";
            $temp="";
            $temp.=exe_query($query,"i",$i,"","","","","");
			if($temp!=""&&trim($temp)!=""){
				$id_posts_dislike_arr_final[]=$i;
			}
		}
		$temp="";
		$temp=implode(" ",$id_posts_dislike_arr_final);
		if(empty($id_posts_dislike_arr_final)){
				$temp="";
			}
		$temp=trim($temp);
		$query="UPDATE `userstags` SET `id_posts_dislike`='$temp' WHERE `id_user`=? ";
        exe_query($query,"i",$user_id,"","","","","");
	}
	if($like!=""&&trim($like)!=""){
		$like_arr=explode(" ",$like);
		$like_arr_final=array();
		foreach($like_arr as $i){
			$query="SELECT `id` FROM `posts` WHERE `id`= ? ";
            $temp="";
            $temp.=exe_query($query,"i",$i,"","","","","");
			if($temp!=""&&trim($temp)!=""){
				$like_arr_final[]=$i;
			}
		}
		$temp="";
		$temp=implode(" ",$like_arr_final);
		if(empty($like_arr_final)){
				$temp="";
			}
		$temp=trim($temp);
		$query="UPDATE `userstags` SET `like_posts`='$temp' WHERE `id_user`=? ";
        exe_query($query,"i",$user_id,"","","","","");
	}
	if($dislike!=""&&trim($dislike)!=""){
		$dislike_arr=explode(" ",$dislike);
		$dislike_arr_final=array();
		foreach($dislike_arr as $i){
			$query="SELECT `id` FROM `posts` WHERE `id`= ? ";
            $temp="";
            $temp.=exe_query($query,"i",$i,"","","","","");
			if($temp!=""&&trim($temp)!=""){
				$dislike_arr_final[]=$i;
			}
		}
		$temp="";
		$temp=implode(" ",$dislike_arr_final);
		if(empty($dislike_arr_final)){
				$temp="";
			}
		$temp=trim($temp);
		$query="UPDATE `userstags` SET `dislike`='$temp' WHERE `id_user`=? ";
        exe_query($query,"i",$user_id,"","","","","");
	}
	if($posts_from_friends!=""&&trim($posts_from_friends)!=""){
		$posts_from_friends_arr=explode(" ",$posts_from_friends);
		$posts_from_friends_arr_final=array();
		foreach($posts_from_friends_arr as $i){
			$query="SELECT `id` FROM `posts` WHERE `id`= ? ";
            $temp="";
            $temp.=exe_query($query,"i",$i,"","","","","");
			if($temp!=""&&trim($temp)!=""){
				$posts_from_friends_arr_final[]=$i;
			}
		}
		$temp="";
		$temp=implode(" ",$posts_from_friends_arr_final);
		if(empty($posts_from_friends_arr_final)){
				$temp="";
			}
		$temp=trim($temp);
		$query="UPDATE `userstags` SET `posts_from_friends`='$temp' WHERE `id_user`=? ";
        exe_query($query,"i",$user_id,"","","","","");
	}
	//#########################################delete notiy
	
		$query="SELECT `notify` FROM `notification` where `user_id` = '$user_id'";
        $notify_old = "";			  
		$notify_old .= exe_query($query,"","","","","","","");
		$notify_old_arr=array();
		$notify_old_arr=explode(" ",$notify_old);
		/*
		commented#viewpost.php?q=5 follow#1 reacted#viewpost.php?q=5
		*/
		/*//echo substr($kj,25); commented
//echo substr($kj,23); reacted 
//echo substr($kj,7); follow
		*/
		$notify_new_arr=array();
		$first=0;
		foreach($notify_old_arr as $kj){
			if($kj==""||trim($kj)=="") continue;
	        $arr_test_arr=explode("#",$kj);
			if($arr_test_arr[0]=="commented"){
				$hamo_test=substr($kj,25);
				$query="SELECT `id` FROM `posts` WHERE `id`= ? ";
                $temp="";
                $temp.=exe_query($query,"i",$hamo_test,"","","","","");
			    if($temp!=""&&trim($temp)!=""){
				    $notify_new_arr[]=$kj;
			    }
			}
			else if($arr_test_arr[0]=="reacted"){
				$hamo_test=substr($kj,23);
				$query="SELECT `id` FROM `posts` WHERE `id`= ? ";
                $temp="";
                $temp.=exe_query($query,"i",$hamo_test,"","","","","");
			    if($temp!=""&&trim($temp)!=""){
				    $notify_new_arr[]=$kj;
			    }
			}
			else if($arr_test_arr[0]=="follow"&&$first==0){
					$notify_new_arr[]=$kj;
			}
			$first++;
		}
			$notify_update_database="";
			$notify_update_database.=implode(" ",$notify_new_arr);
			$query="update `notification` set `notify` = '$notify_update_database' where `user_id` = '$user_id'";
    		exe_query($query,"","","","","","","");
	
}
function search_for_words($words){
	$final_res=array();
		$link = mysqli_connect("localhost","cl51-users-nr3","D.Uyckj4N","cl51-users-nr3");
	$words=test_input($link,$words);
	$words_arr=explode(" ",$words);
	foreach($words_arr as $i){
	$i=trim($i);
	$query="SELECT `id` FROM `posts` WHERE `post` like '%$i%'";
	$res=mysqli_query($link,$query);
	 while($row = mysqli_fetch_array($res))
    {
	    $final_res[]=$row['id'];
    }
	}
	$final_res = array_unique($final_res);
	return $final_res;
}
?>