<?php
    
    include("utilities.php");
    
    if (check_login()){
        if (isset($_COOKIE['first']) && $_COOKIE['first'] == "true"){
            $db = new dbConnect;
            $db->connect();
            
            $title = $db->input(trim($_POST['title']));
            $body = $db->input(trim($_POST['body']));
            $timestamp = time();
            
            if ($title == "" || $body == ""){
                $db->close();
                
                header("LOCATION: /?fail");
                
            } else {
                
                $db->exec_query("INSERT INTO `post` (`user_id`, `region_id`, `type`, `title`, `body`) VALUES ('" . $_SESSION['userid'] . "', '0', 'Claim', '" . $title . "', '" . $body . "')");
            
                $db->close();
                
                setcookie("first", "false", time()-3600, "/");
                header("LOCATION: /?success");
            }
            
        } elseif (isset($_GET['type']) && $_GET['type'] == "remove"){
              $db = new dbConnect;
              $db->connect();
              
              if (isset($_GET['id'])){
                  $id = $db->input(trim($_GET['id']));
                  
                  $result = mysqli_fetch_array($db->exec_query("SELECT `user_id` FROM `post` WHERE `post_id` = '" . $id . "'"));
                  
                  if (is_admin() || is_mod() || $result['user_id'] == $_SESSION['userid']){

                    $db->exec_query("DELETE FROM `post` WHERE `post_id` = '" . $id . "'");
                        
                    echo "Successfully deleted";
                    
                    if ($_SESSION['region']){
                        header("REFRESH: 1; ../?region");
                    } else {
                        header("REFRESH: 1; ../");
                    }
                          
                      
                  } else {
                      if ($_SESSION['region']){
                          header("LOCATION: ../?region");
                      } else {
                          header("LOCATION: ..");
                      }
                  }
              } else {
                  echo "Error removing the post";
                  //header("LOCATION: ..");
              }
              
              $db->close();
          } elseif(isset($_GET['type']) && $_GET['type'] == "remsticky"){
            // change the status of the post (stickied)
            if (isset($_GET['id'])){
                if (is_mod() || is_admin()){
                    
                    $db = new dbConnect;
                    $db->connect();
                    
                    $status = (isset($_GET['num']) && $_GET['num'] == 0) ? 0 : 1;
                    $id = $db->input(trim($_GET['id']));
                    
                    $db->exec_query("UPDATE `post` SET `stickied`='" . $status . "' WHERE `post_id`='" . $id . "'");
                    
                    $db->close();
                    
                    if ($_SESSION['region']){
                        header("LOCATION: ../?region");
                    } else {
                        header("LOCATION: ..");
                    }
                    
                } else {
                    echo "You do not have the permissions";
                }
                
            } else {
                echo "There is no id in the request";
            }
            
        } elseif (isset($_GET['comment'])){
            
            if (isset($_GET['id']) && isset($_POST['text']) && trim($_POST['text']) != ""){
                
                $db = new dbConnect;
                $db->connect();
                
                $text = $db->input(trim($_POST['text']));
                $postid = $db->input(trim($_GET['id']));
                
                $result = $db->exec_query("INSERT INTO `comment` (`post_id`, `user_id`, `body`) VALUES ('" . $postid . "', '" . $_SESSION['userid'] . "', '" . $text . "')");
                
                // get the last insert id
                $commentid = $db->get_insert_id();
                
                $db->close();
                
                header("LOCATION: /?view=" . $postid . "#comment" . $commentid);
                
            } else {
                echo "The comment is blank.";
                
                header("REFRESH: 1; ../?view=" . $_GET['id']);
            }
            
        } elseif(isset($_GET['remcomment'])) {
            
            if (isset($_GET['id'])){
                
                $db = new dbConnect;
                $db->connect();
                
                $id = $db->input(trim($_GET['id']));
                
                // next check if the user owns the comment
                $result = mysqli_fetch_assoc($db->exec_query("SELECT `user_id` FROM `comment` WHERE `comment_id`='" . $id . "'"));
                
                
                if (is_mod() || is_admin() || $result['user_id'] == $_SESSION['userid']){
                    
                    // now remove the comment from the database
                    $db->exec_query("DELETE FROM `comment` WHERE `comment_id` = '" . $id . "'");
                    
                    if (isset($_GET['postid'])){
                        header("LOCATION: ../?view=" . $_GET['postid']);
                    } else {
                        header("LOCATION: .");
                    }
                    
                } else {
                    echo "You do not have the permissions.";
                }
                
            } else {
                echo "There is no comment specified";
            }
            
        } elseif (isset($_GET['claimhold'])){
            if (is_admin() || is_mod()){
                
                $con = new dbConnect;
                $con->connect();
                
                $hold = $con->input(trim($_GET['hold']));
                $user = $con->input(trim($_GET['user']));
                
                $con->exec_query("UPDATE `holdfast` SET `user_id` = '" . $user . "' WHERE `hold_id` = '" . $hold . "'");
                
                $con->close();
                
            }
        } elseif (isset($_GET['post'])){
            
            $db = new dbConnect;
            $db->connect();
            
            $title = $db->input(trim($_POST['title']));
            $body = $db->input(trim($_POST['body']));
            $type = (int)$_POST['type'];
            $region = ($_POST['r'] == "global") ? 0 : (int)$_POST['r'];
            $type = $_POST['type'];
            $types = array("Event", "Meta", "Claim", "News", "Lore", "Conflict-Commit", "Conflict-Rally", "Conflict-Surprise", "Conflict-Score");
            
            $stickied = 0;
            
            if (is_admin() || is_mod()){
                $stickied = (isset($_POST['stickied']) && $_POST['stickied'] == 1) ? 1 : 0;
            }
            
            // check if everything contains something
            if ($title == "" || $body == "" || $type == ""){
                
                echo "You have not entered all the details, go back and make sure to enter everything in";
                
            } else {
                
                // check if user has access to that particular region
                
                if ($type < 9 && $type > -1){
                    
                    $type_text = $types[$type];
                    
                    if (is_admin() && isset($_POST['admintype']) && trim($_POST['admintype']) != ""){
                        
                        $type_text = $db->input(trim($_POST['admintype']));
                        
                    }
                    
                    // check if the user is allowed to post to the region
                    if ((!is_admin() && !is_mod()) && ($region != $_SESSION['hold_region'] && $region != 0)){
                        $_SESSION['message_title'] = "Can not post";
                        $_SESSION['message'] = "You do not have the permissions to post.";
                    } else {
                        $result = $db->exec_query("INSERT INTO `post` (`user_id`, `region_id`, `type`, `title`, `body`, `stickied`) VALUES ('" . $_SESSION['userid'] . "', '" . $region . "', '" . $type_text . "', '" . $title . "', '" . $body . "', '" . $stickied . "')");
                        
                        $id = $db->get_insert_id();
                    }
                    $db->close();
                    
                    // be better if it redirects to the post
                    if ($region == 0){
                        header("LOCATION: ../?view=" . $id);
                    } else {
                        header("LOCATION: ../?view=" . $id);
                    }
                } else {
                    echo "That is not an event";
                }
            }
            
            
        }
    } else {
        header("HTTP/1.0 404 Not Found");
    }
?>