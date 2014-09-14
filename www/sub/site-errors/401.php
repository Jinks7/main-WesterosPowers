<!DOCTYPE html>
<html>
	<head>
		<title>401 Unauthorized - WesterosPowers</title>
		<link rel="stylesheet" href="http://<?php include("../../sub/config.php"); echo $host; ?>/s/style/main.css"/>
            <style>
                #wrapper {
                    display:none;
                } 
                p {
                    margin:50px auto;
                    text-align:center;
                    font-size:20px;
                    color:red;
                }
                #hide {
                    background:white;
                }
            </style>
            
        </head>
        
        <body>
            <?php
                include_once("../../sub/utilities.php");
                
                if (check_login()){
                    include("../../sub/header.php");
                }
                
                
            ?>
		<p>
			<img src="http://<?php echo gethostname(); ?>/s/imgs/wp_banner_black.png" width="70%"><br/>
			<b>401 Unauthorized: </b> You are unauthorized to access this page.
		</p>
	</body>
</html>