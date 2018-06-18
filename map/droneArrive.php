<?php
session_start();

require_once '../Scripts/loginInfo.php';

$userID =  $_SESSION["id"];
$conn = new mysqli($hn, $un, $pw, $db);

$sql = "delete from drones where userID = $userID"; 
$conn->query($sql);
$conn->close();

setcookie("startTime", $droneInfo["startTime"], time()-3600);
setcookie("userAddress",$droneInfo["address"], time()-3600);
setcookie('countyAddress', $countyLocation,  time()-3600);

header('Location: ../');

?>
