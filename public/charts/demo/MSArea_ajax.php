<?php
import_request_variables( "gp", "input_" );

?>

<html>

<SCRIPT LANGUAGE="Javascript" SRC="/automation/fusioncharts/FusionCharts_Evaluation/JSClass/FusionCharts.js">
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
   
    var strURL = "/automation/fusioncharts/demo/Data3.xml";

    //strURL = strURL + "&currTime=" + getTimeForURL();
    //This basically adds a ever-changing parameter which bluffs
    //the browser and forces it to re-load the XML data every time.

   //Update its XML Data URL
   chartObj.setDataURL( strURL );
}
</SCRIPT>

<BODY>
<div id="chart1div">
   This text is replaced by the chart.
</div>
<script type="text/javascript">
   var chart1 = new FusionCharts("/automation/fusioncharts/Charts/MSArea.swf", "ChId1", "600", "400", "0", "1");
   //Start Chart with empty data as we'll later update using JavaScript
   chart1.setDataXML("<chart></chart>");
   chart1.render("chart1div");
</script>
</BODY>

</html>
