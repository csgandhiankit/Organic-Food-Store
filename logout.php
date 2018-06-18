<?php
session_start();

include_once 'Scripts/helperScripts.php';
endSession();
header('Location: index.php');

?>