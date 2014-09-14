<?php
// this file just serves as the main file for the website

session_start();

// find if the user is logged in

if (isset($_SESSION['registered']) && $_SESSION['registered'] == 0){
    unset($_SESSION['login']);
}

if (!isset($_GET['page'])){
    $_GET['page'] = "";
}
// all the pages the user is allowed to view while not logged in
if (!isset($_SESSION['login'])){
	if ($_GET['page'] == "signup")
		include("sub/signup.php");
	else
		include("sub/login.php");
		
		
} else { // all the pages they can access logged in
    
    // update the session variables
    // and check if the are banned
    // or the user's information changed
    include_once("/sub/utilities.php");
    include_once("/sub/config.php");
    
    
    
    if (strtolower($_GET['page']) == "features"){
        include("sub/feature.php");
        get_features();
    } elseif (strtolower($_GET['page']) == "bugs"){
        include("sub/feature.php");  
        get_bugs();
    } elseif (strtolower($_GET['page']) == "search"){ 
        include("sub/search.php");
    } else {
        // if the standard page is requested
        include("sub/home.php");
    }
}


?>

