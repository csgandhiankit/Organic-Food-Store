<?php 
    $cart_items = '';
    $total_price = 0;
    $total_weight = 0; 
    $id = $_SESSION['id'];
    $submitButton = "";
    $toHeavy = '';
    $conn = new mysqli($hn, $un, $pw, $db); //connects to db
    
    if (!$conn->connect_error){ //checks if connected succesfully 
        $sql = "select i.Name, sum(c.amount) as amount, i.Weight, i.Price, i.CategoryName, i.itemName from Cart c, Items i where i.Id = c.ItemID and c.userID =$id group by i.Name";
        
        $result = $conn->query($sql);
        $rows = $result->num_rows;
        
        //loops through all the rows and saves the data from the columns 
        for($i=0; $i < $rows; $i++){
        
            $result->data_seek($i);
            $obj = $result->fetch_array(MYSQLI_ASSOC);
            $total_weight += $obj['amount'] * $obj['Weight'];
            $total_price += $obj['amount'] * $obj['Price'];
            $cart_items .= generateDiv($obj['Name'], $obj['amount'], $obj['Weight'], $obj['Price'], $obj['CategoryName'], $obj['itemName']); 
        }
        $result->close();
        $conn->close();
        
        $shippingCost = 2*(floor($total_weight/15)); //round down
        $total_price += $shippingCost;
        if($total_price != 0 && $total_weight <= 15){
            $submitButton = '<button name="SubmitOrder" type="submit" value="Submit" class="btn btn-primary btn-submit-fix">Continue</button>';
        }
        if($total_weight > 15){
            $toHeavy = '<div class="form-group">
                                <div class="col-xs-12">
                                    <span style="color:#f00;">Order must be less than 15 lb</span>
                                </div>
                            </div>
                            
                            <div class="form-group"><hr /></div>';
        }
        
    }
    else{
        $cart_items = "<h2>We are experiencing server error</h2>";
    }

    

    //@desc: generates html code to display a new row inside the cart table 
    function generateDiv($name, $amount, $weight, $price, $category, $itemName){ //old img = item-10.jpg
        
        $total_price = $amount * $price;
        
        return '<div class="form-group">
                                <div class="col-sm-3 col-xs-3">
                                    <img class="img-responsive" src="images/'.$category.'/'.$name.'.jpg" />
                                </div>
                                <div class="col-sm-6 col-xs-6">
                                    <div class="col-xs-12"><h3 style="margin-top:0;">'.$itemName.'</h3></div>
                                    <div class="col-xs-12"><small>Quantity:<span> '.$amount.'</span></small></div>
                                    <div class="col-xs-12"><small>Total Weight:<span> '.($weight*$amount).' lb</span></small></div>                 
                                </div>
                                <div class="col-sm-3 col-xs-3 text-right">
                                    <h6><span>$</span>'.$total_price.'</h6>
                                </div>
                            </div>
                            <div class="form-group"><hr /></div>';
    }

?>

<form class="form-horizontal" method="POST" action="checkout.php">
    
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 ">
                    <!--REVIEW ORDER-->
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Review Order <div class="pull-right"><small></small></div>
                        </div>
                        <div class="panel-body">
                            <?php echo $cart_items;?>
                            
                             <div class="form-group">
                                <div class="col-xs-12">
                                    <strong>Total Weight</strong>
                                    <div class="pull-right"><span><?php echo $total_weight; ?></span><span> lb</span></div>
                                </div>
                            </div>
                            
                            <div class="form-group"><hr /></div>
                            <?php 
                                echo $toHeavy; 
                            ?>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <strong>Order Total</strong>
                                    <div class="pull-right"><span>$</span><span><?php echo $total_price?></span></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <?php echo $submitButton; ?>
                                </div>
                            </div>
                            
                            
                        </div>
                        
                    </div>
    <!--REVIEW ORDER END-->
                </div>
</form>