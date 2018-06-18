<?php

function randomString($len){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $len; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function stringisSafe($s){
    if(strlen($s) > 0) return true; 
    return false;
}

function removeHTML($input){
    $input = stripslashes($input);
    $input = htmlentities($input);
    return $input;
}

//--- Session handeler ---///
$SESSION_TIME = 86400 * 30; // one day

function startSession($token, $id, $isAdmin, $name){
    $seesionTime = time() + $GLOBALS['SESSION_TIME']; 
    $cookie_name = 'token';
    $cookie_value = $token;
    
    setcookie($cookie_name, $cookie_value, $seesionTime, "/");
    setcookie('id', $id, $seesionTime, "/");

    $_SESSION['id'] = $id;
    $_SESSION['admin'] = $isAdmin;
    $_SESSION['name'] = $name;
    $_SESSION['check'] = hash('ripemd128', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $token);
}

function endSession(){
    $_SESSION = array();
    setcookie(session_name(), '', time() - 2592000,  "/");
    session_destroy();
}

function SessionIsValid(){  
    if(!isset($_COOKIE['token']) || !isset($_SESSION['check']) || !isset($_SESSION['id'])) return false; 
    
    $hash = hash('ripemd128', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $_COOKIE['token']);
    if($hash !== $_SESSION['check']){
        endSession();
        return false; 
    } 
    return true; 
}

///------validation---///

//global varibales
$VIRUS_NAME_MIN_LENGTH = 5; 
$USER_MIN_LENGTH = 5; 
$PASWORD_MIN_LENGTH = 5;



function validate_userName($username){
    $usernameMinLength = $GLOBALS['USER_MIN_LENGTH'];

    if($username =="") 
        return "No username was entered <br>"; 
    if(strlen($username) < $usernameMinLength) 
        return "User name must be atleast $usernameMinLength characters long <br>"; 
    if(preg_match("/[^a-zA-Z0-9_-]/", $username))
        return "username may only have letters, numbers, -, or _ <br>";
    return ""; 
}

function validate_password($password){
    $passwordMinLength = $GLOBALS['PASWORD_MIN_LENGTH'];
    
    if($password =="") 
        return "No password was entered <br>"; 
    if(strlen($password) < $passwordMinLength) 
        return "Password must be atleast $passwordMinLength characters long <br>";        
    if(!preg_match("/[a-z]/", $password) || !preg_match("/[A-Z]/", $password) ||!preg_match("/[0-9]/", $password))
        return "Passwords must have lowercase, uppercase, and a number<br>";
    return "";
}

function validate_email($email){
    if($email == "")
        return "No username was entered <br>";
    if( !((strpos($email, ".") > 0) && (strpos($email, "@") > 0)) && preg_match("/[a-z]/", $email)) 
        return "The Email address is invalid <br>";
}


?>
