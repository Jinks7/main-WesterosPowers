<?php

include("utilities.php");

if (check_login()){
    if (isset($_GET['send'])){
        // if the user wants to send a message
        
        $con = new dbConnect;
        $con->connect();
        
        if (!isset($_POST['message'])){
            echo "";
        } else {
            
            $convoid = $con->input(trim($_GET['send']));
            $message = $con->input(trim($_POST['message']));
            
            // check if the conversation is theirs
            $temp = $con->exec_query("SELECT * FROM `conversation` WHERE (`user_one` = '" . $_SESSION['userid'] . "' OR `user_two` = '" . $_SESSION['userid'] . "') AND `conversation_id` = '" . $convoid . "'");
            if ($temp->num_rows < 1){
                // there is no conversation
                echo "<li class='message-message'>There was an error sending the message.</li>";
            } else {
                
                // submit the message to the database
                $con->exec_query("INSERT INTO `message` (`conversation_id`, `user_id`, `message`) VALUES ('" . $convoid . "', '" . $_SESSION['userid'] . "', '" . $message . "')");
                
                $time =  time();
                $con->exec_query("UPDATE `conversation` SET `lasttime` = '" . $time . "' WHERE `conversation_id` = '" . $convoid . "'");
                
            }
        }
        
        $con->close();
        
    } elseif (isset($_GET['update'])){
        // see if there are any new messages
        
        $con = new dbConnect;
        $con->connect();
        
        $convoid = $con->input(trim($_GET['update']));
        
        // check if the conversation is theirs
        $temp = $con->exec_query("SELECT * FROM `conversation` WHERE (`user_one` = '" . $_SESSION['userid'] . "' OR `user_two` = '" . $_SESSION['userid'] . "') AND `conversation_id` = '" . $convoid . "'");
        if ($temp->num_rows < 1){
            // there is no conversation
            echo "<li class='message-message'>This conversation does not exist</li>";
        } else {
            
            // now get the messages
            $result = $con->exec_query("SELECT * FROM `message` WHERE `conversation_id` = '" . $convoid . "' LIMIT 50");
                
                if ($result->num_rows < 1){
                    echo '<div class="message-message">You have no messages with this person. Start a conversation to see them appear here.</div>';
                } else {
                
                    // display these messages
                    while ($row = mysqli_fetch_assoc($result)){
            ?>    
                <div class="message-message<?php 
                    if ($row['user_id'] == $_SESSION['userid']){
                        echo " right";
                    }
                ?>">
                <?php 
                    echo $row['message']; 
                    
                    // set it too being read if it hasnt already
                    if ($row['is_read'] == 0 && $row['user_id'] != $_SESSION['userid']){
                        $con->exec_query("UPDATE `message` SET `is_read` = '1' WHERE `message_id` = '" . $row['message_id'] . "'");
                    }
                    
                ?>
                
                </div>
            <?php      
                    }
                } 
            
        }
        
        $con->close();
        
    } elseif (isset($_GET['getconvo'])){
        // get the conversation between two users
        // if they have no conversation then say so
        
        $con = new dbConnect;
        $con->connect();
        
        $convoid = $con->input(trim($_GET['getconvo']));
        
        // check if the conversation is theirs
        $temp = $con->exec_query("SELECT * FROM `conversation` WHERE (`user_one` = '" . $_SESSION['userid'] . "' OR `user_two` = '" . $_SESSION['userid'] . "') AND `conversation_id` = '" . $convoid . "'");
        if ($temp->num_rows < 1){
            // there is no conversation
            echo "<li class='message-message'>This conversation does not exist</li>";
        } else {
            // now get the messages
            $result = $con->exec_query("SELECT * FROM `message` WHERE `conversation_id` = '" . $convoid . "' LIMIT 50");
                
                if ($result->num_rows < 1){
                    echo '<div class="message-message">You have no messages with this person. Start a conversation to see them appear here.</div>';
                } else {
                
                    // display these messages
                    while ($row = mysqli_fetch_assoc($result)){
            ?>    
                <div class="message-message<?php 
                    if ($row['user_id'] == $_SESSION['userid']){
                        echo " right";
                    }
                ?>">
                <?php 
                    echo $row['message']; 
                    
                    // set it too being read if it hasnt already
                    if ($row['is_read'] == 0 && $row['user_id'] != $_SESSION['userid']){
                        $con->exec_query("UPDATE `message` SET `is_read` = '1' WHERE `message_id` = '" . $row['message_id'] . "'");
                    }
                    
                ?>
                
                </div>
            <?php      
                    }
                } 
        }
        
        
        $con->close();
        
    } elseif (isset($_GET['createconvo'])) {
        
        $con = new dbConnect;
        $con->connect();
        
        // get the user id from the house name
        if ($_GET['createconvo'] != ""){
            // check if the conversation doesnt already exist
            $name = $con->input(trim($_GET['createconvo']));
            
            
            
            $userinfo = $con->exec_query("SELECT u.title, u.user_id, h.house_id, h.house_name FROM user u, house h WHERE h.house_name = '" . $name . "' AND u.house_id=h.house_id");
            if ($userinfo->num_rows > 0){
                // check if the conversation has already been created
                $userinfo = mysqli_fetch_assoc($userinfo);
                
                if ($userinfo['user_id'] == $_SESSION['userid']){
                    echo "error";
                    die();
                }
                
                $result = $con->exec_query("SELECT * FROM `conversation` WHERE (`user_one` = '" . $_SESSION['userid'] . "' OR `user_two` = '" . $_SESSION['userid'] . "') ");
                while ($row = mysqli_fetch_assoc($result)){
                    if ($row['user_one'] == $userinfo['user_id'] || $row['user_two'] == $userinfo['user_id']){
                        echo "error1";
                        die();
                    }
                }
                
                // next add the conversation to the table
                $con->exec_query("INSERT INTO `conversation` (`user_one`, `user_two`, `lasttime`) VALUES ('" . $_SESSION['userid'] . "', '" . $userinfo['user_id'] . "', '" . time()  . "')");
                
                echo $con->get_insert_id() . ":" . $userinfo['title'] . " " . $userinfo['house_name']; 
                
            } else {
                echo "error";
            }
            
        } else {
            echo "error";
        }
        
        
        $con->close();
        
    } else {
        
    }
}

?>