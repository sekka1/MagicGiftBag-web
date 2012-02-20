/**
 * @class ConnectorAttributeDialogBox
 * @author InfoSoft Global (P) Ltd. www.InfoSoftGlobal.com
 * @version 3.0
 *
 * Copyright (C) InfoSoft Global Pvt. Ltd. 2005-2006
 * ConnectorAttributeDialogBox class helps to accept some attribute for a Connector
 * Dialog Box to accept attributes of a new Connector.
*/
//FCButton
import com.fusioncharts.helper.FCButton;
//Utils Class
import com.fusioncharts.helper.Utils;
//Drop Shadow filter
import flash.filters.DropShadowFilter;
//Combo box
import mx.controls.ComboBox;
//CheckBox
import mx.controls.CheckBox;
//Numeric Stepper
import mx.controls.NumericStepper;
//Text Input
import mx.controls.TextInput;
//Delegate
import mx.utils.Delegate;
//Event Dispatcher
import mx.events.EventDispatcher;
//Class
class com.fusioncharts.helper.ConnectorAttributeDialogBox {
	//Instance variables
	private var parent:MovieClip;
	private var depth:Number;
	private var sX:Number;
	private var sY:Number;
	//Height and width of the Connector Dialog Box
	private var h:Number;
	private var w:Number;
	//Button (Ok button) Position
	private var btnX:Number;
	private var btnY:Number;
	//Corner Radius of the rounded Rectangle of the Dialog Box
	private var cornerRadius:Number;
	//Cosmetic Property
	private var borderColor:String;
	private var borderThickness:Number;
	private var bgColor:String;
	private var yPadding:Number;
	private var fontProps:Object;
	private var dropShadow:Boolean;
	//MovieClip of the dialog Box
	private var db:MovieClip;
	//Movie clip for the button
	private var btnSubmit:FCButton;
	//Movie clip for the Cancel button
	private var btnCancel:FCButton;
	private var btnCancelX:Number;
	private var btnCancelY:Number;
	//Update Information TF
	private var labelTf:TextInput;
	private var strengthTf:NumericStepper;
	private var dashed_ch:CheckBox;
	private var arrowEnd_ch:CheckBox;
	private var arrowStart_ch:CheckBox;
	private var id_cb:ComboBox;
	private var toId:String;
	//Array of to List
	private var dataNotConnected:Array;
	//Container
	private var dbCont:MovieClip;
	//Create listeners for the button
	private var btnListener:Object;
	private var btnCancelListener:Object;
	//Listner for the ComboBox
	private var cbSeriesListener:Object;
	//Constructor function
	/**
	 * Here, we initialize the dialog Box objects.
	 *	@param		target	Movie clip in which we've to create the
	 *						dialog box.
	 *	@param		depth	Depth at which we create dialog box.
	 *	@param		sX		Top X of the stage.
	 *	@param		sY		Top Y of the stage.
	 *	@param		yPadding	y-axis padding (pixels)
	 *	@param		dataNotConnected	List of data items to be added in Combo Box
	*/
	function ConnectorAttributeDialogBox(target:MovieClip, depth:Number, sX:Number, sY:Number, yPadding:Number, dataNotConnected:Array) {
		//Store parameters in instance variables
		this.parent = target;		
		this.sX = sX;
		this.sY = sY;
		this.yPadding = yPadding;
		this.dataNotConnected = dataNotConnected;
		//Create the required movieclip for the dialog box
		this.dbCont = this.parent.createEmptyMovieClip("DialogBoxContainer", depth);
		//Create a movieclip within the dialog box Container for panel
		this.db = this.dbCont.createEmptyMovieClip("bgPanel", 1);
		//Create a movieclip within the dialog box for Submit button
		this.btnSubmit = new FCButton(this.dbCont, 2);
		//Create a movieclip within the dialog box for Cancel button
		this.btnCancel = new FCButton(this.dbCont, 3);
		//Hide the movieClip initially
		this.db._visible = false;
		//Hide the hand cursor
		this.db.useHandCursor = false;
		//Initialize the listener
		this.btnListener = new Object();
		//Initialize the listener
		this.btnCancelListener = new Object();
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
	 * setParams method sets the cosmetic and functional parameters 
	 * for the dialog Box.	 
	 *	@param	height		Sets the height of the rectangle
	 *	@param	width		Sets the width of the rectangle
	 *	@param	cornerRadius	Sets the corner radius of the rounded rectangle.
	 *	@param	bgColor		Background Color
	 *	@param	borderColor	Border Color of the tool tip
	 *	@param	fontProps	Object that holds the font properties for the error message
	 *	@param	dropShadow	Whether to drop shadow for the tool tip.
	*/
	public function setParams(height:Number, width:Number, cornerRadius:Number, bgColor:String, borderColor:String, borderThickness:Number, fontProps:Object, dropShadow:Boolean) {
		//Store parameters in instance variables.
		this.h = height;
		this.w = width;
		this.cornerRadius = cornerRadius;
		this.bgColor = bgColor;
		this.borderColor = borderColor;
		this.borderThickness = borderThickness;
		this.dropShadow = dropShadow;
		this.fontProps = fontProps;
		//Set the initial position of the button based on the width and height of the dialog box
		var btnW:Number = 80;
		var btnH:Number = 20;
		// Create listener object for shape_cb
		this.cbSeriesListener = new Object();
		//Ok Button position
		this.btnX = width/2-btnW/2;
		this.btnY = (4/5*height);
		this.btnCancelX = this.btnX+btnW+5;
		this.btnCancelY = (4/5*height);
		//Set the default values for the button
		this.btnSubmit.setParams("Submit", this.btnX, this.btnY, btnH, btnW,5,"FFFFFF","CCCCCC","Verdana", "000000", 10);
		//Register the event with the button
		this.btnSubmit.addEventListener("click", this.btnListener);
		//Register the listener
		btnListener.click = Delegate.create( this, onClick);
		//Set the default values for the Cancel button
		this.btnCancel.setParams("Cancel", this.btnCancelX, this.btnCancelY, btnH, btnW, 5,"FFFFFF","CCCCCC","Verdana", "000000", 10);
		//Register the event with the button
		this.btnCancel.addEventListener("click", this.btnCancelListener);
		//Register the listener
		btnCancelListener.click = Delegate.create( this, onCancelClick);
		//Create filters and apply
		if (dropShadow) {
			var shadowFilter:DropShadowFilter = new DropShadowFilter(3, 45, 0x666666, 0.8, 4, 4, 1, 1, false, false, false);
			this.db.filters = [shadowFilter];
		}
	}
	/**
	 * drawRoundedRect method helps draw a rounded Rectangle based on the parameters specified.
	 *	Assumptions: Movie clip is already created and line/fill style has
	 *				 already been set before this method is called to
	 *				 draw a rounded rectangle.
	 *	@param		mc		Movie clip in which we've to draw the rounded rectangle
	 *	@param		x		X position of the rectangle
	 *	@param		y		Y position of the rectangle
	 *	@param		w		w width of the rectangle
	 *	@param		h		height of the rectangle
	 *	@param		cornerRadius	corner radius of the rectangle to show the roundness.
	 */
	public function drawRoundedRect(mc:MovieClip, x:Number, y:Number, w:Number, h:Number, cornerRadius:Number):Void {
		//We first see if the user has defined a corner radius for us. If yes, we need to create curves and calculate
		if (cornerRadius>0) {
			//Initialize variables to be usd
			var theta:Number, angle:Number, cx:Number, cy:Number, px:Number, py:Number;
			//Make sure that (w,h) is larger than 2*cornerRadius
			if (cornerRadius>Math.min(w, h)/2) {
				cornerRadius = Math.min(w, h)/2;
			}
			// theta = 45 degrees in radians 
			theta = Math.PI/4;
			// draw top line
			mc.moveTo(x+cornerRadius, y);
			mc.lineTo(x+w-cornerRadius, y);
			//angle is currently 90 degrees
			angle = -Math.PI/2;
			// draw tr corner in two parts
			cx = Math.round(x+w-cornerRadius+(Math.cos(angle+(theta/2))*cornerRadius/Math.cos(theta/2)));
			cy = Math.round(y+cornerRadius+(Math.sin(angle+(theta/2))*cornerRadius/Math.cos(theta/2)));
			px = Math.round(x+w-cornerRadius+(Math.cos(angle+theta)*cornerRadius));
			py = Math.round(y+cornerRadius+(Math.sin(angle+theta)*cornerRadius));
			mc.curveTo(cx, cy, px, py);
			angle += theta;
			cx = Math.round(x+w-cornerRadius+(Math.cos(angle+(theta/2))*cornerRadius/Math.cos(theta/2)));
			cy = Math.round(y+cornerRadius+(Math.sin(angle+(theta/2))*cornerRadius/Math.cos(theta/2)));
			px = Math.round(x+w-cornerRadius+(Math.cos(angle+theta)*cornerRadius));
			py = Math.round(y+cornerRadius+(Math.sin(angle+theta)*cornerRadius));
			mc.curveTo(cx, cy, px, py);
			// draw right line
			mc.lineTo(x+w, y+h-cornerRadius);
			// draw br corner
			angle += theta;
			cx = x+w-cornerRadius+(Math.cos(angle+(theta/2))*cornerRadius/Math.cos(theta/2));
			cy = y+h-cornerRadius+(Math.sin(angle+(theta/2))*cornerRadius/Math.cos(theta/2));
			px = x+w-cornerRadius+(Math.cos(angle+theta)*cornerRadius);
			py = y+h-cornerRadius+(Math.sin(angle+theta)*cornerRadius);
			mc.curveTo(cx, cy, px, py);
			angle += theta;
			cx = x+w-cornerRadius+(Math.cos(angle+(theta/2))*cornerRadius/Math.cos(theta/2));
			cy = y+h-cornerRadius+(Math.sin(angle+(theta/2))*cornerRadius/Math.cos(theta/2));
			px = x+w-cornerRadius+(Math.cos(angle+theta)*cornerRadius);
			py = y+h-cornerRadius+(Math.sin(angle+theta)*cornerRadius);
			mc.curveTo(cx, cy, px, py);
			// draw bottom line
			mc.lineTo(x+cornerRadius, y+h);
			// draw bl corner
			angle += theta;
			cx = x+cornerRadius+(Math.cos(angle+(theta/2))*cornerRadius/Math.cos(theta/2));
			cy = y+h-cornerRadius+(Math.sin(angle+(theta/2))*cornerRadius/Math.cos(theta/2));
			px = x+cornerRadius+(Math.cos(angle+theta)*cornerRadius);
			py = y+h-cornerRadius+(Math.sin(angle+theta)*cornerRadius);
			mc.curveTo(cx, cy, px, py);
			angle += theta;
			cx = x+cornerRadius+(Math.cos(angle+(theta/2))*cornerRadius/Math.cos(theta/2));
			cy = y+h-cornerRadius+(Math.sin(angle+(theta/2))*cornerRadius/Math.cos(theta/2));
			px = x+cornerRadius+(Math.cos(angle+theta)*cornerRadius);
			py = y+h-cornerRadius+(Math.sin(angle+theta)*cornerRadius);
			mc.curveTo(cx, cy, px, py);
			// draw left line
			mc.lineTo(x, y+cornerRadius);
			// draw tl corner
			angle += theta;
			cx = x+cornerRadius+(Math.cos(angle+(theta/2))*cornerRadius/Math.cos(theta/2));
			cy = y+cornerRadius+(Math.sin(angle+(theta/2))*cornerRadius/Math.cos(theta/2));
			px = x+cornerRadius+(Math.cos(angle+theta)*cornerRadius);
			py = y+cornerRadius+(Math.sin(angle+theta)*cornerRadius);
			mc.curveTo(cx, cy, px, py);
			angle += theta;
			cx = x+cornerRadius+(Math.cos(angle+(theta/2))*cornerRadius/Math.cos(theta/2));
			cy = y+cornerRadius+(Math.sin(angle+(theta/2))*cornerRadius/Math.cos(theta/2));
			px = x+cornerRadius+(Math.cos(angle+theta)*cornerRadius);
			py = y+cornerRadius+(Math.sin(angle+theta)*cornerRadius);
			mc.curveTo(cx, cy, px, py);
		} else {
			//No corner radius defined - so draw a simple rectangle
			mc.moveTo(x, y);
			mc.lineTo(x+w, y);
			mc.lineTo(x+w, y+h);
			mc.lineTo(x, y+h);
			mc.lineTo(x, y);
		}
	}
	/**
	 * draw method draw the initial Dialog Box
	*/
	public function draw():Void {
		//Set the border color if required
		if (this.borderColor != "" && this.borderColor != undefined && this.borderColor != null) {
			this.db.lineStyle(this.borderThickness, parseInt(this.borderColor, 16), 100);
		}
		//Set the background color if required                                        
		if (this.bgColor != "" && this.bgColor != undefined && this.bgColor != null) {
			this.db.beginFill(parseInt(this.bgColor, 16), 100);
		}
		this.drawRoundedRect(this.db, 0, 0, this.w, this.h, this.cornerRadius);
		//End Fill
		this.db.endFill();
		//Set the position with respect to the parent class
		this.db._x = this.sX-this.w/2;
		this.db._y = this.sY-this.h/2;
		this.createPanel();
		//Now create an OK button
		//Set the position with respect to the parent class
		this.btnSubmit.setPosition(this.sX-this.w/2+this.btnX, this.sY-this.h/2+this.btnY);
		//Draw the mouse and set the visibility as true
		this.btnSubmit.draw();
		this.btnSubmit.show();
		this.show();
		//Now create a Cancel button
		//Set the position with respect to the parent class
		this.btnCancel.setPosition(this.sX-this.w/2+this.btnCancelX, this.sY-this.h/2+this.btnCancelY);
		//Draw the mouse and set the visibility as true
		this.btnCancel.draw();
		this.btnCancel.show();
	}
	/**
	 *  createPanel method sets the panel with textfield and buttons
	*/
	public function createPanel():Void {
		//Loop Variable
		var i:Number;
		//Set the style object for the Labels
		var styleObj:Object =  new Object();
		styleObj.bold = false;
		styleObj.align = "left";		
		styleObj.vAlign = "middle";
		styleObj.italic = false;
		styleObj.underline = false;
		styleObj.font = this.fontProps.font;
		styleObj.size = this.fontProps.fontSize;
		styleObj.color = this.fontProps.fontColor;
		styleObj.isHTML = true;
		styleObj.leftMargin = 0;
		styleObj.letterSpacing= 0;
		styleObj.bgColor = "";
		styleObj.borderColor = "";
		//Set the Starting position for the components in the panel
		var startX:Number = 20;
		var startY:Number = 20;
		//Starting Depth
		var startDepth:Number = 5;
		//Store the class Reference
		var classRef:Object = this;
		//Create the label for components
		Utils.createText(false, "Label", this.db, startDepth++, startX, startY+4, 0, styleObj);
		//Create an input textfield
		this.labelTf = this.db.createClassObject(mx.controls.TextInput, "label_ti", startDepth++);
		this.labelTf._x = startX+70;
		this.labelTf._y = startY-5;
		this.labelTf.text = "";
		this.labelTf.setSize(75, this.labelTf._height);
		//Now create a checkbox
		Utils.createText(false, "Connect To", this.db, startDepth++, startX, startY+35, 0, styleObj);
		id_cb = this.db.createClassObject(mx.controls.ComboBox, "seriesName_cb", startDepth++);
		//Set the position
		id_cb._x = startX+70;
		id_cb._y = startY+25;
		// Create event handler function.
		cbSeriesListener.change = function (evt_obj:Object) {
			 classRef.toId = evt_obj.target.selectedItem.label;
		}
		//Loop to add all the seriesnames for the dataset series
		//Sort the array before adding to the list
		dataNotConnected.sortOn(["dsId", "id" ]);
		for(i=0;i<this.dataNotConnected.length;i++) {
			id_cb.addItem({data:i, label:this.dataNotConnected[i].dsId+ "," + this.dataNotConnected[i].id});
		}
		//Default
		this.toId = id_cb.selectedItem.label;
		// Add event listener.
		id_cb.addEventListener("change", cbSeriesListener);
		//Create a label text for Strength
		Utils.createText(false, "Strength", this.db, startDepth++, startX+150, startY+4, 0, styleObj);
		//Create an input textfield
		this.strengthTf = this.db.createClassObject(mx.controls.NumericStepper, "strength_ti", startDepth++);
		this.strengthTf._x = startX+205;
		this.strengthTf._y = startY-5;
		this.strengthTf.maximum = 5;
		this.strengthTf.minimum = 0;
		this.strengthTf.setSize(50, 20);
		this.strengthTf.stepSize = 0.1;
		//Draw a checkBox for accepting Dashed parameters
		this.dashed_ch = this.db.createClassObject(mx.controls.CheckBox, "dashed_ch", startDepth++, {label:'Dashed', selected:false});
		this.dashed_ch._x = startX;
		this.dashed_ch._y = startY+50;
		//Draw a checkBox for accepting Dashed parameters
		this.arrowStart_ch = this.db.createClassObject(mx.controls.CheckBox, "arrowStart_ch", startDepth++, {label:'Arrow At Start', selected:false});
		this.arrowStart_ch._x = startX;
		this.arrowStart_ch._y = startY+75;
		//Draw a checkBox for accepting Dashed parameters
		this.arrowEnd_ch = this.db.createClassObject(mx.controls.CheckBox, "arrowEnd_ch", startDepth++, {label:'Arrow At End', selected:false});
		this.arrowEnd_ch._x = startX+100;
		this.arrowEnd_ch._y = startY+75;
	}
	/**
	  * onClick method Handles the event to process once the Submit Button is pressed
	*/
	private function onClick():Void {
		//Dispatch information to parent class to update the required arrays(Connectors, data )
		this.dispatchEvent({type:"onConnectorUpdate", target:this, label:this.labelTf.text, strength:Number(this.strengthTf.value), arrowStart:this.arrowStart_ch.selected, arrowEnd:this.arrowEnd_ch.selected, dashed:this.dashed_ch.selected, toId:this.toId });
		//delete the dialog box
		this.destroy();
	}
	/**
	  * onCancelClick method Handles the event to process once the Cancel Button is pressed
	*/
	private function onCancelClick():Void {
		//Dispatch information to parent class to update the required arrays(Connectors, data )
		this.dispatchEvent({type:"onCancel", target:this});
		//delete the dialog box
		this.destroy();
	}
	/**
	  * show method shows the Dialog Box.
	*/
	public function show():Void {
		//Show the dialog box
		this.db._visible = true;
	}
	/**
	 * hide method hides the tool tip.
	*/
	public function hide():Void {
		//Hide the dialog box
		this.db._visible = false;
		this.btnSubmit.hide();
		this.btnCancel.hide();
	}
	/**
	 * destroy method MUST be called whenever you wish to delete this class's
	 * instance.
	*/
	public function destroy():Void {
		//Remove the Listener
		this.id_cb.removeEventListener("change",this.cbSeriesListener);
		//Delete all the components
		this.id_cb.destroyObject();
		this.arrowStart_ch.destroyObject();
		this.arrowEnd_ch.destroyObject();
		this.strengthTf.destroyObject();
		this.labelTf.destroyObject();
		this.dashed_ch.destroyObject();
		//Delete the movieclip
		this.db.removeMovieClip();
		this.btnSubmit.destroy();
		this.btnSubmit.removeEventListener("click", this.btnListener);
		this.btnCancel.destroy();
		this.btnCancel.removeEventListener("click", this.btnCancelListener);
	}
}
