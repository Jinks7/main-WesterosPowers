<?php
// create a list of free holdfasts
    include_once("utilities.php");
    $con = new dbConnect;
    $con->connect();
    
    $result = $con->exec_query("SELECT h.`hname`, r.`region_name` FROM `holdfast` h, `region` r WHERE `user_id` = 0 AND h.`region_id` = r.`region_id`");
    
?>
<h2>Free Holdfasts</h2>
<ol>
<?php
    while ($row = mysqli_fetch_assoc($result)){
        echo "<li>" . $row['hname'] . " - " . $row['region_name'] . "</li>";
    }
?>
</ol>

<?php
    $con->close();
?>
