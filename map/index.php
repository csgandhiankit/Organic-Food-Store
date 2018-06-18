<?php
session_start();
error_reporting(0); // stops displaying warning from user end

    require_once '../Scripts/loginInfo.php';
    require_once '../Scripts/helperScripts.php';


    $userID =  $_SESSION["id"];
    $conn = new mysqli($hn, $un, $pw, $db);

    if(!SessionIsValid()){ //is user logged in
        header('Location: ../signin.php');
    }
    if(!isset($_SESSION['orderSubmitted'])){ //user did not come from check out form
        
        $droneInfo = getDrone($userID, $conn);
        
        // user does not have any drones in the db
        if(!$droneInfo){ 
            header('Location: ../checkout.php');
        }
        
        //user has drones in the db
        else{
            $countyLocation = getCountyCordinates($userID, $conn);
            setcookie("startTime", $droneInfo["startTime"], time()+(60*60));
            setcookie("userAddress",$droneInfo["address"], time()+(60*60));
            setcookie('countyAddress', $countyLocation,  time()+(60*60));
        }
    }
    else{
        unset($_SESSION['orderSubmitted']);
        dropCart($conn, $userID);
        sendDrone($conn, $userID); 
    }


function getCountyCordinates($userID, $conn){
        $sql = "select c.latitude, c.longitude from users u, supportedCountys c where c.Name = u.County and u.Id = $userID;";
        $result = $conn->query($sql);
        $result->data_seek(0);
        $obj = $result->fetch_array(MYSQLI_ASSOC);
    
        $lat = $obj['latitude'];
        $long = $obj['longitude'];
    
        $result->close();
        return $lat.",".$long;
    }

function sendDrone($conn, $userID){
    
    $time = ceil(microtime(true)*1000); //current time in milisec
    $address = $_COOKIE["userAddress"];
    setcookie("startTime", $time, time()+(60*60));
         
    deployDrone($conn, $time, $address, $userID);
}

function deployDrone($conn, $time, $address, $userID){
    $sql = "insert into drones(address, startTime, userID) values ('$address', $time, $userID)"; 
    if($conn->query($sql)){
        return true;
    }else return false;
}

function getDrone($userID, $conn){
        $sql = "select Id, address, startTime from drones where userID = $userID order by Id desc";
        $result = $conn->query($sql);
        $result->data_seek(0);
        $obj = $result->fetch_array(MYSQLI_ASSOC);
        $droneInfo = $obj;
        $result->close();
        return $droneInfo;
    }


function dropCart($conn, $userID){
    $sql = "delete from Cart where userID = $userID"; 
    if($conn->query($sql)){
        return true;
    }else return false;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">

<head>
    <link rel="icon" type="image/png" href="../images/icons/favicon.png"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>Google Maps</title>
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=AIzaSyCHdx-m6xOsxi9Un8XTSvjAZjUzl1qLX6Q" type="text/javascript"></script>
    <script type="text/javascript" src="../vendor/sweetalert/sweetalert.min.js"></script>
    <script src="epoly.js" type="text/javascript"></script>
</head>

    <body onunload="GUnload()" style="width: 100%; height: 100%; margin:0">
            <div id="map" style="width: 100%; height: 100%; position: absolute;"></div>
                <h1 id="timer" style= "position: absolute; bottom: 20px; margin-left: auto; margin-right: auto; left: 0; right: 0; text-align: center"></h1>

  </body>
</html>
<script src="map.js" type="text/javascript"></script>
<script type="text/javascript">
if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>