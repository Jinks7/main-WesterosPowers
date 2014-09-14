<?php
function get_left(){
    include("config.php");
?>
<ul>
    <b>MAIN</b>
    <hr style="margin-right:30px;margin-top:3px;margin-bottom:3px;"/>
    
    <a href="http://<?php echo $host; ?>/"><li>Home</li></a>
    <a href="http://<?php echo $host; ?>/?message"><li>Messages</li></a>
    <a href="http://<?php echo $host; ?>/?item"><li>Items</li></a>
    <a href="http://<?php echo $host; ?>/?region"><li>Region</li></a>
    <a href="http://<?php echo $host; ?>/?map"><li>Map</li></a>
    <a href="http://<?php echo $host; ?>/?holdfast"><li>Holdfast</li></a>
    <a href="http://<?php echo $host; ?>/?rules"><li>Rules</li></a>
    <a href="http://<?php echo $host; ?>/?setting"><li>Settings</li></a>
    <?php
        if (is_admin() || is_mod()){
    ?>
    <br/>
    <b><?php echo (is_admin()) ? "ADMIN" : "MOD"; ?></b>
    <hr style="margin-right:30px;margin-top:3px;margin-bottom:3px;"/>
    <a href="http://<?php echo $host; ?>/?<?php echo (is_admin()) ? "admin" : "mod"; ?>"><li><?php echo (is_admin()) ? "Admin" : "Mod"; ?></li></a>
    <?php // <a href="http://<?php echo $host; ?><?php ///?report"><li>Reports</li></a>?>
    
    <?php
        }
    ?>
    
</ul>
<ul>
    <b></b>
</ul>
<?php
}

function get_right(){
    // get the recent big events that have occured
    
    include("config.php");
    
    $con = new dbConnect;
    $con->connect();
    $results = $con->exec_query("SELECT * FROM `post` WHERE `region_id` = 0 ORDER BY `stickied` DESC, `timestamp` DESC  LIMIT 7");
    
    echo "<b>Recent Events</b><ul style='margin-left:-20px;'>";
    
    while ($row = mysqli_fetch_assoc($results)){
?>
        <li>[<?php echo $row['type']; ?>] <a href="http://<?php echo $host . "/?view=" . $row['post_id']; ?>"><?php echo $row['title']; ?></a></li>
<?php
    }
    
    echo "</ul>";
    
    $results = $con->exec_query("SELECT * FROM `post` WHERE `region_id` = " . $_SESSION['hold_region'] . " ORDER BY `stickied` DESC, `timestamp` DESC  LIMIT 7");
        
        echo "<b>Recent Events for " . $_SESSION['region_name'] . "</b><ul style='margin-left:-20px;'>";
        
        while ($row = mysqli_fetch_assoc($results)){
    ?>
            <li>[<?php echo $row['type']; ?>] <a href="http://<?php echo $host . "/?view=" . $row['post_id']; ?>"><?php echo $row['title']; ?></a></li>
    <?php
        }
        
        echo "</ul>";
    
    
    $con->close();
}
?>
