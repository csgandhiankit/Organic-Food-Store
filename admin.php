<?php
    session_start();
    error_reporting(0);  // stops displaying warning from user end

        //$old_submit = $_['Submit']; // this gets the submit variable you appended in your form
        $current_url = 'admin.php';
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            header("Location: $current_url");
        }    
ini_set('display_errors', 1);
//error_reporting(0); // stops displaying warning from user end    

    require_once 'Scripts/loginInfo.php';
    require_once 'Scripts/helperScripts.php';


    //
    if(!SessionIsValid() || !$_SESSION['admin']){
        header('Location: signin.php');
    }
    if(!$_SESSION['admin']){ //checks if admin logged in
        header('Location: index.php');
    }


    //---------handles input from user and saves it to db -------//

    $conn = new mysqli($hn, $un, $pw, $db); //connects to db
    $userID = $_SESSION["id"];
    $countyID = getCountyId($userID, $conn);
    
    if(isset($_POST['Submit'])){ //form submitted
       
        $output = $_POST['Submit'];
        $value = explode(",", $output);
        $itemID = $value[0];
        $buttonPressed = $value[1];
        
        if($buttonPressed == "add"){
            $inputVal = $itemID."amount";
            addCart($conn, $_POST[$inputVal], $itemID);
        }
        if($buttonPressed == "subtract"){
            $inputVal = $itemID."amount";
            subtractCart($conn, $_POST[$inputVal], $itemID);
        }
    }

    //--------gets items from db and displays them-------//

    $countyItems = '';

    if (!$conn->connect_error){ //checks if connected succesfully 
        $sql = "select Id, Name, Amount, itemName from Items where countyID = $countyID";
        $result = $conn->query($sql);
        $rows = $result->num_rows;
        
        //loops through all the rows and saves the data from the columns 
        for($i=0; $i < $rows; $i++){
            $result->data_seek($i);
            $obj = $result->fetch_array(MYSQLI_ASSOC);
            $countyItems .= generate($obj['itemName'], $obj['Amount'], $obj['Id']); 
        }
        $result->close();
        $conn->close();
    }

    else $countyItems = "<h2>We are experiencing server error</h2>";

    // gets county Name
    $countyName = "San Mateo";
    $conn = new mysqli($hn, $un, $pw, $db); //connects to db
    if (!$conn->connect_error){ 
        $countyName = getCountyName($userID, $conn);
        $conn->close();
    }
    
    
            
    

    //@desc: generates html code to display a new row inside the cart table 
    function generate($name, $amount, $itemID){ 
        
        return '<tr style ="height:35px">
                    <td>'.$name.'</td>
                        <td>
                            <textarea name="'.$itemID.'amount" type="number" rows = "1" onkeyup="this.value=this.value.replace(/[^\d]/,\'\')">'.$amount.'</textarea>
                        </td>
                        <td>
                            <button  name="Submit" type="submit" value="'.$itemID.',add" class="btn btn-sm btn-primary btn-block" role="button">Add to Inventory</button>
                        </td>
                        <td>
                            <button name="Submit" type="submit" value="'.$itemID.',subtract" class="btn btn-sm btn-primary btn-block" role="button">Subtract from Inventory</button>
                        </td>
                </tr>';
    }

    function updateCart($conn, $amount, $itemID){
        $sql = "update Items set Amount = $amount where Id = $itemID";
        if($conn->query($sql)){
            return true;
        }else return false;
    }

    function subtractCart($conn, $amount, $itemID){
        $sql = "update Items set Amount = Amount - $amount where ID = $itemID";
         if($conn->query($sql)){
            return true;
        }else return false;
    }

    function addCart($conn, $amount, $itemID){
        $sql = "update Items set Amount = Amount + $amount where ID = $itemID";
         if($conn->query($sql)){
            return true;
        }else return false;
    }


    function getCountyId($userID, $conn){
        $sql = "select c.Id from users u, supportedCountys c where c.Name= u.County and u.Id = $userID";
        $result = $conn->query($sql);
        $result->data_seek(0);
        $obj = $result->fetch_array(MYSQLI_ASSOC);
        $countyID = $obj['Id'] *1;
        $result->close();
        return $countyID;
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
	<title>Inventory</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->
	<link rel="icon" type="image/png" href="images/icons/favicon.png"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/themify/themify-icons.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/elegant-font/html-css/style.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/slick/slick.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->
     <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/admin.css">

    
</head>
    
    <?php require_once "nav.php"?>
    
    <section class="bg-title-page p-t-40 p-b-50 flex-col-c-m" style="background-image: url(images/grapesBanner.jpg);">
		<h2 class="l-text2 t-center">
			<?php echo $countyName;?> 
		</h2>
	</section>
    
<body class="animsition">

    <div id="fullscreen_bg" class="fullscreen_bg"/>
        <form class="form-signin" method = "POST" action ="admin.php">
            <div class="container";>
                <div class="row">
                    <div id="invetoryChart">
                    <div class="panel panel-default" style = "width:100%;">
                        <div class="panel panel-primary">
                            <h3 class="text-center">Inventory</h3>
                            <div class="panel-body">    
                                <table class="table table-striped table-condensed"; style = " width:100%;">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Amount</th>
                                            <th>Update</th>  
                                            <th>Delete</th> 
                                        </tr>
                                    </thead>   
                                    <tbody>
                                        
                                        <?php echo $countyItems;?>
                                         
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <?php
        require_once 'footer.html';
    ?>
            
	<!-- Back to top -->
	<div class="btn-back-to-top bg0-hov" id="myBtn">
		<span class="symbol-btn-back-to-top">
			<i class="fa fa-angle-double-up" aria-hidden="true"></i>
		</span>
	</div>




	<script type="text/javascript" src="vendor/animsition/js/animsition.min.js"></script>

	<script src="js/main.js"></script>
</body>
    
</html>
