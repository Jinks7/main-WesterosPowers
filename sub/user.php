<?php

// get information about the user and their holdfast
include_once("utilities.php");

if (check_login()){
    
    // if the user wants to save the file
    if (isset($_GET['save'])){
        
        // check that everything that is needed is here
        if (isset($_POST['text']) || trim($_POST['text']) != ""){
            
            $con = new dbConnect;
            $con->connect();
            
            $hold = (int)$con->input(trim($_GET['house']));
            $text = strip_tags(trim($_POST['text']), '<a><p><b><i><h1><h2><h3><h4><h5><h6><hr>');
            
            $house_result = ($con->exec_query("SELECT * FROM `holdfast` WHERE `hold_id` = '" . $hold . "'"));
            
            if ($house_result->num_rows != 1){
                die("There is no such house.");
            }
            $house_results = mysqli_fetch_assoc($house_result);
            
            if ($house_results['user_id'] == $_SESSION['userid'] || is_mod() || is_admin()){
                
                // now lets save the file
                $my_file = './wiki/' . $house_results['hname'] . '.wiki';
                $handle = fopen($my_file, 'w') or die("Could not save changes.");
                fwrite($handle, $text);
                fclose($handle);
                
                echo "confirm";
                
                $con->close();
            } else {
                echo "You do not have permission to do this.";   
            }
        } else {
            echo "There is no text to save";
        }
        
    } elseif (isset($_GET['user']) && $_GET['user'] != ""){
        
        $con = new dbConnect;
        $con->connect();
        
        $user = (int)$con->input(trim($_GET['user']));
        
        $userresult = ($con->exec_query("SELECT * FROM `user` u, `house` h WHERE u.`user_id` = '" . $user . "' AND h.`house_id` = u.`house_id`"));
        $house_result = ($con->exec_query("SELECT * FROM `holdfast` h, `region` r WHERE h.`user_id` = '" . $user . "' AND r.`region_id` = h.`region_id`"));
        
        if ($userresult->num_rows < 1){
            echo "<p>This user does not exist.</p>";
        } else {
            $userresult = mysqli_fetch_assoc($userresult);
            
    ?>
        <script>
            document.title = "<?php echo $userresult['title'] . " " . $userresult['house_name']; ?> - WesterosPowers";
        </script>
        <h2 style="margin-right:10px;margin-top:20px;" class="nopadding"><?php echo $userresult['title'] . " " . $userresult['rpname'] . " " . $userresult['house_name']; ?> <?php if ($userresult['level'] == 0) { echo "(Admin)"; } elseif ($userresult['level'] == 1) { echo "(Mod)"; } else { echo ""; } ?></h2>
        Created on <?php echo user_time($userresult['created'],"d \of M Y"); ?> in <?php echo $userresult['time_zone']; ?>
        <?php
            if ($_SESSION['userid'] != $_GET['user']){
        ?>
            (<span><a href="http://<?php echo $host . "/?message&idsend=" . urlencode($userresult['house_name']); ?>">Message this person</a></span>)
        <?php
            }

            if ($_SESSION['userid'] == $_GET['user'] || (is_admin() || is_mod())){
                if (isset($_GET['edit'])){
            ?>
                    (<span><a href="http://<?php echo $host . "/?user=" . $_GET['user']; ?>">cancel</a></span>)
            <?php
                } else {
            ?>
                    (<span><a href="http://<?php echo $host . "/?user=" . $_GET['user']; ?>&edit">edit</a></span>)
            <?php
                }
            }
        ?>
        
        <hr/>
        <?php
            if ($house_result->num_rows < 1){
                echo "<span style='color:red;'>This user does not have a holdfast.</span><br/>";
            } else {
                while ($houseresult = mysqli_fetch_assoc($house_result)){
        ?>
        <h3 class="nopadding"><?php echo $houseresult['hname']; ?></h3>
        <ul class="houseinfo">
            <li>Population: <?php echo $houseresult['population']; ?></li>
            <li>Army Population: <?php echo $houseresult['army_pop']; ?></li>
            <li>Money: <?php echo $houseresult['money']; ?></li>
            <li>Region: <?php echo $houseresult['region_name']; ?></li>
        </ul>
        
        <script>
            function saveWiki(){
                var text = document.getElementById("wikitext").value;
                
                var request = new ajaxRequest();
                
                request.onreadystatechange = function(){
                    if (request.readyState == 4){
                            if (request.status == 200){
                                if (request.responseText == "confirm"){
                                    document.location = "/?user=<?php echo $_GET['user']; ?>";
                                } else {
                                    alert(request.responseText);
                                }
                            } else {
                                
                            }
                        }
                };
                
                request.open("POST", "/f/user?house=<?php echo $houseresult['hold_id'] . "&save"; ?>", true);
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                request.send("text=" + encodeURIComponent(text.trim()));
                
                
            }
        </script>
        <?php
                    $items = $con->exec_query("SELECT i.`iname`, COUNT(i.`iname`) AS 'count' FROM `item_owned` o, `item` i WHERE o.`house_id` = '" . $houseresult['hold_id'] . "' AND o.`item_id` = i.`item_id` GROUP BY i.`iname`");
                    if ($items->num_rows > 0){
        ?>
            <h2 class="nopadding">Items</h2>
            <table id="items">
                <tr><td>Name</td><td>Amount</td></tr>
        <?php
                        while ($row = mysqli_fetch_assoc($items)){
                            echo "<tr><td>" . $row['iname'] . "</td><td>" . $row['count'] . "</td></tr>";
                        }  
        ?>
            </table>
        <?php
                    } else {
                        echo "<span style='color:red;'>This user hasn't bought any items yet.</span><br/>";
                    } 
                   //<h3 class="nopadding" style="margin-top:20px;">Wiki</h3>
        ?>
                
                <hr/>
        <?php    
                   
                   $my_file = 'sub/wiki/' . $houseresult['hname'] . '.wiki';
                   $handle = fopen($my_file, 'r');
                   $data = fread($handle,filesize($my_file));
                   fclose($handle);
                   if ($data == ""){
                       echo "There is no wiki for " . $houseresult['hname'] . ".";
                       $handle = fopen($my_file, 'w');
                       fwrite($handle, "This is the wiki for " . $houseresult['hname'] . ". It has not been edited yet.");
                       fclose($handle);
                   } else {
                       if (isset($_GET['edit']) && ($_SESSION['userid'] == $_GET['user'] || is_mod() || is_admin())){
                            echo "<textarea id='wikitext' style='width:96%;height:300px;overflow-y:auto;' maxlength='5000'>" . ($data) . "</textarea>";
                            echo "<button onclick='saveWiki();'>Save</button> Tags that are allowed are: <pre style='display:inline;'>" . htmlspecialchars("<a><p><b><i><h1><h2><h3><h4><h5><h6><hr>") . "</pre>";
                        } else 
                            echo "<p id='wikitext'>".($data)."</p>";
                   }
                   
                   
                }
            }
        }
        $con->close();
        
        echo "<br/><br/>";
        
    } else {
        echo "<br/>Could not find this user.";
    }
    
} else {
    
}


?>