<?php

include_once("utilities.php");

if (check_login()){
    // its okay to use this page
    if (is_admin()){
        if (isset($_POST['change1'])){
            $con = new dbConnect;
            $con->connect();
            
            $title = $con->input($_POST['title']);
            $body = $con->input($_POST['body']);
            $status = $con->input($_POST['status']);
            $id = $con->input($_POST['id']);
            
            $result = $con->exec_query("UPDATE `bug` SET `bugtitle`='" . $title . "', `bugtext`='" . $body . "', `status`='" . $status . "' WHERE `bugid`='" . $id . "'");
            
            $con->close();
            
            if ($result == false){
                echo "Could not change the bug report.";
            } else {
                echo "Success changing report.";
            }
               
        } elseif (isset($_POST['remove1'])) {
            
            $con = new dbConnect;
            $con->connect();

            $id = $con->input($_POST['id']);
            
            $result = $con->exec_query("DELETE FROM `bug` WHERE `bugid`='" . $id . "'");
            
            $con->close();
            
            if ($result == false){
                echo "Could not delete the bug report.";
            } else {
                echo "Success deleting the report.";
            }
            
        } elseif (isset($_POST['change2'])){
            
            $con = new dbConnect;
            $con->connect();
            
            $title = $con->input($_POST['title']);
            $body = $con->input($_POST['body']);
            $status = $con->input($_POST['status']);
            $id = $con->input($_POST['id']);
            
            $result = $con->exec_query("UPDATE `feature` SET `feature_title`='" . $title . "', `feature_text`='" . $body . "', `status`='" . $status . "' WHERE `feature_id`='" . $id . "'");
            
            $con->close();
            
            if ($result == false){
                echo "Could not change the bug report.";
            } else {
                echo "Success changing report.";
            }
            
        } elseif (isset($_POST['remove2'])) {
            
            $con = new dbConnect;
            $con->connect();

            $id = $con->input($_POST['id']);
            
            $result = $con->exec_query("DELETE FROM `feature` WHERE `feature_id`='" . $id . "'");
            
            $con->close();
            
            if ($result == false){
                echo "Could not delete the bug report.";
            } else {
                echo "Success deleting the report.";
            }
            
        } else {
            
        }
    }
    
    
    if (isset($_POST['sub'])){
        //var_dump($_POST);die();
        try {
            if ($_POST['formtype'] == "1"){
                // if it is a bug report 
                
                if ($_POST['title'] == ""){
                    echo "<span class=\"red\">Please enter a title.</span>";
                } elseif ($_POST['body'] == ""){
                    echo "<span class=\"red\">Please enter a discription</span>";
                } else {
                    
                    // add it to the database
                    $con = new dbConnect;
                    $con->connect();
                    
                    $user = new User;
                    
                    $title = $con->input($_POST['title']);
                    $body = $con->input($_POST['body']);
                    $user = $user->get("userid");
                    $status = "Not Fixed";
                    //$time = time();
                    
                    $result = $con->exec_query("INSERT INTO `bug` (`bugtitle`, `bugtext`, `status`, `owner`) VALUES ('" . $title . "', '" . $body . "', '" . $status . "', '" . $user . "') ");
                    
                    $con->close();
                    
                    
                    if ($result == false){
                        // the sql query failed 
                        echo "<span class=\"red\">There was an error saving the bug report.</span>";
                        die();
                    }
                    
                    // tell the user the good news
                    echo "<span class=\"green\">Succesfully entered data.</span>";
                    
                }
                
                
                
            } elseif ($_POST['formtype'] == "2"){
                // if it is a feature request
                if (is_mod() || is_admin()){
                    
                    // add it to the database
                    $con = new dbConnect;
                    $con->connect();
                    
                    $user = new User;
                    
                    $title = $con->input($_POST['title']);
                    $body = $con->input($_POST['body']);
                    $user = $user->get("userid");
                    $status = "Thinking About It";
                    //$time = time();
                    
                    $result = $con->exec_query("INSERT INTO `feature` (`feature_id`, `feature_text`, `status`, `owner`) VALUES ('" . $title . "', '" . $body . "', '" . $status . "', '" . $user . "') ");
                    
                    $con->close();
                    
                    
                    if ($result == false){
                        // the sql query failed 
                        echo "<span class=\"red\">There was an error saving the feature request.</span>";
                        die();
                    }
                    
                    // tell the user the good news
                    echo "<span class=\"green\">Succesfully entered data.</span>";
                    
                    
                    
                    
                } else {
                    include("site-errors/401.php");
                    die();
                }
                
            } else {
                echo "We do not understand your request.";
            }
        } catch(Exception $e) {
            echo "We do not understand your request.";
        }
        
    } else {
        
    }
    
} else {
    // not okay to use this page
    include("site-errors/401.php");
    die();
}



/*******************************************************************************
 * BUG REPORT PAGE
 ******************************************************************************/


function get_bugs(){
    // display form to fill out the bugs
    if (!isset($_POST['sub'])){
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Report a Bug - WesterosPowers</title>
        <link rel="stylesheet" type="text/css" href="/s/style/main.css"/>
        
        <script>
            
            function ajaxRequest(){
                var activexmodes=["Msxml2.XMLHTTP", "Microsoft.XMLHTTP"] //activeX versions to check for in IE
                if (window.ActiveXObject){ //Test for support for ActiveXObject in IE first (as XMLHttpRequest in IE7 is broken)
                    for (var i=0; i<activexmodes.length; i++){
                        try{
                            return new ActiveXObject(activexmodes[i])
                        }
                        catch(e){
                        //suppress error
                        }
                    }
                }
                else if (window.XMLHttpRequest) // if Mozilla, Safari etc
                    return new XMLHttpRequest()
                else
                    return false
            }
            
        
            // ajax call to submit the form
            function submit_form(){
                var request = new ajaxRequest();
                
                request.onreadystatechange = function(){
                    
                    if (request.readyState == 4){
                        if (request.status == 200){
                            document.getElementById("message").innerHTML = request.responseText;
                            
                            if (request.responseText == "<span class=\"green\">Succesfully entered data.</span>"){
                                setTimeout(function(){location.reload()}, 1000);
                            }
                            
                        } else {
                            document.getElementById("message").innerHTML = "<span class=\"red\">Error making request</span>";
                        }
                    }
                }
                
                var title = encodeURIComponent(document.getElementsByName("title")[0].value);
                var body = encodeURIComponent(document.getElementsByName("body")[0].value);
                var parameters = "sub=&title=" + title + "&body=" + body + "&formtype=1";
                
                request.open("POST", "../f/feature", true);
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                request.send(parameters);
                    
                return false;
            }
            
        </script>
    </head>
    <body>
        <div id="wrapper">
            <?php
                include_once("header.php");
            ?>
            <div id="content" class="white_back">
                <form method="POST">
                    <h2 class="title">Submit a Bug Report for WesterosPowers</h2>
                    <p>Please be as descriptive as possible.</p>
                    <input type="text" name="title" placeholder="Bug title" required autocomplete="off"/>
                    <textarea placeholder="Describe the bug." name="body" required autocomplete="off"></textarea>
                    <input type="hidden" name="formtype" value="1"/>
                    <input type="submit" name="sub" onclick="return submit_form();" value="Submit Bug Report"/>
                    <span id="message"></span>
                </form>
                
                <?php
                
                    if (!is_admin()){
                        $con = new dbConnect;
                        $con->connect();
                        $user = new User;
                        $result = $con->exec_query("SELECT * FROM `bug` WHERE `owner` = '" . $user->get("userid") . "' ORDER BY `time`");
                        
                        if ($result->num_rows > 0){
                            echo "<table id=\"normal\" style=\"margin-top:50px;\">";
                            echo "<thead><tr><th>Title</th><th>Owner</th><th>Time</th><th>Status</th></tr></thead><tbody>";
                            while ($row = mysqli_fetch_assoc($result)){
                                
                                $color = "white";
                                
                                if ($row['status'] == "Fixed"){
                                    $color = "green";
                                } elseif ($row['status'] == "Developing Fix"){
                                    $color = "orange";
                                } elseif ($row['status'] == "Not Fixed"){
                                    $color = "red";   
                                }
                                
                                echo "<tr style=\"background:" . $color . ";color:white;\"><td>" . $row['bugtitle'] . "</td><td>You</td><td>" . user_time($row['time']) . "</td><td>" . $row['status'] . "</td></tr>";
                                    
                            }
                            
                            echo "</tbody></table>";
                            
                        }
                        $con->close();
                        echo "</div>";
                    } else {
                        echo "</div>";
                        ?>
<script>

//var currentElement = -1;

function edit(id){
    //if (currentElement != -1)
    //    cancel();
    //currentElement = id;
    var element = document.getElementById("row"+id).childNodes;
    
    element[0].innerHTML = '<input type="text" name="title' + id + '" value="' + element[0].innerHTML + '"/>';
    element[1].innerHTML = '<textarea name="body' + id + '">' + element[1].innerHTML + '</textarea>';
    element[4].innerHTML = "<select name=\"status" + id + "\">" +
                           "<option default value='" + element[4].innerHTML + "'>" + element[4].innerHTML + '</option>' +
                           "<option value='Fixed'>Fixed</option>" + 
                           "<option value='Developing Fix'>Developing Fix</option>" +
                           "<option value='Not Fixed'>Not Fixed</option>" + 
                           "</select>";
    
    element[5].innerHTML = '<a style="cursor:pointer;" onclick="save(' + id + ');">Save</a><br/>' + 
                           '<a href="./">Cancel</a><br/>' +
                           '<a style="cursor:pointer;" onclick="remove_bug(' + id + ');">Remove</a><br/>';
    
}
/*
function cancel(){
    var element = document.getElementById("row"+currentElement).childNodes;
    if (element[0].childNode = "input"){
        document.re
    } else {
        // ignore
    }
}*/

function remove_bug(id){
    // need ajax
    var request = new ajaxRequest();
    
    request.onreadystatechange = function(){
        
        if (request.readyState == 4){
            if (request.status == 200){
                
                alert("Success removing report.");
                location.reload();
                
            } else {
                alert("Could not remove bug report.");
            }
        }
    }
    
    var parameters = "remove1=&id=" + id;
    
    request.open("POST", "../f/feature", true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send(parameters);
        
    return false;
    
}

function save(id){
    // need ajax
    var request = new ajaxRequest();
    
    request.onreadystatechange = function(){
        
        if (request.readyState == 4){
            if (request.status == 200){
                alert(request.responseText);
                location.reload();
                
            } else {
                alert("Could not save bug report.");
            }
        }
    }
    
    var title = encodeURIComponent(document.getElementsByName("title" + id)[0].value);
    var body = encodeURIComponent(document.getElementsByName("body" + id)[0].value);
    var status = encodeURIComponent(document.getElementsByName("status" + id)[0].value);
    var parameters = "change1=&title=" + title + "&body=" + body + "&status=" + status + "&id=" + id;
    
    request.open("POST", "../f/feature", true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send(parameters);
        
    return false;
    
}

</script>                
                        <?php
                        
                        $con = new dbConnect;
                        $con->connect();
                        $result = $con->exec_query("SELECT * FROM `bug` ORDER BY `time`");
                        
                        if ($result->num_rows > 0){
                            
                            echo "<table id=\"normal\" style=\"margin-top:50px;\">";
                            echo "<thead><tr><th>Title</th><th>Description</th><th>Owner</th><th>Time</th><th>Status</th></tr></thead><tbody>";
                            while ($row = mysqli_fetch_assoc($result)){
                                
                                $owner = $con->exec_query("SELECT `first_name`, `last_name` FROM `user` WHERE `user_id`='" . $row['owner'] . "'");
                                $temp = mysqli_fetch_assoc($owner);
                                //var_dump($temp);
                                
                                $color = "white";
                                
                                if ($row['status'] == "Fixed"){
                                    $color = "green";
                                } elseif ($row['status'] == "Developing Fix"){
                                    $color = "orange";
                                } elseif ($row['status'] == "Not Fixed"){
                                    $color = "red";   
                                }
                                
                                echo "<tr id=\"row" . $row['bugid'] . "\" style=\"background:" . $color . ";color:white;\"><td>" . $row['bugtitle'] . "</td><td>" . $row['bugtext'] . "</td><td>" . $temp['first_name'] . " " . $temp['last_name']  . "</td><td>" . user_time($row['time']) . "</td><td>" . $row['status'] . "</td><td style=\"background:white;\"><a style=\"cursor:pointer;\" onclick=\"edit(" . $row['bugid'] . ");\">Edit</a></td></tr>";
                            }
                               
                            echo "</table>";
                            
                        }
                        $con->close();
                    }
                ?>
                
            
            <div id="footer">
                <?php
                    include_once("footer.php");
                    get_footer(false);
                ?>
            </div>
            
        </div>
    </body>
    
    <?php
        include("includes/no_js.php");
    ?>
    
</html>


<?php      
    } else {
        
    }
}


/*******************************************************************************
* FEATURE REQUEST PAGE
******************************************************************************/

function get_features(){
// display form to fill out the bugs
    if (!isset($_POST['sub'])){
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Request a Feature - WesterosPowers</title>
        <link rel="stylesheet" type="text/css" href="/s/style/main.css"/>
        
        <script>
            
            function ajaxRequest(){
                var activexmodes=["Msxml2.XMLHTTP", "Microsoft.XMLHTTP"] //activeX versions to check for in IE
                if (window.ActiveXObject){ //Test for support for ActiveXObject in IE first (as XMLHttpRequest in IE7 is broken)
                    for (var i=0; i<activexmodes.length; i++){
                        try{
                            return new ActiveXObject(activexmodes[i])
                        }
                        catch(e){
                        //suppress error
                        }
                    }
                }
                else if (window.XMLHttpRequest) // if Mozilla, Safari etc
                    return new XMLHttpRequest()
                else
                    return false
            }
            
        
            // ajax call to submit the form
            function submit_form(){
                var request = new ajaxRequest();
                
                request.onreadystatechange = function(){
                    
                    if (request.readyState == 4){
                        if (request.status == 200){
                            document.getElementById("message").innerHTML = request.responseText;
                            
                            if (request.responseText == "<span class=\"green\">Succesfully entered data.</span>"){
                                setTimeout(function(){location.reload()}, 1000);
                            }
                            
                        } else {
                            document.getElementById("message").innerHTML = "<span class=\"red\">Error making request</span>";
                        }
                    }
                }
                
                var title = encodeURIComponent(document.getElementsByName("title")[0].value);
                var body = encodeURIComponent(document.getElementsByName("body")[0].value);
                var parameters = "sub=&title=" + title + "&body=" + body + "&formtype=2";
                
                request.open("POST", "../f/feature", true);
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                request.send(parameters);
                    
                return false;
            }
            
        </script>
    </head>
    <body>
        <div id="wrapper">
            <?php
                include_once("header.php");
            ?>
            <div id="content" class="white_back">
                <form method="POST">
                    <h2 class="title">Request a Feature for WesterosPowers</h2>
                    <p>Please be as descriptive as possible.</p>
                    <input type="text" name="title" placeholder="Feature title" required autocomplete="off"/>
                    <textarea placeholder="Describe the feature." name="body" required autocomplete="off"></textarea>
                    <input type="hidden" name="formtype" value="1"/>
                    <input type="submit" name="sub" onclick="return submit_form();" value="Request Feature"/>
                    <span id="message"></span>
                </form>
                
                <?php
                
                    if (!is_admin()){
                        $con = new dbConnect;
                        $con->connect();
                        $user = new User;
                        $result = $con->exec_query("SELECT * FROM `feature` WHERE `owner` = '" . $user->get("userid") . "' ORDER BY `time`");
                        
                        if ($result->num_rows > 0){
                            echo "<table id=\"normal\" style=\"margin-top:50px;\">";
                            echo "<thead><tr><th>Title</th><th>Owner</th><th>Time</th><th>Status</th></tr></thead><tbody>";
                            while ($row = mysqli_fetch_assoc($result)){
                                
                                $color = "white";
                                
                                if ($row['status'] == "Deployed"){
                                    $color = "green";
                                } elseif ($row['status'] == "In Developement"){
                                    $color = "orange";
                                } elseif ($row['status'] == "Maybe Later"){
                                    $color = "blue";   
                                } elseif ($row['status'] == "Thinking About It"){
                                    $color = "gray";
                                } elseif ($row['status'] == "No"){
                                    $color = "red";
                                } else {
                                    
                                }
                                
                                echo "<tr style=\"background:" . $color . ";color:white;\"><td>" . $row['feature_title'] . "</td><td>You</td><td>" . user_time($row['time']) . "</td><td>" . $row['status'] . "</td></tr>";
                                    
                            }
                            
                            echo "</tbody></table>";
                            
                        }
                        $con->close();
                        echo "</div>";
                    } else {
                        echo "</div>";
                        ?>
<script>

//var currentElement = -1;

function edit(id){
    //if (currentElement != -1)
    //    cancel();
    //currentElement = id;
    var element = document.getElementById("row"+id).childNodes;
    
    element[0].innerHTML = '<input type="text" name="title' + id + '" value="' + element[0].innerHTML + '"/>';
    element[1].innerHTML = '<textarea name="body' + id + '">' + element[1].innerHTML + '</textarea>';
    element[4].innerHTML = "<select name=\"status" + id + "\">" +
                           "<option default value='" + element[4].innerHTML + "'>" + element[4].innerHTML + '</option>' +
                           "<option value='Deployed'>Deployed</option>" + 
                           "<option value='In Developement'>In Developement</option>" +
                           "<option value='Maybe Later'>Maybe Later</option>" + 
                           "<option value='Thinking About It'>Thinking About It</option>" + 
                           "<option value='No'>No</option>" + 
                           "</select>";
    
    element[5].innerHTML = '<a style="cursor:pointer;" onclick="save(' + id + ');">Save</a><br/>' + 
                           '<a href="./">Cancel</a><br/>' +
                           '<a style="cursor:pointer;" onclick="remove_bug(' + id + ');">Remove</a><br/>';
    
}

function remove_bug(id){
    // need ajax
    var request = new ajaxRequest();
    
    request.onreadystatechange = function(){
        
        if (request.readyState == 4){
            if (request.status == 200){
                
                alert("Success removing report.");
                location.reload();
                
            } else {
                alert("Could not remove bug report.");
            }
        }
    }
    
    var parameters = "remove2=&id=" + id;
    
    request.open("POST", "../f/feature", true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send(parameters);
        
    return false;
    
}

function save(id){
    // need ajax
    var request = new ajaxRequest();
    
    request.onreadystatechange = function(){
        
        if (request.readyState == 4){
            if (request.status == 200){
                alert(request.responseText);
                location.reload();
                
            } else {
                alert("Could not save bug report.");
            }
        }
    }
    
    var title = encodeURIComponent(document.getElementsByName("title" + id)[0].value);
    var body = encodeURIComponent(document.getElementsByName("body" + id)[0].value);
    var status = encodeURIComponent(document.getElementsByName("status" + id)[0].value);
    var parameters = "change2=&title=" + title + "&body=" + body + "&status=" + status + "&id=" + id;
    
    request.open("POST", "../f/feature", true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send(parameters);
        
    return false;
    
}

</script>                
                        <?php
                        
                        $con = new dbConnect;
                        $con->connect();
                        $result = $con->exec_query("SELECT * FROM `feature` ORDER BY `time`");
                        
                        if ($result->num_rows > 0){
                            
                            echo "<table id=\"normal\" style=\"margin-top:50px;\">";
                            echo "<thead><tr><th>Title</th><th>Description</th><th>Owner</th><th>Time</th><th>Status</th></tr></thead><tbody>";
                            while ($row = mysqli_fetch_assoc($result)){
                                
                                $owner = $con->exec_query("SELECT `first_name`, `last_name` FROM `user` WHERE `user_id`='" . $row['owner'] . "'");
                                $temp = mysqli_fetch_assoc($owner);
                                
                                $color = "white";
                                
                                if ($row['status'] == "Deployed"){
                                    $color = "green";
                                } elseif ($row['status'] == "In Developement"){
                                    $color = "orange";
                                } elseif ($row['status'] == "Maybe Later"){
                                    $color = "blue";   
                                } elseif ($row['status'] == "Thinking About It"){
                                    $color = "gray";
                                } elseif ($row['status'] == "No"){
                                    $color = "red";
                                } else {
                                    
                                }
                                
                                echo "<tr id=\"row" . $row['feature_id'] . "\" style=\"background:" . $color . ";color:white;\"><td>" . $row['feature_title'] . "</td><td>" . $row['feature_text'] . "</td><td>" . $temp['first_name'] . " " . $temp['last_name']  . "</td><td>" . user_time($row['time']) . "</td><td>" . $row['status'] . "</td><td style=\"background:white;\"><a style=\"cursor:pointer;\" onclick=\"edit(" . $row['feature_id'] . ");\">Edit</a></td></tr>";
                            }
                               
                            echo "</table>";
                            
                        }
                        $con->close();
                    }
                ?>
                
            
            <div id="footer">
                <?php
                    include_once("footer.php");
                    get_footer(false);
                ?>
            </div>
            
        </div>
    </body>
    
    <?php
        include("includes/no_js.php");
    ?>
    
</html>


<?php      
    } else {
        
    }
}

?>