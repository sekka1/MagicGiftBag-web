<?php
// This script generates a FusionChart given a set of FusionChart and Test retrieval parameters 

// Example Usage: http://demonkitty/automation/charts/generateChart.php?data_source=traverse&username=new&password=new&start_time=200901100000&end_time=NOW&device=sjck-ean-gw1.cisco.com&test=Active%20Calls&labelstep=40&chart=MSArea.swf

import_request_variables( "gp", "input_" );

$data_url = $input_data_url;
$chart = $input_chart;

//Test Parameters
$username = urlencode( $input_username );
$password = urlencode( $input_password );
$start_time = urlencode( $input_start_time );
$end_time = urlencode( $input_end_time );
$device = urlencode( $input_device );
$test = urlencode( $input_test );

//Chart Parameters
$chart = urlencode( $input_chart );
$data_source = urlencode( $input_data_source );
$caption = urlencode( $input_title );
$subcaption = "";
$labelstep = urlencode( $input_labelstep );
$width = $input_width;
$height = $input_height;

// URL that will return the XML data for the fusionchart
$data_url = "/automation/charts/parseData.php?data_source=$data_source&username=$username&password=$password&start_time=$start_time&end_time=$end_time&device=$device&test=$test&labelstep=$labelstep&caption=$caption";

?>

<html>

<SCRIPT LANGUAGE="Javascript" SRC="/automation/charts/FusionCharts/JSClass/FusionCharts.js">
//You need to include the above JS file, if you intend to embed the chart using JavaScript.
</SCRIPT>

<SCRIPT LANGUAGE="JavaScript">

function FC_Rendered(DOMId){
   //This method is called whenever a FusionCharts chart is loaded.
   //Check if it's the required chart using ID
   if (DOMId=="ChId1"){
       //Invoke updateChart() method to update chart with new data
       updateChart();
   }
}

function updateChart(){
   //Get reference to chart object using Dom ID "ChId1"
   var chartObj = getChartFromId("ChId1");
   
    var strURL = "<?php echo $data_url; ?>";

    //strURL = strURL + "&currTime=" + getTimeForURL();
    //This basically adds a ever-changing parameter which bluffs
    //the browser and forces it to re-load the XML data every time.

   //Update its XML Data URL
   chartObj.setDataURL( strURL );
}
</SCRIPT>

<BODY>
<div id="chart1div">
    This Chart Needs Falsh. 
</div>
<script type="text/javascript">
   var chart1 = new FusionCharts("/automation/charts/Charts/<?php echo $chart; ?>", "ChId1", "<?php echo $width; ?>", "<?php echo $height; ?>", "0", "1");
   //Start Chart with empty data as we'll later update using JavaScript
   chart1.setDataXML("<chart></chart>");
   chart1.render("chart1div");
</script>
</BODY>

</html>
