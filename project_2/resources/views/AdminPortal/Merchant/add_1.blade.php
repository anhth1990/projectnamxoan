<html>
<head>
<title>Map</title>
<script type='text/javascript'
src='https://maps.googleapis.com/maps/api/js?key=AIzaSyDFLaJwxTIGpZmwfpbEyOU5XZglUq6-5iM&sensor=false'>
</script>
<script src="{{Asset('public/Admin/plugins/jQuery/jQuery-2.1.3.min.js')}}"></script>

<script type='text/javascript'>
    //var latitude = '20.9952092';
    //var longitude ='105.8619672';
    $(document).ready(function(){
        $("body").attr("onload","initialize('20.9952092','105.8619672')");
    });
function initialize(latitude,longitude)
{
    var myLatLng = new google.maps.LatLng(latitude,longitude);

 var mapProp = {
  zoom:17,
  center: myLatLng,
  mapTypeId: google.maps.MapTypeId.ROADMAP
  };
var map=new google.maps.Map(document.getElementById('map_canvas'),mapProp);

var marker = new google.maps.Marker({
  position: myLatLng,
  map: map,
  optimized: false,
  title:'Former About.com Headquarters'
}); 
}


</script>
</head>
<body >

<div id='map_canvas' style='width:300px; height:300px;'></div>
</body>
</html>