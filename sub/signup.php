<?php

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (isset($_SESSION['login']) && $_SESSION['login'] == true){
	header("LOCATION: .");
}


if (isset($_POST['sign'])){
	include("pscripts/request.php");
	
	$signup = signup();
	
	if ($signup){
		// redirect to
		include("success.php");
		die();
	}
}


?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="shortcut icon" href="/s/imgs/favicons/favicon.ico">
		<link rel="stylesheet" href="/s/style/main.css">
		
		<title>Signup - WesterosPowers</title>
		
		<script src="/s/scripts/timezone.js"></script>
		<script>
			function changeValue(){
				var select = document.getElementById("gender");
				var input = document.getElementById("rpname");
				
				var text = select.options[select.selectedIndex].text;
				
				if (text === "Choose gender:"){
					input.placeholder = "";
				} else {
					input.placeholder = text;
				}
			}
			
			function getTimezone(){
				document.getElementsByName("timezone")[0].value = new Date().getTimezone();
			}
			
			// check the inputs to submit to the server
			function check(event){
				var m = document.getElementsByTagName("select");
				for (i=0;i<m.length;i++){
					try {
						validate(m[i]);
					} catch(e){
						
					}
				}
				
				var m = document.getElementsByTagName("input");
				for (i=0;i<m.length;i++){
					try {
						validate(m[i]);
					} catch(e){
						
					}
				}
				
				// check if there were errors and return accordingly
				var returnType = true;
				
				var m = document.getElementsByClassName("arrow_box");
				for (i=0;i<m.length;i++){
					if (m[i].style.display == "block"){
						returnType = false;
					}
				}
				return returnType;
				
			}
			
			function validate(input){
				
				var type = input.type;
				var display = false;
				var text = "";

				if (type == "email"){
					// check that it is a proper email format
					var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
					
					if (input.value.trim() == ""){
						input.value = "";
						display = true;
						text = "This is a required field.";
					} else if (!filter.test(input.value)){
						display = true;
						text = "The email you entered is not a valid email.";
					}
					// could check the email in the database?
				} else if (type == "password"){
					// check if both the passwords are correct
					var pass1 = document.getElementsByName("pword")[0].value;
					var pass2 = document.getElementsByName("rpword")[0].value;
					if (input.name == "pword"){
						if (pass1 == ""){
							display = true;
							text = "This is a required field.";
						} else if (pass1.length < 6){
							
							display = true;
							text = "The password has to be at least 6 characters long.";
						}
					} else {
						if (pass1 !== pass2){
							display = true;
							text = "The passwords do not match. Try re-entering your password in both fields.";
							
						}
					}
					
				} else if (type == "text"){
					if (input.value.trim() === ""){
						input.value = "";
						display = true;
						text = "This is a required field.";
					}
				} else if (input.name == "hname"){
					
					if (input.value.trim() == ""){
						display = true;
						text = "This is a required field.";
						input.style.color = "#999";
					} else if (input.value.trim() == "NONE") {
                        display = true;
                        text = "We apologize, all the house names are currently taken.</br>Contact us at westerospowers@gmail.com and we will try to accomodate you.";
					} else {
						input.style.color = "#000";
					}
					
				} else if (input.name == "gen"){
					if (input.value.trim() == ""){
						display = true;
						text = "This is a required field.";
						input.style.color = "#999";
					} else if (!(input.value == "m" || input.value == "f")){
						display = true;
						text = "Are you trying to change the select values? Shame on you.";
					} else {
						input.style.color = "#000";
					}
				}
				
				var element = input.parentNode.parentNode.childNodes[2]; // selects the error element
				if (display){
					var size = input.getBoundingClientRect();
					element.style.left = size.right + 20 + "px";
					element.innerHTML = text;
					// display element
					element.style.display = "block";
					//element.style.top = element.getBoundingClientRect().top + 2 + "px";

						
				} else {
					element.style.display = "none";	
				}				
				
			}	

			function resize(){
				var m = document.getElementsByTagName("input");
				for (i=0;i<m.length;i++){
					try {
						var element = m[i].parentNode.parentNode.childNodes[2]; // selects the error element
						var size = m[i].getBoundingClientRect();
						element.style.left = size.right + 20 + "px";
					} catch(e){
						
					}
				}
				var m = document.getElementsByTagName("select");
				for (i=0;i<m.length;i++){
					try {
						var element = m[i].parentNode.parentNode.childNodes[2]; // selects the error element
						var size = m[i].getBoundingClientRect();
						element.style.left = size.right + 20 + "px";
					} catch(e){
						
					}
				}
			}
			
		</script>
	</head>
	<body onload="getTimezone();" onresize="resize();" style="padding-top:50px;">
		<div id="wrapper">
			<div id="content">
				<form action="" method="POST">
					<table class="main register">
						<tr><td><img src="/s/imgs/WP_side.png" width="70%"></td></tr>
						<tr><td class="title">Please take the time and register for WP:</td></tr>
						<tr><td class="note">(We will guide you through the process and teach you how to play)</td></tr>
						<tr>
							<?php
								if (isset($_SESSION['serror']) && $_SESSION['serror'] != ""){
									echo '<td id="error">' . $_SESSION['serror'] . '</td>';
									unset($_SESSION['serror']);
								}
								
								include("config.php");
								if (!$signup){
								    ?>
								    <td id="error">Sorry we are not allowing anyone to sign up at this time. <a href="/" style="color:white;">Click here to go back.</a></td>
								    </tr>
								    <?php
								} else {
								
							?>
						</tr>
						<tr><td>
							<table id="form-layout">
								<tr><hr/></tr>
								
								<tr><td></td><td></td><td></td></tr> <!-- This is needed to stop the table from resizing -->
								
								<tr><td class="right">Email: </td><td class><input type="email" name="email" onblur="validate(this);" placeholder="Email"/></td><td class="arrow_box"></td></tr>
								<tr><td class="right">First Name: </td><td><input type="text" name="fname" onblur="validate(this);" placeholder="First Name"/></td><td class="arrow_box"></td></tr>
								<tr><td class="right">Last Name: </td><td><input type="text" name="lname" onblur="validate(this);" placeholder="Last Name"/></td><td class="arrow_box"></td></tr>
								<tr><td class="right">Password: </td><td><input type="password" name="pword" oninput="validate(this);" onblur="validate(this);" placeholder="Password"/></td><td class="arrow_box"></td></tr>
								<tr><td class="right">Retype Password: </td><td><input type="password" name="rpword" oninput="validate(this);" onblur="validate(this);" placeholder="Password"/></td><td class="arrow_box"></td></tr>
								<tr><td><br/></td></tr>
								<tr><td><b>Character: </b></td></tr>
								<tr><td class="right">Gender: </td><td>
									<select name="gen" id="gender" onchange="changeValue();validate(this);" onblur="validate(this);">
										<option value="" style="display:none;">Choose gender:</option>
										<option value="m">Lord</option>
										<option value="f">Lady</option>
									</select>
								</td><td class="arrow_box"></td></tr>
								<tr><td class="right">Role-playing Name: </td><td><input type="text" name="rpname" id="rpname" onblur="validate(this);" placeholder=""/></td><td class="arrow_box"></td></tr>
								<tr><td class="right">House Name: </td><td>
									<select name="hname" onchange="validate(this);" onblur="validate(this);">
										<?php
											// generate list of the houses
											include_once("pscripts/db.php");
											
											$con = new dbConnect;
											$con->connect();
											
											// execute the query
											$result = $con->exec_query("SELECT house_id, house_name FROM `house` WHERE `taken` = '0' ORDER BY `house_name`");
											
											if ($result->num_rows == 0){
											    echo "<option default style=\"isplay:none;\" value=\"NONE\">No houses available</option>";
											} else {
    											// print out each of the options
    											echo '<option value="" style="display:none;">Choose your house name: </option>';
    											while ($row = $result->fetch_assoc()){
    												echo '<option value="' . $row['house_id'] . '">' . $row['house_name'] . "</option>";
    											}
											}
											
											$con->close();
										?>
										
									</select>
								</td><td class="arrow_box"></td></tr>
							</table>
						</td></tr>
						<input type="hidden" name="timezone" value="">
						<tr><td class="submit"><a href="..">Back</a><input type="submit" name="sign" value="Sign Up!" onclick="return check(this);"/></td></tr>
						<tr><td><i style="font-size:13px;">Note: By signing up you are agreeing to our terms and privacy conditions.</i></td></tr>
						<?php
								}
						?>
					</table>
					
				</form>
			</div>
			
			<?php
					include_once("footer.php");
					get_footer(true);
			?>
			
		</div>
		
		
	</body>
	
	<?php
		include("includes/no_js.php");
	?>
	
</html>