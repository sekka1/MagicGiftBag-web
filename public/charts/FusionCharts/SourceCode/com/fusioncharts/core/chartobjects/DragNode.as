/**
* @class DragNode
* @author InfoSoft Global (P) Ltd. www.InfoSoftGlobal.com
* @version 3.0
*
* Copyright (C) InfoSoft Global Pvt. Ltd. 2005-2006
*
* DragNode extends the movie clip class and represents a single
* Node to be drawn on the chart with dragging option. It takes in 
* the required parameters (dimensions) and draws a DragNode.
*/
//Extensions
import com.fusioncharts.extensions.DrawingExt;
import com.fusioncharts.extensions.ColorExt;
import com.fusioncharts.extensions.MathExt;
//Utils class
import com.fusioncharts.helper.Utils;
//Delegate
import mx.utils.Delegate;
//Event Dispatcher
import mx.events.EventDispatcher;
//Dialog Box for adding a new Connector
import com.fusioncharts.helper.ConnectorAttributeDialogBox;
//Image Node
import com.fusioncharts.helper.CreateImageLabel;
// Error Dialog Box
import com.fusioncharts.helper.FCErrorDialogBox;
//Class
class com.fusioncharts.core.chartobjects.DragNode extends MovieClip {
	//Instance variables
	//Parent Movie clip
	private var mcNodeCont:MovieClip;
	//Node movie clip
	private var mcNode:MovieClip;
	//Dialog Box
	private var db:MovieClip;
	//Hidden MC
	private var hiddenMC:MovieClip;
	//Positions
	private var x:Number;
	private var y:Number;
	//Width and height
	private var w:Number;
	private var h:Number;
	private var r:Number;
	//Parent width, height and position
	private var pW:Number;
	private var pH:Number;
	private var pX:Number;
	private var pY:Number;
	private var numSides:Number;
	private var shape:String;
	private var label:String;
	//Label Object
	private var labelStyleObj:Object;
	//Image properties
	private var imagePropObj:Object;
	//Array of Not Connected Dataitems
	private var dataNotConnected:Array;
	//Index to dataset
	private var dsIndex:Number;
	private var index:Number;
	//Border Color, Border Alpha, Border Thickness
	private var borderColor:String;
	private var borderAlpha:Number;
	private var borderThickness:Number;
	//Background Color, Alpha, Ratio, Angle
	private var bgColor:String;
	private var bgAlpha:Number;
	//Image Class
	private var imageIns:CreateImageLabel;
	//3D
	private var use3DLighting:Boolean;
	//Drag
	private var isDraggable:Boolean;
	//Mode
	private var viewMode:Boolean;
	//Listeners
	private var connectorUpdateListener:Object;
	private var cancelListener:Object;
	/**
	 * Constructor method. Takes in parameters and stores
	 * in local variables.
	*/
	function DragNode(node:MovieClip, dbMC:MovieClip, x:Number, y:Number, w:Number, h:Number, pW:Number, pH:Number, pX:Number, pY:Number,  r:Number, numSides:Number, type:String, label:String, labelStyleObj:Object, dsIndex:Number, index:Number, dataNotConnectedArr:Array, viewMode:Boolean, imagePropObj:Object) {
		//Store parameters in instance variables
		this.mcNodeCont = node;
		//Create MC for Node - 1st depth
		this.mcNode = this.mcNodeCont.createEmptyMovieClip("Node", 1);
		this.mcNodeCont.useHandCursor = false;
		this.db = dbMC;
		this.x = x;
		this.y = y;
		this.w = w;
		this.h = h;
		this.r = r;
		this.pW = pW;
		this.pH = pH;
		this.pX = pX;
		this.pY = pY;
		this.numSides = numSides;
		this.shape = type;
		this.label = label;
		this.labelStyleObj = labelStyleObj;
		this.dsIndex = dsIndex;
		this.index = index;
		this.dataNotConnected = dataNotConnectedArr; 
		this.viewMode = viewMode;
		this.imagePropObj = imagePropObj;
		//Default values
		this.use3DLighting = true;
		this.borderColor = "000000";
		this.borderAlpha = 100;
		this.borderThickness = 1;
		this.bgColor = "FF5904";
		this.bgAlpha = 100;
		this.isDraggable = true;
		//Set the initial Position
		this.mcNodeCont._x = 0;
		this.mcNodeCont._y = 0;
		//call initialize on EventDispatcher to
		//implement the event handling functions
		mx.events.EventDispatcher.initialize(this);
		//Initialize the listener's object
		connectorUpdateListener = new Object();
		cancelListener = new Object();
	}
	//these functions are defined in the class to prevent
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
	 * setParams method is use to set the cosmetic paramters for the node
	*/
	public function setParams(isDraggable:Boolean, use3DLighting:Boolean, borderColor:String, borderAlpha:Number, borderThickness:Number, bgColor:String, bgAlpha:Number):Void {
		//Store the parameters
		this.isDraggable = isDraggable;
		this.use3DLighting = use3DLighting;
		this.borderColor = borderColor;
		this.borderAlpha = borderAlpha;
		this.borderThickness = borderThickness;
		this.bgColor = bgColor;
		this.bgAlpha = bgAlpha;
	}
	/**
	 * calculatePoint method calculates a point from the given point with a given distance.
	*/
	private function calculatePoint(fromX:Number, fromY:Number, distance:Number, angle:Number):Object {
		//This function calculates the x and y co-ordinates of a point at an angular distance of "distance,angle" from the base point fromX, fromY
		//Convert the angle into radians
		angle = angle*(Math.PI/180);
		var xPos:Number = fromX+(distance*Math.cos(angle));
		var yPos:Number = fromY-(distance*Math.sin(angle));
		return ({x:xPos, y:yPos});
	}
	/**
	 * draw method draws the Node
	*/
	public function draw():Void {
		//Check which shape to use for drawing the node
		if (this.shape.toUpperCase() == "CIRCLE") {
			this.drawCircle();
		} else if (this.shape.toUpperCase() == "POLYGON") {
			this.drawPoly();
		} else {
			this.drawRectangle();
		}
		//If label is required
		if (this.label != "" && this.label != "undefined") {
			//Maximum Height and Width of the textbox
			var maxW:Number;
			var maxH:Number;
			if (this.shape.toUpperCase() == "RECTANGLE") {
				//If its a rectangle then we set MaxH and MaxW as the height and width of rectangle
				maxH = this.h;
				maxW = this.w;
			} else if (this.shape.toUpperCase() == "CIRCLE" || this.shape.toUpperCase() == "POLYGON") {
				//Calculate the maximum height and width available to us
				//This is calculated by drawing a maximum possible rectangle within the Node
				//which is rectangle by joining the points from center with an angle of 45 and 225
				//and distance equal to radius from centre.
				var firstObj:Object = this.calculatePoint(this.x, this.y, this.r, 45);
				var endObj:Object = this.calculatePoint(this.x, this.y, this.r, 225);
				//Maximum width
				maxW = Math.abs(firstObj.x-endObj.x);
				//Maximum height
				maxH = Math.abs(firstObj.y-endObj.y);
			}
			//If we need to show the image
			if(imagePropObj.imageNode && imagePropObj.url != "") {
				//Create an instance of the imageNode Class
				this.imageIns = new CreateImageLabel(this.mcNodeCont, 2, this.label, maxW, maxH, this.labelStyleObj);
				//Set the parameters according to feed value
				imageIns.setParams(imagePropObj.url, imagePropObj.imageW, imagePropObj.imageH, imagePropObj.imageAlign, imagePropObj.labelAlign);
				//set the image with the text
				imageIns.draw();
			}
			else {
				//Create the actual text field
				Utils.createText(false, this.label, this.mcNodeCont, 2, 0, 0, 0, this.labelStyleObj, true, maxW, maxH);
			}
		}
		//Now define the MENU 
		this.setContextMenu();
	}
	/**
	 * drawRectangle method draws the shape rectangle for the Node
	*/
	private function drawRectangle():Void {
		//Move to (-w/2, 0); 
		this.mcNode.moveTo(-this.w/2, -this.h/2);
		//Set the linestyle to draw a rectangle
		this.mcNode.lineStyle(this.borderThickness, parseInt(this.borderColor, 16), this.borderAlpha);
		// if node is to be shown with 3D effect
		if (this.use3DLighting) {
			// getting a darker color for shadow
			var shadowColor:Number = ColorExt.getDarkColor(this.bgColor, 0.7);
			// getting a lighter color for highlight
			var highLightColor:Number = ColorExt.getLightColor(this.bgColor, 0.7);
			// setting params for gradient
			var strFillType:String = "linear";
			var arrColor:Array = [highLightColor, shadowColor];
			var arrAlpha:Array = [this.bgAlpha, this.bgAlpha];
			var arrRatio:Array = [0, 255];
			// x and y are manipulated to have the highlight in the upper left side of the nodes
			var objMatrix:Object = {matrixType:"box", x:-this.w/2, y:-this.h/2, w:this.w, h:this.h, r:0};
			// set gradient
			this.mcNode.beginGradientFill(strFillType, arrColor, arrAlpha, arrRatio, objMatrix);
		} else {
			// set fill
			this.mcNode.beginFill(parseInt(this.bgColor, 16), this.bgAlpha);
		}
		//Draw rectangle
		this.mcNode.lineTo(this.w/2, -this.h/2);
		this.mcNode.lineTo(this.w/2, this.h/2);
		this.mcNode.lineTo(-this.w/2, this.h/2);
		this.mcNode.lineTo(-this.w/2, -this.h/2);
		//End fill
		this.mcNode.endFill();
		//Set the position
		this.mcNodeCont._x = this.x;
		this.mcNodeCont._y = this.y;
	}
	/**
	 * drawPoly method draws the shape polygon for the Node
	*/
	private function drawPoly():Void {
		//Set the linestyle to draw a rectangle
		this.mcNode.lineStyle(this.borderThickness, parseInt(this.borderColor, 16), this.borderAlpha);
		// if node is to be shown with 3D effect
		if (this.use3DLighting) {
			// getting a darker color for shadow
			var shadowColor:Number = ColorExt.getDarkColor(this.bgColor, 0.7);
			// getting a lighter color for highlight
			var highLightColor:Number = ColorExt.getLightColor(this.bgColor, 0.7);
			// setting params for gradient
			var strFillType:String = "linear";
			var arrColor:Array = [highLightColor, shadowColor];
			var arrAlpha:Array = [this.bgAlpha, this.bgAlpha];
			var arrRatio:Array = [0, 255];
			// x and y are manipulated to have the highlight in the upper left side of the nodes
			var objMatrix:Object = {matrixType:"box", x:-this.r, y:-this.r, w:2*this.r, h:2*this.r, r:r};
			// set gradient
			this.mcNode.beginGradientFill(strFillType, arrColor, arrAlpha, arrRatio, objMatrix);
		} else {
			// set fill
			this.mcNode.beginFill(parseInt(this.bgColor, 16), this.bgAlpha);
		}
		//Draw Polygon
		DrawingExt.drawPoly(this.mcNode, 0, 0, this.numSides, this.r, 90);
		//End fill
		this.mcNode.endFill();
		//Set the position
		this.mcNodeCont._x = this.x;
		this.mcNodeCont._y = this.y;
	}
	/**
	 * drawCircle method draws the shape circle for the Node in the private MC Node.
	*/
	private function drawCircle():Void {
		var toNT:Function = MathExt.toNearestTwip;
		// Variables
		var sweepAngle:Number;
		//Theta value
		var theta:Number;
		//Angles
		var startAngle:Number = 0;
		var angle:Number;
		var angleMid:Number;
		//Segmemts
		var segs:Number;
		//Axis positions
		var ax:Number, ay:Number, bx:Number, by:Number;
		//Control Points
		var controlX:Number, controlY:Number;
		var arc:Number = 360;
		//Divide in 8 segments  
		segs = Math.ceil(Math.abs(arc)/45);
		// Now calculate the sweep angle of each segment.
		sweepAngle = arc/segs;
		theta = -(sweepAngle/180)*Math.PI;
		// convert angle startAngle to radians
		angle = -(sweepAngle/180)*Math.PI;
		// draw the curve in segments no larger than 45 degrees.
		if (segs>0) {
			// draw a line from the center to the start of the curve
			ax = this.r;
			ay = 0;
			this.mcNode.moveTo(ax, ay);
			//Set the linestyle to draw a rectangle
			this.mcNode.lineStyle(this.borderThickness, parseInt(this.borderColor, 16), this.borderAlpha);
			// if node is to be shown with 3D effect
			if (this.use3DLighting) {
				// getting a darker color for shadow
				var shadowColor:Number = ColorExt.getDarkColor(this.bgColor, 0.7);
				// getting a lighter color for highlight
				var highLightColor:Number = ColorExt.getLightColor(this.bgColor, 0.7);
				// setting params for gradient
				var strFillType:String = "radial";
				var arrColor:Array = [highLightColor, shadowColor];
				var arrAlpha:Array = [this.bgAlpha, this.bgAlpha];
				var arrRatio:Array = [0, 255];
				// x and y are manipulated to have the highlight in the upper left side of the nodes
				var objMatrix:Object = {matrixType:"box", x:-this.r, y:-this.r, w:2*this.r, h:2*this.r, r:r};
				// set gradient
				this.mcNode.beginGradientFill(strFillType, arrColor, arrAlpha, arrRatio, objMatrix);
			} else {
				// set fill
				this.mcNode.beginFill(parseInt(this.bgColor, 16), this.bgAlpha);
			}
			// Loop for drawing curve segments
			for (var i:Number = 1; i<=8; ++i) {
				//Set end angle
				var t:Number = (Math.PI/4)*i;
				//Get end co-ordinates
				bx = toNT(this.r*Math.cos(t));
				by = toNT(this.r*Math.sin(t));
				//Control coordinates
				controlX = toNT(this.r*Math.cos((2*((Math.PI/4)*(i-1))+(Math.PI/4))/2)/Math.cos((Math.PI/4)/2));
				controlY = toNT(this.r*Math.sin((2*((Math.PI/4)*(i-1))+(Math.PI/4))/2)/Math.cos((Math.PI/4)/2));
				//Draw Curve
				this.mcNode.curveTo(controlX, controlY, bx, by);
			}
		}
		//End fill  
		this.mcNode.endFill();
		//Set the position
		this.mcNodeCont._x = this.x;
		this.mcNodeCont._y = this.y;
	}
	/**
	 * drawDialogBox method draws the dialog box and show only when the it is required
	*/
	private function drawDialogBox() {
		//Show the dialog box
		//Firstly we draw a hidden rectangle over the canvas to hide all the canvas events
		this.hiddenMC = this.db.createEmptyMovieClip("HiddenMc", 1);
		//Make it visible
		this.hiddenMC._visible = true;
		this.hiddenMC.beginFill(0x000000, 30);
		this.hiddenMC.moveTo(-this.pW/2, -this.pH/2);
		this.hiddenMC.lineTo(this.pW/2, -this.pH/2);
		this.hiddenMC.lineTo(this.pW/2, this.pH/2);
		this.hiddenMC.lineTo(-this.pW/2, this.pH/2);
		this.hiddenMC.lineTo(-this.pW/2, -this.pH/2);
		this.hiddenMC.endFill();
		this.hiddenMC._x = this.pW/2;
		this.hiddenMC._y = this.pH/2;
		this.hiddenMC.onRollOver = hiddenMC.onRollOut = hiddenMC.onPress = hiddenMC.onRelease = function() {
		}
		//Do not show the hand cursor
		this.hiddenMC.useHandCursor = false;
		//If there is at least 1 node which is not connected to this node
		//Then show the attribute dialog box 
		if(this.dataNotConnected.length!=0) {
			//We define all the font properties and pass it as parameter to the dialog box class
			var fontProps:Object = new Object();
			fontProps.font = "Verdana";
			fontProps.fontColor= "000000";
			fontProps.fontSize = 10;
			//Delegate the Connector Listener on updatation Function
			this.connectorUpdateListener.onConnectorUpdate = Delegate.create(this, onConnectorUpdate);
			this.cancelListener.onCancel = Delegate.create(this, onCancel);
			//Create the dialog box
			var db:ConnectorAttributeDialogBox = new ConnectorAttributeDialogBox(this.db, 2, this.pW/2, this.pH/2, 0, dataNotConnected);
			//Add event 
			db.addEventListener("onConnectorUpdate", this.connectorUpdateListener);
			//Add event 
			db.addEventListener("onCancel", this.cancelListener);
			//Set the dialog box parameters
			db.setParams(150, 285, 20, "E9E9E9", "CCCCCC", 2, fontProps, true);
			//draw the dialog box
			db.draw();
		}
		//Else show the error dialog box
		else {
			//Show the error dialog box
			//We define all the font properties and pass it as parameter to the dialog box class
			var fontProps:Object = new Object();
			fontProps.font = "Verdana";
			fontProps.fontColor= "000000";
			fontProps.fontSize = 10;
			//Create the dialog box
			var errorDb:FCErrorDialogBox = new FCErrorDialogBox(this.db, 2, 0, 0, this.pW, this.pH, 0);
			//Set the dialog box parameters
			errorDb.setParams(110, 230, 20, "E9E9E9", "CCCCCC", 2, fontProps, true);
			errorDb.setErrorMessage("You cannot define any more Connectors for this node, as it may be a single Node or already connected to all other nodes.");
			this.hiddenMC._visible = false;
		}
		//Set the container position
		this.db._x = 0;
		this.db._y = 0;
	}
	/**
	 * getDragProp method returns the drag property of the node
	*/
	public function getDragProp():Boolean {
		return this.isDraggable;
	}
	/**
	 * deleteNode method deletes the Node
	*/
	private function deleteNode():Void {
		//Dispatch information to parent class to update the required arrays(Connectors, data ..)
		this.dispatchEvent({type:"onNodeDelete", target:this, dsIndex:this.dsIndex, index:this.index});
		//destory the Node
		this.destroy();
	}
	/**
	 * onConnectorUpdate method updates the Objects when a connector is added
	*/
	private function onConnectorUpdate(evtObj):Void {
		//Hide the hidden MovieClip - which was drawn to make the DB a modal component
		this.hiddenMC._visible = false;
		//Dispatch information to parent class to update the required arrays(Connectors, data ..)
		this.dispatchEvent({type:"onAddConnector", target:this, label:evtObj.label, strength:evtObj.strength, arrowStart:evtObj.arrowStart, arrowEnd:evtObj.arrowEnd, dashed:evtObj.dashed, fromId:this.index, fromDsIndex:this.dsIndex, toDsIndex:evtObj.toId.substr(0, evtObj.toId.indexOf(",", 0)), toId:evtObj.toId.substr(evtObj.toId.indexOf(",", 0)+1, evtObj.toId.length)});
	}
	/**
	 * onCancel method Hides the modal DialogBox
	*/
	private function onCancel():Void {
		this.hiddenMC._visible = false;
	}
	/**
	 * setContextMenu method sets the context menu for the chart.
	 * For this chart, the context items are "Print Chart".
	*/
	private function setContextMenu():Void {
		var chartMenu:ContextMenu = new ContextMenu();
		chartMenu.hideBuiltInItems();
		if(this.isDraggable)  {
			// instantiating ContextMenuItem for each menu item
			var cmiPin:ContextMenuItem = new ContextMenuItem("Pin", pinHandler, false, this.isDraggable);
			//Push Pin item.
			chartMenu.customItems.push(cmiPin);
			var cmiUnPin:ContextMenuItem = new ContextMenuItem("Un-pin", unpinHandler, false, !this.isDraggable);
			//Push UnPin item.
			chartMenu.customItems.push(cmiUnPin);
		}
		//If in Edit Mode we add a menu item ADD A NODE
		if(!this.viewMode) {
			// instantiating ContextMenuItem for each menu item
			var cmiDeleteNode:ContextMenuItem = new ContextMenuItem("Delete Node", Delegate.create(this, deleteNode), true);
			//Push cmiSetAtt item.
			chartMenu.customItems.push(cmiDeleteNode);
			// instantiating ContextMenuItem for each menu item
			var cmiAddConnector:ContextMenuItem = new ContextMenuItem("Add Connector", Delegate.create(this, this.drawDialogBox), true);
			//Push cmiSetAtt item.
			chartMenu.customItems.push(cmiAddConnector);
		}
		// instance reference is stored                                                           
		var instanceRef = this;
		// functions invoked due selection of the menu items are defined
		function pinHandler() {
			cmiPin.enabled = false;
			cmiUnPin.enabled = true;
			instanceRef.isDraggable = false;
		}
		function unpinHandler() {
			// enabling/disabling the ContextMenuItems
			cmiUnPin.enabled = false;
			cmiPin.enabled = true;
			// updating flags about current menu enable status
			instanceRef.isDraggable = true;
		}
		//Assign the menu to mcNode movie clip
		this.mcNodeCont.menu = chartMenu;
	}
	/**
	  * show method shows the select rectangle.
	*/
	public function show():Void {
		//Show the select Node
		this.mcNodeCont._visible = true;
	}
	/**
	 * hide method hides the select rectangle.
	*/
	public function hide():Void {
		//Hide the select Node
		this.mcNodeCont._visible = false;
	}
	/**
	 * destroy method MUST be called whenever you wish to delete this class's
	 * instance.
	*/
	public function destroy():Void {
		//clear the container
		this.mcNodeCont.clear();
		//Clear the Node
		this.mcNode.clear();
		//Destroy the image class 
		this.imageIns.destroy();
		//Delete the movieclip
		this.mcNodeCont.removeMovieClip();
	}
}
