<?php

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (isset($_SESSION['signed']) && $_SESSION['signed'] == true) {
    // lets send the email
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Success! - WesterosPowers</title>
		<link rel="stylesheet" href="http://<?php include_once("config.php"); echo $host; ?>/s/style/main.css" />
	</head>
	
	<body>
	    <div id="wrapper" style="height:200px;">
            <?php
            include("header.php");
            ?>
            <div id="content">
                <!-- This is the main content div -->
                <p>
                    Thank you for signing up with WesterosPowers. We have just sent
                    you a confirmation email, to continue using the site please 
                    go to the link found in the email.
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
	include("site-errors/404.php");
}

?>