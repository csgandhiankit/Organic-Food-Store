<?php
session_start();
error_reporting(0);  // stops displaying warning from user end

include_once 'Scripts/config.php';
include_once 'Scripts/loginInfo.php';
include_once 'Scripts/helperScripts.php';

//set validation error flag as false
$error = false;

//check if form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
	$fname = mysqli_real_escape_string($conn, $_POST['firstname']);
	$lname = mysqli_real_escape_string($conn, $_POST['lastname']);
	$county = mysqli_real_escape_string($conn, $_POST['county']);
	$email = mysqli_real_escape_string($conn, $_POST['email']);
	$password = mysqli_real_escape_string($conn, $_POST['password']);
	$cpassword = mysqli_real_escape_string($conn, $_POST['cpassword']);
	
	//name can contain only alpha characters and space
	if (!preg_match("/^[a-zA-Z ]+$/",$fname)) {
		$error = true;
		$name_error = "Name must contain only alphabets and space";
	}
	if (!preg_match("/^[a-zA-Z ]+$/",$lname)) {
		$error = true;
		$name_error = "Name must contain only alphabets and space";
	}

	if(!filter_var($email,FILTER_VALIDATE_EMAIL)) {
		$error = true;
		$email_error = "Please Enter Valid Email ID";
	}
	if(strlen($password) < 6) {
		$error = true;
		$password_error = "Password must be minimum of 6 characters";
	}
	if($password != $cpassword) {
		$error = true;
		$cpassword_error = "Password and Confirm Password doesn't match";
	}

	if (!$error) {
		$conn = new mysqli($hn, $un, $pw, $db);
        if(!emailIsAvailable($conn, $email)){ //check if password is taken
        	$email_error = "Email is taken";
        }
        else{
        	$salt = randomString(10);
        	$auth = randomString(20);
        	$hashedPass = hash('sha256', $password . $salt);

        	$stmt = $conn->prepare("INSERT INTO users ( Email, firstName, lastName, County, Password, Salt, authtoken, admin ) VALUES (?,?,?,?, ?,?, ?, false)");
        	$stmt->bind_param('sssssss', $email, $fname, $lname, $county, $hashedPass, $salt, $auth);
        	$result = $stmt->execute();
        	$id = getUserID($conn, $email);
        	$stmt->close();
        	if($id){
        		startSession($auth, $id, false, $fname);
        		header('Location: index.php');
        	}
        }
        $conn->close();
    }
}


function getUserID($conn, $email){
	$stmt = $conn->prepare("select id from users where email = ?;");
	$stmt->bind_param('s', $email);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_array(MYSQLI_NUM);
	$stmt->close();
	if(!$row[0]) return false;
	return $row[0];
} 

//returns True is email is not taken
function emailIsAvailable($conn, $email ){
	$stmt = $conn->prepare("select * from users where email = ?");
	$stmt->bind_param('s', $email);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_array(MYSQLI_NUM);
	$stmt->close();
	if(!$row)return true;
	return false;
} 

?>

<!DOCTYPE html>
<html>
<head>
	<title>Register</title>
    <link rel="icon" type="image/png" href="images/icons/favicon.png"/>
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	<link rel="stylesheet" type="text/css" href="./css/login.css">
</head>
<body class="signin-body" style="margin-top: 0px; padding-top: 0px;">
	<div class="container-fluid signin-container" style="padding: 0px 0px 0px 0px !important;
		margin: 0px 0px 0px 0px !important;
		background-image: url('./images/signinbg.jpg');
		background-repeat: no-repeat;
		background-size: auto;
		height: 100vh;
		min-width: 100%;">
		<div class="row" style="margin-top: 5%; margin-left: 0px; margin-right: 0px;">
			<div class="col-md-6 col-md-offset-3">
				<div class="panel panel-login">
					<div class="panel-heading">
						<div class="row">
							<div>
								<a href="index.php" class="" id="">Home</a>
								<a href="#register" class="active" id="register-form-link">| Register</a>
							</div>
						</div>
						<hr>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-12">
								<form onsubmit="return CreateAccountValidate(this)" id="register-form" method="post" name="signup" role="form" style="display: block;" >
									<div class="form-group col-sm-6">
										<input type="text" name="firstname" id="firstname" class="form-control" required value="<?php if($error) echo $fname; ?>" placeholder="First Name" />
										<span class="text-danger"><?php if (isset($name_error)) echo $name_error; ?></span>
									</div>
									<div class="form-group col-sm-6">
										<input type="text" name="lastname" id="lastname" class="form-control" required value="<?php if($error) echo $lname; ?>" placeholder="Last Name" />
										<span class="text-danger"><?php if (isset($name_error)) echo $name_error; ?></span>
									</div>
									<div class="form-group col-sm-12">
										<input type="email" name="email" id="email" class="form-control" required value="<?php if($error) echo $email; ?>" placeholder="Email Address" />
										<span class="text-danger"><?php if (isset($email_error)) echo $email_error; ?></span>
									</div>
									<div class="form-group col-sm-6">
										<input type="password" name="password" id="password" required class="form-control" placeholder="Password">
										<span class="text-danger"><?php if (isset($password_error)) echo $password_error; ?></span>
									</div>
									<div class="form-group col-sm-6">
										<input type="password" name="cpassword" id="confirm-password" required class="form-control" placeholder="Confirm Password">
										<span class="text-danger"><?php if (isset($cpassword_error)) echo $cpassword_error; ?></span>
									</div>
									<div class="form-group col-sm-6 custom-select custom-select-lg mb-3">
<!-- 										<label for="inputState">County</label>
-->										<select id="county" name="county" class="form-control" required class="form-control" 											placeholder="County">
	<option selected>Choose County...</option>
	<option>Santa Clara</option>
	<option>San Mateo</option>
</select>
</div>
<div class="form-group">
	<div class="row">
		<div class="col-sm-6 col-sm-offset-3">
			<input type="submit" name="register-submit" id="register-submit" class="form-control btn btn-register" value="Register Now">
		</div>
	</div>
</div>
</form>
<span class="text-success"><?php if (isset($successmsg)) { echo $successmsg; } ?></span>
<span class="text-danger"><?php if (isset($errormsg)) { echo $errormsg; } ?></span>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="row">
	<div class="col-md-4 col-md-offset-4 text-center">	
		<p style='color:#000; display:inline;' >Already Registered? </p><a href="signin.php">Login Here</a>
	</div>
</div>

</div>
<script type="text/javascript" src="vendor/jquery/jquery-3.2.1.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript" src="./js/login.js"></script>
<script type="text/javascript" src="./js/authenticate.js"></script>
</body>
</html>