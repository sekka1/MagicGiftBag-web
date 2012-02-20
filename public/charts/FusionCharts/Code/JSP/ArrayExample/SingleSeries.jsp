
<%
	/*We've included ../Includes/FusionCharts.jsp, which contains functions
	to help us easily create the charts that can be embedded.*/
	%>
<%@ include file="../Includes/FusionCharts.jsp"%>

<HTML>
<HEAD>
<TITLE>FusionCharts - Array Example using Single Series Column
3D Chart</TITLE>
<%
	/*You need to include the following JS file, if you intend to embed the chart using JavaScript.
	Embedding using JavaScripts avoids the "Click to Activate..." issue in Internet Explorer
	When you make your own charts, make sure that the path to this JS file is correct. Else, you would get JavaScript errors.
	*/
	%>
<SCRIPT LANGUAGE="Javascript" SRC="../../FusionCharts/FusionCharts.js"></SCRIPT>
<style type="text/css">
	<!--
	body {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 12px;
	}
	-->
	</style>
</HEAD>


<BODY>

<CENTER>
<h2>FusionCharts Examples</h2>
<h4>Plotting single series chart from data contained in Array.</h4>
<%
	/*In this example, we plot a single series chart from data contained
	in an array. The array will have two columns - first one for data label
	and the next one for data values.
	
	Let's store the sales data for 6 products in our array. We also store
	the name of products. */
    	String[][] arrData = new String[6][2];
    		//Store Name of Products
    		arrData[0][0] = "Product A";
        	arrData[1][0] = "Product B";
        	arrData[2][0] = "Product C";
        	arrData[3][0] = "Product D";
        	arrData[4][0] = "Product E";
        	arrData[5][0] = "Product F";
        	
        	//Store sales data
        	arrData[0][1] = "567500";
        	arrData[1][1] = "815300";
        	arrData[2][1] = "556800";
        	arrData[3][1]= "734500";
        	arrData[4][1] = "676800";
        	arrData[5][1] = "648500";

	//Now, we need to convert this data into XML. We convert using string concatenation.
	String strXML;
	    int i=0;
	    //Initialize <chart> element
	    strXML = "<chart caption='Sales by Product' numberPrefix='$' formatNumberScale='0'>";
	    //Convert data to XML and append
	    for(i=0;i<arrData.length;i++){
	    	strXML = strXML + "<set label='" +arrData[i][0] + "' value='" + arrData[i][1] + "' />";
	    }
	    //Close <chart> element
    	strXML = strXML + "</chart>";
	
	//Create the chart - Column 3D Chart with data contained in strXML
	String chartCode= createChart("../../FusionCharts/Column3D.swf", "", strXML, "productSales", 600, 300, false, false);
%> <%=chartCode%> <BR>
<BR>
<a href='../NoChart.html' target="_blank">Unable to see the chart
above?</a></CENTER>
</BODY>
</HTML>
