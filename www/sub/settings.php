<?php
function get_settings(){
?>
<h2>Settings</h2>
<form method="POST" action="/?setting&change">
    <h3>Change Password</h3>
    <input type="password" name="current" placeholder="Current Password" /><br/>
    <input type="password" name="new" placeholder="New Password" /><br/>
    <input type="password" name="retype" placeholder="Retype Password"/><br/>
    <input type="submit" value="Change Password"/>
</form>

<?php
if (isset($_GET['change'])){
    
    $con = new dbConnect;
    $con->connect();
    
    $current = $con->input(trim($_POST['current']));
    $new = $con->input(trim($_POST['new']));
    $retype = $con->input(trim($_POST['retype']));
    
    if ($new != $retype){
        echo "Your new passwords didn't match.";
    } elseif ($new == "" || $retype == "" || $current == ""){
        echo "You need to fill in all the inputs";
    } else {
        
        //include("./pscripts/request.php");
        
        $currentpass = create_hash($current);
        $new = create_hash($new);
        
        // check the password against the database, 
        // if it matches change the password
        $result = $con->exec_query("SELECT * FROM `user` WHERE `user_id` = '" . $_SESSION['userid'] . "' AND `password` = '" . $currentpass . "'");
        
        if ($result->num_rows < 1){
            echo "You password confimation is wrong.";
        } else {
            // everything is okay
            
            $con->exec_query("UPDATE `user` SET `password` = '" . $new . "' WHERE `user_id` = '" . $_SESSION['userid'] . "'");
            
            echo "Success!";
            
        }
    }
    
    $con->close();
    
}
// change your password

}
?>