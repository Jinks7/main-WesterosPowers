<!-- This is the standard footers -->

<?php
//if (isset($_SESSION))

function get_footer($main){
    include("config.php");
    if ($main){
        echo "<div id=\"footer\" class=\"front\">";
        echo '<a href="/f/terms?terms">Terms</a> <a href="/f/terms?privacy">Privacy</a> <i>&copy WesterosPowers ' . date("Y") . '</i>';
        echo "</div>";
    } else { // the other footer
?>

<div id="footer" class="main">
    <a href="http://<?php echo $host; ?>/f/terms?terms">Terms</a> | <a href="http://<?php echo $host; ?>/f/terms?privacy">Privacy</a> | <a href="../bugs/">Submit Bug</a> | 
    <?php
        include_once("utilities.php");
        
        // if the user is a mod or admin 
        // they have access to a "Request Feature" option
        if (is_mod() || is_admin()){
            
            echo '<a href="../features/">Request a Feature</a> | ';
            
        }
        
    ?>
    <i>&copy WesterosPowers <?php echo date("Y"); ?> | Created by Hayden Mack</i>
</div>
        
<?php  
    }
    
}

?>