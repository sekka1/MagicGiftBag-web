/**
* @class NodeConnector
* @author InfoSoft Global (P) Ltd. www.InfoSoftGlobal.com
* @version 3.0
*
* Copyright (C) InfoSoft Global Pvt. Ltd. 2005-2006
*
* NodeConnector extends the movie clip class and  draw the connectors
* on the chart for all the NODES. 
*/
//Extensions
import com.fusioncharts.extensions.DrawingExt;
//Utils class
import com.fusioncharts.helper.Utils;
//Delegate
import mx.utils.Delegate;
import mx.events.EventDispatcher;
class com.fusioncharts.core.chartobjects.NodeConnector extends MovieClip {
	//Instance variables
	//Node movie clip
	private var mcConnectorCont:MovieClip;
	private var mcConnector:MovieClip;
	//Index of the connector
	private var index:Number;
	//Label
	private var label:String;
	//Positions
	private var fromX:Number;
	private var fromY:Number;
	private var toX:Number;
	private var toY:Number;
	//Mode
	private var viewMode:Boolean;
	//Arrow parameters
	private var arrowAtStart:Boolean;
	private var arrowAtEnd:Boolean;
	private var fromRadius:Number;
	private var toRadius:Number;
	//Cosmetic property
	private var color:String;
	private var thickness:Number;
	private var alpha:Number;
	private var dashed:Boolean;
	private var dashLen:Number;
	private var dashGap:Number;
	private var strength:Number;
	//Label Object
	private var labelStyleObj:Object;
	/**
	 * Constructor method. Takes in parameters and stores
	 * in local variables.
	*/
	function NodeConnector(connector:MovieClip, index:Number, label:String, posObj:Object, lineProp:Object, labelStyleObj:Object, fromRadius:Number, toRadius:Number, arrowAtStart:Boolean, arrowAtEnd:Boolean, strength:Number, viewMode:Boolean) {
		//Store parameters in instance variables
		this.mcConnectorCont = connector;
		//Create MC for Connector line - 1st depth
		this.mcConnector = this.mcConnectorCont.createEmptyMovieClip("Connector", 1);
		this.label = label;
		this.index = index;
		//Position
		this.fromX = posObj.fromX;
		this.fromY = posObj.fromY;
		this.toX = posObj.toX;
		this.toY = posObj.toY;
		//Radius of the connected Nodes
		this.fromRadius = fromRadius;
		this.toRadius = toRadius;
		this.arrowAtStart = arrowAtStart;
		this.arrowAtEnd = arrowAtEnd;
		//Cosmetic property
		this.color = lineProp.color;
		this.alpha = lineProp.alpha;
		this.thickness = lineProp.thickness;
		this.dashed = lineProp.dashed;
		this.dashGap = lineProp.dashGap;
		this.dashLen = lineProp.dashLen;
		this.strength = strength;
		this.viewMode = viewMode;
		//Label style object
		this.labelStyleObj = labelStyleObj;
		//Set the initial Position
		this.mcConnector._x = 0;
		this.mcConnector._y = 0;
		//call initialize on EventDispatcher to
		//implement the event handling functions
		mx.events.EventDispatcher.initialize(this);
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
	 * draw method draws the Connector
	*/
	public function draw():Void {
		//Set line style
		this.mcConnector.lineStyle(this.thickness, parseInt(this.color, 16), this.alpha);
		//If dashed border is required, then draw it
		if (this.dashed) {
			//Draw the dash
			DrawingExt.dashTo(this.mcConnector, -(this.fromX-this.toX)/2, -(this.fromY-this.toY)/2, (this.fromX-this.toX)/2, (this.fromY-this.toY)/2, this.dashLen, this.dashGap);
		}
		else {
			this.mcConnector.moveTo(-(this.fromX-this.toX)/2, -(this.fromY-this.toY)/2);
			this.mcConnector.lineTo((this.fromX-this.toX)/2, (this.fromY-this.toY)/2);
		}
		//Set the central (with respect to line) Position
		this.mcConnector._x = (this.fromX+this.toX)/2;
		this.mcConnector._y = (this.fromY+this.toY)/2;
		//Set the container position
		this.mcConnectorCont._x = 0;
		this.mcConnectorCont._y = 0;
		//If label is required
		if (this.label != "" && this.label != "undefined") {
			//Create the actual text field
			Utils.createText(false, this.label, mcConnectorCont, 2, (this.fromX+this.toX)/2, (this.fromY+this.toY)/2, 0, labelStyleObj, false, 0, 0);
		}
		//Now draw the Arrow - only if at least 1 arrow is asked for.
		if(this.arrowAtStart || this.arrowAtEnd) {
			this.drawArrow();
		}
		//Only in Edit Mode we define the Menu - to allow delete operation
		if(!this.viewMode) {
			//Now define the MENU 
			this.setContextMenu();
		}
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
	};	
	/**
	 * drawArrow method draws the Connector's arrow
	*/
	private function drawArrow():Void {
		//Variables
		var conLength:Number = 10;
		var slope:Number = (fromY-toY)/(fromX-toX);
		var rDegree:Number, lDegree:Number;
		var xRadius:Number, yRadius:Number;
		//Check which arrow to draw
		if (fromX>=toX) {
			xRadius = fromRadius;
			yRadius = -toRadius;
			rDegree = 45;
			lDegree = 135;
		} else {
			xRadius = -fromRadius;
			yRadius = toRadius;
			rDegree = 135;
			lDegree = 45;
		}
		//Calculate the start and end positions of the arrow
		var arrowStartPoint1:Object = this.calculatePoint((this.fromX-this.toX)/2, (this.fromY-this.toY)/2, xRadius, 180-(Math.atan(slope)*(180/Math.PI)));
		var arrowStartPoint2:Object = this.calculatePoint(-(this.fromX-this.toX)/2, -(this.fromY-this.toY)/2, yRadius, 180-(Math.atan(slope)*(180/Math.PI)));
		var arrowEndPoint1:Object= this.calculatePoint(arrowStartPoint1.x, arrowStartPoint1.y, conLength, 180-(Math.atan(slope)*(180/Math.PI))-rDegree);
		var arrowEndPoint2:Object = this.calculatePoint(arrowStartPoint1.x, arrowStartPoint1.y, conLength, 180-(Math.atan(slope)*(180/Math.PI))+rDegree);
		var arrowEndPoint3:Object = this.calculatePoint(arrowStartPoint2.x, arrowStartPoint2.y, conLength, 180-(Math.atan(slope)*(180/Math.PI))-lDegree);
		var arrowEndPoint4:Object = this.calculatePoint(arrowStartPoint2.x, arrowStartPoint2.y, conLength, 180-(Math.atan(slope)*(180/Math.PI))+lDegree);
		//Now draw the arrows
		if(this.arrowAtStart) {
			this.mcConnector.lineStyle(this.thickness, parseInt(this.color, 16), this.alpha);
			this.mcConnector.moveTo(arrowStartPoint1.x, arrowStartPoint1.y);
			this.mcConnector.lineTo(arrowEndPoint1.x, arrowEndPoint1.y);
			this.mcConnector.moveTo(arrowStartPoint1.x, arrowStartPoint1.y);
			this.mcConnector.lineTo(arrowEndPoint2.x, arrowEndPoint2.y);
		}
		//Check whether we need to draw the end arrow
		if(this.arrowAtEnd) {
			this.mcConnector.moveTo(arrowStartPoint2.x, arrowStartPoint2.y);
			this.mcConnector.lineTo(arrowEndPoint3.x, arrowEndPoint3.y);
			this.mcConnector.moveTo(arrowStartPoint2.x, arrowStartPoint2.y);
			this.mcConnector.lineTo(arrowEndPoint4.x, arrowEndPoint4.y);
		}
	}
	/**
	 * hideConnector method hides the connector.
	*/
	public function hideConnector():Void {
		//Hide the container
		this.mcConnectorCont._visible = false;
	}
	/**
	 * showConnector method show the connector
	*/
	public function showConnector():Void {
		//Show the container
		this.mcConnectorCont._visible = true;
	}
	/**
	 * setContextMenu method sets the context menu for the chart.
	 * For this chart, the context items are "Print Chart".
	*/
	private function setContextMenu():Void {
		var chartMenu:ContextMenu = new ContextMenu();
		chartMenu.hideBuiltInItems();
		// instantiating ContextMenuItem for each menu item
		var cmiDeleteNode:ContextMenuItem = new ContextMenuItem("Delete Connector", Delegate.create(this, deleteConnector), true);
		//Push cmiSetAtt item.
		chartMenu.customItems.push(cmiDeleteNode);
		//Assign the menu to mcNode movie clip
		this.mcConnectorCont.menu = chartMenu;
	}
	/**
	 * deleteConnector method deletes the Connector
	*/
	private function deleteConnector():Void {
		//Dispatch information to parent class to update the required arrays(Connectors, data ..)
		this.dispatchEvent({type:"onConnectorDelete", target:this, index:this.index});
		//destory the Node
		this.destroy();
	}
	/**
	 * destroy method MUST be called whenever you wish to delete this class's
	 * instance.
	*/
	public function destroy():Void {
		//clear the container
		this.mcConnectorCont.clear();
		//Clear the Node
		this.mcConnector.clear();
		//Delete the movieclip
		this.mcConnectorCont.removeMovieClip();
	}
}
