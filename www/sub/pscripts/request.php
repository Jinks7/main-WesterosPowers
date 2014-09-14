<?php

// this is the login script and signup script
// it is just a library of functions that are used to interact with the database

include("db.php");
include("/sub/utilities.php");


// function for the validation login
function login($username, $password){
	
	$con = new dbConnect;
	$con->connect(); // connect to database
	
	// hash the password and escape the $username
	$username = $con->input($username);
	$pass = create_hash($password);
	
	// now using execute query 
	$results = $con->exec_query("SELECT * FROM `user` WHERE `email` = '". $username ."' AND `password` = '" . $pass . "'");
	
	// store some of the returned information into session variables for easy look up	
	if ($results->num_rows == 1){
		// there has been a match
		
		$row = mysqli_fetch_assoc($results);
		
		$_SESSION['userid'] = $row['user_id'];
		$_SESSION['rpname'] = $row['rpname'];
		
		$temp_results = $con->exec_query("SELECT * FROM `house` WHERE `house_id` = '" . $row['house_id'] . "' LIMIT 1");
		$temp_rows = mysqli_fetch_assoc($temp_results);
		
		$_SESSION['hname'] = $temp_rows['house_name'];
		$_SESSION['email'] = $row['email'];
		$_SESSION['level'] = $row['level'];
		$_SESSION['registered'] = $row['registered'];
		$_SESSION['fname'] = $row['first_name'];
		$_SESSION['lname'] = $row['last_name'];
		$_SESSION['timezone'] = $row['time_zone'];
		$_SESSION['holdfast'] = $row['holdfast_id'];
		$_SESSION['title'] = $row['title'];
		
		if ($row['registered'] == 0){
            return false;
        }
		
		// set the log in to true
		$_SESSION['login'] = true;
		
		if ($_SESSION['holdfast'] == ''){
		    
		    $_SESSION['holdfast'] = -500;
		    
		}
		$con->close();
		
		return true;
	} else {
	    $con->close();
		return false;
	}
	// this will return true or false if it has been successful
	$con->close();
	return false;
}

// method for signing up
function signup(){

	$con = new dbConnect;
	$con->connect(); // connect to database
	

	// have the vars for the user's signup
	// and make sure to sanatize them
	$email = $con->input(trim($_POST['email']));
	$fname = $con->input(trim(ucfirst($_POST['fname'])));
	$lname = $con->input(trim(ucfirst($_POST['lname'])));
	$pass = create_hash(trim($_POST['pword']));
	$rpass = create_hash(trim($_POST['rpword']));
	$gen = $con->input(trim($_POST['gen']));
	$rpname = $con->input(trim(ucfirst($_POST['rpname'])));
	$hname = $con->input(trim($_POST['hname']));
	$time = $con->input($_POST['timezone']);
	$timestamp = time();
	
	if ($gen == "m"){
	    $gen = "Lord";
	} else if ($gen == "f"){
	    $gen = "Lady";
	}
	
	
	// check if the passwords match
	if ($pass != $rpass){
		$_SESSION['serror'] = "The passwords do not match.";
		return false;
	} elseif ($con->exec_query("SELECT * FROM `user` WHERE `email`='" . $email . "'")->num_rows >= 1){
	    $_SESSION['serror'] = "This email has already been used.";
	    return false;
	}
	
	$results = mysqli_fetch_assoc($con->exec_query("SELECT * FROM `house` WHERE `house_id`='" . $hname . "'"));
    
    if ($results['taken'] == 1){
        $_SESSION['serror'] = "This house has already been taken, please choose again";
        return false;
    } elseif ($hname == ""){
        $_SESSION['serror'] = "There are no more available houses!<br/>Email the mods westerospowers@gmail.com and we'll accomodate";
        return false;
    } else {
        $_SESSION['house_id'] = $hname;
        $_SESSION['house_name'] = $results['house_name'];
    }
    
	
	$query = 'INSERT INTO `user` (`rpname`, `house_id`, `email`, `password`, `first_name`, `last_name`, `time_zone`, `title`)
				VALUES ("'. $rpname . '", "' . $hname . '", "' . $email . '", "' . $pass . '", "' . $fname . '", "' . $lname . '", "' . $time . '", "' . $gen . '")';
	
	//$_SESSION['serror'] = $query;
	//	return false;
	
	// execute query
	$result = $con->exec_query($query);
	$result = true;
	// close the database
	$con->close();
	
	// -1 signifies an error
	if ($result == false){
		$_SESSION['serror'] = "Something unexpected happened, Please try again later.";
		return false;
	} else {
		// add session variables
		
		$con = new dbConnect;
        $con->connect();
		
		// this shows that the user has just signed up
		$_SESSION['signed'] = true; 
		
		$_SESSION['email'] = $email;
		$_SESSION['fname'] = $fname;
		$_SESSION['lname'] = $lname;
		$_SESSION['title'] = $gen;
		$_SESSION['registered'] = 0;
		$_SESSION['level'] = 3;
		$_SESSION['rpname'] = $rpname;
		$_SESSION['timezone'] = $time;
		
		$temp_results = $con->exec_query("SELECT * FROM `house` WHERE `house_name` = '" . $hname . "' LIMIT 1");
        $temp_rows = mysqli_fetch_assoc($temp_results);
        
        $_SESSION['hname'] = $temp_rows['house_name'];
		$_SESSION['houseid'] = $temp_rows['house_id'];
		
		// this does not contain the users id
		// maybe another query should be run to find the users id
		// i need that user id for the confirm.php

		
		$results = mysqli_fetch_assoc($con->exec_query("SELECT user_id FROM `user` WHERE `email`='" . $_SESSION['email'] . "'"));
		
		$_SESSION['userid'] = $results['user_id'];
		
		// mark the house as taken
		$con->exec_query("UPDATE `house` SET `taken`='1' WHERE `house_id`='" . $_SESSION['house_id'] . "'");
		
		$con->close();
	
	    try {
	        //error_reporting(0);
    	    //$exec = "python C:\\WesterosPowers\\bots\\send_email.py " . $_SESSION['email'] . " email \"WesterosPowers Confirmation\" " . $_SESSION['fname'] . " " . ($_SESSION['title'] == 'm' ? 'Lord' : 'Lady') . " " . $_SESSION['house_name'] . " \"http://" . gethostname() . "/f/confirm?imp=" . rand(1000, 9999999) . "&user=" . $_SESSION['userid'] . "&email=" . $_SESSION['email'] ."\"";
    	    //exec($exec);
    	    throw new Exception("Emails don't currently work."); 
    	    /*
    	        For some reason the school internet
    	        does not let emails thorugh for 
    	        whatever strange reason.
    	    */
	    } catch (Exception $e){
	        include("/sub/config.php");
	        $_SESSION['serror'] = "Sorry there was an error sending you an email. To alleviate this problem we have done the liberty of registering the account for you. Now you just need to <a style='color:black;' href='http://" . $host . "/f/confirm?imp=" . rand(1000, 9999999) . "&user=" . $_SESSION['userid'] . "&email=" . $_SESSION['email'] ."&shell=". md5($_SESSION['email']) ."'>Confirm</a>";
	        
	        // update the db to say that they are registered.
	        //$con = new dbConnect;
	        //$con->connect();
	        
	        //$con->exec_query("UPDATE `user` SET `registered` = '1' WHERE `userid`='" . $_SESSION['userid'] . "'");
	        
	        return false;
	    }
	    
		return true;
	}

}