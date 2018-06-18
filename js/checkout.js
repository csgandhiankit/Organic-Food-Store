      
$(document).ready(function() {
    
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
    
    
    
    $("#addressForm").submit(function(){
        var value = getAddress()
        // If foo already exists
        if( $("[name=address]").length > 0 ){
            $("[name=address]").val(value);
        } 
        else {
            var input = $("<input />", { name : "address", value : value , type : "hidden" });
            $(this).append(input);
        }
    });
    
});

function getAddress(){
    country = $("#country").text();
    state = $("#administrative_area_level_1").text();
    city = $("#locality").text();
    streetAdress =  $("#street_number").text() +" "+$("#route").text();
    zip = $("#postal_code").text();
    
    return streetAdress + ", " + city + ", " + state + ", " + country + " " + zip;
}

// This example displays an address form, using the autocomplete feature
      // of the Google Places API to help users fill in the information.

      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

      var placeSearch, autocomplete;
      var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        country: 'long_name',
        postal_code: 'short_name'
      };

      function initAutocomplete() {
        // Create the autocomplete object, restricting the search to geographical
        // location types.
        autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
            {types: ['geocode']});

        // When the user selects an address from the dropdown, populate the address
        // fields in the form.
        autocomplete.addListener('place_changed', fillInAddress);
      }

      function fillInAddress() {
        // Get the place details from the autocomplete object.
        var place = autocomplete.getPlace();

        for (var component in componentForm) {
          document.getElementById(component).value = '';
          document.getElementById(component).disabled = false;
        }

        // Get each component of the address from the place details
        // and fill the corresponding field on the form.
        clearAddress();
        for (var i = 0; i < place.address_components.length; i++) {
          var addressType = place.address_components[i].types[0];
//            alert("adress type: "+addressType)
          if (componentForm[addressType]) {
            var val = place.address_components[i][componentForm[addressType]];
              $("#"+addressType).text(val)
              
//            document.getElementById(addressType).value = val;
              
          }
        }
      }
    function clearAddress(){
        $("#country").text("")
        $("#administrative_area_level_1").text("")
        $("#locality").text("")
        $("#street_number").text("")
        $("#route").text("")
        $("#postal_code").text("")
    }

      // Bias the autocomplete object to the user's geographical location,
      // as supplied by the browser's 'navigator.geolocation' object.
      function geolocate() {
          
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
            var geolocation = {
              lat: position.coords.latitude,
              lng: position.coords.longitude
            };
            var circle = new google.maps.Circle({
              center: geolocation,
              radius: position.coords.accuracy
            });
            autocomplete.setBounds(circle.getBounds());
          });
        }
      }
