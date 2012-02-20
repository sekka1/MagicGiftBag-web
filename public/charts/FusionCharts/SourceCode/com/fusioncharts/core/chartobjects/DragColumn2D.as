/**
* @class DragColumn2D
* @author InfoSoft Global (P) Ltd. www.InfoSoftGlobal.com
* @version 3.0
*
* Copyright (C) InfoSoft Global Pvt. Ltd. 2005-2006
*
* DragColumn2D extends the movie clip class and represents a single
* 2D Column to be drawn on the chart with dragging option. It takes in 
* the required parameters (dimensions) and draws a column.
*/
//Import required classes
import mx.transitions.Tween;
import mx.transitions.easing.*;
//Extensions
import com.fusioncharts.extensions.MathExt;
import com.fusioncharts.extensions.DrawingExt;
//Event dispatcher
import mx.events.EventDispatcher;
//Delegate
import mx.utils.Delegate;
class com.fusioncharts.core.chartobjects.DragColumn2D extends MovieClip {
	//Instance variables
	//Movie clip container
	private var mc:MovieClip;
	//Drag Bar movie clip
	private var mcDrag:MovieClip;
	//DragBorder movie clip
	private var mcDragBorder:MovieClip;
	//Width and height
	private var w:Number;
	private var h:Number;
	//Original Height  - for negative Dragging 
	private var originalH:Number;
	//Border Color, Border Alpha, Border Thickness
	private var borderColor:String;
	private var borderAlpha:Number;
	private var borderThickness:Number;
	//Background Color, Alpha, Ratio, Angle
	private var bgColor:Array;
	private var bgAlpha:Array;
	private var bgRatio:Array;
	private var bgAngle:Number;
	//Flag whether we need to draw bottom border
	private var drawBottomBorder:Boolean;
	//Snap to Div Properties
	private var snapToDiv:Boolean;
	private var snapToDivOnly:Boolean;
	private var doNotSnap:Boolean;
	private var snapToDivRelaxation:Number;
	//Dash properties
	private var isBorderDashed:Boolean;
	private var dashLen:Number;
	private var dashGap:Number;
	//static array which stores all the div line values
	private static var divLineVal:Array;
	//Drag Column Constant
	private var RHANDLER_HEIGHT:Number = 10;
	/**
	 * Constructor method. Takes in parameters and stores
	 * in local variables.
	*/
	function DragColumn2D(target:MovieClip, dragCol:MovieClip, colDragBorderMC:MovieClip, w:Number, h:Number, borderColor:String, borderAlpha:Number, borderThickness:Number, bgColor:Array, bgAlpha:Array, bgRatio:Array, bgAngle:Number, drawBottomBorder:Boolean, isBorderDashed:Boolean, dashLen:Number, dashGap:Number) {
		//Store parameters in instance variables
		this.mc = target;
		this.mcDrag = dragCol;
		this.mcDragBorder = colDragBorderMC;
		this.w = w;
		//Negate to cancel Flash's reverse Y Co-ordinate system
		this.h = -h;
		//Original Height - to check allowNegativeDrag property
		this.originalH = h;
		this.borderColor = borderColor;
		this.borderAlpha = borderAlpha;
		this.borderThickness = borderThickness;
		this.bgColor = bgColor;
		this.bgAlpha = bgAlpha;
		this.bgRatio = bgRatio;
		this.bgAngle = bgAngle;
		this.drawBottomBorder = drawBottomBorder;
		this.isBorderDashed = isBorderDashed;
		this.dashLen = dashLen;
		this.dashGap = dashGap;
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
	 * draw method draws the drag column
	*/
	public function draw():Void {
		//Set line style if it's not dashed border
		if (!this.isBorderDashed) {
			this.mc.lineStyle(this.borderThickness, parseInt(this.borderColor, 16), this.borderAlpha);
		} else {
			//Set empty line style
			//Set empty line style, as we've already drawn dashed border 
			this.mc.lineStyle();
		}
		//Move to (-w/2, 0); 
		this.mc.moveTo(-w/2, 0);
		//Create matrix object
		var matrix:Object = {matrixType:"box", w:w, h:h, x:-w/2, y:0, r:MathExt.toRadians(this.bgAngle)};
		//Start the fill.
		this.mc.beginGradientFill("linear", this.bgColor, this.bgAlpha, this.bgRatio, matrix);
		//Draw rectangle
		this.mc.lineTo(-w/2, h);
		this.mc.lineTo(w/2, h);
		this.mc.lineTo(w/2, 0);
		if (!drawBottomBorder) {
			//If bottom border is not to be drawn
			this.mc.lineStyle();
		}
		this.mc.lineTo(-w/2, 0);
		//End fill
		this.mc.endFill();
		//If dashed border is required, then draw it
		if (this.isBorderDashed) {
			//Set line style
			this.mc.lineStyle(this.borderThickness, parseInt(this.borderColor, 16), this.borderAlpha);
			//Draw the dash
			DrawingExt.dashTo(this.mc, -w/2, 0, -w/2, h, this.dashLen, this.dashGap);
			DrawingExt.dashTo(this.mc, -w/2, h, w/2, h, this.dashLen, this.dashGap);
			DrawingExt.dashTo(this.mc, w/2, h, w/2, 0, this.dashLen, this.dashGap);
			if (drawBottomBorder) {
				//If bottom border is to be drawn
				DrawingExt.dashTo(this.mc, w/2, 0, -w/2, 0, this.dashLen, this.dashGap);
			}
		}
	}
	/**
	 * draw method draws the drag column
	 *	@param	dataX			X position of the data column.
	 *	@param	canvasY			Y Position of the canvas.
	 *	@param	canvasToY		End Y Position of the canvas.
	 *	@param	dragBorderProp	Object holding the properties of the drag border for each column.
	 *	@param	applyAnimation	whether animation has to be performed after dragging the border
	*/
	public function drawDragRectangle(dataX:Number, canvasY:Number, canvasToY:Number, dragBorderProp:Object, applyAnimation:Boolean):Void {
		//Variables
		var fnOnPress:Function, fnOnRelease:Function;
		//Store the class reference
		var classRef:Object;
		//Set the null linestyle to draw a hidden draggable rectangle
		this.mcDrag.lineStyle();
		//Start the fill.
		this.mcDrag.beginFill(parseInt(this.borderColor, 16), 0);
		//Move to (-w/2, 0); 
		this.mcDrag.moveTo(-w/2, RHANDLER_HEIGHT);
		//Draw rectangle
		this.mcDrag.lineTo(-w/2, -RHANDLER_HEIGHT);
		this.mcDrag.lineTo(w/2, -RHANDLER_HEIGHT);
		this.mcDrag.lineTo(w/2, RHANDLER_HEIGHT);
		this.mcDrag.lineTo(-w/2, RHANDLER_HEIGHT);
		//End fill
		this.mcDrag.endFill();
		//Create Delegate for onPress and onRelease
		fnOnPress = Delegate.create(this, onColumnPress);
		fnOnPress.x = dataX;
		fnOnPress.y = canvasY;
		fnOnPress.toY = canvasToY;
		fnOnPress.w = 0;
		fnOnPress.dragBorderProp = dragBorderProp;
		this.mcDrag.onPress = fnOnPress;
		//fnOnRelease
		fnOnRelease = Delegate.create(this, onColumnRelease);
		fnOnRelease.index = dragBorderProp.index;
		fnOnRelease.dsindex = dragBorderProp.dsindex;
		fnOnRelease.applyAnimation = applyAnimation;
		this.mcDrag.onReleaseOutside = this.mcDrag.onRelease=fnOnRelease;
		//Store the class Reference for access within the event handlers
		classRef = this;
		//Dispatch event to show the roll over using the cursor
		this.mcDrag.onRollOver = function() {
			//Hide the hand cursor
			this.useHandCursor = false;
			classRef.dispatchEvent({type:"onResizeHRollOver", target:classRef, mc:this});
		};
		//Dispatch event to hide the resize handler and show the mouse again
		this.mcDrag.onRollOut = function() {
			classRef.dispatchEvent({type:"onResizeHRollOut", target:classRef, mc:this});
		};
	}
	/**
	* static function to parse all the div line values and store in a static array
	* of the current class.
	*/
	public static function storeDivLineVal(divLineArr:Array) {
		divLineVal = divLineArr;
	}
	/**
	* setSnapToDivProp method sets the global properties for snap to Div for this class
	 * @param	snapToDivFlag		boolean value to indicate snapDiv set or unset.
	 * @param	snapToDivOnlyFlag	boolean value to indicate snapOnly set or unset.
	 * @param	doNotSnapFlag		unset the snapping opiton.
	 * @param	relaxation			value to indicate snapping based on relaxation value
	*/
	public function setSnapToDivProp(snapToDivFlag:Boolean, snapToDivOnlyFlag:Boolean, doNotSnapFlag:Boolean, relaxation:Number) {
		//Sets it class variable for reference purpose
		this.snapToDiv = snapToDivFlag;
		this.snapToDivOnly = snapToDivOnlyFlag;
		this.doNotSnap = doNotSnapFlag;
		this.snapToDivRelaxation = relaxation;
	}
	/**
	 * snapToDivLineOnly method drags the column to either one of the nearest div line 
	 * irrespective of the relaxation point.
	*/
	private function snapToDivLineOnly():Void {
		//Trace each and every div lines
		for (var i:Number = 0; i<=divLineVal.length-1; i++) {
			//Check to which div line limit the new height belongs to
			if (this.mcDrag._y>=divLineVal[i] && this.mcDrag._y<=divLineVal[i+1]) {
				//Set the drag border based on the nearest div line
				if ((Math.abs(this.mcDrag._y-divLineVal[i]))>(Math.abs(this.mcDrag._y-divLineVal[i+1]))) {
					//Snap to next nearest div line
					//Set the height according to the nearest div line value
					h = divLineVal[i+1]-(this.mc._y);
					this.mcDrag.yPos = divLineVal[i+1];
					break;
				} else {
					//Snap to next nearest div line
					//Set the height according to the nearest div line value
					h = divLineVal[i]-(this.mc._y);
					this.mcDrag.yPos = divLineVal[i];
					break;
				}
				
			}
		}
		//Set the drag rectangle position based on the nearest div line
		this.mcDrag._y = this.mcDrag.yPos;
	}
	/**
	 * snapToDivLine method drags the column to its nearest div line based on the relaxation point
	*/
	private function snapToDivLine():Void {
		//Trace each and every div lines
		for (var i:Number = 0; i<divLineVal.length; i++) {
			// If the column is within the relaxation value to any div line then set div line 
			// value else the current dragged position.
			if (Math.abs(this.mcDrag._y-divLineVal[i])<=this.snapToDivRelaxation) {
				//Check to which nearest div line the column belongs to based on the relaxtion value
				this.h = divLineVal[i]-(this.mc._y);
				this.mcDrag.yPos = divLineVal[i];
				break;
			} else {
				this.mcDrag.yPos = this.mcDrag._y;
			}
		}
		//Set the drag rectangle position based on the nearest div line
		this.mcDrag._y = this.mcDrag.yPos;
	}
	// -------------------- EVENT HANDLERS --------------------//
	/**
	 * updateColumn method updates the column by drawing a border while dragging each column
	*/
	private function updateColumn():Void {
		//drag border properties is stored as an arguments
		var dragBorderProp:Object = arguments.caller.dragBorderProp;
		//Clear the previously drawn border ( if any )
		this.mcDragBorder.clear();
		//Calculate the difference in height after the column has been dragged
		var diffHeight:Number = this.mcDrag._y-this.mc._y;
		//Draw the respective border of the column
		mcDragBorder.lineStyle(dragBorderProp.borderThickness, parseInt(dragBorderProp.borderColor, 16), dragBorderProp.borderAlpha);
		mcDragBorder.moveTo(-w/2, 0);
		mcDragBorder.lineTo(-w/2, diffHeight);
		mcDragBorder.lineTo(w/2, diffHeight);
		mcDragBorder.lineTo(w/2, 0);
		mcDragBorder.lineTo(-w/2, 0);
		mcDragBorder.endFill();
		//Set the drag border position - central point registration
		mcDragBorder._x = this.mc._x;
		mcDragBorder._y = this.mc._y;
		//Dispatch to handler the show and hide of the resize handler - onRollOver event
		this.dispatchEvent({type:"onResizeHRollOver", target:this});
	}
	/**
	 * onColumnPress is the delegat-ed event handler method that'll
	 * be invoked when the user clicks his mouse over a column (area above the column for dragging). 
	 * This function is invoked, only if the dragging option is set.
	 * Here, we drag the column border.
	 */
	private function onColumnPress():Void {
		//Function to delegate with arguments
		var fnOnEnterFrame:Function;
		//height, width, x and y are stored in arguments
		var canvasX:Number = arguments.caller.x;
		var canvasY:Number = arguments.caller.y;
		var canvasToY:Number = arguments.caller.toY;
		var dragBorderProp:Object = arguments.caller.dragBorderProp;
		//Allow dragging based on the negative Dragging option
		// if the value is initially set as <0 then we ignore isNegativeDragging property
		if (dragBorderProp.isNegativeDraggable || this.originalH<0) {
			//Make the column area drag in both the planes
			this.mcDrag.startDrag(false, canvasX, canvasY, canvasX, canvasToY);
		} else {
			//Make the column area drag only in positive plane
			this.mcDrag.startDrag(false, canvasX, canvasY, canvasX, dragBorderProp.basePlanePos);
		}
		//OnEnterFrame create a new border and scale it according to position of the draggable area
		fnOnEnterFrame = Delegate.create(this, updateColumn);
		//Store dragBorder properties as an argument
		fnOnEnterFrame.dragBorderProp = dragBorderProp;
		this.mcDrag.onEnterFrame = fnOnEnterFrame;
	}
	/**
	 * onColumnRelease is the delegat-ed event handler method that'll
	 * be invoked when the user releases his mouse after dragging. 
	 * This function is invoked, only if the dragging option is set.
	 * Here, we stop the dragging and set the new height, text and tool tip.
	 */
	private function onColumnRelease():Void {
		//index and dsindex of the column dragged
		var index:Number = arguments.caller.index;
		var dsindex:Number = arguments.caller.dsindex;
		var applyAnimation:Object = arguments.caller.applyAnimation;
		var prevHeight:Number;
		//Free the onEnterFrame Event
		delete this.mcDrag.onEnterFrame;
		//We dispatch an event for rollout function
		this.dispatchEvent({type:"onResizeHRollOut", target:this});
		//stop the dragging event
		this.mcDrag.stopDrag();
		//Store the old height for animation
		prevHeight = this.h;
		//Update the column height based on the current position of the draggable column area
		this.h = this.mcDrag._y-(this.mc._y);
		//Check for snapToDiv param
		if (!this.doNotSnap) {
			if (this.snapToDivOnly) {
				//Snap to div Only sets it to nearest div line
				snapToDivLineOnly();
			} else if (this.snapToDiv) {
				//Snap it to nearest div line based on the relaxation
				snapToDivLine();
			} else {
				//New height would be the position of the drag border
				this.mcDrag.yPos = this.mcDrag._y;
			}
		} else {
			//New height would be the position of the drag border
			this.mcDrag.yPos = this.mcDrag._y;
		}
		//Clear the previous column     
		this.mc.clear();
		//clear the dragging border
		this.mcDragBorder.clear();
		//Redraw the column
		draw();
		if (applyAnimation) {
			//Tween animation
			//If both positive
			if (prevHeight<0 && h<0) {
				var twMC:Tween = new Tween(this.mc, "_height", Regular.easeOut, -prevHeight, -this.h, 0.3, true);
			} else if (prevHeight>0 && h>0) {
				// if both negative
				var twMC:Tween = new Tween(this.mc, "_height", Regular.easeOut, prevHeight, this.h, 0.3, true);
			} else if (prevHeight>0 && h<0) {
				// if last height negative but current positive
				var twMC:Tween = new Tween(this.mc, "_height", Regular.easeOut, 0, -this.h, 0.3, true);
			} else if (prevHeight<0 && h>0) {
				// if last height positive but current negative
				var twMC:Tween = new Tween(this.mc, "_height", Regular.easeOut, 0, this.h, 0.3, true);
			}
		}
		//Dispatch event to modify the display values and tool tip text.      
		this.dispatchEvent({type:"onResizeEnd", target:this, index:index, dsindex:dsindex, colHeight:this.mcDrag.yPos});
	}
}
