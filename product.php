<?php
    session_start(); 
    error_reporting(0);  // stops displaying warning from user end

    require_once 'Scripts/loginInfo.php'; 
    
    $userID = -1; 
    $countyID = 1;
    $items_To_display = '';
    $conn = new mysqli($hn, $un, $pw, $db);

    if(isset($_SESSION["id"])) {
        $userID = $_SESSION["id"];
        $countyID = getCountyId($userID, $conn);
    } 

    
    if (!$conn->connect_error){
        
        $sql = "select * from Items where countyID = $countyID";
        $result = $conn->query($sql);
        $rows = $result->num_rows;
    
        for($i=0; $i < $rows; $i++){
            $result->data_seek($i);
            $obj = $result->fetch_array(MYSQLI_ASSOC);
            $items_To_display .= generateItemsDiv($userID, $obj['Name'], $obj['CategoryName'], $obj['Price'], $obj['Weight'], $obj['Amount'], $obj['countyID'], $obj['Id'], $obj['itemName']); 
        }

        $result->close();
        $conn->close();
    } else {
      $items_To_display = '<h2>Sorry we are experiencing server errors</h2>';  
    }
    
    function getCountyId($userID, $conn){
        $sql = "select c.Id from users u, supportedCountys c where c.Name= u.County and u.Id = $userID;";
        $result = $conn->query($sql);
        $result->data_seek(0);
        $obj = $result->fetch_array(MYSQLI_ASSOC);
        $countyID = $obj['Id'] *1;
        $result->close();
        return $countyID;
    }
    
    

    function generateItemsDiv($userID, $name, $category, $price, $weight, $amount, $countyID, $itemID, $itemName){
        $button  = '';
        if ($userID != -1){ //if user is not logged in remove the button
            $button = '<button class="addtocartbutton flex-c-m size1 bg4 bo-rad-23 hov1 s-text1 trans-0-4">
				            Add to Cart
				        </button>';
        }
        
        if($amount > 0){
            return'<div data-category = "'.$category.'"  class="foodCard col-sm-12 col-md-6 col-lg-4 p-b-50">
							<!-- Block2 -->
							<div class="block2">
								<div class="block2-img wrap-pic-w of-hidden pos-relative">
									<img src="images/'.$category.'/'.$name.'.jpg" alt="IMG-PRODUCT">

									<div class="block2-overlay trans-0-4">
									</div>
								</div>

								<div class="block2-txt p-t-20">
											'.$button.'
								    <br>
									<a data-countyid ="'.$countyID.'" data-itemid ="'.$itemID.'" class="block2-name dis-block s-text3 p-b-5">
										'.$itemName.'
									</a>

									<span class="block2-price m-text6 p-r-5">
										$'.$price.'
									</span>
                                    
                                    
								</div>
							</div>
						</div>';
        }
        else{
            return'<div data-category = "'.$category.'"  class="foodCard col-sm-12 col-md-6 col-lg-4 p-b-50">
							<!-- Block2 -->
							<div class="block2">
								<div class="block2-img wrap-pic-w of-hidden pos-relative">
									<img src="images/outOfStock.jpg" alt="IMG-PRODUCT">
								</div>

								<div class="block2-txt p-t-20">
									<a data-countyid ="'.$countyID.'" data-itemid ="'.$itemID.'" class="block2-name dis-block s-text3 p-b-5">
										'.$itemName.'
									</a>

									<span class="block2-price m-text6 p-r-5">
										$'.$price.'
									</span>
								</div>
							</div>
						</div>';
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Product</title>
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
	<link rel="stylesheet" type="text/css" href="vendor/noui/nouislider.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->
</head>
<body class="animsition">

	<!-- Header -->
	<?php
    require_once "nav.php"; 
    ?>

	<!-- Title Page -->
	<section class="bg-title-page p-t-50 p-b-40 flex-col-c-m" style="background-image: url(images/ricePaddie.jpg);">
		<h2 class="l-text2 t-center">
			Organic Groceries
		</h2>
		<p class="m-text13 t-center">
			
		</p>
	</section>


	<!-- Content page -->
	<section class="bgwhite p-t-55 p-b-65">
		<div class="container">
			<div class="row">
				<div class="col-sm-6 col-md-4 col-lg-3 p-b-50">
					<div class="leftbar p-r-20 p-r-0-sm">
						<!--  -->
						<h4 class="m-text14 p-b-7">
							Categories
						</h4>

						<ul class="p-b-54">
							<li class="p-t-4">
								<a id = 'All' href="#row" class="s-text13">
									All
								</a>
							</li>

							<li class="p-t-4">
								<a id = 'Fruits' href="#row" class="s-text13">
									Fruits
								</a>
							</li>

							<li class="p-t-4">
								<a id = 'Vegetables' href="#row" class="s-text13">
									Vegetables
								</a>
							</li>

							<li class="p-t-4">
								<a id = 'Dairy' href="#row" class="s-text13">
									Dairy
								</a>
							</li>

							<li class="p-t-4">
								<a id = 'Grains' href="#row" class="s-text13">
									Grains
								</a>
							</li>
						</ul>

						<!--  -->
<!--
						<h4 class="m-text14 p-b-32">
							Search
						</h4>

						<div class="search-product pos-relative bo4 of-hidden">
							<input class="s-text7 size6 p-l-23 p-r-50" type="text" name="search-product" placeholder="Search Items...">

							<button class="flex-c-m size5 ab-r-m color2 color0-hov trans-0-4">
								<i class="fs-12 fa fa-search" aria-hidden="true"></i>
							</button>
						</div>
-->
					</div>
				</div>

				<div class="col-sm-6 col-md-8 col-lg-9 p-b-50">

					<!-- Products -->
					<div class="row">
                        <?php 
                        echo $items_To_display;
                        ?>
						
					</div>					
				</div>
			</div>
		</div>
	</section>


	<!-- Footer -->
	<?php 
    require_once "footer.html";
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
    <script type="text/javascript" src="js/shop.js"></script>
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
	<script type="text/javascript" src="vendor/daterangepicker/moment.min.js"></script>
	<script type="text/javascript" src="vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script type="text/javascript" src="vendor/slick/slick.min.js"></script>
	<script type="text/javascript" src="js/slick-custom.js"></script>
<!--===============================================================================================-->
	<script type="text/javascript" src="vendor/sweetalert/sweetalert.min.js"></script>
<!--===============================================================================================-->
	<script type="text/javascript" src="vendor/noui/nouislider.min.js"></script>
	
<!--===============================================================================================-->
	<script src="js/main.js"></script>

</body>
</html>