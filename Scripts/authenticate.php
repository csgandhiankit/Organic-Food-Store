<?php

function authenticateTest($zipCode, $countyName) {
    $santaClaraCounty = array("94022", "94023", "94024", "94035", "94039", "94040", "94041", "94042", "94043", "94085", "94086", "94087", "94088", "94089", "94301", "94302", "94303", "94304", "94305", "94306", "94309", "95002", "95008", "95009", "95011", "95013", "95014", "95015", "95020", "95021", "95026", "95030", "95031", "95032", "95033", "95035", "95036", "95037", "95038", "95042", "95044", "95046", "95050", "95051", "95052", "95053", "95054", "95055", "95056", "95070", "95071", "95101", "95103", "95106", "95108", "95109", "95110", "95111", "95112", "95113", "95115", "95116", "95117", "95118", "95119", "95120", "95121", "95122", "95123", "95124", "95125", "95126", "95127", "95128", "95129", "95130", "95131", "95132", "95133", "95134", "95135", "95136", "95138", "95139", "95140", "95141", "95148", "95150", "95151", "95152", "95153", "95154", "95155", "95156", "95157", "95158", "95159", "95160", "95161", "95164", "95170", "95172", "95173", "95190", "95191", "95192", "95193", "95194", "95196");

    $sanMateoCounty = array("94002", "94005", "94010", "94011", "94014", "94015", "9406", "94017", "94018", "94019", "94020", "94021", "94025", "94026", "94027", "94028", "94030", "94037", "94038", "94044", "94060", "94061", "94062", "94063", "94064", "94065", "94066", "94070", "94074", "94080", "94083", "94128", "94401", "94402", "94403", "94404", "94407");
    
    if(in_array($zipCode, $santaClaraCounty) && strcmp($countyName, "Santa Clara") == 0) {
        return true;
    } else if(in_array($zipCode, $sanMateoCounty) && strcmp($countyName, "San Mateo") == 0) {
        return true;
    } else {
        return false;
    }
}

function authenticateCreditCard($cardNumber, $month, $year){
    $errors = array();
    $errors[0] = checkLuhn($cardNumber);
    $errors[1] = experationValidation($month, $year);
    
    return $errors;
}

function checkLuhn($number) {
    settype($number, 'string');
    $sumTable = array(
        array(0,1,2,3,4,5,6,7,8,9),
        array(0,2,4,6,8,1,3,5,7,9));
    $sum = 0;
    $flip = 0;
    for ($i = strlen($number) - 1; $i >= 0; $i--) {
        $sum += $sumTable[$flip++ & 0x1][$number[$i]];
    }    
    if($sum % 10 === 0) return "";
    else return '<p style="color:#f00">Invalid Number</p>';
}

function experationValidation($month, $year){
    $allMonths = array('January'=>1,'February'=>2, 'March'=>3, 'April'=>4, 'May'=>5,'June'=>6, 'July'=>7, 'August'=>8, 'September'=>9, 'October'=>10, 'November'=>11, 'December'=>12);
    
    $date = explode(" ", date('M Y')); 
    $currentMonth = $allMonths[$date[0]];
    $currentYear = $date[1];
    
    $cm = (int) $currentMonth;
    $cy = (int) $currentYear;
    $m = (int) $month;
    $y = (int) $year; 
    
    if($y < $cy)
        return '<br><p style="color:#f00">Card Expiered</p>';
    else if($y == $cy) {
        if($m < $cm)
            return '<br><p style="color:#f00">Card Expiered</p>';
    }
    return "";
}


?>