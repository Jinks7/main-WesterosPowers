<?php

// change it depending on whether they are logged in
// the level of their account
include_once("utilities.php");


include("config.php");

if (check_login()){
    update_info();
?>
<script>

// event for when the browser window resizes 


</script>
<div id="header">
    <div id="center">
        <div id="headerleft">
            <a href="http://<?php echo $host; ?>/"><img src="http://<?php echo $host; ?>/s/imgs/WP_side.png" /></a>
        </div>
        <span id="fill">|</span>
        <div id="search">
            <form method="GET" action="http://<?php echo $host; ?>/">
            <input type="hidden" name="page" value="search"/>
            <input type="text" placeholder="Search.." name="s" autocomplete="off"/>
            </form>
        </div>
        <div id="headright">
            <?php
                
            ?>
            <a href="http://<?php echo $host . "/?user=" . $_SESSION['userid']; ?>"><?php echo $_SESSION['title'] . " " . $_SESSION['hname']; ?></a> |
            <a href="http://<?php echo $host; ?>">Home</a> | 
            <?php 
                // find how many messages are for the user
                function get_messages(){
                    
                    if (isset($_GET['message'])){
                        return "";
                    }
                    
                    $num = 0;
                    
                    $con = new dbConnect;
                    $con->connect();
                    
                    $result = $con->exec_query("SELECT * FROM `conversation` WHERE (`user_one` = '" . $_SESSION['userid'] . "' OR `user_two` = '" . $_SESSION['userid'] . "')");
                    
                    // find if the conversation has an unread message
                    while ($row = mysqli_fetch_assoc($result)){
                        $num += $con->exec_query("SELECT * FROM `message` WHERE (`conversation_id` = '" . $row['conversation_id'] . "' AND `is_read` = 0 AND `user_id` <> '" . $_SESSION['userid'] . "') ORDER BY `timestamp` DESC LIMIT 1")->num_rows;
                    }
                    
                    $con->close();
                    
                    if ($num == 0){
                        return "";
                    } else {
                        return " (" . $num . ")";
                    }
                }
            ?>
            <a href="http://<?php echo $host . "?message"; ?>">Messages<?php echo get_messages();?></a> | 
            <a href="http://<?php echo $host . "?region"; ?>">Region</a> | 
            <a href="http://<?php echo $host; ?>/f/logout">Logout</a>
        </div>
    </div>
</div>


<?php    
} else {
?>

<div id="header">
    <div id="headerleft">
        <a href="http://<?php echo $host ?>/"><img src="http://<?php echo $host ?>/s/imgs/WP_side.png" /></a>
    </div>
    <span id="fill">|</span>
    <div id="stdright">
        <a href="http://<?php include("config.php"); echo $host; ?>">Login</a> | 
        <a href="http://<?php include("config.php"); echo $host; ?>/signup/">Signup</a>
    </div>
</div>

<?php
}

?>
