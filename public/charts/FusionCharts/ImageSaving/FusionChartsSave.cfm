<!--- Make sure that the width and height are integers --->
<cfset width = int(Form.width)>
<cfset height = int(Form.height)>


<!--- Impose some limits to mitigate DOS attacks --->
<cfif Not (0 lte width and width lte 5000)>
	<cfthrow message="Width out of range.">
</cfif>
<cfif Not (0 lte height and height lte 5000)>
	<cfthrow message="Height out of range.">
</cfif>


<!--- Check if we have the chart data --->
<cfif Not StructKeyExists(Form, "data") or Not Len(Trim( Form.data ))>
	<cfthrow message="Image Data not supplied.">
</cfif>


<!--- Default background color is white --->
<cfif Not StructKeyExists(Form, "bgcolor") or Not Len(Trim( Form.bgcolor ))>
	<cfset Form.bgcolor = "FFFFFF">
</cfif>


<cfset gColor  = CreateObject("java", "java.awt.Color")>
<cfset chart   = CreateObject("java", "java.awt.image.BufferedImage")>
<cfset chart.init( JavaCast("int", width), JavaCast("int", height), chart.TYPE_3BYTE_BGR)>
<cfset gr      = chart.createGraphics()>
<cfset gr.setColor( gColor.decode("##" & Form.bgcolor) )>
<cfset gr.fillRect(0, 0, JavaCast("int", width), JavaCast("int", height))>


<!--- Get rows with pixels --->
<cfset rows = ListToArray(Form.data, ";")>


<cfloop from="1" to="#ArrayLen(rows)#" index="i">

	<cfset pixels = ListToArray(rows[i], ",")>
	<!--- Horizontal index (x scale) --->
	<cfset horizIndex = 0>

	<cfloop from="1" to="#ArrayLen(pixels)#" index="j">

		<cfif ListLen(pixels[j], "_") eq 2>
			<!--- We have the color and the number of times it must be repeated --->
			<cfset color  = ListGetAt(pixels[j], 1, "_")>
			<cfset repeat = ListGetAt(pixels[j], 2, "_")>
		<cfelse>
			<!--- Background color; how many pixels to skip --->
			<cfset color  = "">
			<cfset repeat = ListGetAt(pixels[j], 1, "_")>
		</cfif>

		<cfif Len(Trim( color ))>

			<!---  If the hexadecimal code is less than 6 characters, prefix with 0 to get a 6 char color --->
			<cfif Len(Trim( color )) lt 6>
				<cfset color = RepeatString(0, 6 - Len(Trim( color ))) & color>
			</cfif>

			<!--- Draw a horizontal line for the number of pixels we must repeat --->
			<cfset gr.setColor(gColor.decode("##" & color))>
			<cfset gr.drawLine(JavaCast("int", horizIndex), JavaCast("int", i - 1),
								JavaCast("int", horizIndex + repeat -1), JavaCast("int", i - 1))>
		</cfif>

		<cfset horizIndex = horizIndex + repeat>
	</cfloop>

</cfloop>


<!--- Get writer for Jpeg --->
<cfset writer = "">
<cfset iter = CreateObject("java", "javax.imageio.ImageIO").getImageWritersByFormatName("jpg")>
<cfloop condition="iter.hasNext()">
	<cfset writer = iter.next()>
</cfloop>

<!--- Set Jpeg quality to maximum --->
<cfset jpgParams = CreateObject("java", "javax.imageio.plugins.jpeg.JPEGImageWriteParam").init( CreateObject("java", "java.util.Locale").init("en") )>
<cfset jpgParams.setCompressionMode( jpgParams.MODE_EXPLICIT )>
<cfset jpgParams.setCompressionQuality( 1 )>

<!--- Write image to a memory stream --->
<cfset imageOutput = CreateObject("java", "java.io.ByteArrayOutputStream").init()>
<cfset writer.setOutput( CreateObject("java", "javax.imageio.stream.MemoryCacheImageOutputStream").init( imageOutput ) )>
<cfset writer.write(JavaCast("null", 0), CreateObject("java", "javax.imageio.IIOImage").init(chart, JavaCast("null", 0), JavaCast("null", 0)), jpgParams)>

<!--- Stream the image to the browser (hint browser to display the Save dialog) --->
<cfheader name="Content-Disposition" value="attachment; filename=""FusionCharts.jpg""">
<cfcontent type="image/jpeg" variable="#imageOutput.toByteArray()#">