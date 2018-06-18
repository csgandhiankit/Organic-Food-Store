<?php 
$countName = "San Mateo";

$conn = new mysqli($hn, $un, $pw, $db);
    if(!$conn->connect_error){
        $userID = $_SESSION['id'];
        $countName = getCountyName($userID, $conn);
    }
?>
<!--onsubmit = "return ShippingaddressValidation(this)"-->

<form id= "addressForm" <?php echo "data-county = '$countName'";?> onsubmit = "return ShippingaddressValidation(this)" class="form-horizontal" method="POST" action="checkout.php">

<div class="panel panel-info">
                        <div class="panel-heading">Address</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <h4>Shipping Address</h4>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <p>Please note, your address must be in the <?php echo $countName; ?> County</p>
                                    <strong>Full address:</strong>
                                    </div>
                                <div class="col-md-12">
                                    <input type="text" id="autocomplete" placeholder="Enter your address"
             onFocus="geolocate()" name="fulladdress" class="form-control" value="" />
                                    
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <h4 style="display: inline">Country: </h4>
                                    <p name = "country" id="country" style="display: inline"></p>
                                </div>
<!--
                                <div class="col-md-12">
                                    <input id="country" type="text" name="county" class="form-control" value="" />
                                </div>
-->
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <h4 style="display: inline">State:</h4>
                                    <p id="administrative_area_level_1"  style="display: inline"></p>
                                </div>
<!--
                                <div class="col-md-12">
                                    <input id="administrative_area_level_1" type="text" name="state" class="form-control" value="" />
                                </div>
-->
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <h4 style="display: inline">City:</h4>
                                    <p id="locality" style="display: inline"></p>
                                </div>
<!--
                                <div class="col-md-12">
                                    <input id="locality" type="text" name="city" class="form-control" value="" />
                                </div>
-->
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <h4 style="display: inline">Street Address: </h4>
                                    <p id="street_number" style="display: inline"></p>
                                    <p id="route" style="display: inline"></p>
                                </div>
<!--
                                <div class="col-md-12">
                                    <input id="street_number" type="text" name="address" class="form-control" value="" />
                                    <input id="route" type="text" name="address" class="form-control" value="" />
                                </div>
-->
                            </div>   
                            
                            <div class="form-group">
                                <div class="col-md-12">
                                    <h4 style="display: inline">Zip/Postal Code:</h4>
                                    <p id="postal_code" style="display: inline"></p>
                                </div>
<!--
                                <div class="col-md-12">
                                    <input id="postal_code" type="text" name="zip" class="form-control" value="" />
                                </div>
-->
                            </div>

                            <div class="form-group">
                                <div class=" top-margin col-md-6 col-sm-6 col-xs-12">
                                    <button name="submiteAdress" type="submit" class="btn btn-primary btn-submit-fix">Continue</button>
                                </div>
                            </div>
                        </div>
    
                    </div>

</form>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCHdx-m6xOsxi9Un8XTSvjAZjUzl1qLX6Q&libraries=places&callback=initAutocomplete"
        async defer></script>
<script type="text/javascript" src="js/checkout.js"></script>