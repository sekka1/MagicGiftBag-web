 /**
* LogYAxisChart chart extends the Chart class to render the generic
* template for a chart with single logarithmic axis Chart (Y-axis)
*/
//Import parent Chart class
import com.fusioncharts.core.Chart;
import com.fusioncharts.helper.Utils;
import com.fusioncharts.helper.Logger;
import com.fusioncharts.extensions.DrawingExt;
import com.fusioncharts.extensions.ColorExt;
import com.fusioncharts.extensions.MathExt;
import mx.transitions.Tween;
class com.fusioncharts.core.LogYAxisChart extends Chart 
{
	//Version number (if different from super Chart class)
	//private var _version:String = "3.0.0";
	//Instance variables
	//Storage for div lines
	private var divLines : Array;
	//Axis charts can have trend lines. trendLines array
	//stores the trend lines for an axis chart.
	private var trendLines : Array;
	//numTrendLines stores the number of trend lines
	private var numTrendLines : Number = 0;
	//Number of trend lines below
	private var numTrendLinesBelow : Number = 0;
	//Axis charts can have vertical lines dividing the x-axis
	//labels. Container to store those vLines.
	private var vLines : Array;
	//Number of vertical lines
	private var numVLines : Number = 0;
	/**
	* Constructor function. We invoke the super class'
	* constructor.
	*/
	function LogYAxisChart (targetMC : MovieClip, depth : Number, width : Number, height : Number, x : Number, y : Number, debugMode : Boolean, lang : String, scaleMode:String, registerWithJS:Boolean, DOMId:String)
	{
		//Invoke the super class constructor
		super (targetMC, depth, width, height, x, y, debugMode, lang, scaleMode, registerWithJS, DOMId);
		//Initialize div lines array
		this.divLines = new Array ();
		//Initialize the trendlines array
		this.trendLines = new Array ();
		//Initialize vertical lines container
		this.vLines = new Array ();
	}
	/**
	* getAxisLimits is the method to calculate the axis limits
	* of the chart w.r.t. the mantissa of the yMax and yMin.
	* @param	maxValue	the maximum value of the data set to
	*						be plotted.
	* @param	minValue	the minimum value of the data set to
	*						be plotted.
	*/
	private function getAxisLimits (maxValue : Number, minValue : Number) : Void 
	{
		//Get the base of log chart from params
		var base : Number = this.params.base;
		//Local function to check whether the given parameter is specified or not.
		var validate : Function = function (param) : Boolean 
		{
			if (param == null || param == undefined || param == "" || isNaN (param))
			{
				//Variable is not specified - so return false.
				return false;
			} else 
			{
				//Variable is specified - so return true.
				return true;
			}
		};
		var power : Number;
		// if yAxisMaxValue is valid and greater than yMax
		if (validate (this.params.yAxisMaxValue) && Number (this.params.yAxisMaxValue) >= maxValue)
		{
			this.config.yMax = Number (this.params.yAxisMaxValue);
		} else 
		{
			// for base greater than one
			if (this.params.base > 1)
			{
				power = Math.ceil (Math.log (maxValue) / Math.log (this.params.base));
				// for 0 < base < 1
				
			} else 
			{
				power = Math.floor (Math.log (maxValue) / Math.log (this.params.base));
			}
			this.config.yMax = Math.pow (this.params.base, power);
			this.config.maxLogY = power;
		}
		// if maxLogY is not yet defined
		if ( ! this.config.maxLogY)
		{
			// for base greater than one
			if (this.params.base > 1)
			{
				var maxLogY : Number = Math.ceil (Math.log (this.config.yMax) / Math.log (this.params.base));
				// for 0 < base < 1
				
			} else 
			{
				var maxLogY : Number = Math.floor (Math.log (this.config.yMax) / Math.log (this.params.base));
			}
			this.config.maxLogY = maxLogY;
		}
		//
		// if yAxisMinValue is valid and less than yMin
		if (validate (this.params.yAxisMinValue) && Number (this.params.yAxisMinValue) <= minValue)
		{
			this.config.yMin = Number (this.params.yAxisMinValue);
		} else 
		{
			// for base greater than one
			if (this.params.base > 1)
			{
				power = Math.floor (Math.log (minValue) / Math.log (this.params.base));
				// for 0 < base < 1
				
			} else 
			{
				power = Math.ceil (Math.log (minValue) / Math.log (this.params.base));
			}
			this.config.yMin = Math.pow (this.params.base, power);
			this.config.minLogY = power;
		}
		// if minLogY is not yet defined
		if ( ! this.config.minLogY)
		{
			// for base greater than one
			if (this.params.base > 1)
			{
				var minLogY : Number = Math.floor (Math.log (this.config.yMin) / Math.log (this.params.base));
				// for 0 < base < 1
				
			} else 
			{
				var minLogY : Number = Math.ceil (Math.log (this.config.yMin) / Math.log (this.params.base));
			}
			this.config.minLogY = minLogY;
		}
	}
	/**
	* getAxisPosition is the method to calculate the pixel
	* position of a value in the logarithmic scale.
	*	@param	value					Numerical value for which we need
	*									pixel axis position
	*	@param	startAxisPos			Pixel start position for that axis
	*	@param	endAxisPos				Pixel end position for that axis
	*	@param	isYAxis					Flag indicating whether it's y axis
	*	@param	xPadding				Padding at left and right sides in
	*									case of a x-axis
	*	@return						The pixel position of the value on
	*									the given axis.
	*/
	private function getAxisPosition (value : Number, ax : Number, bx : Number, startAxisPos : Number, endAxisPos : Number, isYAxis : Boolean, xPadding : Number) : Number 
	{
		var absolutePosition : Number, relativePosition : Number;
		//Length of axis in pixels
		var axisLength : Number = endAxisPos - startAxisPos;
		// calculating the mantissa of the uppermost major divLine
		if (this.params.base > 1)
		{
			var minLogY : Number = Math.ceil (Math.log (this.config.yMin) / Math.log (this.params.base));
		} else 
		{
			var minLogY : Number = Math.floor (Math.log (this.config.yMin) / Math.log (this.params.base));
		}
		// calculating the mantissa of the lowermost major divLine
		if (this.params.base > 1)
		{
			var maxLogY : Number = Math.floor (Math.log (this.config.yMax) / Math.log (this.params.base));
		} else 
		{
			var maxLogY : Number = Math.ceil (Math.log (this.config.yMax) / Math.log (this.params.base));
		}
		// mantissa difference
		var n : Number = maxLogY - minLogY;
		// log of the the part above the uppermost major divLine
		var k1 : Number = Math.log (this.config.yMax) / Math.log (this.params.base) - maxLogY;
		// log of the the part below the lowermost major divLine
		var k2 : Number = minLogY - Math.log (this.config.yMin) / Math.log (this.params.base);
		//Length in pixels of the difference between any two successive major divLines
		var divInterval : Number = axisLength / (n + k1 + k2);
		//If we're calculating this for yAxis.
		if (isYAxis)
		{
			//Length in pixels between the origin position and the point
			relativePosition = ((Math.log (value) / Math.log (this.params.base)) - (Math.log (this.config.yMin) / Math.log (this.params.base))) * divInterval;
			//If the axis is inverted (Y-axis), accomodate for same.
			if (this.params.invertYAxis)
			{
				absolutePosition = startAxisPos + relativePosition;
			} else 
			{
				//Normal axis.
				absolutePosition = endAxisPos - relativePosition;
			}
		} else 
		{
			//TODO: --- check divInterval using axisLength earlier
			axisLength -= 2 * xPadding;
			//TODO: --- check absolutePosition since axisLength has changed
			absolutePosition = startAxisPos + ((Math.log (value) / Math.log (this.params.base)) - (Math.log (this.config.xMin) / Math.log (this.params.base))) * divInterval;
		}
		return absolutePosition;
	}
	/**
	* calcDivs is the method to calculate the numerical value
	* of the divLines and store as object in an array along
	* with other properties.
	*/
	private function calcDivs () : Void 
	{
		//Variables
		var divLineValue : Number;
		//Initialize counter to 0
		var counter : Number = 0;
		//Initial and check value
		var iniValue : Number, checkValue : Number;
		//Get log of this.params.base w.r.t. base=10
		var r : Number = Number (String (Math.log (this.params.base) / Math.log (10)));
		//R is integer if this.params.base is an integer power of 10. So, set
		//num div lines accordingly.
		this.config.numDivLines = getFN (this.params.numMinorDivLines, (Math.floor (r) == r) ? 8 : 4);
		//Initialize iniValue and checkValue w.r.t base value
		if (this.params.base > 1)
		{
			//If base > 1
			iniValue = this.config.maxLogY;
			checkValue = this.config.minLogY;
		} else if (this.params.base > 0 && this.params.base < 1)
		{
			//If base between 0 and 1.
			iniValue = this.config.minLogY;
			checkValue = this.config.maxLogY;
		}
		//Initialize r to maxLogY. It will be incremented/decremented within loop as required
		var r : Number = this.config.maxLogY;
		//Loop to calculate major div lines
		for (var i : Number = iniValue; i >= checkValue; -- i)
		{
			//Select the major divLines
			// Conditions for major div lines:
			// 1. If y-axis is inverted and div value is not the very first one
			// 	  (the lowest one coinciding with x-axis)
			// OR
			// 2. If y-axis is not inverted and div value is not the very last one
			//	  (the lowest one coinciding with x-axis)
			//if ((this.params.invertYAxis && r != this.config.maxLogY) || !(this.params.invertYAxis || i == checkValue)) {
			//Get the numerical value of div line by getting power of base.
			divLineValue = Math.pow (this.params.base, r);
			//Create the div line object
			// conditional to avoid plot of major divLines and their axis labels beyond plot area
			//if (i != checkValue && i != iniValue) {
			if (this.config.yMin <= divLineValue && this.config.yMax >= divLineValue)
			{
				this.divLines [counter] = this.returnDataAsDivLine (divLineValue, true);
			}
			//Increment counter to be used in calculation for minor divLines
			counter ++;
			//}
			//If it's upper limit (mantissa), no need of the minor divLines, as only major can be plotted
			if (i == checkValue)
			{
				continue;
			}
			//Multiplication factor
			//For base <1, powers go in negative -0.1 is 10^-1, 0.001 is 10 ^ -3 and so on.
			var d : Number = (this.params.base > 1) ? - 1 : 1;
			//Numeric interval between two succesive major divLines (variable)
			var slabInterval : Number = Math.pow (this.params.base, r) - Math.pow (this.params.base, r + d);
			//Numeric interval between two succesive minor divLines (variable) in between 2 successive major divLines
			var subInterval : Number = slabInterval / (this.config.numDivLines + 1);
			//Iterate through minor div lines
			for (var j : Number = 1; j <= this.config.numDivLines; ++ j)
			{
				//Get the numeric value for minor div line
				//By adding the sub-interval to power of base (of major div line)
				divLineValue = Math.pow (this.params.base, r + d) + subInterval * j;
				//Create the object
				// conditional to avoid plot of minor divLines and their axis labels beyond plot area
				//if (divLineValue>=this.config.yMin && divLineValue<=this.config.yMax) {
				if (this.config.yMin <= divLineValue && this.config.yMax >= divLineValue)
				{
					this.divLines [counter] = this.returnDataAsDivLine (divLineValue, false);
				}
				//Increment
				counter ++;
			}
			//Update counter corresponding to next major divLine w.r.t. base value
			if (this.params.base > 1)
			{
				r --;
			} else 
			{
				r ++;
			}
		}
		var isYMinDivLine : Boolean, isYMaxDivLine : Boolean;
		// iterating to check if the limits are divLines and flagged
		for (var u in this.divLines)
		{
			for (var e in this.divLines [u])
			{
				if (e == 'value')
				{
					isYMinDivLine = (this.divLines [u][e] == this.config.yMin) ? true : false;
					isYMaxDivLine = (this.divLines [u][e] == this.config.yMax) ? true : false;
				}
			}
		}
		// if yMin is not a divLine
		if ( ! isYMinDivLine)
		{
			// include yMin as a divLine
			this.divLines [counter] = this.returnDataAsDivLine (this.config.yMin, false);
			counter ++;
		}
		// if yMax is not a divLine
		if ( ! isYMaxDivLine)
		{
			// include yMax as a divLine
			this.divLines [counter] = this.returnDataAsDivLine (this.config.yMax, false);
		}
		//Now, sort the div lines on value
		this.divLines.sortOn ("value", Array.NUMERIC);
	}
	/**
	* returnDataAsDivLine is the method to return object
	* holding parameters passed and others.
	* @param	value		numerical value corresponding to divLine
	* @param	isMajor		boolean value denoting if the divLine
	*						is a major one or a minor one
	* @return				object holding all the parameters for
	*						rendering the divLines.
	*/
	private function returnDataAsDivLine (value : Number, isMajor : Boolean) : Object 
	{
		// container constructed
		var divLineObject : Object = new Object ();
		// parameters assigned in properties of the container
		divLineObject.value = value;
		divLineObject.isMajor = isMajor;
		//Format the div value - set decimal places as this.params.decimals, so that none of the decimals get stripped off
		divLineObject.displayValue = this.formatNumber (value, this.params.formatNumber, this.params.decimals, false, this.params.formatNumberScale, this.params.defaultNumberScale, this.config.nsv, this.config.nsu, this.params.numberPrefix, this.params.numberSuffix);
		//Set the showValue flag to the required value.
		if (isMajor && this.params.showYAxisValues)
		{
			//If it's a major div line and y-axis values are to be shown
			divLineObject.showValue = true;
		} else if ( ! isMajor && this.params.showMinorDivLineValues)
		{
			//If it's a minor div line and minor div line values are to be shown
			divLineObject.showValue = true;
		} else if (this.config.yMin == value || this.config.yMax == value)
		{
			// limits to be displayed
			divLineObject.showValue = true;
		} else 
		{
			//Other cases - hide
			divLineObject.showValue = false;
		}
		//Return object
		return divLineObject;
	}
	/**
	* returnDataAsTrendObj method takes in functional parameters, and creates
	* an object to represent the trend line as a unified object.
	*	@param	startValue		Starting value of the trend line.
	*	@param	endValue		End value of the trend line (if different from start)
	*	@param	displayValue	Display value for the trend (if custom).
	*	@param	color			Color of the trend line
	*	@param	thickness		Thickness (in pixels) of line
	*	@param	alpha			Alpha of the line
	*	@param	isTrendZone		Flag to control whether to show trend as a line or block(zone)
	*	@param	showOnTop		Whether to show trend over data plot or under it.
	*	@param	isDashed		Whether the line would appear dashed.
	*	@param	dashLen			Length of dash (if isDashed selected)
	*	@param	dashGap			Gap of dash (if isDashed selected)
	*	@param	valueOnRight	Whether to put the trend value on right side of canvas
	*	@return				An object encapsulating these values.
	*/
	private function returnDataAsTrendObj (startValue : Number, endValue : Number, displayValue : String, color : String, thickness : Number, alpha : Number, isTrendZone : Boolean, showOnTop : Boolean, isDashed : Boolean, dashLen : Number, dashGap : Number, valueOnRight : Boolean) : Object 
	{
		//Create an object that will be returned.
		var rtnObj : Object = new Array ();
		//Store parameters as object properties
		rtnObj.startValue = startValue;
		rtnObj.endValue = endValue;
		rtnObj.displayValue = displayValue;
		rtnObj.color = color;
		rtnObj.thickness = thickness;
		rtnObj.alpha = alpha;
		rtnObj.isTrendZone = isTrendZone;
		rtnObj.showOnTop = showOnTop;
		rtnObj.isDashed = isDashed;
		rtnObj.dashLen = dashLen;
		rtnObj.dashGap = dashGap;
		rtnObj.valueOnRight = valueOnRight;
		//Flag whether trend line is proper
		rtnObj.isValid = true;
		//Holders for dimenstions
		rtnObj.y = 0;
		rtnObj.toY = 0;
		//Text box y position
		rtnObj.tbY = 0;
		//Return
		return rtnObj;
	}
	/**
	* returnDataAsVLineObj method takes in functional parameters, and creates
	* an object to represent the vertical axis distribution line as a unified object.
	*	@param	index		Index of the vertical line w.r.t data specified.
	*	@param	color		Color of the vertical line.
	*	@param	thickness	Thickness of the line.
	*	@param	alpha		Alpha of the line.
	*	@param	isDashed	Whether the line should appear as dashed.
	*	@param	dashLen		Length of dash (if isDashed).
	*	@param	dashGap		Gap length (if isDashed)
	*	@return			An object encapsulating these values.
	*/
	private function returnDataAsVLineObj (index : Number, color : String, thickness : Number, alpha : Number, isDashed : Boolean, dashLen : Number, dashGap : Number) : Object 
	{
		//Create an object that will be returned.
		var rtnObj : Object = new Array ();
		//Store parameters as object properties
		rtnObj.index = index;
		rtnObj.color = color;
		rtnObj.thickness = thickness;
		rtnObj.alpha = alpha;
		rtnObj.isDashed = isDashed;
		rtnObj.dashLen = dashLen;
		rtnObj.dashGap = dashGap;
		//Set a flag for validity
		rtnObj.isValid = true;
		//Holders for dimenstions
		rtnObj.x = 0;
		//Return
		return rtnObj;
	}
	/**
	* parseVLineNode method parses the vertical line node and stores it in
	* local objects
	*	@param	vLineNode	XML Node representing the vertical axis division
	*						line.
	*	@param	index		Index of the division line. Index represents the
	*						numerical index of data item/category on the left
	*						side of v line.
	*/
	private function parseVLineNode (vLineNode : XMLNode, index : Number)
	{
		//Variables for local use
		var color : String, thickness : Number, alpha : Number;
		var isDashed : Boolean, dashLen : Number, dashGap : Number;
		//Increment count
		this.numVLines ++;
		//Get attributes array
		var lineAttr : Array = this.getAttributesArray (vLineNode);
		//Extract attributes
		color = String (formatColor (getFV (lineAttr ["color"] , "333333")));
		thickness = getFN (lineAttr ["thickness"] , 1);
		alpha = getFN (lineAttr ["alpha"] , 80);
		isDashed = toBoolean (Number (getFV (lineAttr ["dashed"] , 0)));
		dashLen = getFN (lineAttr ["dashlen"] , 5);
		dashGap = getFN (lineAttr ["dashgap"] , 2);
		//Create object and store
		this.vLines [this.numVLines] = this.returnDataAsVLineObj (index, color, thickness, alpha, isDashed, dashLen, dashGap);
	}
	/**
	* parseTrendLineXML method parses the XML node containing trend line nodes
	* and then stores it in local objects.
	*	@param		arrTrendLineNodes		Array containing Trend LINE nodes.
	*	@return							Nothing.
	*/
	private function parseTrendLineXML (arrTrendLineNodes : Array) : Void 
	{
		//Define variables for local use
		var startValue : Number, endValue : Number, displayValue : String;
		var color : String, thickness : Number, alpha : Number;
		var isTrendZone : Boolean, showOnTop : Boolean, isDashed : Boolean;
		var dashLen : Number, dashGap : Number, valueOnRight : Boolean;
		//Loop variable
		var i : Number;
		//Iterate through all nodes in array
		for (i = 0; i <= arrTrendLineNodes.length; i ++)
		{
			//Check if LINE node
			if (arrTrendLineNodes [i].nodeName.toUpperCase () == "LINE")
			{
				//Update count
				numTrendLines ++;
				//Store the node reference
				var lineNode : XMLNode = arrTrendLineNodes [i];
				//Get attributes array
				var lineAttr : Array = this.getAttributesArray (lineNode);
				//Extract and store attributes
				startValue = getFN (this.getSetValue(lineAttr ["startvalue"]) , this.getSetValue(lineAttr ["value"]));
				endValue = getFN (this.getSetValue(lineAttr ["endvalue"]) , startValue);
				displayValue = lineAttr ["displayvalue"];
				color = String (formatColor (getFV (lineAttr ["color"] , "333333")));
				thickness = getFN (lineAttr ["thickness"] , 1);
				isTrendZone = toBoolean (Number (getFV (lineAttr ["istrendzone"] , 0)));
				alpha = getFN (lineAttr ["alpha"] , (isTrendZone == true) ? 40 : 99);
				showOnTop = toBoolean (getFN (lineAttr ["showontop"] , 0));
				isDashed = toBoolean (getFN (lineAttr ["dashed"] , 0));
				dashLen = getFN (lineAttr ["dashlen"] , 5);
				dashGap = getFN (lineAttr ["dashgap"] , 2);
				valueOnRight = toBoolean (getFN (lineAttr ["valueonright"] , 0));
				//Create trend line object
				this.trendLines [numTrendLines] = returnDataAsTrendObj (startValue, endValue, displayValue, color, thickness, alpha, isTrendZone, showOnTop, isDashed, dashLen, dashGap, valueOnRight);
				//Update numTrendLinesBelow
				numTrendLinesBelow = (showOnTop == false) ? ( ++ numTrendLinesBelow) : numTrendLinesBelow;
			}
		}
	}
	/**
	* validateTrendLines method helps us validate the different trend line
	* points entered by user in XML. Some trend points may fall out of
	* chart range (yMin,yMax) and we need to invalidate them. Also, we
	* need to check if displayValue has been specified. Else, we specify
	* formatted number as displayValue.
	*	@return		Nothing
	*/
	private function validateTrendLines ()
	{
		//Sequentially do the following.
		//- Check if start value and end value are numbers. If not,
		//  invalidate them
		//- Check range of each trend line against chart yMin,yMax and
		//  devalidate wrong ones.
		//- Resolve displayValue conflict.
		//- Calculate and store y position of start and end position.
		//Loop variable
		var i : Number;
		for (i = 0; i <= this.numTrendLines; i ++)
		{
			//If the trend line start/end value is NaN or out of range
			if (isNaN (this.trendLines [i].startValue) || (this.trendLines [i].startValue < this.config.yMin) || (this.trendLines [i].startValue > this.config.yMax) || isNaN (this.trendLines [i].endValue) || (this.trendLines [i].endValue < this.config.yMin) || (this.trendLines [i].endValue > this.config.yMax))
			{
				//Invalidate it
				this.trendLines [i].isValid = false;
			} else 
			{
				//We resolve displayValue conflict
				this.trendLines [i].displayValue = getFV (this.trendLines [i].displayValue, this.formatNumber (this.trendLines [i].startValue, this.params.formatNumber, this.params.yAxisValueDecimals, false, this.params.formatNumberScale, this.params.defaultNumberScale, this.config.nsv, this.config.nsu, this.params.numberPrefix, this.params.numberSuffix));
			}
		}
	}
	/**
	* feedMacros method feeds macros and their respective values
	* to the macro instance. This method is to be called after
	* calculatePoints, as we set the canvas and chart co-ordinates
	* in this method, which is known to us only after calculatePoints.
	*	@return	Nothing
	*/
	private function feedMacros () : Void 
	{
		//Feed macros one by one
		//Chart dimension macros
		this.macro.addMacro ("$chartStartX", this.x);
		this.macro.addMacro ("$chartStartY", this.y);
		this.macro.addMacro ("$chartWidth", this.width);
		this.macro.addMacro ("$chartHeight", this.height);
		this.macro.addMacro ("$chartEndX", this.width);
		this.macro.addMacro ("$chartEndY", this.height);
		this.macro.addMacro ("$chartCenterX", this.width / 2);
		this.macro.addMacro ("$chartCenterY", this.height / 2);
		//Canvas dimension macros
		this.macro.addMacro ("$canvasStartX", this.elements.canvas.x);
		this.macro.addMacro ("$canvasStartY", this.elements.canvas.y);
		this.macro.addMacro ("$canvasWidth", this.elements.canvas.w);
		this.macro.addMacro ("$canvasHeight", this.elements.canvas.h);
		this.macro.addMacro ("$canvasEndX", this.elements.canvas.toX);
		this.macro.addMacro ("$canvasEndY", this.elements.canvas.toY);
		this.macro.addMacro ("$canvasCenterX", this.elements.canvas.x + (this.elements.canvas.w / 2));
		this.macro.addMacro ("$canvasCenterY", this.elements.canvas.y + (this.elements.canvas.h / 2));
	}
	/**
	* drawCanvas method renders the chart canvas.
	*	@return	Nothing
	*/
	private function drawCanvas () : Void 
	{
		//Create a new movie clip container for canvas
		var canvasMC = this.cMC.createEmptyMovieClip ("Canvas", this.dm.getDepth ("CANVAS"));
		
		//Parse the color, alpha and ratio array
		var canvasColor : Array = ColorExt.parseColorList (this.params.canvasBgColor);
		var canvasAlpha : Array = ColorExt.parseAlphaList (this.params.canvasBgAlpha, canvasColor.length);
		var canvasRatio : Array = ColorExt.parseRatioList (this.params.canvasBgRatio, canvasColor.length);
			
		//Create matrix object
		var matrix : Object = {
			matrixType : "box", w : this.elements.canvas.w, h : this.elements.canvas.h, x : - (this.elements.canvas.w / 2) , y : - (this.elements.canvas.h / 2) , r : MathExt.toRadians (this.params.canvasBgAngle)
		};
		//Start the fill.
		canvasMC.beginGradientFill ("linear", canvasColor, canvasAlpha, canvasRatio, matrix);
		
		if (this.params.useRoundEdges==true){
			//Rounded Canvas
			DrawingExt.drawRoundedRect(canvasMC, -this.elements.canvas.w/2, -this.elements.canvas.h/2, this.elements.canvas.w, this.elements.canvas.h, {tl:this.roundEdgeRadius,tr:this.roundEdgeRadius,bl:0,br:0}, {t:"", l:"", b:"", r:""}, {t:0, b:0, l:0, r:0}, {t:0, l:0, b:0, r:0});
			canvasMC._x = this.elements.canvas.x + this.elements.canvas.w/2;
			canvasMC._y = this.elements.canvas.y + this.elements.canvas.h/2;
		}else{		
			//Normal Canvas - Rectangular			
			//Set border properties - not visible
			//canvasMC.lineStyle(this.params.canvasBorderThickness, parseInt(this.params.canvasBorderColor, 16), this.params.canvasBorderAlpha);
			canvasMC.lineStyle ();
			//Move to (-w/2, 0);
			canvasMC.moveTo ( - (this.elements.canvas.w / 2) , - (this.elements.canvas.h / 2));
			
			//Draw the rectangle with center registration point
			canvasMC.lineTo (this.elements.canvas.w / 2, - (this.elements.canvas.h / 2));
			canvasMC.lineTo (this.elements.canvas.w / 2, this.elements.canvas.h / 2);
			canvasMC.lineTo ( - (this.elements.canvas.w / 2) , this.elements.canvas.h / 2);
			canvasMC.lineTo ( - (this.elements.canvas.w / 2) , - (this.elements.canvas.h / 2));
			//Set the x and y position
			canvasMC._x = this.elements.canvas.x + this.elements.canvas.w / 2;
			canvasMC._y = this.elements.canvas.y + this.elements.canvas.h / 2;
			//End Fill
			canvasMC.endFill ();
			// --------------------------- DRAW CANVAS BORDER --------------------------//
			//Canvas Border
			if (this.params.canvasBorderAlpha > 0)
			{
				//Create a new movie clip container for canvas
				var canvasBorderMC = this.cMC.createEmptyMovieClip ("CanvasBorder", this.dm.getDepth ("CANVASBORDER"));
				//Set border properties
				canvasBorderMC.lineStyle (this.params.canvasBorderThickness, parseInt (this.params.canvasBorderColor, 16) , this.params.canvasBorderAlpha);
				//Move to (-w/2, 0);
				canvasBorderMC.moveTo ( - (this.elements.canvas.w / 2) , - (this.elements.canvas.h / 2));
				//Draw the rectangle with center registration point
				canvasBorderMC.lineTo (this.elements.canvas.w / 2, - (this.elements.canvas.h / 2));
				canvasBorderMC.lineTo (this.elements.canvas.w / 2, this.elements.canvas.h / 2);
				canvasBorderMC.lineTo ( - (this.elements.canvas.w / 2) , this.elements.canvas.h / 2);
				canvasBorderMC.lineTo ( - (this.elements.canvas.w / 2) , - (this.elements.canvas.h / 2));
				//Set the x and y position
				canvasBorderMC._x = this.elements.canvas.x + this.elements.canvas.w / 2;
				canvasBorderMC._y = this.elements.canvas.y + this.elements.canvas.h / 2;
			}			
		}
		//Apply animation
		if (this.params.animation)
		{
			if (this.params.useRoundEdges==true){
				this.styleM.applyAnimation (canvasBorderMC, this.objects.CANVAS, this.macro, canvasBorderMC._x, - this.elements.canvas.w / 2, canvasBorderMC._y, - this.elements.canvas.h / 2, 100, 100, 100, null);
			}
			this.styleM.applyAnimation (canvasMC, this.objects.CANVAS, this.macro, canvasMC._x, - this.elements.canvas.w / 2, canvasMC._y, - this.elements.canvas.h / 2, 100, 100, 100, null);
		}
		//Apply filters
		this.styleM.applyFilters (canvasMC, this.objects.CANVAS);
		clearInterval (this.config.intervals.canvas);
	}
	/**
	* calcTrendLinePos method helps us calculate the y-co ordinates for the
	* trend lines
	* NOTE: validateTrendLines and calcTrendLinePos could have been composed
	*			into a single method. However, in calcTrendLinePos, we need the
	*			canvas position, which is possible only after calculatePoints
	*			method has been called. But, in calculatePoints, we need the
	*			displayValue of each trend line, which is being set in
	*			validateTrendLines. So, validateTrendLines is invoked before
	*			calculatePoints method and calcTrendLinePos is invoked after.
	*	@return		Nothing
	*/
	private function calcTrendLinePos ()
	{
		//Loop variable
		var i : Number;
		for (i = 0; i <= this.numTrendLines; i ++)
		{
			//We proceed only if the trend line is valid
			if (this.trendLines [i].isValid == true)
			{
				//Calculate and store y-positions
				this.trendLines [i].y = this.getAxisPosition (this.trendLines [i].startValue, null, null, this.elements.canvas.y, this.elements.canvas.toY, true, 0);
				//If end value is different from start value
				if (this.trendLines [i].startValue != this.trendLines [i].endValue)
				{
					//Calculate y for end value
					this.trendLines [i].toY = this.getAxisPosition (this.trendLines [i].endValue, null, null, this.elements.canvas.y, this.elements.canvas.toY, true, 0);
					//Now, if it's a trend zone, we need mid value
					if (this.trendLines [i].isTrendZone)
					{
						//For textbox y position, we need mid value.
						this.trendLines [i].tbY = Math.min (this.trendLines [i].y, this.trendLines [i].toY) + (Math.abs (this.trendLines [i].y - this.trendLines [i].toY) / 2);
					} else 
					{
						//If the value is to be shown on left, then at left
						if (this.trendLines [i].valueOnRight)
						{
							this.trendLines [i].tbY = this.trendLines [i].toY;
						} else 
						{
							this.trendLines [i].tbY = this.trendLines [i].y;
						}
					}
					//Height
					this.trendLines [i].h = (this.trendLines [i].toY - this.trendLines [i].y);
				} else 
				{
					//Just copy
					this.trendLines [i].toY = this.trendLines [i].y;
					//Set same position for value tb
					this.trendLines [i].tbY = this.trendLines [i].y;
					//Height
					this.trendLines [i].h = 0;
				}
			}
		}
	}
	//---------------------------- VISUAL RENDERING METHODS ------------------------------//
	/**
	* drawVLines method draws the vertical axis lines on the chart
	*/
	private function drawVLines () : Void 
	{
		var depth : Number = this.dm.getDepth ("VLINES");
		//Movie clip container
		var vLineMC : MovieClip;
		//Loop var
		var i : Number;
		//Iterate through all the v div lines
		for (i = 1; i <= this.numVLines; i ++)
		{
			if (this.vLines [i].isValid == true)
			{
				//If it's a valid line, create a movie clip
				vLineMC = this.cMC.createEmptyMovieClip ("vLine_" + i, depth);
				//Just draw line
				vLineMC.lineStyle (this.vLines [i].thickness, parseInt (this.vLines [i].color, 16) , this.vLines [i].alpha);
				//Now, if dashed line is to be drawn
				if ( ! this.vLines [i].isDashed)
				{
					//Draw normal line line keeping 0,0 as registration point
					vLineMC.moveTo (0, 0);
					vLineMC.lineTo (0, - this.elements.canvas.h);
				} else 
				{
					//Dashed Line line
					DrawingExt.dashTo (vLineMC, 0, 0, 0, - this.elements.canvas.h, this.vLines [i].dashLen, this.vLines [i].dashGap);
				}
				//Re-position line
				vLineMC._x = this.vLines [i].x;
				vLineMC._y = this.elements.canvas.toY;
				//Apply animation
				if (this.params.animation)
				{
					this.styleM.applyAnimation (vLineMC, this.objects.VLINES, this.macro, vLineMC._x, 0, vLineMC._y, 0, 100, null, 100, null);
				}
				//Apply filters
				this.styleM.applyFilters (vLineMC, this.objects.VLINES);
				//Increase depth
				depth ++;
			}
		}
		delete vLineMC;
		//Clear interval
		clearInterval (this.config.intervals.vLine);
	}
	/**
	* drawTrendLines method draws the trend lines on the chart
	* with their respective values.
	*/
	private function drawTrendLines () : Void 
	{
		var trendFontObj : Object;
		var trendValueObj : Object;
		var lineBelowDepth : Number = this.dm.getDepth ("TRENDLINESBELOW");
		var valueBelowDepth : Number = this.dm.getDepth ("TRENDVALUESBELOW");
		var lineAboveDepth : Number = this.dm.getDepth ("TRENDLINESABOVE");
		var valueAboveDepth : Number = this.dm.getDepth ("TRENDVALUESABOVE");
		var lineDepth : Number;
		var valueDepth : Number;
		var tbAnimX : Number = 0;
		//Movie clip container
		var trendLineMC : MovieClip;
		//Get font
		trendFontObj = this.styleM.getTextStyle (this.objects.TRENDVALUES);
		//Set vertical alignment
		trendFontObj.vAlign = "middle";
		//Loop variable
		var i : Number;
		//Iterate through all the trend lines
		for (i = 1; i <= this.numTrendLines; i ++)
		{
			if (this.trendLines [i].isValid == true)
			{
				//If it's a valid trend line
				//Get the depth and update counters
				if (this.trendLines [i].showOnTop)
				{
					//If the trend line is to be shown on top.
					lineDepth = lineAboveDepth;
					valueDepth = valueAboveDepth;
					lineAboveDepth ++;
					valueAboveDepth ++;
				} else 
				{
					//If it's to be shown below columns.
					lineDepth = lineBelowDepth;
					valueDepth = valueBelowDepth;
					lineBelowDepth ++;
					valueBelowDepth ++;
				}
				trendLineMC = this.cMC.createEmptyMovieClip ("TrendLine_" + i, lineDepth);
				//Now, draw the line or trend zone
				if (this.trendLines [i].isTrendZone)
				{
					//Create rectangle
					trendLineMC.lineStyle ();
					//Absolute height value
					this.trendLines [i].h = Math.abs (this.trendLines [i].h);
					//We need to align rectangle at L,M
					trendLineMC.moveTo (0, 0);
					//Begin fill
					trendLineMC.beginFill (parseInt (this.trendLines [i].color, 16) , this.trendLines [i].alpha);
					//Draw rectangle
					trendLineMC.lineTo (0, - (this.trendLines [i].h / 2));
					trendLineMC.lineTo (this.elements.canvas.w, - (this.trendLines [i].h / 2));
					trendLineMC.lineTo (this.elements.canvas.w, (this.trendLines [i].h / 2));
					trendLineMC.lineTo (0, (this.trendLines [i].h / 2));
					trendLineMC.lineTo (0, 0);
					//Re-position
					trendLineMC._x = this.elements.canvas.x;
					trendLineMC._y = this.trendLines [i].tbY;
				} else 
				{
					//Just draw line
					trendLineMC.lineStyle (this.trendLines [i].thickness, parseInt (this.trendLines [i].color, 16) , this.trendLines [i].alpha);
					//Now, if dashed line is to be drawn
					if ( ! this.trendLines [i].isDashed)
					{
						//Draw normal line line keeping 0,0 as registration point
						trendLineMC.moveTo (0, 0);
						trendLineMC.lineTo (this.elements.canvas.w, this.trendLines [i].h);
					} else 
					{
						//Dashed Line line
						DrawingExt.dashTo (trendLineMC, 0, 0, this.elements.canvas.w, this.trendLines [i].h, this.trendLines [i].dashLen, this.trendLines [i].dashGap);
					}
					//Re-position line
					trendLineMC._x = this.elements.canvas.x;
					trendLineMC._y = this.trendLines [i].y;
				}
				//Apply animation
				if (this.params.animation)
				{
					this.styleM.applyAnimation (trendLineMC, this.objects.TRENDLINES, this.macro, null, 0, trendLineMC._y, 0, 100, 100, 100, null);
				}
				//Apply filters
				this.styleM.applyFilters (trendLineMC, this.objects.TRENDLINES);
				//---------------------------------------------------------------------------//
				//Set color
				trendFontObj.color = this.trendLines [i].color;
				//Now, render the trend line value, based on its position
				if (this.trendLines [i].valueOnRight == false)
				{
					//Value to be placed on right
					trendFontObj.align = "right";
					//Create text
					trendValueObj = createText (false, this.trendLines [i].displayValue, this.cMC, valueDepth, this.elements.canvas.x - this.params.yAxisValuesPadding, this.trendLines [i].tbY, 0, trendFontObj, false, 0, 0);
					//X-position for text box animation
					tbAnimX = this.elements.canvas.x - this.params.yAxisValuesPadding - trendValueObj.width;
				} else 
				{
					//Left side
					trendFontObj.align = "left";
					//Create text
					trendValueObj = createText (false, this.trendLines [i].displayValue, this.cMC, valueDepth, this.elements.canvas.toX + this.params.yAxisValuesPadding, this.trendLines [i].tbY, 0, trendFontObj, false, 0, 0);
					//X-position for text box animation
					tbAnimX = this.elements.canvas.toX + this.params.yAxisValuesPadding;
				}
				//Animation and filter effect
				if (this.params.animation)
				{
					this.styleM.applyAnimation (trendValueObj.tf, this.objects.TRENDVALUES, this.macro, tbAnimX, 0, this.trendLines [i].tbY - (trendValueObj.height / 2) , 0, 100, null, null, null);
				}
				//Apply filters
				this.styleM.applyFilters (trendValueObj.tf, this.objects.TRENDVALUES);
			}
		}
		delete trendLineMC;
		delete trendValueObj;
		delete trendFontObj;
		//Clear interval
		clearInterval (this.config.intervals.trend);
	}
	/**
	* drawDivLines method draws the div lines on the chart
	*/
	private function drawDivLines () : Void 
	{
		var divLineValueObj : Object;
		var divLineFontObj : Object;
		var yPos : Number;
		var depth : Number = this.dm.getDepth ("DIVLINES") - 1;
		//Movie clip container
		var divLineMC : MovieClip;
		//Get div line font
		divLineFontObj = this.styleM.getTextStyle (this.objects.YAXISVALUES);
		//Set alignment
		divLineFontObj.align = "right";
		divLineFontObj.vAlign = "middle";
		//Iterate through all the div line values
		var i : Number;
		for (i = 0; i < this.divLines.length; i ++)
		{
			//If it's the first or last div Line (limits)
			if ((i == 0) || (i == this.divLines.length - 1))
			{
				if (this.divLines [i].showValue)
				{
					depth ++;
					//Get y position for textbox
					yPos = this.getAxisPosition (this.divLines [i].value, null, null, this.elements.canvas.y, this.elements.canvas.toY, true, 0);
					//Create the limits text
					divLineValueObj = createText (false, this.divLines [i].displayValue, this.cMC, depth, this.elements.canvas.x - this.params.yAxisValuesPadding, yPos, 0, divLineFontObj, false, 0, 0);
				}
			} else 
			{
				//It's a div interval - div line
				//Get y position
				yPos = this.getAxisPosition (this.divLines [i].value, null, null, this.elements.canvas.y, this.elements.canvas.toY, true, 0);
				//Render the line
				depth ++;
				divLineMC = this.cMC.createEmptyMovieClip ("DivLine_" + i, depth);
				//Draw the line - set cosmetics based on whether it's a mahor or minor div line
				if (this.divLines [i].isMajor)
				{
					divLineMC.lineStyle (this.params.divLineThickness, parseInt (this.params.divLineColor, 16) , this.params.divLineAlpha);
				} else 
				{
					divLineMC.lineStyle (this.params.minorDivLineThickness, parseInt (this.params.minorDivLineColor, 16) , this.params.minorDivLineAlpha);
				}
				if (this.params.divLineIsDashed)
				{
					//Dashed line
					DrawingExt.dashTo (divLineMC, - this.elements.canvas.w / 2, 0, this.elements.canvas.w / 2, 0, this.params.divLineDashLen, this.params.divLineDashGap);
				} else 
				{
					//Draw the line keeping 0,0 as registration point
					divLineMC.moveTo ( - this.elements.canvas.w / 2, 0);
					//Normal line
					divLineMC.lineTo (this.elements.canvas.w / 2, 0);
				}
				//Re-position the div line to required place
				divLineMC._x = this.elements.canvas.x + this.elements.canvas.w / 2;
				divLineMC._y = yPos - (this.params.divLineThickness / 2);
				//Apply animation and filter effects to div line
				//Apply animation
				if (this.params.animation)
				{
					this.styleM.applyAnimation (divLineMC, this.objects.DIVLINES, this.macro, null, 0, divLineMC._y, 0, 100, 100, null, null);
				}
				//Apply filters
				this.styleM.applyFilters (divLineMC, this.objects.DIVLINES);
				//So, check if we've to show div line values
				if (this.divLines [i].showValue)
				{
					//Increase Depth
					depth ++;
					//Create the text
					divLineValueObj = createText (false, this.divLines [i].displayValue, this.cMC, depth, this.elements.canvas.x - this.params.yAxisValuesPadding, yPos, 0, divLineFontObj, false, 0, 0);
				}
			}
			//Apply animation and filter effects to div line (y-axis) values
			if (this.divLines [i].showValue)
			{
				if (this.params.animation)
				{
					this.styleM.applyAnimation (divLineValueObj.tf, this.objects.YAXISVALUES, this.macro, this.elements.canvas.x - this.params.yAxisValuesPadding - divLineValueObj.width, 0, yPos - (divLineValueObj.height / 2) , 0, 100, null, null, null);
				}
				//Apply filters
				this.styleM.applyFilters (divLineValueObj.tf, this.objects.YAXISVALUES);
			}
		}
		delete divLineValueObj;
		delete divLineFontObj;
		//Clear interval
		clearInterval (this.config.intervals.divLines);
	}
	/**
	* reInit method re-initializes the chart. This method is basically called
	* when the user changes chart data through JavaScript. In that case, we need
	* to re-initialize the chart, set new XML data and again render.
	*/
	public function reInit () : Void 
	{
		//Invoke super class's reInit
		super.reInit ();
		//Re-set indexes to 0
		this.numTrendLines = 0;
		this.numTrendLinesBelow = 0;
		this.numVLines = 0;
		//Re-create container arrays
		this.divLines = new Array ();
		this.trendLines = new Array ();
		this.vLines = new Array ();
	}
}
