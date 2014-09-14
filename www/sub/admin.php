<?php

include_once("utilities.php");

if (check_login()){
    if (is_admin() || is_mod()){
        if (isset($_GET['holdfast'])){
            if (isset($_GET['save'])){
                $con = new dbConnect;
                $con->connect();
                if (isset($_GET['rem'])){
                    $id = (int)$con->input(trim($_GET['rem']));
                    
                    $bool = $con->exec_query("DELETE FROM `holdfast` WHERE `hold_id` = '" . $id . "'");
                    if (!$bool) {
                        echo "error";
                    }
                    
                } else {
                    $id = (int)$con->input(trim($_GET['save']));
                    
                    // get the values
                    $name = $con->input(trim($_POST['name']));
                    $user = $con->input(trim($_POST['user']));
                    $population = $con->input(trim($_POST['population']));
                    $popcap = $con->input(trim($_POST['popcap']));
                    $army_pop = $con->input(trim($_POST['army_pop']));
                    $money = $con->input(trim($_POST['money']));
                    $moneyper = $con->input(trim($_POST['moneyper']));
                    $itemcap = $con->input(trim($_POST['itemcap']));
                    $region = $con->input(trim($_POST['region']));
                    
                    if ($id == 0){ // create a new item
                        
                        $con->exec_query("INSERT INTO `holdfast`(`user_id`, `hname`, `population`, `army_pop`, `money`, `money_per_year`, `pop_cap`, `item_cap`, `region_id`) VALUES ('" . $user . "', '" . $name . "', '" . $population . "', '" . $army_pop . "', '" . $money  . "', '" . $moneyper . "', '" . $popcap . "', '" . $itemcap . "', '" . $region . "')");
                        
                    } else { // update the item
                        
                        $con->exec_query("UPDATE `holdfast` SET `user_id`='" . $user . "',`hname`='" . $name . "',`population`='" . $population . "',`army_pop`='" . $army_pop . "',`money`='" . $money . "',`money_per_year`='" . $moneyper . "',`pop_cap`='" . $popcap . "',`item_cap`='" . $itemcap . "', `region_id`='" . $region . "' WHERE `hold_id` = '" . $id . "'");
                        echo "UPDATE `holdfast` SET `user_id`='" . $user . "',`hname`='" . $name . "',`population`='" . $population . "',`army_pop`='" . $army_pop . "',`money`='" . $money . "',`money_per_year`='" . $moneyper . "',`pop_cap`='" . $popcap . "',`item_cap`='" . $itemcap . "', `region_id`='" . $region . "' WHERE `house_id` = '" . $id . "'";
                        
                    }
                }
                $con->close();
            } elseif (isset($_GET['get'])){
                               
                $con = new dbConnect;
                $con->connect();
                               
                $get = (int)$con->input(trim($_GET['get']));
                               
                $result = mysqli_fetch_assoc($con->exec_query("SELECT * FROM `holdfast` h, `region` r WHERE h.`hold_id` = '" . $get . "' AND r.`region_id` = h.`region_id`"));
                ?>
                             
                    Name: <input type="text" name="name" placeholder="Name" value="<?php echo $result['hname']; ?>"/><br/>
                    User: <select name="user">
                    <?php
                        $owned = mysqli_fetch_assoc($con->exec_query("SELECT * FROM `user` u, `house` h WHERE u.`user_id` = '" . $result['user_id'] . "' AND u.`house_id` = h.`house_id`"));
                        echo "<option value='" . $owned['user_id'] . "'>" . $owned['house_name'] . "</option>";
                        echo "<option value='0'>None</option>";
                        $res = $con->exec_query("SELECT * FROM `user` u, `house` h WHERE u.`house_id` = h.`house_id`");
                        while ($row = mysqli_fetch_assoc($res)){
                            echo "<option value='" . $row['user_id'] . "'>" . $row['house_name'] . "</option>";
                        }
                    ?>
                    </select><br/>
                    Population: <input type="text" name="population" placeholder="Population" value="<?php echo $result['population']; ?>"/><br/>
                    Population Max: <input type="text" name="popcap" placeholder="Population Max" value="<?php echo $result['pop_cap']; ?>"/><br/>
                    Army Population: <input type="text" name="army_pop" placeholder="Army Population" value="<?php echo $result['army_pop']; ?>"/><br/>
                    Money: <input type="text" name="money" placeholder="Money" value="<?php echo $result['money']; ?>"/><br/>
                    Money per year: <input type="text" name="moneyper" placeholder="Money Per Year" value="<?php echo $result['money_per_year']; ?>"/><br/>
                    Item Max: <input type="text" name="itemcap" placeholder="Item Max" value="<?php echo $result['item_cap']; ?>"/><br/>
                    Region: <select name="region">
                        <option value="<?php echo $result['region_id']; ?>"><?php echo $result['region_name']; ?></option>
                    <?php
                        $res = $con->exec_query("SELECT * FROM `region`");
                        while ($row = mysqli_fetch_assoc($res)){
                            echo "<option value='" . $row['region_id'] . "'>" . $row['region_name'] . "</option>";
                        }
                    ?>
                    </select><br/>
                    <input type="submit" value="Save" onclick="saveInfo(<?php echo $get; ?>, 'holdfast', false);"/>
                <?php
                if ($get != 0){
                ?>
                    <input type="submit" value="Remove" onclick="saveInfo(<?php echo $get; ?>, 'holdfast', true);"/>
                <?php  
                }
                $con->close();
                               
            } else {
                $con = new dbConnect;
                $con->connect();
                // get a list of items and their ids
                echo "<h3 class='nopadding'>Holdfasts</h3>"; 
                $result = $con->exec_query("SELECT `hold_id`, `hname` FROM `holdfast` ORDER BY `hname`");
                echo "<select page='holdfast' onchange='change(this);'>";
                echo "<option value='0'>Create New</option>";
                while ($row = mysqli_fetch_assoc($result)){
                    echo "<option value='" . $row['hold_id'] . "'>" . $row['hname'] . "</option>";
                }
                echo "</select";
                             
                ?>
                <br/>
                <div id="cont">
                    <input type="text" name="name" placeholder="Name"/><br/>
                    <select name="user">
                        <option>User</option>
                        <option value='0'>None</option>
                    <?php
                        $res = $con->exec_query("SELECT * FROM `user` u, `house` h WHERE u.`house_id` = h.`house_id`");
                        while ($row = mysqli_fetch_assoc($res)){
                            echo "<option value='" . $row['user_id'] . "'>" . $row['house_name'] . "</option>";
                        }
                    ?>
                    </select><br/>
                    <input type="text" name="population" placeholder="Population"/><br/>
                    <input type="text" name="popcap" placeholder="Population Max"/><br/>
                    <input type="text" name="army_pop" placeholder="Army Population"/><br/>
                    <input type="text" name="money" placeholder="Money"/><br/>
                    <input type="text" name="moneyper" placeholder="Money Per Year"/><br/>
                    <input type="text" name="itemcap" placeholder="Item Max"/><br/>
                    <select name="region">
                        <option value="">Region</option>
                    <?php
                        $res = $con->exec_query("SELECT * FROM `region`");
                        while ($row = mysqli_fetch_assoc($res)){
                            echo "<option value='" . $row['region_id'] . "'>" . $row['region_name'] . "</option>";
                        }
                    ?>
                    </select><br/>
                    <input type="submit" value="Save" onclick="saveInfo(0, 'item', false);"/>
                </div>
                <?php
                    $con->close();
                }
        } elseif (isset($_GET['user'])){
            if (isset($_GET['save'])){
                $con = new dbConnect;
                $con->connect();
                if (isset($_GET['rem'])){
                    $id = (int)$con->input(trim($_GET['rem']));
                    
                    $bool = $con->exec_query("DELETE FROM `user` WHERE `user_id` = '" . $id . "'");
                    if (!$bool) {
                        echo "error";
                    }
                    
                } else {
                    $id = (int)$con->input(trim($_GET['save']));
                    
                    // get the values
                    $name = $con->input(trim($_POST['name']));
                    $email = $con->input(trim($_POST['email']));
                    $house = $con->input(trim($_POST['house']));
                    $title = $con->input(trim($_POST['title']));
                    $level = $con->input(trim($_POST['level']));
                    
                    
                    if ($id == 0){ // create a new item
                        
                        $con->exec_query("INSERT INTO `user` (`rpname`, `email`, `house_id`, `level`, `title`) VALUES ('" . $name . "', '" . $email . "', '" . $house . "', '" . $level . "', `" . $title . "`)");
                        
                    } else { // update the item
                        
                        $con->exec_query("UPDATE `user` SET `rpname`='" . $name . "', `email`='" . $email . "', `house_id`='" . $house . "', `level`='" . $level . "', `title`='" . $title . "' WHERE `user_id` = '" . $id . "'");
                        
                    }
                }
                $con->close();
            } elseif (isset($_GET['get'])){
                               
                $con = new dbConnect;
                $con->connect();
                               
                $get = (int)$con->input(trim($_GET['get']));
                               
                $result = mysqli_fetch_assoc($con->exec_query("SELECT * FROM `user` WHERE `user_id` = '" . $get . "'"));
                ?>
                    <input type="text" name="name" placeholder="Name" value="<?php echo $result['rpname']; ?>"/><br/>
                    <input type="text" name="email" placeholder="Email" value="<?php echo $result['email']; ?>"/><br/>
                    <input type="text" name="title" placeholder="Title" value="<?php echo $result['title']; ?>"/><br/>
                    <select name="house">
                    <?php
                        $temp = mysqli_fetch_assoc($con->exec_query("SELECT `house_id`, `house_name` FROM `house` WHERE `house_id` = '" . $result['house_id'] . "'"));
                        echo "<option value='" . $temp['house_id'] . "'>" . $temp['house_name'] . "</option>";
                        
                        $temp = $con->exec_query("SELECT `house_id`, `house_name` FROM `house` WHERE `taken` = 0 ORDER BY `house_name`");
                        while ($row = mysqli_fetch_assoc($temp)){
                            echo "<option value='" . $row['house_id'] . "'>" . $row['house_name'] . "</option>";
                        }
                    ?>
                    </select><br/>
                    <select name="level">
                        <option value="<?php echo $result['level']; ?>"><?php echo ($result['level'] == 0) ? "Admin" : (($result['level'] == 1) ? "Mod" : "Standard"); ?></option>
                        <?php
                            
                            if (is_mod()){
                        ?>
                            <option value="3">Standard</option>
                            <option value="1">Mod</option>
                        <?php
                            } elseif (is_admin()){
                        ?>
                            <option value="3">Standard</option>
                            <option value="1">Mod</option>
                            <option value="0">Admin</option>
                        <?php
                            }
                        ?>
                    </select><br/>
                    <input type="submit" value="Save" onclick="saveInfo(<?php echo $get; ?>, 'user', false);"/>
                <?php
                if ($get != 0){
                ?>
                    <input type="submit" value="Remove" onclick="saveInfo(<?php echo $get; ?>, 'user', true);"/>
                <?php  
                }
                $con->close();
                               
            } else {
                $con = new dbConnect;
                $con->connect();
                // get a list of items and their ids
                echo "<h3 class='nopadding'>Users</h3>"; 
                if (is_mod()){
                    $result = $con->exec_query("SELECT `user_id`, `house_id` FROM `user` WHERE `level` <> 0");
                } else {
                    $result = $con->exec_query("SELECT `user_id`, `house_id` FROM `user`");
                }
                
                echo "<select page='user' onchange='change(this);'>";
                echo "<option value='0'>Create New</option>";
                while ($row = mysqli_fetch_assoc($result)){
                    $house = mysqli_fetch_assoc($con->exec_query("SELECT * FROM `house` WHERE `house_id` = '" . $row['house_id'] . "'"));
                    echo "<option value='" . $row['user_id'] . "'>" . $house['house_name'] . "</option>";
                }
                echo "</select";
                             
                ?>
                <br/>
                <div id="cont">
                    <input type="text" name="name" placeholder="Name"/><br/>
                    <input type="text" name="email" placeholder="Email"/><br/>
                    <input type="text" name="title" placeholder="Title"/><br/>
                    <select name="house">
                        <option>House Name:</option>
                    <?php
                        $result = $con->exec_query("SELECT `house_id`, `house_name` FROM `house` WHERE `taken` = 0 ORDER BY `house_name`");
                        while ($row = mysqli_fetch_assoc($result)){
                            echo "<option value='" . $row['house_id'] . "'>" . $row['house_name'] . "</option>";
                        }
                    ?>
                    </select><br/>
                    <select name="level">
                        <option>Level</option>
                        <?php
                            if (is_mod()){
                        ?>
                            <option value="3">Standard</option>
                            <option value="1">Mod</option>
                        <?php
                            } elseif (is_admin()){
                        ?>
                            <option value="3">Standard</option>
                            <option value="1">Mod</option>
                            <option value="0">Admin</option>
                        <?php
                            }
                        ?>
                    </select><br/>
                    <input type="submit" value="Save" onclick="saveInfo(0, 'user', false);"/>
                </div>
                <?php
                    $con->close();
                }
        } elseif (isset($_GET['house'])){
            if (isset($_GET['save'])){
                $con = new dbConnect;
                $con->connect();
                if (isset($_GET['rem'])){
                    $id = (int)$con->input(trim($_GET['rem']));
                    
                    $bool = $con->exec_query("DELETE FROM `house` WHERE `house_id` = '" . $id . "'");
                    if (!$bool) {
                        echo "error";
                    }
                    
                } else {
                    $id = (int)$con->input(trim($_GET['save']));
                    
                    // get the values
                    $name = $con->input(trim($_POST['name']));
                    $taken = $con->input(trim($_POST['taken']));
                    
                    if ($id == 0){ // create a new item
                        
                        $con->exec_query("INSERT INTO `house` (`house_name`, `taken`) VALUES ('" . $name . "', '" . $taken . "')");
                        
                    } else { // update the item
                        
                        $con->exec_query("UPDATE `house` SET `house_name`='" . $name . "', `taken` = '" . $taken . "' WHERE `house_id` = '" . $id . "'");
                        
                    }
                }
                $con->close();
            } elseif (isset($_GET['get'])){
                               
                $con = new dbConnect;
                $con->connect();
                               
                $get = (int)$con->input(trim($_GET['get']));
                               
                $result = mysqli_fetch_assoc($con->exec_query("SELECT * FROM `house` WHERE `house_id` = '" . $get . "'"));
                ?>
                             
                    <input type="text" name="name" placeholder="Name" value="<?php echo $result['house_name']; ?>"/><br/>
                    <input type="text" name="taken" placeholder="Taken" value="<?php echo $result['taken']; ?>"/><br/>
                    <input type="submit" value="Save" onclick="saveInfo(<?php echo $get; ?>, 'house', false);"/>
                <?php
                if ($get != 0){
                ?>
                    <input type="submit" value="Remove" onclick="saveInfo(<?php echo $get; ?>, 'house', true);"/>
                <?php  
                }
                $con->close();
                               
            } else {
                $con = new dbConnect;
                $con->connect();
                // get a list of items and their ids
                echo "<h3 class='nopadding'>House</h3>"; 
                $result = $con->exec_query("SELECT `house_id`, `house_name` FROM `house` ORDER BY `house_name`");
                echo "<select page='house' onchange='change(this);'>";
                echo "<option value='0'>Create New</option>";
                while ($row = mysqli_fetch_assoc($result)){
                    echo "<option value='" . $row['house_id'] . "'>" . $row['house_name'] . "</option>";
                }
                echo "</select";
                             
                ?>
                <br/>
                <div id="cont">
                    <input type="text" name="name" placeholder="Name"/><br/>
                    <input type="number" min=0 max=1 name="taken" placeholder="Taken"/><br/>
                    <input type="submit" value="Save" onclick="saveInfo(0, 'house', false);"/>
                </div>
                <?php
                    $con->close();
                }
        } elseif (isset($_GET['region'])){
            if (isset($_GET['save'])){
                $con = new dbConnect;
                $con->connect();
                if (isset($_GET['rem'])){
                    $id = (int)$con->input(trim($_GET['rem']));
                    
                    $bool = $con->exec_query("DELETE FROM `region` WHERE `region_id` = '" . $id . "'");
                    if (!$bool) {
                        echo "error";
                    }
                    
                } else {
                    $id = (int)$con->input(trim($_GET['save']));
                    
                    // get the values
                    $name = $con->input(trim($_POST['name']));
                    
                    if ($id == 0){ // create a new item
                        
                        $con->exec_query("INSERT INTO `region` (`region_name`) VALUES ('" . $name . "')");
                        
                    } else { // update the item
                        
                        $con->exec_query("UPDATE `region` SET `region_name`='" . $name . "' WHERE `region_id` = '" . $id . "'");
                        
                    }
                }
                $con->close();
            } elseif (isset($_GET['get'])){
                               
                $con = new dbConnect;
                $con->connect();
                               
                $get = (int)$con->input(trim($_GET['get']));
                               
                $result = mysqli_fetch_assoc($con->exec_query("SELECT * FROM `region` WHERE `region_id` = '" . $get . "'"));
                ?>
                             
                    <input type="text" name="name" placeholder="Name" value="<?php echo $result['region_name']; ?>"/><br/>
                    <input type="submit" value="Save" onclick="saveInfo(<?php echo $get; ?>, 'region', false);"/>
                <?php
                if ($get != 0){
                ?>
                    <input type="submit" value="Remove" onclick="saveInfo(<?php echo $get; ?>, 'region', true);"/>
                <?php  
                }
                $con->close();
                               
            } else {
                $con = new dbConnect;
                $con->connect();
                // get a list of items and their ids
                echo "<h3 class='nopadding'>Region</h3>";  
                $result = $con->exec_query("SELECT `region_id`, `region_name` FROM `region` ORDER BY `region_name`");
                echo "<select page='region' onchange='change(this);'>";
                echo "<option value='0'>Create New</option>";
                while ($row = mysqli_fetch_assoc($result)){
                    echo "<option value='" . $row['region_id'] . "'>" . $row['region_name'] . "</option>";
                }
                echo "</select";
                             
                ?>
                <br/>
                <div id="cont">
                    <input type="text" name="name" placeholder="Name"/><br/>
                    <input type="submit" value="Save" onclick="saveInfo(0, 'region', false);"/>
                </div>
                <?php
                    $con->close();
                }
          } elseif (isset($_GET['banned'])){
            if (isset($_GET['save'])){
                $con = new dbConnect;
                $con->connect();
                if (isset($_GET['rem'])){
                    $id = (int)$con->input(trim($_GET['rem']));
                    
                    $bool = $con->exec_query("DELETE FROM `banned` WHERE `user_id` = '" . $id . "'");
                    if (!$bool) {
                        echo "error";
                    }
                    
                } else {
                    $id = (int)$con->input(trim($_GET['save']));

                    // get the values
                    $name = $con->input(trim($_POST['userid']));
                    // this is in hours
                    $hours = (int)$con->input(trim($_POST['time']));
                    $time = time()+60*60*$hours;
                    
                    $reason = $con->input(trim($_POST['reason']));
                    
                    if ($id == 0){ // create a new item
                        
                        $con->exec_query("INSERT INTO `banned` (`user_id`, `time`, `reason`) VALUES ('" . $name . "', '" . $time . "', '" . $reason . "')");
                        
                    } else { // update the item
                        $con->exec_query("UPDATE `banned` SET `time`='" . $time . "', `reason`='" . $reason . "' WHERE `user_id` = '" . $id . "'");
                    }
                }
                $con->close();
            } elseif (isset($_GET['get'])){
                               
                $con = new dbConnect;
                $con->connect();
                               
                $get = (int)$con->input(trim($_GET['get']));
                               
                $result = mysqli_fetch_assoc($con->exec_query("SELECT * FROM `banned` WHERE `user_id` = '" . $get . "'"));
                ?>
                    <?php
                        if ($get == 0){
                    ?>
                        <select name="userid">
                            <option>User</option>
                        <?php
                            $temp = $con->exec_query("SELECT * FROM `user` u, `house` h WHERE u.`house_id` = h.`house_id`");
                            
                            while ($temprow = mysqli_fetch_assoc($temp)){
                                echo "<option value='" . $temprow['user_id'] . "'>" . $temprow['house_name'] . "</option>";
                            }
                        ?>
                        </select><br/>
                    <?php
                            $num = 0;
                        } else {
                            $tempname = mysqli_fetch_assoc($con->exec_query("SELECT * FROM `user` u, `house` h WHERE u.`user_id` = '" . $get . "' AND u.`house_id` = h.`house_id`"))['house_name'];
                    ?>
                            <input type="text" name="userid" disabled placeholder="Name" value="<?php echo $tempname; ?>"/><br/>
                            
                    <?php
                            $num = ceil((($result['time'] - time())/(60*60)));
                        }
                        
                        
                    ?>
                    <input type="number" name="time" placeholder="Time" value="<?php echo $num; ?>"/><br/>
                    <input type="text" name="reason" placeholder="Description" value="<?php echo $result['reason']; ?>"/><br/>
                    <input type="submit" value="Save" onclick="saveInfo(<?php echo $get; ?>, 'banned', false);"/>
                <?php
                if ($get != 0){
                ?>
                    <input type="submit" value="Remove" onclick="saveInfo(<?php echo $get; ?>, 'banned', true);"/>
                <?php  
                }
                $con->close();
                               
            } else {
                $con = new dbConnect;
                $con->connect();
                // get a list of items and their ids
                echo "<h3 class='nopadding'>Banned</h3>";            
                $result = $con->exec_query("SELECT `user_id` FROM `banned`");
                echo "<select page='banned' onchange='change(this);'>";
                echo "<option value='0'>Create New</option>";
                while ($row = mysqli_fetch_assoc($result)){
                    $temp = mysqli_fetch_assoc($con->exec_query("SELECT * FROM `user` u, `house` h WHERE u.`user_id` = '" . $row['user_id'] . "' AND u.`house_id` = h.`house_id`"));
                    echo "<option value='" . $row['user_id'] . "'>" . $temp['house_name'] . "</option>";
                }
                echo "</select";
                             
                ?>
                <br/>
                <div id="cont">
                    <select name="userid">
                        <option>User</option>
                    <?php
                        $temp = $con->exec_query("SELECT * FROM `user` u, `house` h WHERE u.`house_id` = h.`house_id`");
                        
                        while ($temprow = mysqli_fetch_assoc($temp)){
                            echo "<option value='" . $temprow['user_id'] . "'>" . $temprow['house_name'] . "</option>";
                        }
                    ?>
                    </select><br/>
                    <input type="number" name="time" placeholder="Time (In hours)"/><br/>
                    <input type="text" name="reason" placeholder="Reason"/><br/>
                    <input type="submit" value="Save" onclick="saveInfo(0, 'banned', false);"/>
                </div>
                <?php
                    $con->close();
                }
        } elseif (isset($_GET['item'])){
            if (isset($_GET['save'])){
                $con = new dbConnect;
                $con->connect();
                if (isset($_GET['rem'])){
                    $id = (int)$con->input(trim($_GET['rem']));
                    
                    $bool = $con->exec_query("DELETE FROM `item` WHERE `item_id` = '" . $id . "'");
                    if (!$bool) {
                        echo "error";
                    }
                    
                } else {
                    $id = (int)$con->input(trim($_GET['save']));
                    
                    // get the values
                    $name = $con->input(trim($_POST['name']));
                    $description = $con->input(trim($_POST['description']));
                    $cost = $con->input(trim($_POST['cost']));
                    $amount = $con->input(trim($_POST['amount']));
                    
                    if ($id == 0){ // create a new item
                        
                        $con->exec_query("INSERT INTO `item` (`iname`, `description`, `cost`, `amount`) VALUES ('" . $name . "', '" . $description . "', '" . $cost . "', '" . $amount . "')");
                        
                    } else { // update the item
                        
                        $con->exec_query("UPDATE `item` SET `iname`='" . $name . "', `description`='" . $description . "', `cost`='" . $cost . "', `amount`='" . $amount . "' WHERE `item_id` = '" . $id . "'");
                        
                    }
                }
                $con->close();
            } elseif (isset($_GET['get'])){
                               
                $con = new dbConnect;
                $con->connect();
                               
                $get = (int)$con->input(trim($_GET['get']));
                               
                $result = mysqli_fetch_assoc($con->exec_query("SELECT * FROM `item` WHERE `item_id` = '" . $get . "'"));
                ?>
                             
                    <input type="text" name="name" placeholder="Name" value="<?php echo $result['iname']; ?>"/><br/>
                    <input type="text" name="description" placeholder="Description" value="<?php echo $result['description']; ?>"/><br/>
                    <input type="number" name="cost" placeholder="Cost" value="<?php echo $result['cost']; ?>"/><br/>
                    <input type="amount" name="amount" placeholder="Amount" value="<?php echo $result['amount']; ?>"/><br/>
                    <input type="submit" value="Save" onclick="saveInfo(<?php echo $get; ?>, 'item', false);"/>
                <?php
                if ($get != 0){
                ?>
                    <input type="submit" value="Remove" onclick="saveInfo(<?php echo $get; ?>, 'item', true);"/>
                <?php  
                }
                $con->close();
                               
            } else {
                $con = new dbConnect;
                $con->connect();
                // get a list of items and their ids
                echo "<h3 class='nopadding'>Items</h3>";         
                $result = $con->exec_query("SELECT `item_id`, `iname` FROM `item` ORDER BY `iname`");
                echo "<select page='item' onchange='change(this);'>";
                echo "<option value='0'>Create New</option>";
                while ($row = mysqli_fetch_assoc($result)){
                    echo "<option value='" . $row['item_id'] . "'>" . $row['iname'] . "</option>";
                }
                echo "</select";
                             
                ?>
                <br/>
                <div id="cont">
                    <input type="text" name="name" placeholder="Name"/><br/>
                    <input type="text" name="description" placeholder="Description"/><br/>
                    <input type="number" name="cost" placeholder="Cost"/><br/>
                    <input type="amount" name="amount" placeholder="Amount"/><br/>
                    <input type="submit" value="Save" onclick="saveInfo(0, 'item', false);"/>
                </div>
                <?php
                    $con->close();
                }
        } else {
                $page = (is_admin()) ? "Admin" : "Mod";
                echo "<h2 class='nopadding' style='margin-top:20px;'>" . $page . " Control Panel</h2>";
    ?>
        <script>
            
            function saveInfo(id, page, rem){
                // boolean if it needs to be removed or not
                if (rem){ // remove the item
                    
                    // send the ajax get request to remove the item
                    var request = new ajaxRequest();
                    
                    request.onreadystatechange = function(){
                        if (request.readyState == 4){
                                if (request.status == 200){
                                    //document.getElementById(id).innerHTML = request.responseText;
                                    if (request.responseText == "error"){
                                        alert("There was an error");
                                    } else {
                                        alert("Successfully removed item.");
                                        getPage("/f/admin?" + page, "admin-content");
                                    }
                                } else {
                                    alert("Sorry there was an error");
                                }
                            }
                    };
                    
                    request.open("GET", "/f/admin?" + page + "&save&rem=" + id, true);
                    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    request.send();
                    
                } else { // save the item
                    
                    // get all the values
                    var string = "";
                    var elements = document.getElementById("cont").childNodes;
                    
                    for (i=0;i<elements.length;i++){
                        if (elements[i].nodeName == "INPUT" || elements[i].nodeName == "SELECT"){
                            string += elements[i].getAttribute("name") + "=" + elements[i].value + "&";  
                        }
                    }
                    // create an ajax post request and send the values
                    
                    var request = new ajaxRequest();
                    
                    request.onreadystatechange = function(){
                        if (request.readyState == 4){
                                if (request.status == 200){
                                    if (id != 0){
                                        getPage("/f/admin?" + page + "&get=" + id, "cont");
                                    } else {
                                        getPage('/f/admin?' + page, 'admin-content');
                                    }
                                } else {
                                    alert("Sorry there was an error");
                                }
                            }
                    };
                    
                    request.open("POST", "/f/admin?" + page + "&save=" + id, true);
                    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    request.send(string);
                    
                }
                
            }
            
            function change(select){
                getPage("/f/admin?" + select.getAttribute("page") + "&get=" + select.value, "cont");
            }
        
            function getPage(url, id){
                var request = new ajaxRequest();
                
                request.onreadystatechange = function(){
                    if (request.readyState == 4){
                            if (request.status == 200){
                                document.getElementById(id).innerHTML = request.responseText;
                                resizeHeight();
                            } else {
                                alert("Sorry there was an error");
                            }
                        }
                };
                
                request.open("GET", url, true);
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                request.send();
            }
            
        </script>
        <div id="admin-panel">
            <a onclick="getPage('/f/admin?user', 'admin-content');">Users</a> | 
            <a onclick="getPage('/f/admin?holdfast', 'admin-content');">Holdfasts</a> | 
            <a onclick="getPage('/f/admin?house', 'admin-content');">Houses</a> | 
            <a onclick="getPage('/f/admin?region', 'admin-content');">Regions</a> | 
            <a onclick="getPage('/f/admin?banned', 'admin-content');">Banned</a> | 
            <a onclick="getPage('/f/admin?item', 'admin-content');">Items</a></div>   
        
        <div id="admin-content">
            Click above to go to one of the sections.
        </div>
        
    <?php  
        }
    } else {
        echo "<br/>There is nothing here.";
    }
}


?>