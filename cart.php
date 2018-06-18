<?php
session_start();
error_reporting(0); // stops displaying warning from user end

    require_once 'Scripts/loginInfo.php';
    require_once 'Scripts/helperScripts.php';
    
    if(!SessionIsValid()){
        header('Location: signin.php');
    }

    $cart_items = '';
    $total_price = 0;
    $total_weight = 0; 
    $id = $_SESSION['id'];
    $conn = new mysqli($hn, $un, $pw, $db); //connects to db
    
    if (!$conn->connect_error){ //checks if connected succesfully 
        $sql = "select i.Name, sum(c.amount) as amount, i.Weight, i.Price, i.CategoryName, i.Id, i.itemName from Cart c, Items i where i.Id = c.ItemID and c.userID =$id group by i.Name order by i.Id ASC";
        $result = $conn->query($sql);
        $rows = $result->num_rows;
        
        //loops through all the rows and saves the data from the columns 
        for($i=0; $i < $rows; $i++){
        
            $result->data_seek($i);
            $obj = $result->fetch_array(MYSQLI_ASSOC);
            $total_weight += $obj['amount'] * $obj['Weight'];
            $total_price += $obj['amount'] * $obj['Price'];
            $cart_items .= generateDiv($obj['Name'], $obj['amount'], $obj['Weight'], $obj['Price'], $obj['CategoryName'], $obj['Id'], $obj['itemName']); 
        }
        $result->close();
        $conn->close();
    }
    else{
        $cart_items = "<h2>We are experiencing server error</h2>";
    }

    

    //@desc: generates html code to display a new row inside the cart table 
    function generateDiv($name, $amount, $weight, $price, $category, $id, $itemName){ //old img = item-10.jpg
        
        $total_price = $amount * $price;
        $total_weight = $amount * $weight;
        return '<tr class="table-row" id = "item'.$id.'">
							<td class="column-1">
								<div class="cart-img-product b-rad-4 o-f-hidden">
									<img src="images/'.$category.'/'.$name.'.jpg" alt="IMG-PRODUCT">
								</div>
							</td>
							<td class="column-2">'.$itemName.'</td>
							<td class="column-3">$'.$price.'/'.$weight.' lb</td>
							<td class="column-4">
								<div class="flex-w bo5 of-hidden w-size17">
									<button class="btn-num-product-down color1 flex-c-m size7 bg8 eff2">
										<i class="fs-12 fa fa-minus" aria-hidden="true"></i>
									</button>

									<input style="pointer-events:none;" class="size8 m-text18 t-center num-product" type="number" name="num-product1" value="'.$amount.'">

									<button class="btn-num-product-up color1 flex-c-m size7 bg8 eff2">
										<i class="fs-12 fa fa-plus" aria-hidden="true"></i>
									</button>
								</div>
							</td>
                            <td class="column-5">'.$total_weight.' lb</td>
				            <td class="column-6">$'.$total_price.'</td>
                            <td class="column-7 p-l-30" style="padding-top:0; padding-bottom:0;"><i data-itemid = "'.$id.'" class="fa fa-trash delete"></i></td>
                            
						</tr>';
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Cart</title>
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
    <script type="text/javascript" src="vendor/sweetalert/sweetalert.min.js"></script>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->
</head>
<body class="animsition">

	<!-- Header -->
	<?php 
    include "nav.php";
    ?>

	<!-- Title Page -->
	<section class="bg-title-page p-t-40 p-b-50 flex-col-c-m" style="background-image: url(images/sunset.jpg);">
		<h2 class="l-text2 t-center">
			Cart
		</h2>
	</section>

	<!-- Cart -->
	<section class="cart bgwhite p-t-70 p-b-100">
		<div class="container">
			<!-- Cart item -->
			<div class="container-table-cart pos-relative">
				<div class="wrap-table-shopping-cart bgwhite">
					<table class="table-shopping-cart">
						<tr class="table-head">
							<th class="column-1"></th>
							<th class="column-2">Product</th>
							<th class="column-3">Price</th>
							<th class="column-4 p-l-70">Quantity</th>
                            <th class="column-5">Weight</th>
							<th class="column-6">Total</th>
						</tr>
                        
<!--                    display cart items-->
                        <?php echo $cart_items?>
                        
					</table>

					<table class="table-shopping-cart" style="margin-top: 1%; margin-bottom: 0px;">
                    <tr>
							<th class="column-1"><h4 >Total Weight:</h4></th>
							<th class="column-2"> </th>
							<th class="column-3"> </th>
							<th class="column-4"> </th>
							<th id= "totalWeight" class="column-5" style="white-space: nowrap;"><?php echo $total_weight;?> lb</th>
                    </tr>
                </table>
                <hr />

                <table class="table-shopping-cart">
				
                    <tr>
							<th class="column-1"><h4>Total:</h4></th>
							<th class="column-2"> </th>
							<th class="column-3"> </th>
							<th class="column-4"> </th>
							<th id= "totalPrice" class="column-5" style="white-space: nowrap">$<?php echo $total_price;?></th>
                    </tr>

                </table>
                <hr />

				</div>
			</div>

            <div class="flex-w flex-sb-m p-t-25 p-b-25 bo8 p-l-35 p-r-60 p-lr-15-sm">
				<div class="flex-w flex-m w-full-sm">
				</div>

				<div class="size10 trans-0-4 m-t-10 m-b-10">
					<!-- Button -->
					<button id = "updateCart" class="flex-c-m sizefull bg1 bo-rad-23 hov1 s-text1 trans-0-4">
						Update Cart
					</button>
				</div>
			</div>
            
             <div class="flex-w flex-sb-m p-t-25 p-b-25 bo8 p-l-35 p-r-60 p-lr-15-sm">
				<div class="flex-w flex-m w-full-sm">
				</div>

				<div class="size10 trans-0-4 m-t-10 m-b-10">
					<!-- Button -->
                    <a href="checkout.php">
                        <button class="flex-c-m sizefull bg1 bo-rad-23 hov1 s-text1 trans-0-4">
                            Checkout	
                        </button> 
                    </a>
				</div>
			</div>
            
			<!-- Total -->
			
		</div>
	</section>



	<!-- Footer -->
	<?php
        require_once 'footer.html';
    ?>



	<!-- Back to top -->
	<div class="btn-back-to-top bg0-hov" id="myBtn">
		<span class="symbol-btn-back-to-top">
			<i class="fa fa-angle-double-up" aria-hidden="true"></i>
		</span>
	</div>

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
    <script type="text/javascript" src="js/cart.js"></script>
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
