<?php

// this is the page to buy items

// it checks if the user is logged in
include_once("utilities.php");
if (check_login()){
    
    if (isset($_POST['amount']) && isset($_POST['id'])){
        
        // create a connection to the database
        $con = new dbConnect;
        $con->connect();
        
        $amount = abs((int)$con->input(trim($_POST['amount'])));
        $itemid = (int)$con->input(trim($_POST['id']));
        
        // check if the user has enough money..
        $result = mysqli_fetch_assoc($con->exec_query("SELECT * FROM `item` WHERE `item_id` = '" . $itemid . "'"));
        
        $cost = ((int)$result['cost'] * $amount);
        
        if ($cost > $_SESSION['money']){
            echo ":You do not have enough money.";
        } else {
            
            $query = "";
            
            // create the query
            for ($i=0;$i<$amount;$i++){
                $query .= "('" . $itemid . "','" . $_SESSION['hold_id'] . "')";
                if ($i < $amount-1){
                    $query .= ", ";
                }
            }
            
            // then enter the values into the database
            $con->exec_query("INSERT INTO `item_owned` (`item_id`, `house_id`) VALUES " . $query);
            
            // and take the money away from the house id
            $money = $_SESSION['money'] - $cost;
            $con->exec_query("UPDATE `holdfast` SET `money` = '" . $money . "' WHERE `hold_id` = '" . $_SESSION['hold_id'] . "'");
            
            // get the new amount of money and return it
            $return = mysqli_fetch_assoc($con->exec_query("SELECT `money` FROM `holdfast` WHERE `hold_id` = '" . $_SESSION['hold_id'] . "'"));
            $_SESSION['money'] = $return['money'];
            echo $return['money'] . ":Successfully bought item";
            
        }
        $con->close();
    } else {
        echo ":There was an error buying the item.";
    }
} else {
    include("site-errors/404.php");
}

?>