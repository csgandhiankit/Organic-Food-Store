<?php 
session_start();
error_reporting(0);  // stops displaying warning from user end
require_once 'Scripts/loginInfo.php';
require_once 'Scripts/helperScripts.php';
require_once 'Scripts/authenticate.php';
if(!SessionIsValid()){
    header('Location: signin.php');
}
$page = "subParts/order.php";
$onOrder = '';
$onAddress = '';
$errors = array("", "");
$number = "authenticateCreditCard($cardNumber, $month, $year);"; 
if(isset($_POST['SubmitOrder'])){ //order form signed 
    $page = "subParts/address.php";
    $onOrder = 'step_complete';
}
if(isset($_POST['submiteAdress'])){ //address form signed 
    
    $conn = new mysqli($hn, $un, $pw, $db);
    if(!$conn->connect_error){
        $page = "subParts/card.php";
        $onOrder = 'step_complete';
        $onAddress = 'step_complete';
       
    
        $seesionTime = time() + $GLOBALS['SESSION_TIME']; 
        $shippingAddress = $_POST['address'];
        $exploded = explode(" ", $shippingAddress);
        $ziploc = count($exploded);
        $zipCode = $exploded[$ziploc-1];
        
        setcookie('userAddress',  $shippingAddress, $seesionTime, "/");
    
        $id = $_SESSION['id'];                
        $countyLocation = getCountyCordinates($id, $conn);
        setcookie('countyAddress',  $countyLocation, $seesionTime, "/");
        
        $countyName = getCountyName($id, $conn);
        
        if(!authenticateTest($zipCode, $countyName)){
         $url = "index.php";
         header('Location: '.$url);   
        }
    }
}
if(isset($_POST['cardSubmitted'])){
    $page = "subParts/card.php";
    $onOrder = 'step_complete';
    $onAddress = 'step_complete';
    
    $cardNumber = $_POST["cardNumber"];
    $month = $_POST["month"];
    $year = $_POST["year"];
    $errors = authenticateCreditCard($cardNumber, $month, $year); 
    if($errors[0] == "" && $errors[1]=="") {
        $_SESSION['orderSubmitted'] = true;
        header('Location: map');
    }
}
$processBar = ' <span class="step step_complete"> 
                    <a href="#" class="check-bc">Cart</a>
                    <span class="step_line step_complete"></span>
                </span>
                
                <span class="step '.$onOrder.'"> 
                    <a href="#" class="check-bc">Address</a> 
                    <span class="step_line "></span>
                    <span class="step_line '.$onOrder.'"></span>
                </span>
                <span class="step '.$onAddress.'"> 
                    <a href="#" class="check-bc">Card</a> 
                    <span class="step_line '.$onAddress.'"></span>
                </span>
               ';
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
function getCountyName($userID, $conn){
        $sql = "select c.Name from users u, supportedCountys c where c.Name = u.County and u.Id = $userID";
        $result = $conn->query($sql);
        $result->data_seek(0);
        $obj = $result->fetch_array(MYSQLI_ASSOC);
    
        $name = $obj['Name'];
    
        $result->close();
        return $name;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Checkout</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/icons/favicon.png"/>
<!--    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">-->
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" type="text/css" href="fonts/themify/themify-icons.css">
    <link rel="stylesheet" type="text/css" href="fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/elegant-font/html-css/style.css">
    <link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
    <link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/slick/slick.css">
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="vendor/sweetalert/sweetalert.min.js"></script>
    <script type="text/javascript" src="js/authenticate.js"></script>
    <link rel="stylesheet" href="css/checkout.css" >
    

  </head>  
<body class="animsition" style="padding:0">
    <?php 
        include "nav.php" 
    ?>

<section class="bg-title-page p-t-40 p-b-50 flex-col-c-m" style="background-image: url(images/healthy.jpg);">
        <h2 class="l-text2 t-center">
            It Is Time to Eat!
        </h2>
    </section>


<!-- Include the above in your HEAD tag -->
<div class="container wrapper">
            <div class="row cart-head">
                <div class="container">
                <div class="row">
                    <p></p>
                </div>
                <div class="row">
                    <div style="display: table; margin: auto;">
                        <?php echo $processBar?>
                    </div>
                </div>
                <div class="row">
                    <p></p>
                </div>
                </div>
            </div>    
            <div class="row cart-body">
<!--                <form class="form-horizontal" method="post" action="">-->
                    <!-- old location of order-->
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 ">
                    <!--SHIPPING METHOD-->
                    <?php 
                        require_once $page;
                    ?>
                </div>
                
<!--                </form>-->
            </div>
            <div class="row cart-footer">
        
            </div>
    </div>
    
        <div class="btn-back-to-top bg0-hov" id="myBtn">
        <span class="symbol-btn-back-to-top">
            <i class="fa fa-angle-double-up" aria-hidden="true"></i>
        </span>
    </div>
    
    
    <?php
        require_once 'footer.html';
    ?>

    <!-- Container Selection -->
    <div id="dropDownSelect1"></div>
    <div id="dropDownSelect2"></div>

<!--===============================================================================================-->
    <script type="text/javascript" src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
    <script type="text/javascript" src="vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
    <script type="text/javascript" src="vendor/bootstrap/js/popper.js"></script>
    <script type="text/javascript" src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
    <script type="text/javascript" src="vendor/select2/select2.min.js"></script>
    <script type="text/javascript">
        $(".selection-1").select2({
            minimumResultsForSearch: 20,
            dropdownParent: $('#dropDownSelect1')
        });
        $(".selection-2").select2({
            minimumResultsForSearch: 20,
            dropdownParent: $('#dropDownSelect2')
        });
    </script>
<!--===============================================================================================-->
    <script src="js/main.js"></script>
    
</body>
</html>