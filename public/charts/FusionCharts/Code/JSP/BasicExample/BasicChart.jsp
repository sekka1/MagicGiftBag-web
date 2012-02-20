<%
	/*
	We've included ../Includes/FusionCharts.jsp, which contains functions
	to help us easily create the charts that can be embedded.
	*/
%>
<%@ include file="../Includes/FusionCharts.jsp"%>
<HTML>
<HEAD>
<TITLE>FusionCharts - Simple Column 3D Chart</TITLE>
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
<h4>Basic example using pre-built Data.xml</h4>
<%
	
	/*
	This page demonstrates the ease of generating charts using FusionCharts.
	For this chart, we've used a pre-defined Data.xml (contained in /Data/ folder)
	Ideally, you would NOT use a physical data file. Instead you'll have 
	your own JSP to create the XML data document. Such examples are also present.
	For a head-start, we've kept this example very simple.
	*/
	
	//Create the chart - Column 3D Chart with data from Data/Data.xml
	String chartHTMLCode=createChartHTML("../../FusionCharts/Column3D.swf", "Data/Data.xml", "", "myFirst", 600, 300, false);
	
	
%> <%=chartHTMLCode%> <BR>
<BR>
<a href='../NoChart.html' target="_blank">Unable to see the chart
above?</a></CENTER>
</BODY>
</HTML>
