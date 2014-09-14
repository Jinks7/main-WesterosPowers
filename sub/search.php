<?php

include_once("utilities.php");

if (check_login() && isset($_GET['s'])){
    $db = new dbConnect;
    $db->connect();
    $search_term = $db->input(trim($_GET['s']));
    $db->close();
?>

<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $search_term; ?> - Search - WesterosPowers</title>
        <link rel="stylesheet" href="http://<?php echo $host; ?>/s/style/main.css"/>
        <script>
            function resizeHeight(){
                //find the content length in the sidebars and resize the window based on that
                var height = document.getElementById("body").offsetHeight;
                var sideheight = document.getElementById("sideright").offsetHeight;
                if (sideheight > height){
                    document.getElementById("content").style.height = sideheight + "px";
                } else {
                    document.getElementById("content").style.height = height + "px";
                }
                
            }
            window.onload = function(){
                resizeHeight();
            };
        </script>
    </head>
    
    <body>
        <div id="wrapper">
            <?php
                include("header.php");
            ?>
            
            <div id="content" class="main">
            
                <div id="sideleft">
                    <?php
                        include_once("sideleft.php");
                        get_left();
                    ?>
                </div>
                
                <div id="sideright">
                    <?php
                        get_right();
                    ?>
                </div>
            
                <div id="body" class="mainclass">
                    <p style="font-size:22px;">
                        <span style="color:#C86464;">Searching for:</span> <?php echo $search_term; ?>
                    </p>
                    <?php
                        $db->connect();
                        
                        // get the users that match from the database
                        
                        $houseresult = $db->exec_query("SELECT * FROM `house` WHERE `house_name` LIKE '%" . $search_term . "%' AND `taken` = 1");
                        if ($houseresult->num_rows > 0){
                    ?>
                        <h3 class="nopadding" style="margin-bottom:-10px;">Users</h3>
                        <ul class="nostyle">
                            <?php
                                while ($temp = mysqli_fetch_assoc($houseresult)){
                                    $user_temp = mysqli_fetch_assoc($db->exec_query("SELECT * FROM `user` WHERE `house_id` = '" . $temp['house_id'] . "'"));
                            ?>
                                <li><a href="http://<?php echo $host; ?>/?user=<?php echo $user_temp['user_id']; ?>"><?php echo $user['title'] . " " . $temp['house_name']; ?></a></li>
                            <?php
                                }
                            ?>
                        </ul>
                    <?php
                        }
                        
                        // get posts that match from the database
                        $result = $db->exec_query("SELECT * FROM `post` WHERE `title` LIKE '%" . $search_term . "%'");
                        
                        if ($result->num_rows < 1 && $houseresult->num_rows < 1){
                    ?>
                        <div id="outer-message">
                            <div id="pop-message">
                                <b style="font-size:18px;">Sorry!</b>
                                <hr/>
                                No results found
                            </div>
                        </div>
                    <?php
                        } elseif ($result->num_rows > 0) {
                        ?>
                            <h3 class="nopadding">Events</h3>
                        <?php
                        }
                        while ($row = mysqli_fetch_assoc($result)){
                    ?>
                        <div id="post" class="<?php echo "post" . $row['post_id'] . (($row['stickied'] == 1) ? " stickied" : "");  ?>">
                            <span id="type">[<?php echo $row['type']; ?>]</span> <a id="ptitle" href="http://<?php echo $host . "/?view=" . $row['post_id']; ?>"><?php echo $row['title']; ?></a><br/>
                            <span>
                            <?php
                                $time = user_time($row['timestamp'],"d \of M Y \a\\t H:i:s"); //(strtotime($row['timestamp']));
                                echo "Event occured " . $time; //- time();
                                echo " by ";
                                
                                $temp_results = $db->exec_query("SELECT `title`, `house_id`, `user_id` FROM `user` WHERE `user_id`='" . $row['user_id'] . "' LIMIT 1");
                                
                                $temp_row = mysqli_fetch_assoc($temp_results);
                                
                                $temp = $db->exec_query("SELECT * FROM `house` WHERE `house_id` = '" . $temp_row['house_id'] . "'");

                                $temp_rows = mysqli_fetch_assoc($temp);
                                
                                echo " <a href='http://" . $host . "/?user=" . $temp_row['user_id'] . "'>" . $temp_row['title'] . " " . $temp_rows['house_name'] . "</a>";
                                
                                $comment = $db->exec_query("SELECT * FROM `comment` WHERE `post_id` = '" . $row['post_id'] . "'");
                                
                            ?>
                            </span><br/>
                            <span>
                                <a href="http://<?php echo $host . "/?view=" . $row['post_id']; ?>">View<?php echo "(" . $comment->num_rows . ")"; ?></a>
                                | <a href="" onclick="alert('Successfully marked as spam.');mark_as_spam(<?php echo $row['post_id']; ?>);">Spam</a>
                                <?php if(is_admin() || is_mod() || $row['user_id'] == $_SESSION['userid']){?>| <a href="http://<?php echo $host . '/f/post?type=remove&id=' . $row['post_id']; ?>" onclick="return confirm('Are you sure you want to delete post: <?php echo $row['title']; ?> ');">Remove</a>
                                <?php } if((is_admin() || is_mod()) && $row['stickied'] == 1){?>| <a href="http://<?php echo $host . '/f/post?type=remsticky&num=0&id=' . $row['post_id']; ?>">Remove Sticky</a>
                                <?php } if((is_admin() || is_mod()) && $row['stickied'] == 0){?>| <a href="http://<?php echo $host . '/f/post?type=remsticky&num=1&id=' . $row['post_id']; ?>">Add Sticky</a> <?php } ?>
                            </span>
                        </div>
                    <?php
                        }
                        
                        $db->close();
                    ?>
                </div>
            </div>
            <?php
                include("footer.php");
                get_footer(false);
            ?>
        </div>
    </body>
</html>

<?php
} else {
    include("site-errors/403.php");
}

?>