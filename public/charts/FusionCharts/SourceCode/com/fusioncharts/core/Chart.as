 /**
* @class Chart
* @author InfoSoft Global (P) Ltd. www.InfoSoftGlobal.com
* @version 3.0
*
* Copyright (C) InfoSoft Global Pvt. Ltd. 2005-2006
*
* Chart class is the super class for a FusionCharts chart from
* which individual chart classes inherit. The chart class is
* responsible for a lot of features inherited by child classes.
* Chart class also bunches code that is used by all other charts
* so as to avoid code duplication.
*/
//Utilities
import com.fusioncharts.helper.Utils;
//Log class
import com.fusioncharts.helper.Logger;
//Enumeration class
import com.fusioncharts.helper.FCEnum;
//String extension
import com.fusioncharts.extensions.StringExt;
//Color Extension
import com.fusioncharts.extensions.ColorExt;
//Math Extension
import com.fusioncharts.extensions.MathExt;
//Drawing Extension
import com.fusioncharts.extensions.DrawingExt;
//Custom Error Object
import com.fusioncharts.helper.FCError;
//Tool Tip
import com.fusioncharts.helper.ToolTip;
//Style Managers
import com.fusioncharts.core.StyleObject;
import com.fusioncharts.core.StyleManager;
//Default Colors Class
import com.fusioncharts.helper.DefaultColors;
//Macro Class
import com.fusioncharts.helper.Macros;
//Depth Manager
import com.fusioncharts.helper.DepthManager;
//Delegate
import mx.utils.Delegate;
//Class to help as saving as image
import com.fusioncharts.helper.BitmapSave;
//Progress bar
import com.fusioncharts.helper.FCProgressBar;
//External Interface - to expose methods via JavaScript
import flash.external.ExternalInterface;
//Event Dispatcher
import mx.events.EventDispatcher;
//Class Definition
class com.fusioncharts.core.Chart 
{
	//Instance properties
	//Version
	private var _version : String = "3.0.7";
	//XML data object for the chart.
	private var xmlData : XML;
	//Array and Enumeration listing charts objects
	//arrObjects array would store the list of chart
	//objects as string. The motive is to retrieve this
	//string information to be added to log.
	public var arrObjects : Array;
	//Object Enumeration stores the above array elements
	//(chart objects) as enumeration, so that we can refer
	//to each chart element as a numeric value.
	public var objects : FCEnum;
	//Object to store chart elements
	private var elements : Object;
	//Object to store chart parameters
	//All attributes retrieved from XML will be stored in
	//params object.
	private var params : Object;
	//Object to store chart configuration
	//Any calculation done by our code will be stored in
	//config object. Or, if we over-ride any param values
	//we store in config.
	private var config : Object;
	//DepthManager instance. The DepthManager class helps us
	//allot and retrieve depths of various objects in the chart.
	private var dm : DepthManager;
	//Movie clip in which the entire chart will be built.
	//If chart is not being loaded into another Flash movie,
	//parentMC is set as _root (as we need only 1 chart per
	//movie timeline).
	private var parentMC : MovieClip;
	//Movie clip reference for actual chart MC
	//All chart objects (movie clips) would be rendered as
	//sub-movie clips of this movie clip.
	private var cMC : MovieClip;
	//Movie clip reference for log MC. The logger elements
	//are contained as a part of this movie clip. Even if the
	//movie is not in debug mode, we create at least the
	//parent log movie clip.
	private var logMC : MovieClip;
	//Movie clip reference for tool tip. We created a separate
	//tool tip movie clip because of two reasons. One, tool tip
	//always appears above the chart. So, we've created tool tip
	//movie clip at a depth greater than that of cMC(chart movie
	//clip). Secondly, the tool tip is not an integral part of
	//chart - it's a helper class.
	private var ttMC : MovieClip;
	//Tool Tip Object. This object is common to all charts.
	//Whenever we need to show/hide tool tips, we called methods
	//of this class.
	private var tTip : ToolTip;
	//Movie clip reference for text box which will be used to determine
	//text width, height for various text fields. During calculation
	//of points (width/height) for chart, we need to simulate various
	//text fields so that we come to know their exact width/height.
	//Based on that, we accomodate other elements on chart. This
	//movie clip is the container for that test text field movie clip.
	//This text field never shows on the chart canvas.
	private var tfTestMC : MovieClip;
	//Co-ordinates for generating test TF
	//We put it outside stage so that it is never visible.
	private var testTFX : Number = - 2000;
	private var testTFY : Number = - 2000;
	//Embedded Font
	//Denotes which font is embedded as a part of the chart. If you're
	//loading the chart in your movie, you need to embed the same font
	//face (plain - not bold - not italics) in your movie, to enable
	//rotated labels. Else, the rotated labels won't show up at all.
	private var _embeddedFont : String = "Verdana";
	//Reference to logger class instance.
	private var lgr;
	//Depth in parent movie clip in which we've to create chart
	//This is useful when you are loading this chart class as a part
	//of your Flash movie, as then you can create various charts at
	//various depths of a single movie clip. In case of single chart
	//(non-load), this is set to 3 (as 1 and 2 are reserved for global
	//progress bar and global application text).
	private var depth : Number;
	//Width & Height of chart in pixels. If the chart is in exactFit
	//mode, the width and height remains the same as that of original
	//document (.fla). However, everything is scaled in proportion.
	//In case of noScale, these variables assume the width and height
	//provided either by chart constructor (when loading chart in your
	//flash movie) or HTML page.
	private var width : Number, height : Number;
	//X and Y Position of top left of chart. When loading the chart in
	//your flash movie, you might want to shift the chart to particular
	//position. These x and y denote that shift.
	private var x : Number, y : Number;
	//Debug mode - Flag whether the chart is in debug mode. It's passed
	//from the HTML page as OBJECT/EMBED variable debugMode=1/0.
	private var debugMode : Boolean;
	//Counter to store timeElapsed. The chart animates sequentially.
	//e.g., the background comes first, then canvas, then div lines.
	//So, we internally need to keep a track of time passed, so that
	//we can call next what to render.
	private var timeElapsed : Number = 0;
	//Language for application messages. By default, we show application
	//messages in English. However, if you need to define your application
	//messages, you can do so in com\fusioncharts\includes\AppMessages.as
	//This value is passed from HTML page as OBJECT/EMBED variable.
	private var lang : String;
	//Scale mode - noScale or exactFit.
	//This value is passed from HTML page as OBJECT/EMBED variable.
	private var scaleMode : String;
	//Is Online Mode. If the chart is working online, we avoid caching
	//of data. Else, we cache data.
	private var isOnline : Boolean;
	//Style Manager object. The style manager object handles the style
	//quotient (FONT, BLUR, BEVEL, GLOW, SHADOW, ANIMATION) of different
	//elements of chart.
	private var styleM : StyleManager;
	//Macros container. Macros help the user define pre-defined dynamic
	//values in XML for setting animation position.
	private var macro : Macros;
	//Storage for default color class. If the user doesn't specify
	//colors in XML for his data, we've a list of colors which we can
	//choose from. DefaultColors class helps us cyclic iterate through
	//the same.
	public var defColors : DefaultColors;
	//Store a short name reference for Utils.getFirstValue function
	//and Utils.getFirstNumber function
	//As we'll be using this function a lot.
	private var getFV : Function;
	private var getFN : Function;
	//Short name for ColorExt.formatHexColor function
	private var formatColor : Function;
	//Short name for Utils.createText function
	private var createText : Function;
	//Error handler. We've a custom error object to represent
	//any chart error. All such errors get logged and none are visible
	//to end user, to make their experience smooth.
	var e : FCError;
	//Whether to register chart with JS
	private var registerWithJS:Boolean;
	//DOM Id
	private var DOMId:String;
	//Flag to indicate whether we've conveyed the chart rendering event
	//to JavaScript and loader Flash
	private var renderEventConveyed:Boolean = false;
	//Text field to hold application messages.
	private var tfAppMsg : TextField;
	//Variable to store the maximum number of decimal places in any given
	//data
	private var maxDecimals : Number = 0;
	//Global variable to store the corner radius of 2D bars/columns.
	private var roundEdgeRadius:Number = 6;
	/**
	* Constructor method for chart. Here, we store the
	* properties of the chart from constructor parameters
	* in instance variables.
	* @param	targetMC	Parent movie clip reference in which
	*						we'll create chart movie clips
	* @param	depth		Depth inside parent movie clip in which
	*						we'll create chart movie clips
	* @param	width		Width of chart
	* @param	height		Height of chart
	* @param	x			x Position of chart
	* @param	y			y Position of chart
	* @param	debugMode	Boolean value indicating whether the chart
	*						is in debug mode.
	* @param	lang		2 Letter ISO code for the language of application
	*						messages
	* @param	scaleMode	Scale mode of the movie - noScale or exactFit
	*/
	function Chart (targetMC : MovieClip, depth : Number, width : Number, height : Number, x : Number, y : Number, debugMode : Boolean, lang : String, scaleMode : String, registerWithJS : Boolean, DOMId : String)
	{
		//Get the reference to Utils.getFirstValue
		this.getFV = Utils.getFirstValue;
		//Get the reference to getFirstNumber
		this.getFN = Utils.getFirstNumber;
		//Get reference to ColorExt.formatHexColor
		this.formatColor = ColorExt.formatHexColor;
		//Get reference to Utils.createText
		this.createText = Utils.createText;
		//Store properties in instance variables
		this.parentMC = targetMC;
		this.depth = depth;
		this.width = width;
		this.height = height;
		this.x = getFN (x, 0);
		this.y = getFN (y, 0);
		this.debugMode = getFV (debugMode, false);
		this.lang = getFV (lang, "EN");
		this.scaleMode = getFV (scaleMode, "noScale");
		this.registerWithJS = getFV(registerWithJS, false);
		this.DOMId = getFV(DOMId, "");
		//Initialize parameter storage object
		this.params = new Object ();
		//Initialize chart configuration storage object
		this.config = new Object ();
		//Object to store chart rendering intervals
		this.config.intervals = new Object ();
		//Elements object to store the various elements of chart.
		this.elements = new Object ();
		//Initialize style manager
		this.styleM = new StyleManager (this);
		//Initialize default colors class
		this.defColors = new DefaultColors ();
		//Initialize Macros
		this.macro = new Macros ();
		//Initialize Depth Manager
		this.dm = new DepthManager (0);
		// ----- CREATE REQUIRED MOVIE CLIPS NOW -----//
		//Create the chart movie clip container
		this.cMC = parentMC.createEmptyMovieClip ("Chart", depth + 1);
		//Re-position the chart Movie clip to required x and y position
		this.cMC._x = this.x;
		this.cMC._y = this.y;
		//Create movie clip for tool tip
		this.ttMC = parentMC.createEmptyMovieClip ("ToolTip", depth + 3);
		//Initialize tool tip by setting co-ordinates and span area
		this.tTip = new ToolTip (this.ttMC, this.x, this.y, this.width, this.height, 8);
		//Text-field test movie clip
		this.tfTestMC = parentMC.createEmptyMovieClip ("TestTF", depth + 4);
		//Create the movie clip holder for log
		this.logMC = parentMC.createEmptyMovieClip ("Log", depth + 2);
		//Re-position the log Movie clip to required x and y position
		this.logMC._x = this.x;
		this.logMC._y = this.y;
		//Create the log instance
		this.lgr = new Logger (logMC, this.width, this.height);
		if (this.debugMode)
		{
			/**
			* If the chart is in debug mode, we:
			* - Show log.
			*/
			//Log the chart parameters
			this.log ("Info", "Chart loaded and initialized.", Logger.LEVEL.INFO);
			this.log ("Initial Width", String (this.width) , Logger.LEVEL.INFO);
			this.log ("Initial Height", String (this.height) , Logger.LEVEL.INFO);
			this.log ("Scale Mode", this.scaleMode, Logger.LEVEL.INFO);
			this.log ("Debug Mode", (this.debugMode == true) ? "Yes" : "No", Logger.LEVEL.INFO);
			this.log ("Application Message Language", this.lang, Logger.LEVEL.INFO);
			//Now show the log
			lgr.show ();
		}
		//Expose image saving functionality to JS. 
		if (this.registerWithJS==true && ExternalInterface.available){
			ExternalInterface.addCallback("saveAsImage",this, saveAsImage);
		}
		//Expose the methods to JavaScript using ExternalInterface		
		if (this.registerWithJS==true && ExternalInterface.available){
			ExternalInterface.addCallback("print", this, printChart);
		}
		//Initialize EventDispatcher to implement the event handling functions
		mx.events.EventDispatcher.initialize(this);
	}
	//These functions are defined in the class to prevent
	//the compiler from throwing an error when they are called
	//They are not implemented until EventDispatcher.initalize(this)
	//overwrites them.
	public function dispatchEvent() {
	}
	public function addEventListener() {
	}
	public function removeEventListener() {
	}
	public function dispatchQueue() {
	}
	/**
	 * exposeChartRendered method is called when the chart has rendered. 
	 * Here, we expose the event to JS (if required) & also dispatch a
	 * event (so that, if other movies are loading this chart, they can listen).
	*/
	private function exposeChartRendered():Void{
		//Proceed, if we've not already conveyed the event
		if (this.renderEventConveyed==false){
			//Expose event to JS
			if (this.registerWithJS==true &&  ExternalInterface.available){
				ExternalInterface.call("FC_Rendered", this.DOMId);
			}
			//Dispatch an event to loader class
			this.dispatchEvent({type:"rendered", target:this});
			//Update flag that we've conveyed both rendered events now.
			this.renderEventConveyed = true;
		}
		//Clear calling interval
		clearInterval(this.config.intervals.renderedEvent);
	}
	//----------- DATA RELATED AND PARSING METHODS ----------------//
	/**
	* setXMLData helps set the XML data for the chart. The XML data
	* is acquired from external source. Like, if you load the chart
	* in your movie, you need to create/load the XML data and handle
	* the loading events (etc.). Finally, when the proper XML is loaded,
	* you need to pass it to Chart class. When FusionCharts is loaded
	* in HTML, the .fla file loads the XML and displays loading progress
	* bar and text. Finally, when loaded, errors are checked for, and if
	* ok, the XML is passed to chart for further processing and rendering.
	*	@param	objXML	XML Object containing the XML Data
	*	@return		Nothing.
	*/
	public function setXMLData (objXML : XML) : Void 
	{
		//If the XML data has no child nodes, we display error
		if ( ! objXML.hasChildNodes ())
		{
			//Show "No data to display" error
			tfAppMsg = this.renderAppMessage (_global.getAppMessage ("NODATA", this.lang));
			//Add a message to the log.
			this.log ("ERROR", "No data to display. There isn't any node/element in the XML document. Please check if your dataURL is properly URL Encoded or, if XML has been correctly embedded in case of dataXML.", Logger.LEVEL.ERROR);
		} else 
		{
			//We've data.
			//Store the XML data in class
			this.xmlData = new XML ();
			this.xmlData = objXML;
			//Show rendering chart message
			tfAppMsg = this.renderAppMessage (_global.getAppMessage ("RENDERINGCHART", this.lang));
			//If the chart is in debug mode, then add XML data to log
			if (this.debugMode)
			{
				var strXML : String = this.xmlData.toString ();
				//First we need to convert < and > in XML to &lt; and &gt;
				//As our logger textbox is HTML enabled.
				strXML = StringExt.replace (strXML, "<", "&lt;");
				strXML = StringExt.replace (strXML, ">", "&gt;");
				//Also convert carriage returns to <BR> for better viewing.
				strXML = StringExt.replace (strXML, "/r", "<BR>");
				this.log ("XML Data", strXML, Logger.LEVEL.CODE);
			}
		}
	}
	/**
	* parseStyleXML method parses the XML nodes which defines the Styles
	* elements (application and definition). This method is common to all
	* charts, as STYLE is an integral part of FusionCharts v3. So, we've
	* defined this parsing in Chart class, to avoid code duplication.
	*	@param	arrStyleNodes	Array of XML Nodes containing style definition
	*	@return				Nothing
	*/
	private function parseStyleXML (arrStyleNodes : Array) : Void
	{
		//Loop variables
		var k : Number;
		var l : Number;
		var m : Number;
		//Search for Definition Node first
		for (k = 0; k < arrStyleNodes.length; k ++)
		{
			if (arrStyleNodes [k].nodeName.toUpperCase () == "DEFINITION")
			{
				//Store the definition nodes in arrDefNodes
				var arrDefNodes : Array = arrStyleNodes [k].childNodes;
				//Iterate through each definition node and extract the style parameters
				for (l = 0; l < arrDefNodes.length; l ++)
				{
					//If the node name is STYLE, store it
					if (arrDefNodes [l].nodeName.toUpperCase () == "STYLE")
					{
						//Store the node reference
						var styleNode : XMLNode = arrDefNodes [l];
						//Get the attributes array
						var styleAttr : Array = this.getAttributesArray (styleNode)
						//Get attributes of style definition
						var styleName : String = styleAttr ["name"];
						var styleTypeName : String = styleAttr ["type"];
						//Now, if the style type identifier is valid, we proceed
						try 
						{
							//Get the style type id from style type name
							var styleTypeId : Number = this.styleM.getStyleTypeId (styleTypeName);
							//If the control comes here, that means the style type identifier is correct.
							//Create a style object to store the attributes for this style
							var styleObj : StyleObject = new StyleObject ();
							//Now, iterate through all attributes and store them in style obj
							//WE NECESSARILY NEED TO CONVERT ALL ATTRIBUTES TO LOWER CASE
							//BEFORE STORING IT IN STYLE OBJECT
							var attr : Object;
							for (attr in styleNode.attributes)
							{
								styleObj [attr.toLowerCase ()] = styleNode.attributes [attr];
							}
							//Add this style to style manager
							this.styleM.addStyle (styleName, styleTypeId, styleObj);
						} catch (e : com.fusioncharts.helper.FCError)
						{
							//If the control comes here, that means the given style type
							//identifier is invalid. So, we log the error message to the
							//logger.
							this.log (e.name, e.message, e.level);
						}
					}
				}
			}
		}
		//Definition nodes have been stored. So search and store application nodes
		for (k = 0; k < arrStyleNodes.length; k ++)
		{
			if (arrStyleNodes [k].nodeName.toUpperCase () == "APPLICATION")
			{
				//Store the application nodes in arrAppNodes
				var arrAppNodes : Array = arrStyleNodes [k].childNodes;
				for (l = 0; l < arrAppNodes.length; l ++)
				{
					//If it's an application definition
					if (arrAppNodes [l].nodeName.toUpperCase () == "APPLY")
					{
						//Get attributes array
						var appAttr : Array = this.getAttributesArray (arrAppNodes [l]);
						//Extract the attributes
						var toObject : String = appAttr ["toobject"];
						//NECESSARILY CONVERT toObject TO UPPER CASE FOR MATCHING
						toObject = toObject.toUpperCase ();
						var styles : String = appAttr ["styles"];
						//Now, we need to check if the given Object is a valid chart object
						if (isChartObject (toObject))
						{
							//If it's a valid chart object, we get the id of the object
							//and associate the list of styles.
							//First, we need to convert the comma separated list of styles
							//into an array
							var arrStyles : Array = new Array ();
							arrStyles = styles.split (",");
							//Get the numeric id of the chart object
							var objectId = this.objects [toObject];
							//Now iterate through each style defined for this object and associate
							for (m = 0; m < arrStyles.length; m ++)
							{
								try
								{
									//Associate object with style.
									this.styleM.associateStyle (objectId, arrStyles [m]);
								}
								catch (e : com.fusioncharts.helper.FCError)
								{
									//If the control comes here, that means the given object name
									//is invalid. So, we log the error message to the logger.
									this.log (e.name, e.message, e.level);
								}
							}
						} 
						else
						{
							this.log ("Invalid Chart Object in Style Definition", "\"" + toObject + "\" is not a valid Chart Object. Please see the list above for valid Chart Objects.", Logger.LEVEL.ERROR);
						}
					}
				}
			}
		}
		//Clear garbage
		delete arrDefNodes;
		delete arrAppNodes;
		delete styleNode;
		delete attr;
	}
	//----------- CORE FUNCTIONAL METHODS ----------//
	/**
	* setChartObjects method stores the list of chart objects
	* in local arrObjects array and objects enumeration.
	*	@param	arrObjects	Array listing chart objects.
	*	@return			Nothing
	*/
	private function setChartObjects (arrObjects : Array) : Void 
	{
		//Copy array to instance variable
		this.arrObjects = arrObjects;
		//Store the list of chart objects in enumeration
		this.objects = new FCEnum (arrObjects);
		//Create the list if in debug mode and add to log
		var i : Number;
		if (this.debugMode)
		{
			var strChartObjects : String = "";
			for (i = 0; i < arrObjects.length; i ++)
			{
				strChartObjects += "<LI>" + arrObjects [i] + "</LI>";
			}
			this.log ("Chart Objects", strChartObjects, Logger.LEVEL.INFO);
		}
	}
	/**
	* isChartObject method helps check whether the given
	* object name is a valid chart object (pre-defined).
	*	@param	isChartObject	Name of chart object
	*	@return				Boolean value indicating whether
	*							it's a valid chart object.
	*/
	private function isChartObject (strObjName : String) : Boolean 
	{
		//By default assume invalid
		var isValid : Boolean = false;
		var i : Number;
		//Iterate through the list of objects to see if this is
		//valid
		for (i = 0; i < arrObjects.length; i ++)
		{
			if (arrObjects [i].toUpperCase () == strObjName)
			{
				isValid = true;
				break;
			}
		}
		return isValid;
	}
	/**
	* setToolTipParam method sets the parameter for tool tip.
	*/
	private function setToolTipParam ()
	{
		//Get the style object for tool tip
		var tTipStyleObj : Object = this.styleM.getTextStyle (this.objects.TOOLTIP);
		this.tTip.setParams (tTipStyleObj.font, tTipStyleObj.size, tTipStyleObj.color, tTipStyleObj.bgColor, tTipStyleObj.borderColor, tTipStyleObj.isHTML, false);
	}
	/**
	* log method records a message to the chart's logger. We record
	* messages in the logger, only if the chart is in debug mode to
	* save memory
	*	@param	strTitle	Title of messsage
	*	@param	strMsg		Message to be logged
	*	@param	intLevel	Numeric level of message - a value from
	*						Logger.LEVEL Enumeration
	*/
	public function log (strTitle : String, strMsg : String, intLevel : Number)
	{
		if (debugMode)
		{
			lgr.record (strTitle, strMsg, intLevel);
		}
	}
	/**
	 * returnAbtMenuItem method returns a context menu item that reads
	 * "About FusionCharts".
	*/
	private function returnAbtMenuItem():ContextMenuItem{
		//Create a about context menu item
		var aboutCMI : ContextMenuItem = new ContextMenuItem ("About FusionCharts", Delegate.create (this, openAboutFusionCharts));
		aboutCMI.separatorBefore = true;		
		return aboutCMI;
	}
	/**
	 * returnImageSaveMenuItem method returns the context menu item to save image.
	*/
	private function returnImageSaveMenuItem():ContextMenuItem{
		//Create the context menu item
		var saveCMI : ContextMenuItem = new ContextMenuItem ("Save as Image", Delegate.create (this, saveAsImage));
		saveCMI.separatorBefore = true;		
		return saveCMI;
	}
	/**
	 * openAboutFusionCharts is the handler for "About FusionCharts"
	 * context menu item
	*/
	private function openAboutFusionCharts():Void{
		//Open FusionCharts website
		getURL("http://www.fusioncharts.com/","_blank");
	}		
	/**
	 * saveAsImage method saves the chart as an image.
	*/
	private function saveAsImage():Void{
		//Here, we do the following:
		//1. Create an instance of BitmapSave to capture the chart's image.
		//2. Define listener objects to track progress of it.
		//3. Send data to the defined server side script.
		//RLE Compression is done while capturing the bitmap data.
		
		//Reference to this class
		var classRef = this;
		
		//Create listener object for capture.
		var cList:Object = new Object();		

		//Progress bar positioning and dimension
		var PBWidth:Number = (this.width > 200) ? 150 : (this.width - 25);
		var PBStartX:Number = this.x + this.width/2 - PBWidth/2;
		var PBStartY:Number = this.y + this.height/2 - 15;

		//Create a progress bar to show capturing pixels.
		var imgSaveBg:MovieClip;
		//Text field to show percent complete for image saving progress bar
		var tfImageSavePB:TextField;
		//Create the empty movie clips
		imgSaveBg = this.parentMC.createEmptyMovieClip("ImgSaveBg", this.depth+5);
		//Create a black overlay rectangle
		imgSaveBg.beginFill(0x000000,20);
		imgSaveBg.moveTo(this.x, this.y);
		imgSaveBg.lineTo(this.x + this.width, this.y);
		imgSaveBg.lineTo(this.x + this.width, this.y + this.height);
		imgSaveBg.lineTo(this.x, this.y + this.height);
		imgSaveBg.lineTo(this.x, this.y);
		
		//Another white rectangle in center
		var pad:Number = 20;
		imgSaveBg.beginFill(0xFFFFFF,100);
		imgSaveBg.lineStyle(1, 0xE2E2E2, 100);
		imgSaveBg.moveTo(PBStartX - pad, PBStartY - pad);
		imgSaveBg.lineTo(PBStartX  + PBWidth + pad, PBStartY - pad);
		imgSaveBg.lineTo(PBStartX  + PBWidth + pad, PBStartY + 40 + pad);
		imgSaveBg.lineTo(PBStartX  - pad , PBStartY + 40 + pad);
		imgSaveBg.lineTo(PBStartX - pad, PBStartY - pad);
		
		//Capture mouse event from everything otherwise underneath
		imgSaveBg.useHandCursor = false;
		imgSaveBg.onRollOver = function(){
		}
		
		//Instantiate the progress bar
		var imgSPB:FCProgressBar = new FCProgressBar(this.parentMC, this.depth+6, 0, 100, PBStartX, PBStartY, PBWidth, 15, this.params.imageSaveDialogColor, this.params.imageSaveDialogColor, 1);
		
		//Create the text
		tfImageSavePB = Utils.createText (false, "Capturing data", this.parentMC, this.depth+7, this.x + this.width/2, this.y + this.height/2 + 15, null, {align:"center", vAlign:"bottom", bold:false, italic:false, underline:false, font:"Verdana", size:10, color:this.params.imageSaveDialogFontColor, isHTML:true, leftMargin:0, letterSpacing:0, bgColor:"", borderColor:""}, false, 0, 0).tf;
		var tFormat:TextFormat = tfImageSavePB.getTextFormat();
		
		//Event to detect when capturing is complete.
		cList.onCaptureComplete = function(eventObj:Object){
			//Remove all progress bar related movie clips
			imgSPB.destroy();
			tfImageSavePB.removeTextField();
			imgSaveBg.removeMovieClip();			
			//Capturing is complete. Send it to server side script.			
			classRef.sendImageData(eventObj.out);			
		}
		
		//Event to detect progress of capturing
		cList.onProgress = function(eventObj:Object){
			//Update the text field
			tfImageSavePB.htmlText = "<font face='Verdana' size='10' color='#" + this.params.imageSaveDialogFontColor + "'>Capturing data: " + eventObj.percentDone + "%</font>";
			tfImageSavePB.setTextFormat(tFormat);
			//Set the value of progress bar
			imgSPB.setValue(eventObj.percentDone);
		}

		//Create an instance of BitmapSave 
		var bmpS:BitmapSave = new BitmapSave(this.cMC,this.x,this.y,this.width,this.height,0xffffff);		
		
		//Before we start capturing, we need to make sure that none of the movie clips
		//are cached as bitmap. So run a function that does this job.
		if(!this.cMC.skipBmpCacheCheck){
			var arrCache:Array = this.setPreSaving(this.cMC);
		}
		
		//Capture the bitmap now.
		bmpS.capture();
		
		//Now that the bitmap is captured, we need to set the cache property to original
		if(!this.cMC.skipBmpCacheCheck){
			this.resetPostSaving(this.cMC, arrCache)
		}
		
		//Add the event listeners
		bmpS.addEventListener("onCaptureComplete", cList);
		bmpS.addEventListener("onProgress", cList);
		
		return;	
	}	
	/**
	 * This method sets the bitmap caching of all objects in the chart
	 * so as to avoid freezing of interface.
	*/
	private function setPreSaving(mc:MovieClip):Array{
		//Get the list of filters.
		var arrMcFilters:Array = new Array()
		//Iterate through each movie clip
		for(var i in mc){
			//Work only if it's a movie clip.
			if(mc[i] instanceof MovieClip){
				//Store the filters for this MC
				arrMcFilters[i] = new Array();
				arrMcFilters[i]['filters'] = mc[i].filters;
				mc[i].filters = [];
				//Store the cache property
				arrMcFilters[i]['cache'] = mc[i].cacheAsBitmap;
				mc[i].cacheAsBitmap = false;
				//Store children
				arrMcFilters[i]['children'] = arguments.callee(mc[i]);
			}
		}
		//Return the array
		return arrMcFilters;
	}
	/**
	 * This method restores the bitmap caching state of all the objects
	 * in the chart, once capturing is done.
	*/
	private function resetPostSaving(mc:MovieClip, arrMcFilters:Array){
		for(var i in arrMcFilters){			
			mc[i].filters = arrMcFilters[i]['filters'];
			mc[i].cacheAsBitmap = arrMcFilters[i]['cache'];
			arguments.callee(mc[i],arrMcFilters[i]['children']);
		}
	}
	
	/**
	 * sendImageData actually sends the image data to the server.
	*/
	private function sendImageData(strData:String):Void{
		//Create a loadvars object
		var l:LoadVars = new LoadVars();		
		//Set width and height
		l.width = this.width;
		l.height = this.height;
		//Color to ignore
		l.bgcolor = "FFFFFF";
		l.data = strData;
		//Send the data to server side script.
		l.send(this.params.imageSaveURL, "_self", "POST");
		//Delete loadvars after sending data
		delete l;
	}
	/**
	* printChart method prints the chart.
	*/
	public function printChart () : Void
	{
		//Create a Print Job Instance
		var PrintQueue = new PrintJob ();
		//Show the Print box.
		var PrintStart : Boolean = PrintQueue.start ();
		//If User has selected Ok, set the parameters.
		if (PrintStart)
		{
			//Add the chart MC to the print job with the required dimensions
			//If the chart width/height is bigger than page width/height, we need to scale.
			if (this.width>PrintQueue.pageWidth || this.height>PrintQueue.pageHeight){				
				//Scale on the lower factor
				var factor:Number = Math.min((PrintQueue.pageWidth/this.width),(PrintQueue.pageHeight/this.height));
				//Scale the movie clip to fit paper size 
				this.cMC._xScale = factor*100;
				this.cMC._yScale = factor*100;
			}
			//Add the chart to printer queue
			PrintQueue.addPage (this.cMC, {xMin : 0, xMax : this.width, yMin : 0, yMax : this.height}, {printAsBitmap : true});
			//Send the page for printing
			PrintQueue.send ();
			//Re-scale back to normal form (as the printing is over).
			this.cMC._xScale = this.cMC._yScale = 100;
		}		
		delete PrintQueue;
	}
	/**
	* reInit method re-initializes the chart. This method is basically called
	* when the user changes chart data through JavaScript. In that case, we need
	* to re-initialize the chart, set new XML data and again render.
	*/
	public function reInit () : Void
	{
		//Re-initialize params and config object
		this.params = new Object ();
		this.config = new Object ();
		//Object to store chart rendering intervals
		this.config.intervals = new Object ();
		//Re-create an empty chart movie clip
		this.cMC = parentMC.createEmptyMovieClip ("Chart", depth + 1);		
		//Re-position the chart Movie clip to required x and y position
		this.cMC._x = this.x;
		this.cMC._y = this.y;
		//Reset the style manager
		this.styleM = new StyleManager (this);
		//Reset macros
		this.macro = new Macros ();
		//Reset the default colors iterator
		this.defColors.reset ();
		//Reset depth manager
		this.dm.clear ();
		this.dm.setStartDepth (0);
		//Set timeElapsed to 0
		this.timeElapsed = 0;
	}
	/**
	* remove method removes the chart by clearing the chart movie clip
	* and removing any listeners. However, the logger still stays on.
	* To remove the logger too, you need to call destroy method of chart.
	*/
	public function remove () : Void 
	{
		//Remove all the intervals (which might not have been cleared)
		//from this.config.intervals
		var item : Object;
		for (item in this.config.intervals)
		{
			//Clear interval
			clearInterval (this.config.intervals [item]);
		}
		//Remove application message (if any)
		this.removeAppMessage (this.tfAppMsg);
		//Remove the chart movie clip
		cMC.removeMovieClip ();
		//Hide tool tip
		tTip.hide ();
	}
	/**
	* destroy method destroys the chart by removing the chart movie clip,
	* logger movie clip, and removing any listeners.
	*/
	public function destroy () : Void 
	{
		//Remove the chart first
		this.remove ();
		//Remove the chart movie clip
		cMC.removeMovieClip ();
		//Destroy the logger
		this.lgr.destroy ();
		//Destroy tool tip
		this.tTip.destroy ();
		//Remove tool tip movie clip
		this.ttMC.removeMovieClip ();
		//Remove test text field movie clip
		this.tfTestMC.removeMovieClip ();
		//Remove logger movie clip
		this.logMC.removeMovieClip ();
	}
	// -------------------- UTILITY METHODS --------------------//
	/**
	* getAttributesArray method helps convert the list of attributes
	* for an XML node into an array.
	* Reason:
	* Starting ActionScript 2, the parsing of XML attributes have also
	* become case-sensitive. However, prior versions of FusionCharts
	* supported case-insensitive attributes. So we need to parse all
	* attributes as case-insensitive to maintain backward compatibility.
	* To do so, we first extract all attributes from XML, convert it into
	* lower case and then store it in an array. Later, we extract value from
	* this array.
	* Once this array is returned, IT'S VERY NECESSARY IN THE CALLING CODE TO
	* REFERENCE THE NAME OF ATTRIBUTE IN LOWER CASE (STORED IN THIS ARRAY).
	* ELSE, UNDEFINED VALUE WOULD SHOW UP.
	*	@param	xmlNd	XML Node for which we've to get the attributes.
	*	@return		An associative array containing the attributes. The name
	*					of attribute (in all lower case) is the key and attribute value
	*					is value.
	*/
	private function getAttributesArray (xmlNd : XMLNode) : Array
	{
		//Array that will store the attributes
		var	atts : Array = new Array ();
		//Object used to iterate through the attributes collection
		var obj : Object;
		//Iterate through each attribute in the attributes collection,
		//convert to lower case and store it in array.
		for (obj in xmlNd.attributes)
		{
			//Store it in array
			atts [obj.toString ().toLowerCase ()] = xmlNd.attributes [obj];
		}
		//Return the array
		return atts;
	}
	/**
	* returnDataAsElement method returns the data passed to this
	* method as an Element Object. We store each chart element as an
	* obejct to unify the various properties.
	*	@param	x		Start X of the element
	*	@param	y		Start Y of the element
	*	@param	w		Width of the element
	*	@param	h		Height of the element
	*	@return		Object representing the element
	*/
	private function returnDataAsElement (x : Number, y : Number, w : Number, h : Number) : Object
	{
		//Create new object
		var element : Object = new Object ();
		element.x = x;
		element.y = y;
		element.w = w;
		element.h = h;
		//Calculate and store toX and toY
		element.toX = x + w;
		element.toY = y + h;
		//Return
		return element;
	}
	/**
	* toBoolean method converts numeric form (1,0) to Flash
	* boolean.
	*	@param	num		Number (0,1)
	*	@return		Boolean value based on above number
	*/
	private function toBoolean (num : Number) : Boolean
	{
		return ((num == 1) ? (true) : (false));
	}
	/**
	* invokeLink method invokes a link for any defined drill down item.
	* A link in XML needs to be URL Encoded. Also, there are a few prefixes,
	* parameters that can be added to link so as to defined target link
	* opener object. For e.g., a link can open in same window, new window,
	* frame, pop-up window. Or, a link can invoke a JavaScript method.
	* Prefixes can be N - new window, F - Frame, P - Pop up
	*	@param	strLink	Link to be invoked.
	*/
	private function invokeLink (strLink : String) : Void
	{
		//We continue only if the link is not empty
		if (strLink != undefined && strLink != null && strLink != "")
		{
			//Unescape the link - as it might be URL Encoded
			strLink = (this.params.unescapeLinks)?(unescape (strLink)):(strLink);
			//Now based on what the first character in the link is (N, F, P, S, J)
			//we invoke the link.
			if (strLink.charAt (0).toUpperCase () == "N" && strLink.charAt (1) == "-")
			{
				//Means we have to open the link in a new window.
				getURL (strLink.slice (2) , "_blank");
			} else if (strLink.charAt (0).toUpperCase () == "F" && strLink.charAt (1) == "-")
			{
				//Means we have to open the link in a frame.
				var dashPos : Number = strLink.indexOf ("-", 2);
				//strLink.slice(dashPos+1) indicates link
				//strLink.substr(2, dashPos-2) indicates frame name
				getURL (strLink.slice (dashPos + 1) , strLink.substr (2, dashPos - 2));
			} else if (strLink.charAt (0).toUpperCase () == "P" && strLink.charAt (1) == "-")
			{
				//Means we have to open the link in a pop-up window.
				var dashPos : Number = strLink.indexOf ("-", 2);
				var commaPos : Number = strLink.indexOf (",", 2);
				getURL ("javaScript:var " + strLink.substr (2, commaPos - 2) + " = window.open('" + strLink.slice (dashPos + 1) + "','" + strLink.substr (2, commaPos - 2) + "','" + strLink.substr (commaPos + 1, dashPos - commaPos - 1) + "'); " + strLink.substr (2, commaPos - 2) + ".focus(); void(0);");
			} else if (strLink.charAt (0).toUpperCase () == "J" && strLink.charAt (1) == "-")
				{
					//We can operate JS link only if ExternalInterface is available and the chart 
					//has been registered
					if (this.registerWithJS==true &&  ExternalInterface.available){
						//Means we have to open the link as JavaScript
						var dashPos : Number = strLink.indexOf ("-", 2);
						//strLink.slice(dashPos+1) indicates arguments if any
						//strLink.substr(2, dashPos-2) indicates link
						//If no arguments, just call the link
						if (dashPos==-1){
							ExternalInterface.call(strLink.substr(2, strLink.length-2));
						}else{
							//There could be multiple parameters. We just pass them as a single string to JS method.
							ExternalInterface.call(strLink.substr(2, dashPos-2), strLink.slice(dashPos+1));
						}
					}
				}else if (strLink.charAt (0).toUpperCase () == "S" && strLink.charAt (1) == "-")
			{
				//Means we have to convey the link as a event to parent Flash movie
				this.dispatchEvent({type:"linkClicked", target:this, link:strLink.slice (2)});				
			} else 
			{
				//Open the link in same window
				getURL (strLink, "_self");
			}
		}
	}
	/**
	* renderAppMessage method helps display an application message to
	* end user.
	* @param	strMessage	Message to be displayed
	* @return				Reference to the text field created
	*/
	private function renderAppMessage (strMessage : String) : TextField 
	{
		return _global.createBasicText (strMessage, this.parentMC, depth, this.x + (this.width / 2) , this.y + (this.height / 2) , "Verdana", 10, "666666", "center", "bottom");
	}
	/**
	* removeAppMessage method removes the displayed application message
	* @param	tf	Text Field reference to the message
	*/
	private function removeAppMessage (tf : TextField)
	{
		tf.removeTextField ();
	}
	// --------------------- VISUAL RENDERING METHODS ------------------//
	/**
	* drawBackground method renders the chart background. The background
	* cant be solid color or gradient. All charts have a backround. So, we've
	* defined drawBackground in Chart class itself, so that sub classes can
	* directly access it (as it's common).
	*	@return		Nothing
	*/
	private function drawBackground () : Void
	{
		//Create a new movie clip container for background
		var bgMC = this.cMC.createEmptyMovieClip ("Background", this.dm.getDepth ("BACKGROUND"));
		//Parse the color, alpha and ratio array
		var bgColor : Array = ColorExt.parseColorList (this.params.bgColor);
		var bgAlpha : Array = ColorExt.parseAlphaList (this.params.bgAlpha, bgColor.length);
		var bgRatio : Array = ColorExt.parseRatioList (this.params.bgRatio, bgColor.length);
		//Move to (-w/2, 0); - 0,0 registration point at center (x,y)
		bgMC.moveTo ( - (this.width / 2) , - (this.height / 2));
		//Create matrix object
		var matrix : Object = {
			matrixType : "box", w : this.width, h : this.height, x : - (this.width / 2) , y : - (this.height / 2) , r : MathExt.toRadians (this.params.bgAngle)
		};
		//If border is to be shown
		if (this.params.showBorder)
		{
			bgMC.lineStyle (this.params.borderThickness, parseInt (this.params.borderColor, 16) , this.params.borderAlpha);
		}
		//Border thickness half
		var bth : Number = this.params.borderThickness / 2;
		//Start the fill.
		bgMC.beginGradientFill ("linear", bgColor, bgAlpha, bgRatio, matrix);
		//Draw the rectangle with center registration point
		bgMC.lineTo (this.width / 2 - bth, - (this.height / 2) + bth);
		bgMC.lineTo (this.width / 2 - bth, this.height / 2 - bth);
		bgMC.lineTo ( - (this.width / 2) + bth, this.height / 2 - bth);
		bgMC.lineTo ( - (this.width / 2) + bth, - (this.height / 2) + bth);
		//Set the x and y position
		bgMC._x = this.width / 2;
		bgMC._y = this.height / 2;
		//End Fill
		bgMC.endFill ();
		//Apply animation
		if (this.params.animation)
		{
			this.styleM.applyAnimation (bgMC, this.objects.BACKGROUND, this.macro, bgMC._x, - this.width / 2, bgMC._y, - this.height / 2, 100, 100, 100, null);
		}
		//Apply filters
		this.styleM.applyFilters (bgMC, this.objects.BACKGROUND);		
	}
	/**
	* loadBgSWF method loads the background .swf file (if required)
	*/
	private function loadBgSWF () : Void
	{
		//We load the BG SWF only if it has been specified and it doesn't contain any colon characters
		//(to disallow XSS attacks)
		if (this.params.bgSWF != "")
		{
			if (this.params.bgSWF.indexOf(":")==-1 && this.params.bgSWF.indexOf("%3A")==-1){
				//Create a movie clip container
				var bgSWFMC : MovieClip = this.cMC.createEmptyMovieClip ("BgSWF", this.dm.getDepth ("BGSWF"));
				//Load the clip
				bgSWFMC.loadMovie (this.params.bgSWF);
				//Set alpha
				bgSWFMC._alpha = this.params.bgSWFAlpha;
			}else{
				this.log ("bgSWF not loaded", "The bgSWF path contains special characters like colon, which can be potentially dangerous in XSS attacks. As such, FusionCharts has not loaded the bgSWF. If you've specified the absolute path for bgSWF URL, we recommend specifying relative path under the same domain.", Logger.LEVEL.ERROR);
			}
		}
	}
	/**
	* drawClickURLHandler method draws the rectangle over the chart
	* that responds to click URLs. It draws only if clickURL has been
	* defined for the chart.
	*/
	private function drawClickURLHandler () : Void
	{
		//Check if it needs to be created
		if (this.params.clickURL != "")
		{
			//Create a new movie clip container for background
			var clickMC = this.cMC.createEmptyMovieClip ("ClickURLHandler", this.dm.getDepth ("CLICKURLHANDLER"));
			clickMC.moveTo (0, 0);
			//Set fill with 0 alpha
			clickMC.beginFill (0xffffff, 0);
			//Draw the rectangle
			clickMC.lineTo (this.width, 0);
			clickMC.lineTo (this.width, this.height);
			clickMC.lineTo (0, this.height);
			clickMC.lineTo (0, 0);
			//End Fill
			clickMC.endFill ();
			clickMC.useHandCursor = true;
			//Set click handler
			var strLink : String = this.params.clickURL;
			var chartRef : Chart = this;
			clickMC.onMouseDown = function ()
			{
				chartRef.invokeLink (strLink);
			}
			clickMC.onRollOver = function ()
			{
				//Empty function just to show hand cursor
				
			}
		}
	}
	// ---------- NUMBER DETECTION, FORMATTING RELATED METHODS -------//
	/**
	* detectNumberScales method detects whether we've been provided
	* with number scales. If yes, we parse them. This method needs to
	* called before calculatePoints, as calculatePoint methods calls
	* formatNumber, which in turn uses number scales.
	* Here, we also adjust the numberPrefix and numberSuffix if they're
	* still present in encoded form. Like, if the user has specified encoded
	* numberPrefix and Suffix in dataURL mode, it will show the direct value.
	* Therefore, we need to change them into proper characters.
	* Finally, we set the proper number of decimals for the chart based on the
	* decimal input.
	*	@return	Nothing.
	*/
	private function detectNumberScales () : Void
	{
		//Check if either has been defined
		if (this.params.numberScaleValue.length == 0 || this.params.numberScaleUnit.length == 0 || this.params.formatNumberScale == false)
		{
			//Set flag to false
			this.config.numberScaleDefined = false;
		} else 
		{
			//Set flag to true
			this.config.numberScaleDefined = true;
			//Split the data into arrays
			this.config.nsv = new Array ();
			this.config.nsu = new Array ();
			//Parse the number scale value
			this.config.nsv = this.params.numberScaleValue.split (",");
			//Convert all number scale values to numbers as they're
			//currently in string format.
			var i : Number;
			for (i = 0; i < this.config.nsv.length; i ++)
			{
				this.config.nsv [i] = Number (this.config.nsv [i]);
				//If any of numbers are NaN, set defined to false
				if (isNaN (this.config.nsv [i]))
				{
					this.config.numberScaleDefined = false;
				}
			}
			//Parse the number scale unit
			this.config.nsu = this.params.numberScaleUnit.split (",");
			//If the length of two arrays do not match, set defined to false.
			if (this.config.nsu.length != this.config.nsv.length)
			{
				this.config.numberScaleDefined = false;
			}
		}
		//Convert numberPrefix and numberSuffix now.
		this.params.numberPrefix = this.unescapeChar (this.params.numberPrefix);
		this.params.numberSuffix = this.unescapeChar (this.params.numberSuffix);
		//Always keep to a decimal precision of minimum 2 if the number
		//scale is defined, as we've just checked for decimal precision of numbers
		//and not the numbers against number scale. So, even if they do not need yield a
		//decimal, we keep 2, as we do not force decimals on numbers.
		if (this.config.numberScaleDefined == true)
		{
			maxDecimals = (maxDecimals > 2) ?maxDecimals : 2;
		}
		//Get proper value for decimals
		this.params.decimals = Number (getFV (this.params.decimals, maxDecimals));
		//Decimal Precision cannot be less than 0 - so adjust it
		if (this.params.decimals < 0)
		{
			this.params.decimals = 0;
		}
		//Get proper value for yAxisValueDecimals
		this.params.yAxisValueDecimals = Number (getFV (this.params.yAxisValueDecimals, this.params.decimals));
	}
	/**
	* unescapeChar method helps to unescape certain escape characters
	* which might have got through the XML. Like, %25 is escaped back to %.
	* This function would be used to format the number prefixes and suffixes.
	*	@param	strChar		The character or character sequence to be unescaped.
	*	@return			The unescaped character
	*/
	private function unescapeChar (strChar : String) : String
	{
		//Perform only if strChar is defined
		if (strChar == "" || strChar == undefined)
		{
			return "";
		}
		//If it doesnt contain a %, return the original string
		if (strChar.indexOf ("%") == - 1)
		{
			return strChar;
		}
		//We're not doing a case insensitive search, as there might be other
		//characters provided in the Prefix/Suffix, which need to be present in lowe case.
		//Create the conversion table.
		var cTable : Array = new Array ();
		cTable.push (
		{
			char : "%", encoding : "%25"
		});
		cTable.push (
		{
			char : "&", encoding : "%26"
		});
		cTable.push (
		{
			char : "£", encoding : "%A3"
		});
		cTable.push (
		{
			char : "€", encoding : "%E2%82%AC"
		});
		//v2.3 Backward compatible Euro
		cTable.push (
		{
			char : "€", encoding : "%80"
		});
		cTable.push (
		{
			char : "¥", encoding : "%A5"
		});
		cTable.push (
		{
			char : "¢", encoding : "%A2"
		});
		cTable.push (
		{
			char : "₣", encoding : "%E2%82%A3"
		});
		cTable.push (
		{
			char : "+", encoding : "%2B"
		});
		cTable.push (
		{
			char : "#", encoding : "%23"
		});
		//Loop variable
		var i : Number;
		//Return string (escaped)
		var rtnStr : String = strChar;
		for (i = 0; i < cTable.length; i ++)
		{
			if (strChar == cTable [i].encoding)
			{
				//If the given character matches the encoding, convert to character
				rtnStr = cTable [i].char;
				break;
			}
		}
		//Return it
		return rtnStr;
		//Clean up
		delete cTable;
	}
	/**
	* formatNumber method helps format a number as per the user
	* requirements.
	* Requires this.config.numberScaleDefined to be defined (boolean)
	*	@param		intNum				Number to be formatted
	*	@param		bFormatNumber		Flag whether we've to format
	*									decimals and add commas
	*	@param		decimalPrecision	Number of decimal places we need to
	*									round the number to.
	*	@param		forceDecimals		Whether we force decimal padding.
	*	@param		bFormatNumberScale	Flag whether we've to format number
	*									scale
	*	@param		defaultNumberScale	Default scale of the number provided.
	*	@param		numScaleValues		Array of values (for scaling)
	*	@param		numScaleUnits		Array of Units (for scaling)
	*	@param		numberPrefix		Number prefix to be added to number.
	*	@param		numberSuffix		Number Suffix to be added.
	*	@return						Formatted number as string.
	*
	*/
	private function formatNumber (intNum : Number, bFormatNumber : Boolean, decimalPrecision : Number, forceDecimals : Boolean, bFormatNumberScale : Boolean, defaultNumberScale : String, numScaleValues : Array, numScaleUnits : Array, numberPrefix : String, numberSuffix : String) : String 
	{
		//First, if number is to be scaled, scale it
		//Number in String format
		var strNum : String = String (intNum);
		//Number Scale
		var strScale : String
		if (bFormatNumberScale)
		{
			strScale = defaultNumberScale;
		}else
		{
			strScale = "";
		}
		if (bFormatNumberScale && this.config.numberScaleDefined)
		{
			//Get the formatted scale and number
			var objNum : Object = formatNumberScale (intNum, defaultNumberScale, numScaleValues, numScaleUnits);
			//Store from return in local primitive variables
			strNum = String (objNum.value);
			intNum = objNum.value;
			strScale = objNum.scale;
		}
		//Now, if we've to format the decimals and commas
		if (bFormatNumber)
		{
			//Format decimals
			strNum = formatDecimals (intNum, decimalPrecision, forceDecimals);
			//Format commas now
			strNum = formatCommas (strNum);
		}
		//Now, add scale, number prefix and suffix
		strNum = numberPrefix + strNum + strScale + numberSuffix;
		return strNum;
	}
	/**
	* formatNumberScale formats the number as per given scale.
	* For example, if number Scale Values are 1000,1000 and
	* number Scale Units are K,M, this method will divide any
	* value over 1000000 using M and any value over 1000 (<1M) using K
	* so as to give abbreviated figures.
	* Number scaling lets you define your own scales for numbers.
	* To clarify further, let's consider an example. Say you're plotting
	* a chart which indicates the time taken by a list of automated
	* processes. Each process in the list can take time ranging from a
	* few seconds to few days. And you've the data for each process in
	* seconds itself. Now, if you were to show all the data on the chart
	* in seconds only, it won't appear too legible. What you can do is
	* build a scale of yours and then specify it to the chart. A scale,
	* in human terms, would look something as under:
	* 60 seconds = 1 minute
	* 60 minute = 1 hr
	* 24 hrs = 1 day
	* 7 days = 1 week
	* First you would need to define the unit of the data which you're providing.
	* Like, in this example, you're providing all data in seconds. So, default
	* number scale would be represented in seconds. You can represent it as under:
	* <graph defaultNumberScale='s' ...>
	* Next, the scale for the chart is defined as under:
	* <graph numberScaleValue='60,60,24,7' numberScaleUnit='min,hr,day,wk' >
	* If you carefully see this and match it with our range, whatever numeric
	* figure was present on the left hand side of the range is put in
	* numberScaleValue and whatever unit was present on the right side of
	* the scale has been put under numberScaleUnit - all separated by commas.
	
	*	@param	intNum				The number to be scaled.
	*	@param	defaultNumberScale	Scale of the number provided.
	*	@param	numScaleValues		Incremental list of values (divisors) on
	*								which the number will be scaled.
	*	@param
	*/
	private function formatNumberScale (intNum : Number, defaultNumberScale : String, numScaleValues : Array, numScaleUnits : Array) : Object 
	{
		//Create an object, which will be returned
		var objRtn : Object = new Object ();
		//Scale Unit to be stored (assume default)
		var strScale : String = defaultNumberScale;
		var i : Number = 0;
		//If the scale unit or values have something fed in them
		//we manipulate the scales.
		for (i = 0; i < numScaleValues.length; i ++)
		{
			if (Math.abs (Number (intNum)) >= numScaleValues [i])
			{
				strScale = numScaleUnits [i];
				intNum = Number (intNum) / numScaleValues [i];
			} else 
			{
				break;
			}
		}
		//Set the values as properties of objRtn
		objRtn.value = intNum;
		objRtn.scale = strScale;
		return objRtn;
	}
	/**
	* formatDecimals method formats the decimal places of a number.
	* Requires the following to be defined:
	* this.params.decimalSeparator
	* this.params.thousandSeparator
	*	@param	intNum				Number on which we've to work.
	*	@param	decimalPrecision	Number of decimal places to which we've
	*								to format the number to.
	*	@param	forceDecimals		Boolean value indicating whether to add decimal
	*								padding to numbers which are falling as whole
	*								numbers?
	*	@return					A number with the required number of decimal places
	*								in String format. If we return as Number, Flash will remove
	*								our decimal padding or un-wanted decimals.
	*/
	private function formatDecimals (intNum : Number, decimalPrecision : Number, forceDecimals : Boolean) : String 
	{
		//If no decimal places are needed, just round the number and return
		if (decimalPrecision <= 0)
		{
			return String (Math.round (intNum));
		}
		//Round the number to specified decimal places
		//e.g. 12.3456 to 3 digits (12.346)
		//Step 1: Multiply by 10^decimalPrecision - 12345.6
		//Step 2: Round it - i.e., 12346
		//Step 3: Divide by 10^decimalPrecision - 12.346
		var tenToPower : Number = Math.pow (10, decimalPrecision);
		var strRounded : String = String (Math.round (intNum * tenToPower) / tenToPower);
		//Now, strRounded might have a whole number or a number with required
		//decimal places. Our next job is to check if we've to force Decimals.
		//If yes, we add decimal padding by adding 0s at the end.
		if (forceDecimals)
		{
			//Add a decimal point if missing
			//At least one decimal place is required (as we split later on .)
			//10 -> 10.0
			if (strRounded.indexOf (".") == - 1)
			{
				strRounded += ".0";
			}
			//Finally, we start add padding of 0s.
			//Split the number into two parts - pre & post decimal
			var parts : Array = strRounded.split (".");
			//Get the numbers falling right of the decimal
			//Compare digits in right half of string to digits wanted
			var paddingNeeded : Number = decimalPrecision - parts [1].length;
			//Number of zeros to add
			for (var i = 1; i <= paddingNeeded; i ++)
			{
				//Add them
				strRounded += "0";
			}
		}
		return (strRounded);
	}
	/**
	* formatCommas method adds proper commas to a number in blocks of 3
	* i.e., 123456 would be formatted as 123,456
	*	@param	strNum	The number to be formatted (as string).
	*					Why are numbers taken in string format?
	*					Here, we are asking for numbers in string format
	*					to preserve the leading and padding 0s of decimals
	*					Like as in -20.00, if number is just passed as number,
	*					Flash automatically reduces it to -20. But, we've to
	*					make sure that we do not disturb the original number.
	*	@return		Formatted number with commas.
	*/
	private function formatCommas (strNum : String) : String 
	{
		//intNum would represent the number in number format
		var intNum : Number = Number (strNum);
		//If the number is invalid, return an empty value
		if (isNaN (intNum))
		{
			return "";
		}
		var strDecimalPart : String = "";
		var boolIsNegative : Boolean = false;
		var strNumberFloor : String = "";
		var formattedNumber : String = "";
		var startPos : Number = 0;
		var endPos : Number = 0;
		//Define startPos and endPos
		startPos = 0;
		endPos = strNum.length;
		//Extract the decimal part
		if (strNum.indexOf (".") != - 1)
		{
			strDecimalPart = strNum.substring (strNum.indexOf (".") + 1, strNum.length);
			endPos = strNum.indexOf (".");
		}
		//Now, if the number is negative, get the value into the flag
		if (intNum < 0)
		{
			boolIsNegative = true;
			startPos = 1;
		}
		//Now, extract the floor of the number
		strNumberFloor = strNum.substring (startPos, endPos);
		//Now, strNumberFloor contains the actual number to be formatted with commas
		// If it's length is greater than 3, then format it
		if (strNumberFloor.length > 3)
		{
			// Get the length of the number
			var lenNumber : Number = strNumberFloor.length;
			for (var i : Number = 0; i <= lenNumber; i ++)
			{
				//Append proper commans
				if ((i > 2) && ((i - 1) % 3 == 0))
				{
					formattedNumber = strNumberFloor.charAt (lenNumber - i) + this.params.thousandSeparator + formattedNumber; 
				} else 
				{
					formattedNumber = strNumberFloor.charAt (lenNumber - i) + formattedNumber; 
				}
			}
		} else 
		{
			formattedNumber = strNumberFloor; 
		}
		// Now, append the decimal part back
		if (strDecimalPart != "")
		{
			formattedNumber = formattedNumber + this.params.decimalSeparator + strDecimalPart; 
		}
		//Now, if neg num
		if (boolIsNegative == true)
		{
			formattedNumber = "-" + formattedNumber; 
		}
		//Return
		return formattedNumber;
	}
	/**
	* getSetValue method helps us check whether the given set value is specified.
	* If not, we take steps accordingly and return values.
	*	@param	num		Number (in string/object format) which we've to check.
	*	@return		Numeric value of the number. (or NaN)
	*/
	private function getSetValue (num) : Number
	{
		//If it's not a number, or if input separators characters
		//are explicity defined, we need to convert value.
		var setValue : Number;
		if (isNaN (num) || (this.params.inThousandSeparator != "") || (this.params.inDecimalSeparator != ""))
		{
			//Number in XML can be invalid or missing (discontinuous data)
			//So, if the length is undefined, it's missing.
			if (num.length == undefined)
			{
				//Missing data. So just add it as NaN.
				setValue = Number (num);
			}else
			{
				//It means the number can have different separator, or
				//it can be non-numeric.
				setValue = this.convertNumberSeps (num);
			}
		}else
		{
			//Simply convert it to numeric form.
			setValue = Number (num);
		}
		//Get the decimal places in data (if not integral value)
		if ( ! isNaN (setValue) && Math.floor (setValue) != setValue)
		{
			var decimalPlaces : Number = MathExt.numDecimals (setValue);
			//Store in class variable
			maxDecimals = (decimalPlaces > maxDecimals) ?decimalPlaces : maxDecimals;
		}
		//Return value
		return setValue;
	}
	/**
	* convertNumberSeps method helps us convert the separator (thousands and decimal)
	* character from the user specified input separator characters to normal numeric
	* values that Flash can handle. In some european countries, commas are used as
	* decimal separators and dots as thousand separators. In XML, if the user specifies
	* such values, it will give a error while converting to number. So, we accept the
	* input decimal and thousand separator from user, so thatwe can covert it accordingly
	* into the required format.
	* If the number is still not a valid number after converting the characters, we log
	* the error and return 0.
	*	@param	strNum	Number in string format containing user defined separator characters.
	*	@return		Number in numeric format.
	*/
	private function convertNumberSeps (strNum : String) : Number
	{
		//If thousand separator is defined, replace the thousand separators
		//in number
		//Store a copy
		var origNum : String = strNum;
		if (this.params.inThousandSeparator != "")
		{
			strNum = StringExt.replace (strNum, this.params.inThousandSeparator, "");
		}
		//Now, if decimal separator is defined, convert it to .
		if (this.params.inDecimalSeparator != "")
		{
			strNum = StringExt.replace (strNum, this.params.inDecimalSeparator, ".");
		}
		//Now, if the original number was in a valid format(with just different sep chars),
		//it has now been converted to normal format. But, if it's still giving a NaN, it means
		//that the number is not valid. So, we add to log and store it as undefined data.
		if (isNaN (strNum))
		{
			this.log ("ERROR", "Invalid number " + origNum + " specified in XML. FusionCharts can accept number in pure numerical form only. If your number formatting (thousand and decimal separator) is different, please specify so in XML. Also, do not add any currency symbols or other signs to the numbers.", Logger.LEVEL.ERROR);
		}
		return Number (strNum);
	}
}
