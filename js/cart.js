$(document).ready(function(){
    
    //prevent user from typing in the input
    $('input').keydown(function(e) {
        e.preventDefault();
        return false;
    });
    
    //decrement buttton clicked
    $(".btn-num-product-down").click(function() {
        updateTotalWeightAndPrice();
    });
    
    //increment button clicked
    $(".btn-num-product-up").click(function() {
        updateTotalWeightAndPrice()
    });
    
    //trash icon clicked 
    $(".delete").click(function() {
        deleteItem($(this));
    });
    
    //updateCart
     $("#updateCart").click(function() {
         updatecart()
     });
}); 

function updatecart(){
    json = JSON.stringify(getItems())
    communicatToDatabase(json)
}

//@desc: Loops through dom to get all the items in cart 
//@return: a array with array of that has the values {itemID, amount}
function getItems(){
    items = []
    $('tr.table-row').each(function () {
        amount = parseFloat($(this).find("td.column-4").find("div").find('input').val());
        itemID = parseFloat($(this).find("td.column-7").find("i").data("itemid"))
        items.push([itemID, amount])
    });
    return items;
}

//@desc: removes row from the html code and updates the total price and weight
function deleteItem(item){
    id = "#item" + item.data("itemid"); 
    $(id).replaceWith('');
    updateTotalWeightAndPrice();
}

//@desc: uses jQuery to travers the DOM to find all the values and update them
function updateTotalWeightAndPrice(){
    
    var totalWeight = 0; 
    var totalPrice = 0;
    $('tr.table-row').each(function () {
        
        //gets the price and weight in the formate of $2.99/1.00 lb
        priceWeight = $(this).find('td.column-3').html().split("/")
        
        //turns String into double 
        productPrice = priceWeight[0]
        productPrice = parseFloat(productPrice.substring(1, productPrice.length));
    
        productWight = priceWeight[1];
        productWight = parseFloat(productWight.substring(0, productWight.length-3));
    
        amount = parseFloat($(this).find("td.column-4").find("div").find('input').val());
       
        
        $(this).find("td.column-6").replaceWith(' <td class="column-6">$'+(amount * productPrice).toFixed(2)+'</td>')
        
        totalWeight += amount * productWight
        totalPrice += amount * productPrice
        
        $(this).find("td.column-5").replaceWith(' <td class="column-5">'+(amount * productWight).toFixed(2)+' lb</td>');
        
    });
    
    $('#totalPrice').replaceWith('<th id= "totalPrice" class="column-5" style="white-space: nowrap">$'+totalPrice.toFixed(2)+'</th>');    
    
    $('#totalWeight').replaceWith('<th id= "totalWeight" class="column-5" style="white-space: nowrap">'+totalWeight.toFixed(2)+' lb</th>');
}

//--------navigation logic ---------// 
function updateNav(items){
    HTML_cart_items = ''
    HTML_cart_item_mobile= ''
    total_price = 0
    total_weight = 0
    total_items = 0
    for(i=0; i < items.length; i++){
        total_weight += items[i]['amount'] * items[i]['Weight']
        total_price += items[i]['amount'] * items[i]['Price']
        HTML_cart_items += generateCartItem(items[i]['Name'], items[i]['amount'], items[i]['Weight'], items[i]['Price'], items[i]['CategoryName'], items[i]['itemName'])
        
        HTML_cart_item_mobile = generateMobileCartItem(items[i]['Name'], items[i]['amount'], items[i]['Weight'], items[i]['Price'], items[i]['CategoryName'], items[i]['itemName'])
    }

    cart = generateCartHTML(HTML_cart_items, total_price)
    mobileCart = generateMobileCartHTML(HTML_cart_items, total_price)
    
    $('#icon-number').replaceWith('<span id="icon-number" class="header-icons-noti">'+items.length+'</span>')
    $('#cart-desktop').replaceWith(cart)
    
    $("#icon-number-mobile").replaceWith('<span id="icon-number-mobile" class="header-icons-noti">'+items.length+'</span>')
    $('#cart-mobile').replaceWith(mobileCart)
}


function generateMobileCartItem(name, amount, weight, price, category, itemName) {
    
    return '<li class="header-cart-item">'
                +'<div class="header-cart-item-img">'
				+'<img src="images/' +category+ '/'+ name +'.jpg" alt="IMG">'
                    +'</div>'
                    +'<div class="header-cart-item-txt">'
				    +'<a href="#" class="header-cart-item-name">'+itemName+'</a>'
                    + '<span class="header-cart-item-info">'+ amount + ' x '+ price +'</span>'				
					+ '</div>'				
				    +'</li>'
    
}

function generateMobileCartHTML(items, cost){
    return '<div id="cart-mobile" class="header-cart header-dropdown">'
            + '<ul class="header-cart-wrapitem">' + items +'</ul>'
            + '<div class="header-cart-total">Total: $' + cost.toFixed(2) +'</div>'
            + '<div class="header-cart-buttons">'
                + '<div class="header-cart-wrapbtn">'		
                    + '<a href="cart.php" class="flex-c-m size1 bg1 bo-rad-20 hov1 s-text1 trans-0-4">View Cart</a>'
                + '</div>'						
		          + '<div class="header-cart-wrapbtn">'
                    + '<a href="checkout.php" class="flex-c-m size1 bg1 bo-rad-20 hov1 s-text1 trans-0-4">Check Out</a>'
				+ '</div>'
            + '</div>'
        +'</div>'
}


function generateCartItem(name, amount, weight, price, category, itemName){
        return '<li class="header-cart-item">'
									+'<div class="header-cart-item-img">'
										+'<img src="images/' + category + '/'+ name + '.jpg" alt="IMG">'
									+'</div>'

									+'<div class="header-cart-item-txt">'
										+'<a href="#" class="header-cart-item-name">'
											+ itemName 
										+'</a>'

										+'<span class="header-cart-item-info">'
											+ amount + 'x' + price
										+'</span>'
									+'</div>'
								+'</li>';
}

function generateCartHTML(items, cost){
        return '<div id="cart-desktop" class="header-cart header-dropdown">'
							+'<ul id = "cart_list" class="header-cart-wrapitem">'
                                +items
							+'</ul>'

							+'<div class="header-cart-total">'
								+"Total: " +cost.toFixed(2)
							+"</div>"

							+'<div class="header-cart-buttons">'
								+'<div class="header-cart-wrapbtn">'
    
									+'<a href="cart.php" class="flex-c-m size1 bg1 bo-rad-20 hov1 s-text1 trans-0-4">View Cart</a>'									
								+"</div>"
								+'<div class="header-cart-wrapbtn">'
									+'<a href="cart.php" class="flex-c-m size1 bg1 bo-rad-20 hov1 s-text1 trans-0-4">Check Out</a>'
								+"</div>"
							+"</div>"
						+"</div>";
    }


//----- Ajax ---------// 


//@desc: calls the the addToCart php script to update the database. Passes vaibles by doing a POST request. 
function communicatToDatabase(items){
    $.ajax({
                url: "Scripts/updateCart.php",
                type: 'POST',
                data: { items: items},
                dataType: 'json',
                
                success: function (data) {
                    if(data){
                        if(data.success){
                            swal( "success", "Cart Has Been Updated", "success");
                            updateNav(data.cart)
                        }
                        else {
                            //check if not enough items to fulfill request 
                            if(data.missing != null){ 
                                errorMessage = "Sorry we do not have enough " +data.missing + " to update your cart."
                                swal({ title: "Error", text: errorMessage, icon: "error"});
                            }
                            else {
                                swal({ title: "Error", text: errorMessage, icon: "error"});
                            }                                
                        }
                    }
                    else swal({ title: "Error2", text: "Please try again later", icon: "error"});
                    
                    },
                error: function (jqXHR, exception) {
                    swal({ title: "Error3", text: "Please try again later", icon: "error"});
                }
            });
}






