    if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById("map"));
        map.addControl(new GMapTypeControl());
        map.setCenter(new GLatLng(0,0),2);
        var dirn = new GDirections();
        var step = 10; // metres
        var tick = 50; // milliseconds
        var poly;
        var eol;
        var drone = new GIcon();
            drone.image="caricon.png"
            drone.iconSize=new GSize(32,18);
            drone.iconAnchor=new GPoint(16,9);
        var marker;
        var k=0;
        var mph = 30/2.24;
        var endTime;
        var arivalTime;
        var startTime;


        function animate(d) {
            
            currentTime = new Date(); 
            timeLeft = (endTime - currentTime)/1000
             distanceCovered = eol - (timeLeft * step);
            
            //Drone Arrived 
            if (timeLeft < 0) { 
                swal("Success", "Your Order Has Arrived", "success")
                    .then((value) => {
                        window.location.replace("droneArrive.php");
                    });
                return;
            }
            var p = poly.GetPointAtDistance(distanceCovered);
            map.panTo(p);
            marker.setPoint(p);
            setTimeout("animate("+(d+step)+")", tick);            
        }



    GEvent.addListener(dirn,"load", function() {
        poly=dirn.getPolyline();
        eol=poly.Distance();
        map.setCenter(poly.getVertex(0),17);
        map.addOverlay(new GMarker(poly.getVertex(0),G_START_ICON));
        map.addOverlay(new GMarker(poly.getVertex(poly.getVertexCount()-1),G_END_ICON));
        marker = new GMarker(poly.getVertex(0),{icon:drone});
        map.addOverlay(marker);
        
        
        //timer logic
        reloadTime = 1000/tick;
//        stepdist = dirn.getRoute(0).getDistance().meters;
//        steptime = dirn.getRoute(0).getDuration().seconds;

        
        step = (1609.344 * mph)/(3600)
        timeToComplete = ((eol/step)/reloadTime)+2.5 //in seconds
        endTime = new Date(startTime + 1000 * timeToComplete + 2000);
        step = eol/timeToComplete;
        
        currentTime = new Date(); 
        timeLeft = (endTime - currentTime)/1000
        if(timeLeft < 0) timeLeft = 0;
        
        startTimer(timeLeft);
        setTimeout("animate(0)",2000); 
        
      });

        
    GEvent.addListener(dirn,"error", function() {
        swal({ title: "Error", text: "Location(s) not recognised. Code: "+dirn.getStatus().code, icon: "error"});
      });
        

    function start() {
        UserAddress = getCookie('userAddress');
        countyAddress = getCookie('countyAddress');
        startTime = getCookie("startTime") * 1;
        
        startpoint = countyAddress //"37.336123, -121.895933";
        endpoint = UserAddress;
        
        dirn.loadFromWaypoints([startpoint,endpoint],{getPolyline:true,getSteps:true});
      }
        start()
    }
        
    //Displaying how many seconds are left
    function startTimer(timeleft){ 
        timeleft = Math.ceil(timeleft)
        
        var downloadTimer = setInterval(function(){
            
            hours = Math.floor((timeleft/3600) % 60);
            minutes = Math.floor((timeleft/60) % 60);
            seconds = Math.floor(timeleft % 60);
            
            document.getElementById("timer").textContent = "ETA: " + hours + "h "
            + minutes + "m " + seconds + "s";
            if(timeleft <= 0)
                clearInterval(downloadTimer);
            
            timeleft--;
        },1000);
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