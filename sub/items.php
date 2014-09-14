<?php
function get_items(){
    echo "<h2 style='margin-left:10px;'>Items</h2>";

    $con = new dbConnect;
    $con->connect();
    
    $house_result = ($con->exec_query("SELECT * FROM `holdfast` WHERE `user_id` = '" . $_SESSION['userid'] . "'"));
    if ($house_result->num_rows < 1){
        echo "You do not have a holdfast. You can not buy items until you make a claim for a holdfast.";
    } else {
        while ($row = mysqli_fetch_assoc($house_result)){
            echo "<h3>" . $row['hname'] . " (<a href='./?item'>Refresh</a>)</h3>";
            $items = $con->exec_query("SELECT i.`iname`, COUNT(i.`iname`) AS 'count' FROM `item_owned` o, `item` i WHERE o.`house_id` = '" . $_SESSION['hold_id'] . "' AND o.`item_id` = i.`item_id` GROUP BY i.`iname`");
            if ($items->num_rows > 0){
            ?>
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
                echo "<span style='color:red;'>This holdfast does not have any items yet.</span><br/>";
            } 
            
            // display every item that is available to buy
            ?>
                </br>
                <h4 class="nopadding">Buy Items (Money: $<span id="money"><?php echo $_SESSION['money'];?></span>)</h4>
                <script>
                    function buy(item, id){
                        // create an ajax request to buy an item
                        
                        // get which item the user is requesting
                        var amount = item.parentElement.parentElement.getElementsByTagName("td")[3].childNodes[1];
                        var request = new ajaxRequest();
                        
                        // disable inputs
                        amount.disabled = true;
                        item.disabled = true;
                        
                        request.onreadystatechange = function(){
                            if (request.readyState == 4){
                                    if (request.status == 200){
                                    
                                        // a timer is set because it will stop people from 
                                        // buying lots of things over and over
                                        setTimeout(function(){
                                            var text = request.responseText.split(":");
                                            
                                            if (text[0] != "")
                                                document.getElementById("money").innerHTML = text[0];
                                        
                                            item.parentElement.parentElement.getElementsByTagName("td")[5].innerHTML = text[1];
                                            item.parentElement.parentElement.getElementsByTagName("td")[5].style.display = "block";
                                            
                                            setTimeout(function(){
                                                item.parentElement.parentElement.getElementsByTagName("td")[5].style.display = "none";
                                            }, 3000);
                                            
                                            
                                            amount.value = 1;
                                            changeCost(amount);
                                            amount.disabled = false;
                                            item.disabled = false;
                                        }, 1000);
                                        
                                    } else {
                                        alert("Error buying item.");
                                    }
                                }
                        };
                        
                        request.open("POST", "/f/itemhandle", true);
                        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        request.send("amount=" + Math.abs(amount.value) + "&id=" + id);
                        
                        
                    }
                    
                    function changeCost(item){
                        var times = parseInt(item.value);
                        var costArray = item.parentElement.parentElement.getElementsByTagName("td")[2].childNodes;
                        var result = Math.abs(times) * parseInt(costArray[0].innerHTML);
                        costArray[1].innerHTML = result;
                    }
                    
                    function checkInput(input){
                        if (input.value.length > 4)
                            input.value = input.value.slice(0,4); 
                        else if (input.value > 2000)
                            input.value = 2000;
                        
                        changeCost(input);
                    }
                    
                </script>
                <table id="buyitem">
                
                    <tr><th>Item</th><th>Description</th><th>Cost</th><th>Amount</th><th>Buy</th></tr>
                    <?php
                        // get all the items
                        $res = $con->exec_query("SELECT * FROM `item` ORDER BY `iname`");
                        $i = 2;
                        while ($row = mysqli_fetch_assoc($res)){
            ?>
                        <tr id="<?php echo "item" . $row['item_id']; ?>" class="<?php echo ($i%2 == 0) ? "black" : ""; ?>">
                            <td>
                                <?php echo $row['iname']; ?>
                            </td>
                            <td>
                                <?php echo $row['description']; ?>
                            </td>
                            <td><span style="display:none;"><?php echo $row['cost']; ?></span><span><?php echo $row['cost']; ?>
                                </span>
                            </td>
                            <td>
                                <input type="number" onchange="changeCost(this);" oninput="checkInput(this);" value="1" min="0" max="2000" maxlength="5"/>
                            </td>
                            <td>
                                <input type="button" onclick="buy(this, <?php echo $row['item_id']; ?>);" value="Buy"/>
                            </td>
                            <td class="arrow_box">
                            </td>
                        </tr>
            <?php
                            $i++;
                        }
                    ?>
                </table>
            <?php
            
            echo "<hr/>";
            
            
            
        }
        
    }
    $con->close();

}
?>