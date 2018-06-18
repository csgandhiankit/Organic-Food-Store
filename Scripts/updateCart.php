<?php
session_start();

require_once 'loginInfo.php';
require_once 'helperScripts.php';

error_reporting(0); 
class response{}
$response = new response;

//checks if POST values are set 
if(!$_POST || !isset($_POST['items']) || !isset($_SESSION["id"])) $response->success = false;

else {
    
    $conn = new mysqli($hn, $un, $pw, $db);
    $items =  json_decode($_POST['items']);
    $userID =  $_SESSION["id"];
    $countyID = getCountyId($userID, $conn);
    $response = new response;
    
    if (!$conn->connect_error){ //connected to db succesfully         
        
        $oldCart = getCart($conn, $userID);
        $missingItems = enoughAmountOfItems($oldCart, $items);
        
        if($missingItems != "") {
            $response->success = false;
            $response->missing = $missingItems;
        }
        else {
            
            if (!incrementItems( $oldCart , $conn)){ 
                $response->success = false;
            }
            else {
                if(dropCart($userID, $conn)){
                    if(createCart($userID, $countyID, $items, $conn)){
                        $response->cart = getCart($conn, $userID);
                        $response->success = true;
                    }
                }
                else $response->success = false;
            }
        }
        $conn->close();
        
    }else $response->success = false;
}

    echo json_encode($response);

    
    //---------------function -------------------------
    function createCart($userID, $countyID, $items, $conn){
        for($i = 0; $i < count($items); $i++) {
            $itemID = $items[$i][0];
            $amount = $items[$i][1];
            if(!addToCart($conn, $userID, $itemID, $countyID, $amount)){ //increment item
                return false;
            }
        }
        return true;
    }
    
    //@desc: gets county id 
    function getCountyId($userID, $conn){
        $sql = "select c.Id from users u, supportedCountys c where c.Name= u.County and u.Id = $userID";
        $result = $conn->query($sql);
        $result->data_seek(0);
        $obj = $result->fetch_array(MYSQLI_ASSOC);
        $countyID = $obj['Id'] *1;
        $result->close();
        return $countyID;
    }

    //@desc: loops through all items and incremnts them in the db
    function incrementItems($items, $conn){
        for($i = 0; $i < count($items); $i++) {
            
            $itemID = $items[$i]["Id"];
            $amount = $items[$i]["amount"];
            if(!incrementItem($itemID, $amount, $conn)){ //increment item
                return false;
            }
        }
        return true;
    }

    function incrementItem($itemID, $amount, $conn){
        $sql = "update Items set Amount = Amount + $amount where Id =$itemID"; 
        if($conn->query($sql)){
            return true;
        }else return false;
    }

    function decrementItem($itemID, $amount, $conn){
        $sql = "update Items set Amount = Amount - $amount where Id = $itemID"; 
        if($conn->query($sql)){
            return true;
        }else return false;
    }


    function dropCart($userID, $conn){
        $sql = "delete from Cart where userID = $userID"; 
        if($conn->query($sql)){
            return true;
        }else return false;
    }

    function getCart($conn, $userID){
        $sql = "select i.Id, i.Name, sum(c.amount) as amount, i.Weight, i.Price, i.CategoryName, i.itemName, i.Amount as itemAmount from Cart c, Items i where i.id = c.ItemID and c.userID = $userID group by i.Id order by i.Id ASC";
        $result = $conn->query($sql);
        $obj = array(); 
        if($result){
            $rows = $result->num_rows;
            for($i=0; $i < $rows; $i++){
                $result->data_seek($i);
                $obj[] = $result->fetch_array(MYSQLI_ASSOC);
            }
        }else return false;
        
        $result->close();
        return $obj; 
    }



    //@desc: adds a new row to the the cart table in the db
    //@Param: $conn - connection to the database 
    //@param: $userID - int: users id 
    //@param: $itemID - int: id of food item
    //@param: $itemID - int: id of county 
    function addToCart($conn, $userID, $itemID, $countyID, $amount){
        $sql = "insert into Cart(ItemID, Amount, userID, countyID) values($itemID, $amount, $userID, $countyID)";
        if($conn->query($sql)){
            if(decrementItem($itemID, $amount, $conn)){
                return true;
            }else return false;
           
        }else return false;
    }



    //-----------------unused methods, but might be helpful later-------------------//
    

    //@desc: checks if the we have enough items of what the user requested to fullfill request 
    //@param: $oldCart - array of an associated array with coloumns of db from items table
    //@param: $newCart - array of an int[]. amount = $newCart[$j][1]; itemID = $newCart[$j][0];
    function enoughAmountOfItems($oldCart, $newCart){
                
        $j = 0; 
        for($i = 0; $i < count($oldCart); $i++) {
            $cart1itemID = $oldCart[$i]["Id"];
            $cart2itemID = $newCart[$j][0];
            
            if ($cart1itemID == $cart2itemID) {
                $amountLeft = $oldCart[$i]["itemAmount"];
                $cart1Amount = $oldCart[$i]["amount"];
                $cart2Amount = $newCart[$j][1];
                
                $amountAdded = $cart2Amount - $cart1Amount;
                $itemName = $oldCart[$i]["itemName"];
                if($amountLeft - $amountAdded < 0) {
                    return $itemName;
                }
                $j++;
            }
        }
        return "";
    }


    //@desc: increments the amount of food item into the database
    //@Param: $conn - connection to the database 
    //@param: $userID - int: users id 
    //@param: $itemID - int: id of food item
    //@param: $itemID - int: id of county 
    function increaseAmountInCart($conn, $userID, $itemID, $countyID){
        $sql = "update Cart set Amount = Amount+1  where userID =$userID and itemID = $itemID";
        if($conn->query($sql)){
            return true;
        }else return false;
    }


    //@desc: checks items and sees if the desired amount is less than the total amount in the db
    function checkItemAmount($itemID, $desiredAmount, $conn) {
        
        $sql = "select Name, Amount from Items where Id = $itemID";
        $result = $conn->query($sql);
        $result->data_seek(0);
        $obj = $result->fetch_array(MYSQLI_ASSOC);
        
        if($obj["amount"] < $desiredAmount){
            return $obj["apple"];
        }        
        $result->close();
        
        return ""; 
    }

?>