<?php

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (isset($_POST['log'])){
	// the user is trying to log in
	include("pscripts/request.php");
	
	if (isset($_POST['user']) && isset($_POST['pass'])){
		// login
		$logged = login($_POST['user'], $_POST['pass']);
		
		if ($logged){
		    header("LOCATION: ./");
		} else {
		    if (isset($_SESSION['registered']) && $_SESSION['registered'] == 0){
                $_SESSION['error'] = "Sorry but you need to register your email before you can login";
            } else {
		    	$_SESSION['error'] = "Incorrect login details! Please try again.";
            }
            
            include_once("utilities.php");
            $_GET['uname'] = parse_xss($_POST['user']);
            //$_GET['uname'] = parse_xss($_POST['user']);
		}
	} else {
		
	}
	
}

if (isset($_SESSION['login']) && $_SESSION['login'] == true){
	header("LOCATION: .");
}

?>

<!DOCTYPE html>
<html>
	<head>
		<link rel="shortcut icon" href="/s/imgs/favicons/favicon.ico">
		<link rel="stylesheet" href="/s/style/main.css">
	
		<title>Login - WesterosPowers</title>
		
		<script>
			// onload check what javascript functions the user's browser can handle and display errors accordingly
			function userBrowser(){
				// features this website needs
				// it needs canvas
				
				// fill this in later
			}
			
			function login(form){
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
				var display = false;
				var text = "";
				
				if (input.type == "text"){	

					var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
					
					if (input.value.trim() == ""){
						input.value = "";
						display = true;
						text = "Please enter your email.";
					} else if (!filter.test(input.value)){
						display = true;
						text = "The email you entered is not a valid email.";
					}
				
					
				} else if (input.type == "password"){
					if (input.value.trim() == ""){
						display = true;
						text = "Please enter your password.";
					}				
				
				}
				
				var element = input.parentNode.parentNode.parentNode.childNodes[1]; // selects the error element
				if (display){
					var size = input.getBoundingClientRect();
					element.style.left = size.right + 20 + "px";
					element.innerHTML = text;
					// display element
					element.style.display = "block";
					element.style.top = size.top + "px";

						
				} else {
					element.style.display = "none";	
				}		
			}
			
			function resize(){
				var m = document.getElementsByTagName("input");
				for (i=0;i<m.length;i++){
					try {
						var element = m[i].parentNode.parentNode.parentNode.childNodes[1]; // selects the error element
						var size = m[i].getBoundingClientRect();
						element.style.left = size.right + 20 + "px";
					} catch(e){
						
					}
				}
			}
			
		</script>
	</head>
	<body class="login" onload="userBrowser();" onresize="resize();">
		<div id="wrapper">
			<div id="head">
				<!-- This is the header div -->
			</div>
			<div id="content">
				<!-- This is the main content div -->
			
				<form method="POST" action="">
					<table class="main login">
						<tr><td><img src="/s/imgs/WP_side.png" width="70%"></td></tr>
						<tr><td>Play against your friends in this text-based game inspired by Game of Thrones</td></tr>
						<?php
						    include("config.php");
						    if ($login){
						?>
						<tr>
						<?php
							if (isset($_SESSION['error']) && $_SESSION['error'] != ""){
									echo '<td id="error">' . $_SESSION['error'] . '</td>';
									unset($_SESSION['error']);
							} 
							if (isset($_GET['banned'])) {
							    echo '<td id="error">Sorry, you have been banned.</td>';
							}
						?>
						</tr>
						<tr><td></td><td></td></tr>
						<tr><td><div id="text-right"><input name="user" id="userinput" type="text" oninput="validate(this);" onblur="validate(this);" class="login-input" placeholder="Username" spellcheck="false" value="<?php if(isset($_GET['uname'])){echo htmlspecialchars($_GET['uname']);}?>"/></div></td><td class="arrow_box"></td></tr>
						<tr><td><div id="text-right"><input name="pass" type="password" oninput="validate(this);" onblur="validate(this);" class="login-input" placeholder="Password" <?php if(isset($_GET['uname'])) echo 'autofocus'; ?>/></div></td><td class="arrow_box"></td></tr>
						<tr><td><div id="text-right">Don't have an account? <a href="./signup/">Sign up</a><input type="submit" name="log" onclick="return login(this.parentNode);" value="Login" /></div></td></tr>
						<?php
						    } else {
						?>
						    <tr>
						        <td id="error" style="width:280px;">Sorry the server is down for maintainance.</td>
                            </tr>
						<?php
						    }
						?>
					</table>
				</form>
			</div>
			
		</div>
		
		<!-- This is the footer div (Will always be at the bottom of the page -->
		<?php
				include("footer.php");
				get_footer(true);
		?>
		
	</body>
	
	<?php
		include("includes/no_js.php");
	?>
	
</html>