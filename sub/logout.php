<?php

if (isset($_GET['banned'])){
    session_start();
    session_destroy();
    
    header("LOCATION: ../?banned");
} else {
    session_start();
    session_destroy();
    
    header("LOCATION: ..");   
}

?>