<?php

function get_message(){
?>
<h2 style="margin-left:5px;margin-bottom:5px;font-family:sans-serif;font-size:23px;">Messages</h2>
<div id="message-container">
    
    <div id="message-names">
        <ul>
            <?php
                
                $con = new dbConnect;
                $con->connect();
            
                
                $result = $con->exec_query("SELECT * FROM `conversation` WHERE `user_one` = '" . $_SESSION['userid'] . "' OR `user_two` = '" . $_SESSION['userid'] . "' ORDER BY `lasttime` DESC");
                
                if ($result->num_rows < 1){
                    echo '<li class="selected" onclick="setSelected(this);">...</li>';
                } else {
                    $convoid = -1;
                    $first = "selected";
                    while ($row = mysqli_fetch_assoc($result)){
                        $id = ($_SESSION['userid'] == $row['user_one']) ? $row["user_two"] : $row['user_one'];
                        $name = mysqli_fetch_assoc($con->exec_query("SELECT u.title, h.house_name FROM user u, house h WHERE u.user_id='" . $id . "' AND h.house_id=u.house_id"));
                        if ($first == "selected"){
                            $convoid = $row['conversation_id'];
                        }
            ?>
                <li class="<?php echo $first; ?>" convid="<?php echo $row['conversation_id']; ?>" onclick="setSelected(this);"><?php echo $name['title'] . " " . $name['house_name']; ?></li>
            <?php
                    $first = "";
                    }
                    
                }
                
                $con->close();
            
            ?>
            
        </ul>
        
        <form id="addnew" onsubmit="return newMessage(this.children[0].value);">
            <input type="text" onfocus="toggleNote(true);" onblur="toggleNote(false);" placeholder="Type Housename Here">
            <span class="note">*Type the Housename of someone you would like to send a message to and press enter. If you wanted to talk to Lord Chelsted eg. "Chelsted".</span>
        </form>
    </div>
    
    <div id="message-content">
        
        <?php
            // get the messages for this conversation
            //echo $convoid;
            $con = new dbConnect;
            $con->connect();
            
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
            
            $con->close();
        ?>
        
    </div>
    
    <form id="send" onsubmit="return send();">
        <input type="text" placeholder="Type here.."><input type="submit" value="Send">
    </form>
    
    
</div>
<script>
    // this is the whole script for the messaging side of things
    var id = <?php echo ($convoid == "") ? -1 : $convoid; ?>;
    
    function toggleNote(temp){
        var note = document.getElementsByClassName("note")[0];
        
        if (temp){
            note.style.display = "block";
        } else {
            note.style.display = "none";
        }
    }
    
    function send(){
        var text = document.getElementById("send").getElementsByTagName("input")[0];
        
        if (text.value.trim() != "" && id != -1){
            
            // send the message to the user with the specified id
            
            var temp = document.getElementById("message-content");
            temp.innerHTML += "<li class='message-message right'>" + text.value.trim() + "</i>";
            
            var request = new ajaxRequest();
            
            
            request.onreadystatechange = function(){
                if (request.readyState == 4){
                        if (request.status == 200){
                            var temp = document.getElementById("message-content");
                            //temp.innerHTML = request.responseText;
                            //alert(request.responseText);
                            temp.scrollTop = temp.scrollHeight;
                        } else {
                            
                        }
                    }
            };
            
            request.open("POST", "/f/messagefunc?send=" + id, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send("message=" + encodeURIComponent(text.value.trim()));
            
            
        } else if (id == -1){
            
        }
        
        text.value = "";
        text.focus();
        
        return false;
    }
    
    function newMessage(text){
        
        // call an ajax request with that housename
        // and then return the id and their housename
        // then add their name to the list
        var request = new ajaxRequest();
        
        
        request.onreadystatechange = function(){
            if (request.readyState == 4){
                    if (request.status == 200){
                        if (request.responseText == "error"){
                            document.getElementById("addnew").children[0].value = "";
                            document.getElementById("addnew").children[0].blur();
                            alert("There are no users with this name.");
                        } else if (request.responseText == "error1"){
                            document.getElementById("addnew").children[0].value = "";
                            document.getElementById("addnew").children[0].blur();
                            alert("You already have a conversation with this user.");
                        } else {
                            var list = document.getElementById("message-names").children[0];
                            var string = request.responseText.split(":");
                            if (list.children[0].innerHTML == "..."){
                                list.removeChild(list.children[0]);
                                addToList(string[1], string[0]);
                            } else {
                                // create a new element
                                addToList(string[1], string[0]);
                            }
                            
                            document.getElementById("addnew").children[0].value = "";
                            document.getElementById("addnew").children[0].blur();
                        }
                    } else {
                        
                    }
                }
        };
        
        request.open("GET", "/f/messagefunc?createconvo=" + text, true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send();
        
        return false;
    }
    
    function addToList(name, id){
        var list = document.getElementById("message-names").children[0];
        
        var item = document.createElement("li");
        item.setAttribute("onclick", "setSelected(this);");
        item.setAttribute("convid", id);
        item.appendChild(document.createTextNode(name));
        list.appendChild(item);

        setSelected(item);

    }
    
    function setSelected(temp){
        if (temp.className != "selected"){
            // unselect the old one
            var list = document.getElementById("message-names").children[0];
            for (i=0;i<list.children.length;i++){
                if (list.children[i].className == "selected"){
                    list.children[i].className = "";
                }
            }
            
            // now select the new one
            temp.className = "selected";
            id = temp.getAttribute("convid");
            
            document.getElementById("message-content").innerHTML = "";
            
            // now request for all the messages
            var request = new ajaxRequest();
            
            
            request.onreadystatechange = function(){
                if (request.readyState == 4){
                        if (request.status == 200){
                            var temp = document.getElementById("message-content");
                            temp.innerHTML = request.responseText;
                            
                            temp.scrollTop = temp.scrollHeight;
                        } else {
                            
                        }
                    }
            };
            
            request.open("GET", "/f/messagefunc?getconvo=" + id, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send();
            
        }
    }
    
    window.onload = function(){
        var temp = document.getElementById("message-content");
        temp.scrollTop = temp.scrollHeight;
        
        <?php
            if (isset($_GET['idsend'])){
                echo "newMessage('" . htmlspecialchars(trim($_GET['idsend'])) . "');";
            }
        ?>
    
        setInterval(function(){
            // update the current messages
            var request = new ajaxRequest();
            
            
            request.onreadystatechange = function(){
                if (request.readyState == 4){
                        if (request.status == 200){
                            var temp = document.getElementById("message-content");
                            
                            if (temp.innerHTML != request.responseText){
                                temp.innerHTML = request.responseText;
                                temp.scrollTop = temp.scrollHeight;
                                
                            }
                            
                        } else {
                            
                        }
                    }
            };
            
            if (id != -1){
                request.open("GET", "/f/messagefunc?update=" + id, true);
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                request.send();
            } 
            
        }, 1000*2);
    }


</script>

<?php
}

?>