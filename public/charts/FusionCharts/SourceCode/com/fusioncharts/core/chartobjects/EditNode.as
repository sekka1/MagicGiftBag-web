/**
* @class EditNode
* @author InfoSoft Global (P) Ltd. www.InfoSoftGlobal.com
* @version 3.0
*
* Copyright (C) InfoSoft Global Pvt. Ltd. 2005-2006
*
* EditNode represents a single node to be drawn dynamically on the chart.
*/
//Extensions
import com.fusioncharts.extensions.ColorExt;
//Delegate
import mx.utils.Delegate;
//Dispatch
import mx.events.EventDispatcher;
//Utils class
import com.fusioncharts.helper.Utils;
//Dialog Box
import com.fusioncharts.helper.NodeAttributeDialogBox;
class com.fusioncharts.core.chartobjects.EditNode extends MovieClip {
	//Instance variables
	private var mcEditNodeCont:MovieClip;
	//Node movie clip
	private var mcEditNode:MovieClip;
	private var hiddenMC:MovieClip;
	//Positions
	private var x:Number;
	private var y:Number;
	//Width and height
	private var w:Number;
	private var h:Number;
	//Parent width and height
	private var pW:Number;
	private var pH:Number;
	private var pX:Number;
	private var pY:Number;
	private var r:Number;
	private var numSides:Number;
	private var shape:String;
	private var label:String;
	private var labelStyleObj:Object;
	//Series Name Array which needs to be forwarded to Attribute DB Class
	private var seriesNameArr:Array;
	//ID Array which needs to be forwarded to Attribute DB Class
	private var idArr:Array;
	//Border Color, Border Alpha, Border Thickness
	private var borderColor:String;
	private var borderAlpha:Number;
	private var borderThickness:Number;
	//Background Color, Alpha, Ratio, Angle
	private var bgColor:String;
	private var bgAlpha:Number;
	//3D
	private var use3DLighting:Boolean;
	//Drag
	private var isDraggable:Boolean;
	//Listener
	private var nodeUpdateListener:Object;
	private var cancelListener:Object;
	/**
	 * Constructor method. Takes in parameters and stores
	 * in local variables.
	*/
	function EditNode(node:MovieClip, x:Number, y:Number, w:Number, h:Number, pW:Number, pH:Number, pX:Number, pY:Number, r:Number, numSides:Number, type:String, seriesNameArr:Array, idArr:Array) {
		//Store parameters in instance variables
		this.mcEditNodeCont = node;
		//Create MC for Node - 1st depth
		this.mcEditNode = this.mcEditNodeCont.createEmptyMovieClip("EditNode", 1);
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
		this.seriesNameArr = seriesNameArr;
		this.idArr = idArr;
		//Default values
		this.use3DLighting = true;
		this.borderColor = "000000";
		this.borderAlpha = 100;
		this.borderThickness = 1;
		this.bgColor = "FF5904";
		this.bgAlpha = 100;
		this.isDraggable = true;
		//Set the initial Position
		this.mcEditNodeCont._x = 0;
		this.mcEditNodeCont._y = 0;
		//Initialize the listeners
		nodeUpdateListener = new Object();
		cancelListener = new Object();
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
	 * setParams method is use to set the cosmetic paramters for the node
	*/
	public function setParams(isDraggable:Boolean, use3DLighting:Boolean, borderColor:String, borderAlpha:Number, borderThickness:Number, bgColor:String, bgAlpha:Number):Void {
		this.isDraggable = isDraggable;
		this.use3DLighting = use3DLighting;
		this.borderColor = borderColor;
		this.borderAlpha = borderAlpha;
		this.borderThickness = borderThickness;
		this.bgColor = bgColor;
		this.bgAlpha = bgAlpha;
	}
	/**
	 * draw method draws the Node
	*/
	public function draw():Void {
		//Draw the default node
		this.drawRectangle();
		//Now define the MENU 
		this.setContextMenu();
		//Dialog box to accept the parameters
		this.drawDialogBox();
	}
	/**
	 * drawRectangle method draws the shape rectangle for the Node
	*/
	private function drawRectangle():Void {
		//Move to (-w/2, 0); 
		this.mcEditNode.moveTo(-this.w/2, -this.h/2);
		//Set the linestyle to draw a rectangle
		this.mcEditNode.lineStyle(this.borderThickness, parseInt(this.borderColor, 16), this.borderAlpha);
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
			this.mcEditNode.beginGradientFill(strFillType, arrColor, arrAlpha, arrRatio, objMatrix);
		} else {
			// set fill
			this.mcEditNode.beginFill(parseInt(this.bgColor, 16), this.bgAlpha);
		}
		//Draw rectangle
		this.mcEditNode.lineTo(this.w/2, -this.h/2);
		this.mcEditNode.lineTo(this.w/2, this.h/2);
		this.mcEditNode.lineTo(-this.w/2, this.h/2);
		this.mcEditNode.lineTo(-this.w/2, -this.h/2);
		//End fill
		this.mcEditNode.endFill();
		//Set the position
		this.mcEditNodeCont._x = this.x;
		this.mcEditNodeCont._y = this.y;
	}
	/**
	 * drawDialogBox method draws the dialog box and show only when the it is required
	*/
	private function drawDialogBox() {
		//Show the dialog box
		//Firstly we draw a hidden rectangle over the canvas to hide all the canvas events
		this.hiddenMC = this.mcEditNodeCont.createEmptyMovieClip("HiddenMc", 3);
		this.hiddenMC.beginFill(0x000000, 30);
		this.hiddenMC.moveTo(-this.pW/2, -this.pH/2);
		this.hiddenMC.lineTo(this.pW/2, -this.pH/2);
		this.hiddenMC.lineTo(this.pW/2, this.pH/2);
		this.hiddenMC.lineTo(-this.pW/2, this.pH/2);
		this.hiddenMC.lineTo(-this.pW/2, -this.pH/2);
		this.hiddenMC.endFill();
		this.hiddenMC._x = this.pX+this.pW/2-this.x;
		this.hiddenMC._y = this.pY+this.pH/2-this.y;
		this.hiddenMC.onRollOver = hiddenMC.onRollOut = hiddenMC.onPress = hiddenMC.onRelease = function() {
		}
		//Do not show the hand cursor for the hidden MC
		this.hiddenMC.useHandCursor = false;
		//We define all the font properties and pass it as parameter to the dialog box class
		var fontProps:Object = new Object();
		fontProps.font = "Verdana";
		fontProps.fontColor= "000000";
		fontProps.fontSize = 10;
		//Delegate the Node Listener on updatation Function
		this.nodeUpdateListener.onNodeUpdate = Delegate.create(this, onNodeUpdate);
		//On cancel - we do not modify the chart
		this.cancelListener.onCancel = Delegate.create(this, onCancel);
		//Create the dialog box
		var db:NodeAttributeDialogBox = new NodeAttributeDialogBox(this.mcEditNodeCont, 4, this.pX+this.pW/2-this.x, this.pY+this.pH/2-this.y, 0, this.seriesNameArr, this.idArr);
		//Add event - 1 for Submit button and other for cancel button
		db.addEventListener("onNodeUpdate", this.nodeUpdateListener);
		db.addEventListener("onCancel", this.cancelListener);
		//Set the dialog box parameters
		db.setParams(235, 300, 20, "E9E9E9", "CCCCCC", 2, fontProps, true);
		//draw the dialog box
		db.draw();
		//Hide it initially - show only when the error arrives
		db.show();
	}
	/**
	 * getDragProp method returns the drag property of the node
	*/
	public function getDragProp():Boolean {
		return this.isDraggable;
	}
	/**
	 * setContextMenu method sets the context menu for the chart.
	*/
	private function setContextMenu():Void {
		var nodeMenu:ContextMenu = new ContextMenu();
		nodeMenu.hideBuiltInItems();
		// instantiating ContextMenuItem for each menu item
		var cmiDeleteNode:ContextMenuItem = new ContextMenuItem("Delete Node", Delegate.create(this, destroy), true);
		//Push cmiSetAtt item.
		nodeMenu.customItems.push(cmiDeleteNode);
		//Assign the menu to mcEditNode movie clip
		this.mcEditNodeCont.menu = nodeMenu;
	}
	/**
	  * show method shows the Editable Node.
	*/
	public function show():Void {
		//Show the select Node
		this.mcEditNodeCont._visible = true;
	}
	/**
	 * hide method hides the Editable Node.
	*/
	public function hide():Void {
		//Hide the select Node
		this.mcEditNodeCont._visible = false;
	}
	//---------------EVENT HANDLERS ----------------------------
	/**
	 * onNodeUpdate is the delegat-ed event handler method that'll
	 * be invoked when the user updates a paticular node from a dialog box.
	*/
	private function onNodeUpdate(evtObj):Void {
		//Hide the hidden MC
		this.hiddenMC._visible = false;
		//remove the movie clip
		this.hiddenMC.removeMovieClip();
		//If the width and height entered are correct
		if(evtObj.nodeW != undefined) {
			this.mcEditNode._width = evtObj.nodeW;
		}
		if(evtObj.nodeH != undefined)	{
			this.mcEditNode._height = evtObj.nodeH;
		}
		//Dispatch information to parent class to update the required arrays(Connectors, data )
		this.dispatchEvent({type:"onNodeUpdate", target:this, x:this.x, y:this.y, id:evtObj.id, dsId:evtObj.dsSeriesName.substr(evtObj.dsSeriesName.indexOf(",", 0)+1, evtObj.dsSeriesName.length), h:evtObj.nodeH, w:evtObj.nodeW, r:evtObj.nodeR, nodeShape:evtObj.nodeShape, name:evtObj.nodeName, numSide: evtObj.numSide, url: evtObj.url, imageNode: evtObj.imageNode});
	}
	/**
	 * onCancel method Hides the modal class
	*/
	private function onCancel():Void {
		this.hiddenMC._visible = false;
		//remove the movie clip
		this.hiddenMC.removeMovieClip();
		//Destory all the objects
		this.destroy();
		//Dispatch information to parent class to update the required arrays(Connectors, data )
		this.dispatchEvent({type:"onCancel", target:this});
	}
	/**
	 * destroy method MUST be called whenever you wish to delete this class's
	 * instance.
	*/
	public function destroy():Void {
		//clear the container
		this.mcEditNodeCont.clear();
		//Clear the Node
		this.mcEditNode.clear();
		//Delete the movieclip
		this.mcEditNodeCont.removeMovieClip();
	}
}
