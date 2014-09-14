<?php

include_once("utilities.php");

if (!check_login()){
	header("LOCATION: ../");
}

// if the cookie first is set to true
// echo out the claim post and the 
// tutorial will start
if (isset($_COOKIE['first']) && $_COOKIE['first'] == "true"){
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Claim - WesterosPowers</title>
        <link rel="stylesheet" href="/s/style/main.css"/>
        <style>
            .holdfast {
                width:300px;
            }
            
            textarea {
                width:96%;
                height:250px;
                resize:vertical;
            }
        
            .holdfast, textarea {
                border:1px solid #000;
            }
            .holdfast:focus, textarea:focus {
                box-shadow:0px 0px 0px red;
            }
        </style>
    </head>
    
    <body>
        <div id="wrapper">
            <?php 
                include("header.php");
            ?>
            
            <div id="content" class="main">
                
                <div id="sideleft">
                    
                </div>
                
                <div id="sideright">
                
                </div>
                
                <div id="body">
                    <?php
                        if (isset($_GET['fail'])){
                    ?>
                        <p style="color:red;text-align:center;font-size:20px;">Something went wrong please try again.</p>
                    <?php
                        } elseif (isset($_GET['success'])){
                    ?>
                        <p>it works</p>
                    <?php
                        echo $_COOKIE['first'];
                        }
                    ?>
                
                    <p>
                        Welcome! We noticed that this is the first time you have logged
                        in to WesterosPowers and you have not claimed a Holdfast. 
                        You need to claim a Holdfast before you 
                        are able to play the game with others!
                    </p>
                    <p>
                        Which holdfast you choose will change which region you are 
                        situated in and which region's sub board
                        you are able to see and post to.
                    </p>
                    <form name="tutclaim" method="POST" action="http://<?php include("config.php"); echo $host; ?>/f/post">
                        <?php
                            
                            $user = new User;
                            
                            $title = $user->get("title");
                            $hname = $user->get("rpname");
                        
                            $message = "I " . $title . " " . $hname . ", claim this holdfast as my own and swear fealty to the Lords of the region in which it is situated and the King.";
                        ?>
                        
                        <script>
                            function change(){
                                var option = document.getElementById("hold");
                                var text = option[option.selectedIndex].innerHTML;
                                var name = "<?php echo $hname; ?>";
                                var title = "<?php echo $title; ?>";
                                document.getElementById("post-text").innerHTML = "I " + title + " " + name + ", claim " + text + " as my own and swear fealty to the Lords of the region in which it is situated and the King.";
                                
                                document.getElementById("post-title").value = "Claim of " + text;
                                
                            }
                        </script>
                        <p>
                            Please choose a Holdfast:<br/>
                            <select name="holdfast" id="hold" class="holdfast"autocomplete="off" required onchange="change();">
                                <option default style="display:none;">Choose Holdfast:</option>
                                <?php
                                    // get a list of the available holdfasts
                                    include_once("utilities.php");
                                    $db = new dbConnect;
                                    $db->connect();
                                    
                                    $result = $db->exec_query("SELECT hold_id, hname FROM `holdfast` WHERE `user_id`='0'");
                                    
                                    while ($row = $result->fetch_assoc()){
                                        echo "<option value=\"" . $row['hold_id'] . "\">" . $row['hname'] . "</option>";
                                    }
                                    
                                    $db->close();
                
                                ?>
                            </select>
                        </p>
                        
                        <p>
                            This is an optional post box to make your claim. If you
                            don't type anything a default message will be posted. If you
                            want to capture the interest of readers and show off your 
                            role-playing skills, you should type your message!<br/>
                            <textarea id="post-text" name="body" placeholder="<?php echo $message; ?>" autocomplete="off" maxlength="800"></textarea>
                            <input type="hidden" id="post-title" name="title" value="" />
                        </p>
                        
                        <input type="submit" value="Request Claim"/>
                    </form>
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
    die();
}

// this will be the main page that includes everything the user can access while logged in

if (isset($_GET['region']) ){
    if (isset($_SESSION['hold_region'])){
        $region = true;
        $_SESSION['region'] = true;
    } else {
        
        $_SESSION['message_title'] = "Error!";
        $_SESSION['message'] = "You do not belong to a region yet. Please claim a holdfast to become part of a region";
        $region = false;
        $_SESSION['region'] = false;
    }
    
    
    
} else {
    $region = false;
    $_SESSION['region'] = false;
}

?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php 
		    $page = "Home";
		    
		    if (isset($_GET['message'])){
		        $page = "Messages";
		    } elseif (isset($_GET['map'])){
		        $page = "Map";
		    } elseif (isset($_GET['region'])){
		        $page = "Region";
		    } elseif (isset($_GET['user'])){
		        $page = "User";
		    } elseif(isset($_GET['rules'])) {
		        $page = "Rules";
		    } elseif(isset($_GET['setting'])) {
                $page = "Settings";
            } elseif(isset($_GET['holdfast'])) {
                $page = "Free Holdfasts";
            } elseif(isset($_GET['item'])) {
                $page = "Items";
            } elseif(isset($_GET['report'])) {
                  $page = "Report";
            } elseif(isset($_GET['admin']) || isset($_GET['mod'])) {
		        $page = (isset($_GET['admin'])) ? "Admin" : "Mod";
            }
		    echo $page;
		?> - WesterosPowers</title>
		<link rel="stylesheet" href="/s/style/main.css"/>
		
		
		<script src="/s/scripts/ajax.js"></script>
		
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
		    
		    window.onhashchange = function(){
		        checkHash();
		    };
		    
		    window.onload = function(){
		        checkHash();
		        resizeHeight();
		        
		        // hide the post form
		    };
		    
		    function checkHash(){
		        switch(window.location.hash){
                    case "#post":
                        getAjaxPage("f/post?temp");
                        break;
                }
		    }
		    
		    function setHash(text){
		        window.location.hash = text;
		    }
		    
		    function clear(){
		        document.getElementById("body").innerHTML = "";
		    }
		    
		    function showPost(){
		        document.getElementById("post-form").style.display = "block";
		        resizeHeight();
		    }
		    
		    function hidePost(){
		        document.getElementById("post-form").style.display = "none";
                resizeHeight();
		    }
		    
		    function textAreaAdjust(oField) {
		        if (oField.scrollHeight > oField.clientHeight) {
                      oField.style.height = oField.scrollHeight + "px";
                }
                
                if (oField.value == ""){
                    oField.style.height = "80px";
                }
                
                resizeHeight();
		        
            }
		</script>
		
	</head>
	<body onresize="resizeHeight();">
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
    	 
                    
    	            
    	                <?php
    	                    if (isset($_GET['view'])){
    	                        
    	                        include_once("post-page.php");
    	                        $id = (int)$_GET['view'];
    	                        get_post($id);
    	                        
    	                    } elseif (isset($_GET['message'])) { 
    	                    
    	                        include("message.php");
    	                        
    	                        get_message();
    	                        
    	                    } elseif(isset($_GET['user'])) {
    	                        
    	                        include("user.php");
    	                        
    	                    } elseif(isset($_GET['rules'])) {
                                // get some help documents
                                include("rules.php");
                                
                                  
                            } elseif(isset($_GET['map'])) {
    	                        
    	                        include("map.php");
    	                        
    	                    } elseif(isset($_GET['setting'])) {
    	                        include("settings.php");
    	                        get_settings();
    	                    } elseif(isset($_GET['item'])) {
    	                        include("items.php");
    	                        get_items();
    	                    } elseif(isset($_GET['holdfast'])) {
    	                        include("holdfast.php");
    	                    } elseif(isset($_GET['report'])) {
                                  include("report.php");
                             } elseif(isset($_GET['admin']) || isset($_GET['mod'])) {
    	                        
    	                        include("admin.php");
    	                        
    	                    } else {
    	                ?>
    	            <?php
    	                    
    	                    // get te information about what to show to the user
    	                    $page = (int)$_GET['page'];
                            
                            if ($page == 0){
                                $page = 1;
                                
                            }
                            
                            // set the order to the default, popularity
                            $order = (isset($_GET['orderby'])) ? $_GET['orderby'] : "newest";
                            
                            if ($order != "popularity" && $order != "newest"){
                                $order = "popularity";
                            }
    	                ?>
    	                
    	                <div style="margin-top:10px;margin-left:10px;">
    	                    <h2 style="margin-bottom:5px;font-family:sans-serif;font-size:23px;">Events <?php if ($region) { echo "for " . $_SESSION['region_name'];} ?></h2>
    	                    
    	                    Order By 
    	                    <?php
    	                        
    	                        
    	                    
    	                        if ($order == "popularity"){
    	                    ?>
    	                        <a href="http://<?php echo $host; ?>/?orderby=newest&page=<?php echo $page; if ($region) { echo "&region";} ?>">Newest</a> | Popularity
    	                    <?php
    	                        } elseif ($order == "newest"){
    	                   ?>         
    	                        Newest | <a href="http://<?php echo $host; ?>/?orderby=popularity&page=<?php echo $page; if ($region) { echo "&region";} ?>">Popularity</a>
    	                    <?php
    	                        }
    	                    ?>
    	                    | <a onclick="showPost();">Write a new post</a>
    	                    <hr/>
    	                    
    	                    <div id="post-form">
                                <h3>Write a post<?php 
                                    if ($region){
                                        echo " to " . $_SESSION['region_name'];
                                    }
                                ?>:</h3>
                                <script>
                                    function check(form){
                                        var text = document.getElementById("postformtext").value;
                                        var title = document.getElementById("postformtitle").value;
                                        
                                        if (text.trim() == "" || title.trim() == ""){
                                            alert("Please fill in the title and the body of the post.");
                                            return false;
                                        } else {
                                            return true;
                                        }
                                    }
                                </script>
                                <form method="POST" onsubmit="return check(this);" action="http://<?php echo $host; ?>/f/post?post">
                                    <b>Type: </b>
                                    <?php
                                        if (is_admin()){
                                    ?>
                                        <input type="text" name="admintype"/>
                                    <?php
                                        }
                                    ?>
                                    <select name="type">
                                        <option value="0">Event</option>
                                        <option value="1">Meta</option>
                                        <option value="2">Claim</option>
                                        <option value="3">News</option>
                                        <option value="4">Lore</option>
                                        <option value="5">Conflict-Commit</option>
                                        <option value="6">Conflict-Rally</option>
                                        <option value="7">Conflict-Surprise</option>
                                        <option value="8">Conflict-Score</option>
                                    </select>
                                    <br/>
                                    <input id="postformtitle" type="text" autocomplete="off" name="title" placeholder="What is your title?"/>
                                    <br/>
                                    <textarea id="postformtext" autocomplete="off" name="body" onkeyup="textAreaAdjust(this);" placeholder="Type your post here.."></textarea>
                                    <input type="hidden" name="r" value="<?php
                                        if ($region){
                                            echo $_SESSION['hold_region'];
                                        } else {
                                            echo "global";
                                        }
                                    ?>"/> 
                                    <br/>
                                    <?php
                                        if (is_admin() || is_mod()){
                                    ?>
                                        <b>Stickied: </b><input name="stickied" value = "1" type="checkbox"/>
                                    <?php
                                        }
                                    ?>
                                    <br/>
                                    <input type="submit" value="Post Entry"/> <a onclick="hidePost();">Cancel</a>
                                </form>
                            </div>
    	                    
    	                </div>
    	                
    	                
    	                <?php
                             // this is where the messages go
                             
                             if (isset($_GET['success'])){
                         ?>
                            <div id="outer-message">
                                <div id="pop-message">
                                    <b style="font-size:18px;">Welcome!</b>
                                    <hr/>
                                    Welcome to WesterosPowers. Your claim request has been posted. Please wait until one of the mods responds to your request. Until then please have a look around.
                                </div>
                            </div>
                        <?php
                             } elseif (isset($_SESSION['message'])){
                        ?>
                            <div id="outer-message">
                                <div id="pop-message">
                        <?php
                                if (isset($_SESSION['message_title'])){
                        ?>
                                    <b style="font-size:18px;"><?php echo $_SESSION['message_title']; ?></b>
                                    <hr/>
                        <?php
                                }
                        ?>
                                    <?php echo $_SESSION['message']; ?>
                                </div>
                            </div>
                        <?php
                            unset($_SESSION['message']);
                            unset($_SESSION['message_title']);
                             }
                         
                         ?>
    	                
    	                
    	                <!-- POSTS -->
                        
                        <?php
                            include_once("utilities.php");
                            
                            $con = new dbConnect;
                            $con->connect();
                            
                            // find the number of rows in the posts table
                            //$post_rows = $con->exec_query("SELECT * FROM `post` WHERE `region_id` = 0")->num_rows;
                            $num = $page*$num_post-$num_post;
                            
                            $var = "timestamp";
                            if ($order == "popularity"){
                                $var = "views";
                            }
                            
                            $region_id = 0;
                            if ($region){
                                $region_id = $_SESSION['hold_region'];
                            }
                            
                            $results = $con->exec_query("SELECT * FROM `post` WHERE `region_id` = " . $region_id . " ORDER BY `stickied` DESC, `" . $var . "` DESC  LIMIT " . $num . ", " . $num_post);
                            
                            if ($results->num_rows == 0){
                                echo "<p style='margin-left:20px;color:#e33;'>There are no posts to display. If you make a post it will apear here</p>";
                            } else {
                            
                            
                                while ($row = mysqli_fetch_assoc($results)){
                        ?>
                        
                        <div id="post" class="<?php echo "post" . $row['post_id'] . (($row['stickied'] == 1) ? " stickied" : "");  ?>">
                            <span id="type">[<?php echo $row['type']; ?>]</span> <a id="ptitle" href="http://<?php echo $host . "/?view=" . $row['post_id']; ?>"><?php echo $row['title']; ?></a><br/>
                            <span>
                            <?php
                                $time = user_time($row['timestamp'],"d \of M Y \a\\t H:i:s"); //(strtotime($row['timestamp']));
                                echo "Event occured " . $time; //- time();
                                echo " by ";
                                
                                $temp_results = $con->exec_query("SELECT `title`, `house_id`, `user_id` FROM `user` WHERE `user_id`='" . $row['user_id'] . "' LIMIT 1");
                                
                                $temp_row = mysqli_fetch_assoc($temp_results);
                                
                                $temp = $con->exec_query("SELECT * FROM `house` WHERE `house_id` = '" . $temp_row['house_id'] . "'");

                                $temp_rows = mysqli_fetch_assoc($temp);
                                
                                echo " <a href='http://" . $host . "/?user=" . $temp_row['user_id'] . "'>" . $temp_row['title'] . " " . $temp_rows['house_name'] . "</a>";
                                
                                $comment = $con->exec_query("SELECT * FROM `comment` WHERE `post_id` = '" . $row['post_id'] . "'");
                                
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
                            }
                        
                        ?>
                        
                        <?php
                            $results = $con->exec_query("SELECT * FROM `post` WHERE `region_id` = " . $region_id);
                            $pages = floor($results->num_rows/$num_post+1);
                            
                            if ($pages != 1 && $pages != 0){
                        ?>
    	                <hr/>
        	            <div id="bottom" style="text-align:center;">
        	                <?php
        	                    if ($page-1 != 0){
        	                ?>
                	                <a href="http://<?php 
                	                
                	                echo $host . "/?";
                	                
                	                if (isset($_GET['region'])){
                	                    echo "region&";
                	                }
                	                
                	                echo "orderby=" . $order . "&";
                	                
                	                // get the page number:
                	                $pa = $page-1;
                	                
                	                echo "page=" . $pa;
                	                
                	                
                	                ?>">Previous</a>
        	                <?php 
        	                    } 
        	                ?> /
        	                
        	                <?php
        	                    if ($page != $pages){
        	                ?>
                	                
                	                <a href="http://<?php 
                	                
                	                echo $host . "/?";
                                    
                                    if (isset($_GET['region'])){
                                        echo "region&";
                                    }
                                    
                                    echo "orderby=" . $order . "&";
                                    
                                    // get the page number:
                                    $pa = $page+1;
                                    
                                    echo "page=" . $pa;
                	                
                	                ?>">Next</a>
        	                <?php
        	                    }
        	                ?>
    	                </div>
    	                <?php
                            }
                            $con->close();
    	                }
    	                ?>
            		</div>
            		
        		</div>
    		    <?php
    		        include("footer.php");
    		        get_footer(false);
    		    ?>
    		
	    </div>
	</body>
	
	<?php
		include("includes/no_js.php");
	?>
	
</html>
