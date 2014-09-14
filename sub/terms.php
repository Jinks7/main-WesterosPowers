<?php

/*
	Basic stuff
	It could be better but i figured it was only the terms and privacy conditions
	not many people will bother too much in looking i gather...
	improvements could be made in the future to make it look better
*/

include("config.php");

if (isset($_GET['privacy'])){
    
?>
<html>
    <head>
        <title>Privacy Policy - WesterosPowers</title>
        <link rel="stylesheet" href="/s/style/main.css">
    </head>
    <body style="overflow:hidden;">
    <?php 
	    include_once("header.php");
	?>
	<div id="content">
        <div style="width:600px;height:400px;border:1px solid black;overflow:auto;margin:50px 20px;margin-bottom:0px;padding:5px">
        	<?php echo read_file("pscripts/privacy.html"); ?>
        </div>
        <a href="http://<?php echo $host; ?>/" style="margin-left:20px;">Back</a>
    </div>

<?php
	include("footer.php");
    get_footer(true);
?>

    </body>
</html>
	
<?php
	
} elseif(isset($_GET['terms'])){
	// include header, so users can stay logged in and see their info
	
	?>
    <html>
        <head>
            <title>Terms of Service - WesterosPowers</title>
            <link rel="stylesheet" href="/s/style/main.css">
        </head>
        <body style="overflow:hidden;">
        <?php 
            include_once("header.php");
        ?>
        <div id="content">
            <div style="width:600px;height:400px;border:1px solid black;overflow:auto;margin:50px 20px;margin-bottom:0px;padding:5px">
                <?php echo read_file("pscripts/terms.html"); ?>
            </div>
            <a href="http://<?php echo $host; ?>" style="margin-left:20px;">Back</a>
        </div>
    <?php
        include("footer.php");
        get_footer(true);
    ?>
    
        </body>
    </html>
        
    <?php
	
} else {
	include("./site-errors/404.php");
	
}


function read_file($name){
	$file = fopen($name, "r") or die("An error occured"); // general response as to stop users from understanding that  text files are being used
	$text = fread($file,filesize($name));
	fclose($file);
	return $text;
}


?>