<?php
    function get_post($idnew){
        // get all the information from the database
        // and display it
        include("config.php");
        
        
        
        $con = new dbConnect;
        $con->connect();
        
        $id = (int)$idnew;
        
        // get post information
        $res = ($con->exec_query("SELECT * FROM `post` WHERE `post_id`='" . $id . "'"));
        $result = mysqli_fetch_assoc($res);
        // if the post actually exists
        
        if ($res->num_rows < 1){
        ?>
            <div id="outer-message">
                <div id="pop-message">
                    <b style="font-size:18px;">Sorry!</b>
                    <hr/>
                    This post no longer exists
                </div>
            </div>
        <?php
            return;
        }
        
        // check if the user is part of this region
        if ($result['region_id'] != $_SESSION['hold_region'] && $result['region_id'] != 0){
        ?>
            <div id="outer-message">
                <div id="pop-message">
                    <b style="font-size:18px;">Sorry!</b>
                    <hr/>
                    You do not have access to this region.
                </div>
            </div>
        <?php  
            return;
        }
        
        // get the comments
        $comments = $con->exec_query("SELECT * FROM `comment` WHERE `post_id`='" . $id . "'");
        
        // next add view to the database
        $con->exec_query("UPDATE `post` SET `views` = `views` + 1 WHERE `post_id`='" . $id . "'");
        
        
        // now create the page for the user
?>
<script>
    document.title = "<?php echo $result['title'];?> - WesterosPowers";
    
    
    function editPost(){
        alert("This feature is not available yet.");
    }
    
</script>
<div id="post-content">
    
    <div id="post-title">
        <span id="post-large"><?php echo "[".$result['type'] . "] <span style='color:#C86464;'>" . $result['title'] . "</span>"; ?></span><br/>
        <span id="post-info">
        <?php
            // get information about the post
            // eg. Event occured *date* by Lord Stark
            $time = user_time($result['timestamp'],"d \of M Y \a\\t H:i:s"); //(strtotime($row['timestamp']));
            echo "Event occured " . $time; //- time();
            echo " by ";
            
            $temp_results = $con->exec_query("SELECT `title`, `house_id`, `user_id` FROM `user` WHERE `user_id`='" . $result['user_id'] . "' LIMIT 1");
            
            $temp_row = mysqli_fetch_assoc($temp_results);
            
            $temp = $con->exec_query("SELECT * FROM `house` WHERE `house_id` = '" . $temp_row['house_id'] . "'");

            $temp_rows = mysqli_fetch_assoc($temp);
            
            echo " <a href='http://" . $host . "/?user=" . $temp_row['user_id'] . "'>" . $temp_row['title'] . " " . $temp_rows['house_name'] . "</a>";
            
            $comment = $con->exec_query("SELECT * FROM `comment` WHERE `post_id` = '" . $row['post_id'] . "'");
            
        ?>
        
        </span>
    </div>
    
    <div id="post-body">
        <?php echo nl2br($result['body']); ?>
    </div>
    
    <span>
        <a href="" onclick="alert('Successfully marked as spam.');mark_as_spam(<?php echo $row['post_id']; ?>);">Spam</a>
        <?php if (is_mod() || is_admin() || $_SESSION['userid'] == $result['user_id']){ ?> | <a onclick="editPost();">Edit</a> <?php } ?>
        <?php if(is_admin() || is_mod() || $result['user_id'] == $_SESSION['userid']){?>| <a href="http://<?php echo $host . '/f/post?type=remove&id=' . $result['post_id']; ?>" onclick="return confirm('Are you sure you want to delete post: <?php echo $result['title']; ?> ');">Remove</a>
        <?php } if((is_admin() || is_mod()) && $result['stickied'] == 1){?>| <a href="http://<?php echo $host . '/f/post?type=remsticky&num=0&id=' . $result['post_id']; ?>">Remove Sticky</a>
        <?php } if((is_admin() || is_mod()) && $result['stickied'] == 0){?>| <a href="http://<?php echo $host . '/f/post?type=remsticky&num=1&id=' . $result['post_id']; ?>">Add Sticky</a> <?php } ?>
        <?php
            if ((is_admin() || is_mod()) && $result['type'] == "Claim"){
        ?>
            <br/>
            <br/>
            <div style="text-align: center; margin: auto; width: 300px; border-radius: 5px; border: 1px solid rgb(0, 0, 0); padding: 10px; background: none repeat scroll 0% 0% rgb(68, 68, 68); box-shadow: 3px 3px 3px rgb(153, 153, 153); color: rgb(238, 238, 238);">
                <?php
                    
                    // check the title against the holdfasts in the database
                    $var = explode(" ", $result['title']);
                    
                    for ($i=0;$i<count($var);$i++){
                        
                        $claimresult = $con->exec_query("SELECT * FROM `holdfast` WHERE `hname` LIKE '%" . $var[$i] . "%' AND `user_id` = 0");
                        
                        if ($claimresult->num_rows != 1){
                            $error = 1;
                        } else {
                            $error = 0;
                            $i = count($var);
                            $claimresult = mysqli_fetch_assoc($claimresult);
                ?>
                        Do you accept this claim for <?php 
                            
                            echo $claimresult['hname'];
                            
                        ?>?<br/>
                        <script>
                        
                            function decline(){
                                var textarea = document.getElementById('commenttext');
                                textarea.value = "Sorry but your claim for <?php echo $claimresult['hname']; ?> was not accepted.";
                                textarea.focus();
                            }
                            
                            function accept(){
                                // ajax request to page to add it to the database
                                
                                var request = new ajaxRequest();
                                
                                
                                request.onreadystatechange = function(){
                                    if (request.readyState == 4){
                                            if (request.status == 200){
                                                if (request.responseText == "error"){
                                                    alert("There was an error");
                                                } else {
                                                    var textarea = document.getElementById('commenttext');
                                                    textarea.value = "Your claim for <?php echo $claimresult['hname']; ?> was accepted!";
                                                    textarea.focus();
                                                }
                                            } else {
                                                
                                            }
                                        }
                                };
                                
                                request.open("GET", "http://10.5.1.15/f/post?claimhold&hold=<?php echo $claimresult['hold_id']; ?>&user=<?php echo $temp_row['user_id']; ?>", true);
                                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                                request.send();
                                
                            }
                        </script>
                        <a onclick="accept();">Accept</a> | <a onclick="decline();">Decline</a><br/>
                        (Check this is the correct holdfast)
                <?php
                        }
                        
                        
                    }
                    
                    if ($error == 1){
                        echo "The holdfast could not be found. Maybe it is already taken.";
                    }
                ?>
            </div>
        <?php
            }
        ?>
    </span>
    
    <hr/>
    
    <div id="write-comment">
        <script>
            function check(){
                var temp = document.getElementById("commenttext");
                if (temp.value.trim() == ""){
                    document.getElementById("commentstatus").innerHTML = "You can't have an empty comment!";
                    temp.value = "";
                    temp.focus();
                    
                    setTimeout(function(){
                        document.getElementById("commentstatus").innerHTML = "";
                    }, 4000);
                    return false;
                } else {
                    return true;
                }
            }
        </script>
        <form method="post" action="http://<?php echo $host . "/f/post?comment&id=" . $idnew; ?>">
            Comment:<br/>
            <textarea onkeyup="textAreaAdjust(this);" id="commenttext" style="width:98%;height:70px;" name="text"></textarea><br/>
            <input type="submit" value="Save" onclick="return check();"/>
            <span id="commentstatus"></span>
        </form>
    </div>
    
    <div id="comments">
    <?php
        
        if ($comments->num_rows < 1){
            echo "There are no comments";   
        } else {
            
            while ($row = mysqli_fetch_assoc($comments)){
        ?>
            <div id="comment<?php echo $row['comment_id']?>" class="comment">
                <span class="name">
                <?php 
                    // get the name of the user
                    $res = mysqli_fetch_assoc($con->exec_query("SELECT `title`, `house_id`, `user_id` FROM `user` WHERE `user_id`='" . $row['user_id'] . "'"));
                ?>    
                    <a style="color:white;" href="http://<?php echo $host; ?>/?user=<?php echo $res['user_id']; ?>"> 
                <?php
                    echo $res['title'] . " ";
                    $house = mysqli_fetch_assoc($con->exec_query("SELECT `house_name` FROM `house` WHERE `house_id`='" . $res['house_id'] . "'"));
                    echo $house['house_name'];
                ?>
                    </a>
                </span> Posted on <?php echo user_time($row['timestamp'],"d \of M Y \a\\t H:i:s"); ?>
                <p>
                    <?php echo nl2br($row['body']); ?>
                </p>
                <?php
                    if (is_mod() || is_admin() || $_SESSION['userid'] == $row['user_id']){
                ?>
                <span>
                    <a onclick="editPost();">Edit</a> | 
                    <a href="http://<?php echo $host; ?>/f/post?remcomment&id=<?php echo $row['comment_id']; ?>&postid=<?php echo $result['post_id']; ?>">Remove</a>
                </span>
                <?php
                    }
                ?>
            </div>
        <?php        
            }
            
        }
    ?>    
    </div>
    
</div>

<?php
        $con->close();
    }
    
?>