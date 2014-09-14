<?php

/******************************************************************
*                                                                 *
*            MAIN UTILITIES FILE FOR STANDARD FUNCTIONS            *
*                                                                 *
******************************************************************/

include_once("pscripts/db.php");
include_once("config.php");


function check_login(){
    // start the session if not already started
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
    
    if (isset($_SESSION['login']) && $_SESSION['login'] == true){
        return true;
    } else {
        return false;   
    }
}

function is_mod(){
    if (check_login()){
        $user = new User;
        if ($user->get("level") == 1){
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function is_admin(){
    if (check_login()){
        $user = new User;
        if ($user->get("level") == 0){
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function heightened(){
    if (check_login()){
        $user = new User;
        if ($user->get("level") == 2){
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function is_standard(){
    if (check_login()){
        $user = new User;
        if ($user->get("level") == 3){
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function update_info(){
    
    // update all the info from the database
    
    $con = new dbConnect;
    $con->connect();
    
    $result = ($con->exec_query("SELECT * FROM `user` WHERE `user_id`='" . $_SESSION['userid'] . "'"));
    
    if ($result->num_rows < 1){
        header("LOCATION: ../f/logout");
    }
    
    $result = mysqli_fetch_assoc($result);
    
    $_SESSION['email'] = $result['email'];
    $_SESSION['fname'] = $result['first_name'];
    $_SESSION['lname'] = $result['last_name'];
    $_SESSION['title'] = $result['title'];
    $_SESSION['level'] = $result['level'];
    $_SESSION['rpname'] = $result['rpname'];
    $_SESSION['timezone'] = $result['timezone'];
    
    update_holdfast();
    
    // then check if the user has been banned
    if (is_banned()){
        header("LOCATION: /f/logout?banned");
    }
    
    $con->close();
    
}

function update_holdfast(){
    // update all the information about the holdfast
    
    $con = new dbConnect;
    $con->connect();
    
    $result = $con->exec_query("SELECT * FROM `holdfast` WHERE `user_id` = '" . $_SESSION['userid'] . "'");
    
    if ($result->num_rows < 1){
        // the user does not have a holdfast :(
        $_SESSION['message_title'] = "No Holdfast";
        $_SESSION['message'] = "You do not have a holdfast, please claim one. You will have limited things you can do until you get one.";
        
        unset($_SESSION['hold_region']);
        
    } else {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['hold_name'] = $row['hname'];
        $_SESSION['hold_id'] = $row['hold_id'];
        $_SESSION['hold_region'] = $row['region_id'];
        $_SESSION['money'] = $row['money'];
        
        // next get the region
        $res = mysqli_fetch_assoc($con->exec_query("SELECT * FROM `region` WHERE `region_id` = '" . $_SESSION['hold_region'] . "'"));
        
        $_SESSION['region_name'] = $res['region_name'];
        
    }
    
    $con->close();
    // if the user does not have a holdfast
    // set the session variable to false;
    
}

function is_banned(){
    // check if the user is banned
    // this should be checked on each page
    $con = new dbConnect;
    $con->connect();
    
    $query = "SELECT * FROM `banned` WHERE `user_id` = \"" . $_SESSION["userid"] . "\"";
    
    $result = $con->exec_query($query);
    
    if ($result->num_rows == 1){
        // the user has been banned
        // but before we return false,
        // lets check the date
        $row = mysqli_fetch_assoc($result);
        
        if (time() > $row['time']){
            // delete the row from the table
            $con->exec_query("DELETE FROM `banned` WHERE `user_id` = '" . $user->get("userid") . "' LIMIT 1");
            
            $con->close();
            return false;
        } else {
            $con->close();
            return true;
        }
        
    } else {
        $con->close();
        return false;
    }
    
    
}

function parse_xss($string){
    return htmlspecialchars($string);
}

function make_safe($string){
    $string = strip_html_tags($string);
    $string = mysql_real_escape_string(trim($string));
    return $string;
}

function execInBackground($cmd) {
    if (substr(php_uname(), 0, 7) == "Windows"){
        pclose(popen("start /B ". $cmd, "r")); 
    }
    else {
        exec($cmd . " > /dev/null &");  
    }
}

function get_date($date, $timezone, $format = "d-m-Y H:i"){
    //$offset=4*60*60; //converting 4 hours to seconds.
    //$dateFormat="d-m-Y H:i"; //set the date format
    //$timeNdate=gmdate($dateFormat, time()-$offset); //get GMT date - 4

    return -1;
    
}

function user_time($timestamp, $format = "m/d/Y h:i:s a"){
    // works for all timezones include DST
    $user = new User;
    
    date_default_timezone_set($user->get("timezone"));
    
    return date($format, strtotime($timestamp));
    
}

function time_from($timestamp){
    $time_now = time();
    
    $diff = $time_now - $timestamp;
    
    return date("H:i:s", $diff);
    
    $years   = round($diff / (365*60*60*24)); 
    $months  = round(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
    $days    = round(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
    $hours   = round(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 
    $minutes  = round(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60); 
    $seconds = round(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60)); 
    
    if ($years != 0 && !($years < 0)){
        $text .= ($years == 1 ? "about a year ago" : "about " . $years . " years ago");
    } elseif ($months != 0 && !($months < 0)){
        $text .= ($months == 1 ? "about a month ago" : "about " . $months . " months ago");
    } elseif ($days != 0 && !($days < 0)){
        $text .= ($days == 1 ? "about a day ago" : "about " . $days . " days ago");
    } elseif ($hours != 0 && !($hours < 0)){
        $text .= ($hours == 1 ? "about an hour ago" : "about " . $hours . " hours ago");
    } elseif ($minutes != 0 && !($minutes < 0)){
        $text .= ($minutes == 1 ? "about a minute ago" : "about " . $minutes . " minutes ago");
    } else {
        $text .= "under a minute ago";
    }
    
    //return $text;
    
    printf("%d years, %d months, %d days, %d hours, %d minutes, %d seconds\n", $years, $months, $days, $hours, $minutes, $seconds);
    
}




// USER CLASS
// keeps all the session variables centralized
class User {
    
    /*
    LIST OF ALL SESSION VARS THAT ARE USED FOR THE USER
    
    1. login
    2. registered
    3. rpname
    4. level
    5. userid
    6. houseid
    7. email
    8. fname
    9. lname
    10. holdfast
    
    // soon to be
    region
    
    */
    
    function __construct(){
        
    }
    
    function __destruct(){
        
    }
    
    function add($name, $value){
        $_SESSION[$name] = $value;
    }
    
    function remove($name){
        unset($_SESSION[$name]);
    }
    
    function get($name){
        if (isset($_SESSION[$name])){
            return $_SESSION[$name];
        } else {
            return null;
        }
    }
    
    function getAll(){
        $results;
        foreach($_SESSION as $key=>$val){
            $results[$key] = $val;
        }
        return $results;
    }
    
}



//standard static defines
// NOTE: THIS IS INSECURE, for it to be secure the salts would need to change for every user
define("SALT_S", "K2UZXysT5xAfsfhUfuacmQ8d");
define("SALT_E", "nKhRmpBzGnAPctLEhNEnMRJ8");
define("HASH_ALGORITHM", "sha256");

function create_hash($pass, $hash = HASH_ALGORITHM){
    return hash($hash, SALT_S . $pass . SALT_E);    
}

?>