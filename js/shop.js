$(document).ready(function(){
     $("#All").click(function() {
        showAll();
    });
    
    $("#Fruits").click(function() {
        hideFruits();
    });
    
    $("#Vegetables").click(function() {
        hideVegetables();
    })
    
    $("#Dairy").click(function(){
        hideDairy();
    })
    
    $("#Grains").click(function(){
        hideGrains();
    })
    
}); 

function showAll(){
    
    $(".foodCard").hide();
        $(".foodCard").each(function(){
            $(this).show();
    });
}

function hideFruits(){
    
    $(".foodCard").hide();
        $(".foodCard").each(function(){
            if($(this).data("category") == 'Fruits') {
                $(this).show();
            }
    });
}

function hideVegetables(){
    
    $(".foodCard").hide();
        $(".foodCard").each(function(){
            if($(this).data("category") == 'Vegetables') {
                $(this).show();
            }
    });
}
                            
function hideDairy(){
    
    $(".foodCard").hide();
        $(".foodCard").each(function(){
            if($(this).data("category") == 'Dairy') {
                $(this).show();
            }
    });
}

function hideGrains(){
    
    $(".foodCard").hide();
        $(".foodCard").each(function(){
            if($(this).data("category") == 'Grains') {
                $(this).show();
            }
    });
}

function updateNav(items, itemClicked){
    HTML_cart_items = ''
    HTML_cart_item_mobile= ''
    total_price = 0
    total_weight = 0
    total_items = 0
    for(i=0; i < items.length; i++){
        total_weight += items[i]['amount'] * items[i]['Weight']
        total_price += items[i]['amount'] * items[i]['Price']
        HTML_cart_items += generateCartItem(items[i]['Name'], items[i]['amount'], items[i]['Weight'], items[i]['Price'], items[i]['CategoryName'],items[i]['itemName'])
        
        HTML_cart_item_mobile += generateMobileCartItem(items[i]['Name'], items[i]['amount'], items[i]['Weight'], items[i]['Price'], items[i]['CategoryName'], items[i]['itemName'])
        
        if(items[i]["Id"] == itemClicked && items[i]["quantity"] <= 0){
            window.location.reload();
        }
    }
    
    cart = generateCartHTML(HTML_cart_items, total_price)
    mobileCart = generateMobileCartHTML(HTML_cart_item_mobile, total_price)
    
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

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}


//@desc: add to cart buttion clicked listiner. Gets items info and calls a php script to update cart
$('.addtocartbutton').each(function(){
			var nameProduct = $(this).parent().parent().parent().find('.block2-name').html();
            var itemID =  $(this).parent().parent().parent().find('.block2-name').data('itemid')
            var userID =  getCookie('id');
            var countyID = $(this).parent().parent().parent().find('.block2-name').data('countyid')
    
			$(this).on('click', function(){
                communicatToDatabase(nameProduct, itemID, userID, countyID);
			});
		});


//@desc: calls the the addToCart php script to update the database. Passes vaibles by doing a POST request. 
function communicatToDatabase(nameProduct, itemID, userID, countyID){
    $.ajax({
                url: "Scripts/addToCart.php",
                type: 'POST',
                data: { itemid: itemID, userid: userID, countyid: countyID},
                dataType: 'json',
                success: function (data) {
                    if(data){
                        if(data.success){
                            swal(nameProduct, data.status, "success");
                            updateNav(data.cart, itemID)
                        }
                        else {
                            swal({ title: "Error", text: "Please try again later", icon: "error"});
                        }
                    }
                    else swal({ title: "Error", text: "Please try again later", icon: "error"});
                    
                    },
                error: function (jqXHR, exception) {
                    swal({ title: "Error", text: "Please try again later", icon: "error"});
                }
            });
}
