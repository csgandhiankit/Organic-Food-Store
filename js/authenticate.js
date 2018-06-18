
var UserNameLegthMin = 5;
var passwordLengthMin = 5;

function ShippingaddressValidation(form){
    
    country = $("#country").text();
    state = $("#administrative_area_level_1").text();
    city = $("#locality").text();
    streetAdress =  $("#street_number").text() +" "+$("#route").text();
    zip = $("#postal_code").text();
    usersCounty = $("#addressForm").data("county")
    
    fail = validateCountry(country)
    fail += validateState(state)
    fail += validateZip(zip, usersCounty)
    
    p = country +", "+ state + ", " + city + ", " + streetAdress + ", " + zip
    
    if(fail == "")
       return true;
    else {
        swal({ title: "Invalid Input", text: "Sorry we cannot ship to this address", icon: "error"});
        return false
    }    
}

function CreateAccountValidate(form){
    
    //firstname , lastname, county, zip, email, password, cpassword
    
    fail = validateFirstName(form.firstname.value);
    fail += validateLastName(form.lastname.value);
    fail += validateCounty(form.county.value);
    fail += validatePassword(form.password.value, form.cpassword.value);
    fail += validateEmail(form.email.value);
		
    if (fail == ""){
        return true; 
    } 
    else {
        swal({ title: "Invalid Input", text: fail, icon: "error"});
        return false 
    }
}

function validateCountry(country){
    if(country.length == 0)
        return "Please enter your country \n"
    if (country !== "United States")
        return "This service is only available in the United States \n"
    return ""
}

function validateState(state) {
    if(state.length == 0)
        return "Please enter your state \n"
    if(state !== "CA"){
        return "This service is not available in your State \n"
    }
    return ""
}

function validateZip(zipcode, usersCounty){
    allZipcodes = supportedCounties[usersCounty];
    
    if(zipcode.length ==0)
        return "please enter your zipcode \n"
    if(allZipcodes[zipcode])
        return ""
    return "This service is not available in your county \n"
}

function validateFirstName(name){
    if (name == "") return "First Name was not entered.\n"
	else if (/[^a-zA-Z]/.test(name))
		return "First name may only have letters.\n"
	return ""
}

function validateLastName(name){
    if (name == "") return "Last Name was not entered.\n"
	else if (/[^a-zA-Z]/.test(name))
		return "Last name may only have letters<br>.\n"
	return ""
}


function validateCounty(county){
    if (county == "Choose County...") return "No county was entered.\n";
	return "";
}

function validateZipcode(zipcode){
    if (zipcode == "") return "No zipcode was entered.\n"
	else if (zipcode.length < 5)
		return "Zipcode must be at least 5 characters.\n"
	else if (/[^0-9]/.test(zipcode))
		return "Zipcode may only have numbers. \n"
	return ""
}


function validateEmail(email){
    if (email == "") return "No Email was entered.\n"
	else if (!((email.indexOf(".") > 0) && (email.indexOf("@") > 0)) || /[^a-zA-Z0-9.@_-]/.test(email))
		return "The Email address is invalid.\n"
	return ""

}

function validatePassword(password, cpassword){
    
    if (password == "") return "No Password was entered.\n"
	else if (password.length < passwordLengthMin)
		return "Passwords must be at least "+passwordLengthMin+" characters.\n"
	else if (!/[a-z]/.test(password) || ! /[A-Z]/.test(password) ||!/[0-9]/.test(password))
		return "Passwords must have lowercase, uppercase, and a number.\n"
    else if(password !== cpassword)
        return "Passwords do not match.\n";
	return ""
}

var SantaClaraCountyZip = { "94022": true, "94023": true, "94024": true, "94035": true, "94039": true, "94040": true, "94041": true, "94042": true, "94043": true, "94085": true, "94086": true, "94087": true, "94088": true, "94089": true, "94301": true, "94302": true, "94303": true, "94304": true, "94305": true, "94306": true, "94309": true, "95002": true, "95008": true, "95009": true, "95011": true, "95013": true, "95014": true, "95015": true, "95020": true, "95021": true, "95026": true, "95030": true, "95031": true, "95032": true, "95033": true, "95035": true, "95036": true, "95037": true, "95038": true, "95042": true, "95044": true, "95046": true, "95050": true, "95051": true, "95052": true, "95053": true, "95054": true, "95055": true, "95056": true, "95070": true, "95071": true, "95101": true, "95103": true, "95106": true, "95108": true, "95109": true, "95110": true, "95111": true, "95112": true, "95113": true, "95115": true, "95116": true, "95117": true, "95118": true, "95119": true, "95120": true, "95121": true, "95122": true, "95123": true, "95124": true, "95125": true, "95126": true, "95127": true, "95128": true, "95129": true, "95130": true, "95131": true, "95132": true, "95133": true, "95134": true, "95135": true, "95136": true, "95138": true, "95139": true, "95140": true, "95141": true, "95148": true, "95150": true, "95151": true, "95152": true, "95153": true, "95154": true, "95155": true, "95156": true, "95157": true, "95158": true, "95159": true, "95160": true, "95161": true, "95164": true, "95170": true, "95172": true, "95173": true, "95190": true, "95191": true, "95192": true, "95193": true, "95194": true, "95196": true};
var SanMateoCountyZip = { "94002": true, "94005": true, "94010": true, "94011": true, "94014": true, "94015": true, "9406": true, "94017": true, "94018": true, "94019": true, "94020": true, "94021": true, "94025": true, "94026": true, "94027": true, "94028": true, "94030": true, "94037": true, "94038": true, "94044": true, "94060": true, "94061": true, "94062": true, "94063": true, "94064": true, "94065": true, "94066": true, "94070": true, "94074": true, "94080": true, "94083": true, "94128": true, "94401": true, "94402": true, "94403": true, "94404": true, "94407": true};
var supportedCounties = {"Santa Clara": SantaClaraCountyZip, "San Mateo": SanMateoCountyZip}



  
