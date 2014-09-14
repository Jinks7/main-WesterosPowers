<?php
// this is the file that the user uses to confirm

include("config.php");

if (isset($_GET['email']) && isset($_GET['user']) && isset($_GET['shell'])){
    
    include_once("pscripts/db.php");
    ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Confirmation - WesterosPowers</title>
        <link rel="stylesheet" href="http://<?php include("config.php"); echo $host; ?>/s/style/main.css" />
    </head>
    <body>
        
        <div id="wrapper" style="height:200px;">
            <?php include("header.php"); ?>
        
            <div id="content">
                <p>
                    
                    <?php
                        $con = new dbConnect;
                        $con->connect();
                        if (md5($_GET['email']) == $_GET['shell']){
                            $result = $con->exec_query("SELECT * FROM `user` WHERE `email`='" . $con->input($_GET['email']) . "' AND `user_id`='" . $con->input($_GET['user']) . "' AND `registered` <> 1");
                            $temp = mysqli_fetch_assoc($result);
                            
                            if ($temp['registered'] == "0" || $result->num_rows != 0){
                                $result = $con->exec_query("UPDATE `user` SET `registered`='1' WHERE `email`='" . $con->input($_GET['email']) . "' AND `user_id`='" . $con->input($_GET['user']) . "'");
                                
                                
                                session_start();
                                session_destroy();
                                
                                
                                // set a cookie that doesnt expire anytime soon
                                // tells the login page that its the first time 
                                // the user has logged in
                                setcookie("first", "true", time()+3600*48*26, "/");
                    ?>        
                        You have successfully confirmed your email and are now allowed 
                        to access the site. <a href="http://<?php echo $host; ?>">Click here to login.</a>
                    <?php      
                            } else {
                                echo "This account has already been registered or this account does not exist.";
                            }
                        } else {
                            echo "An error occured :(";
                        }
                        
                        $con->close();
                        
                    ?>
                
                </p>
            </div>
            
            <?php 
                include("footer.php");
                get_footer(true);
            ?>
            
        </div>
        
    </body>
</html>
    <?php
} else {
    include_once("utilities.php");
    include("site-errors/404.php");
}

?>