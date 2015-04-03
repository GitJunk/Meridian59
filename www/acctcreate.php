<?php

/* Meridian 59 Server Account Creation Script
 * Written by Daenks (daenks@daenks.org) (c) 2013
 * You may use this software as is with no guarantee or warranty
 * You may distribute this software. You may modify it to meet your needs.
 * Do not remove this header block.
 */

function display_form() { ?>
	<style type="text/css">
		td {
			font-size: small;
			font-family: Verdana, Arial, Helvetica;
			color: #ffffff;
			background: #000000;
		}
		
		body {
			font-size: small;
			font-family: Verdana, Arial, Helvetica;
			color: #ffffff;
			background: #000000;
		}
	</style>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<table>
			<tr>
				<td align="left">Username:</td>
			</tr>
			
			<tr>
				<td>
					<input type="text" name="username" size="16">
				</td>
			</tr>
			
			<tr>
				<td align="left">Password:</td>
			</tr>
			
			<tr>
				<td>
					<input type="password" name="password1" size="16">
				</td>
			</tr>
			
			<tr>
				<td align="left">Confirm Password:</td>
			</tr>
			
			<tr>
				<td>
					<input type="password" name="password2" size="16">
				</td>
			</tr>
			
			<tr>
				<td>
					<input type="submit" name="submit" text="Create">
				</td>
			</tr>
		</table>
	</form>
<?php }

function validate_input() {	
	$error = false;
	
	if(!(strlen($_POST['username']) > 3)) {
		echo "<b><font color=\"red\">Username must be at least 4 characters</font></b><br>";
		$error = true;
	}
	
	if(!(strlen($_POST['password1']) > 5)) {
		echo "<b><font color=\"red\">Password must be at least 6 characters</font></b><br>";
		$error = true;
	}
	
	if(!($_POST['password1'] == $_POST['password2'])) {
		echo "<b><font color=\"red\">Password fields must match</font></b><br>";
		$error = true;
	}
	
	if(!preg_match('/[a-z]/',strtolower($_POST['username']))) {
		echo "<b><font color=\"red\">Username must contain at least one letter</font></b><br>";
		$error = true;
	}
	
	if($error) display_form();
	else check_account_exists();
}

function check_account_exists() {
	$username = strtolower(trim($_POST['username']));
	$command = "show account $username\r\n";
	
	$sock = fsockopen("127.0.0.1", 5900, $errornumber, $errorstring, 30);
	if(!$sock) die("Error $errornumber: $errorstring");
	
	fputs($sock, $command);
	sleep(1);
	
	$result = fread($sock, 1000);
	fclose($sock);
	
	if(strpos($result, "Cannot find account")) create_account();
	
	else {
		echo "<b><font color=\"red\">That account name is taken, please try again.</font></b><br>";
		display_form();
	}
}

function create_account() {
	$username = strtolower(trim($_POST['username']));
	$password = trim($_POST['password1']);
	$command = "create account user $username $password\r\n";
	
	$sock = fsockopen("127.0.0.1", 5900, $errornumber, $errorstring, 30);
	if(!$sock) die("Error $errornumber: $errorstring");
	
	fputs($sock, $command);
	sleep(1);
	
	$result = fread($sock, 1000);
	$lines = explode("ACCOUNT", $result);
	$accountID = trim(trim($lines[1]), ".");
	
	$command = "create user $accountID\r\n";
	
	fputs($sock, $command);
	sleep(1);
	$result = fread($sock, 1000);
	
	fputs($sock, $command);
	sleep(1);
	$result = fread($sock, 1000);
	
	fputs($sock, $command);
	sleep(1);
	$result = fread($sock, 1000);
	
	fputs($sock, $command);
	sleep(1);
	$result = fread($sock, 1000);
	
	echo "<b><font color=\"blue\">Account $username created with four character slots.</font></b><br>";
}
?>

<html>
	<head>
		<title>Meridian 59 - Server 112 - Account Creator</title>
	</head>
	
	<body>
		<?php
			if(!isset($_POST['submit'])) { display_form(); }
			else { validate_input(); }
		?>
	</body>
</html>