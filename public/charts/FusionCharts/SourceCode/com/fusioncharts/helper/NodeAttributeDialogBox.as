/**
 * @class NodeAttributeDialogBox
 * @author InfoSoft Global (P) Ltd. www.InfoSoftGlobal.com
 * @version 3.0
 *
 * Copyright (C) InfoSoft Global Pvt. Ltd. 2005-2006
 * NodeAttributeDialogBox class helps to accept some attribute for a NODE - to add new Nodes
*/
//FCButton
import com.fusioncharts.helper.FCButton;
import com.fusioncharts.helper.Utils;
//Drop Shadow filter
import flash.filters.DropShadowFilter;
//Combo box
import mx.controls.ComboBox;
//Check Box
import mx.controls.CheckBox;
//Text Input
import mx.controls.TextInput;
//Delegate
import mx.utils.Delegate;
import mx.events.EventDispatcher;
class com.fusioncharts.helper.NodeAttributeDialogBox {
	//Instance variables
	private var parent:MovieClip;
	private var depth:Number;
	private var sX:Number;
	private var sY:Number;
	private var h:Number;
	private var w:Number;
	private var btnX:Number;
	private var btnY:Number;
	private var cornerRadius:Number;
	private var borderColor:String;
	private var borderThickness:Number;
	private var bgColor:String;
	private var yPadding:Number;
	private var fontProps:Object;
	private var dropShadow:Boolean;
	//MovieClip of the dialog Box
	private var db:MovieClip;
	//Movie clip for the button
	private var btnOk:FCButton;
	//Movie clip for the Cancel button
	private var btnCancel:FCButton;
	private var btnCancelX:Number;
	private var btnCancelY:Number;
	//Update information
	private var dsSeriesName:String;
	private var nodeShape:String;
	private var seriesNameArr:Array;
	private var idArr:Array;
	//Container
	private var dbCont:MovieClip;
	//Create a listener for the button
	private var btnListener:Object;
	private var btnCancelListener:Object;
	private var listenerObject:Object 
	//Check Box
	private var image_ch:CheckBox;
	//Input Textfield
	private var idTf:TextInput;
	private var errorTf:TextField;
	private var errorInvalidTf:TextField;
	private var nameTf:TextInput;
	private var widthTf:TextInput;
	private var heightTf:TextInput;
	private var radiusTf:TextInput;	
	private var numSidesTf:TextInput;	
	private var urlTf:TextInput;
	private var shapes_cb:ComboBox;
	private var seriesName_cb:ComboBox;
	
	
	//Constructor function
	/**
	 * Here, we initialize the dialog Box objects.
	 *	@param		target	Movie clip in which we've to create the
	 *						dialog box.
	 *	@param		depth	Depth at which we create dialog box.
	 *	@param		sX		Top X of the stage.
	 *	@param		sY		Top Y of the stage.
	 *	@param		yPadding	y-axis padding (pixels)
	 *	@param		seriesNameArr	array holding all the seriesName of the datasets
	 *	@param		idArr			array holding all the previous data items id - for checking new ID
	*/
	function NodeAttributeDialogBox(target:MovieClip, depth:Number, sX:Number, sY:Number, yPadding:Number, seriesNameArr:Array, idArr:Array) {
		//Store parameters in instance variables
		this.parent = target;		
		this.sX = sX;
		this.sY = sY;
		this.yPadding = yPadding;
		this.seriesNameArr = seriesNameArr;
		this.idArr = idArr;
		//Create the required movieclip for the dialog box
		this.dbCont = this.parent.createEmptyMovieClip("DialogBoxContainer", depth);
		//Create a movieclip within the dialog box Container for panel
		this.db = this.dbCont.createEmptyMovieClip("bgPanel", 1);
		//Create a movieclip within the dialog box for OK button
		this.btnOk = new FCButton(this.dbCont, 2);
		//Create a movieclip within the dialog box for Cancel button
		this.btnCancel = new FCButton(this.dbCont, 3);
		//Hide the movieClip initially
		this.db._visible = false;
		//Hide the hand cursor
		this.db.useHandCursor = false;
		//Initialize the listener
		this.btnListener = new Object();
		this.btnCancelListener = new Object();
		listenerObject = new Object();
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
		this.btnX = width/2-btnW/2-5;
		this.btnY = (4/5*height)+5;
		this.btnCancelX = this.btnX+btnW+10;
		this.btnCancelY = (4/5*height)+5;
		//Set the default values for the button
		this.btnOk.setParams("Submit", this.btnX, this.btnY, btnH, btnW,5,"FFFFFF","CCCCCC","Verdana", "000000", 10);
		//Register the event with the button
		this.btnOk.addEventListener("click", this.btnListener);
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
		this.btnOk.setPosition(this.sX-this.w/2+this.btnX, this.sY-this.h/2+this.btnY);
		//Draw the mouse and set the visibility as true
		this.btnOk.draw();
		this.btnOk.show();
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
		//Set the text for the error message
		var styleObj:Object =  new Object();
		styleObj.bold = false;
		styleObj.align = "left";		
		styleObj.vAlign = "center";
		styleObj.italic = false;
		styleObj.underline = false;
		styleObj.font = this.fontProps.font;
		styleObj.size = this.fontProps.fontSize;
		styleObj.color = "FF0000";
		styleObj.isHTML = true;
		styleObj.leftMargin = 0;
		styleObj.letterSpacing= 0;
		styleObj.bgColor = "";
		styleObj.borderColor = "";
		//Initial position within the DB
		var startX:Number = 20;
		var startY:Number = 20;
		//Starting depth for all the components in the DB
		var startDepth:Number = 2;
		//Create an error in ID text field
		this.errorTf = Utils.createText(false, "This Id exists for another node. Please use a different Id.", this.db, startDepth++, startX, startY+148, 0, styleObj, true, 250, 50).tf;
		this.errorInvalidTf = Utils.createText(false, "Id cannot be blank.", this.db, startDepth++, startX, startY+150, 0, styleObj).tf;
		//Make it hidden initially
		this.errorTf._visible = false;
		this.errorInvalidTf._visible = false;
		//Set the default color style
		styleObj.color = this.fontProps.fontColor;
		//Now create the ID label TF
		Utils.createText(false, "Id", this.db, startDepth++, startX, startY, 0, styleObj);
		//Create an input textfield
		this.idTf = this.db.createClassObject(mx.controls.TextInput, "id_ti", startDepth++);
		this.idTf._x = startX+55;
		this.idTf._y = startY-2;
		this.idTf.setSize(60, this.idTf._height);
		Utils.createText(false, "Label", this.db, startDepth++, startX, startY+50, 0, styleObj);
		//Create an input textfield
		this.nameTf = this.db.createClassObject(mx.controls.TextInput, "label_ti", startDepth++);
		this.nameTf._x = startX+55;
		this.nameTf._y = startY+50;
		this.nameTf.setSize(120, this.nameTf._height);
		//Create the SHAPE label 
		Utils.createText(false, "Shape", this.db, startDepth++, startX, startY+75, 0, styleObj);
		var widthLabelTf:TextField = Utils.createText(false, "Width", this.db, startDepth++, startX, startY+101, 0, styleObj, true, 50, 20).tf;
		//Create an input textfield
		this.widthTf = this.db.createClassObject(mx.controls.TextInput, "width_ti", startDepth++);
		this.widthTf._x = startX+55;
		this.widthTf._y = startY+100;
		this.widthTf.setSize(40, this.widthTf._height);
		this.widthTf.restrict = "0-9"
		Utils.createText(false, "Dataset", this.db, startDepth++, startX, startY+25, 0, styleObj);
		var heightLabelTf:TextField = Utils.createText(false, "Height", this.db, startDepth++, startX+120, startY+101, 0, styleObj, true, 50, 20).tf;
		var radiusLabelTf:TextField = Utils.createText(false, "Radius", this.db, startDepth++, startX, startY+101, 0, styleObj, true, 50, 20).tf;
		var numSideLabelTf:TextField = Utils.createText(false, "Sides", this.db, startDepth++, startX+120, startY+101, 0, styleObj, true, 80, 20).tf;
		//Hide it initially - as Rectangle is the default shape chosen
		numSideLabelTf._visible = false;
		radiusLabelTf._visible = false;
		//Create an input textfield
		this.heightTf = this.db.createClassObject(mx.controls.TextInput, "height_ti", startDepth++);
		this.heightTf._x = startX+170;
		this.heightTf._y = startY+100;
		this.heightTf.setSize(40, this.heightTf._height);
		this.heightTf.restrict = "0-9"
		//Create an input textfield for the radius
		this.radiusTf = this.db.createClassObject(mx.controls.TextInput, "radius_ti", startDepth++);
		this.radiusTf._x = startX+55;
		this.radiusTf._y = startY+100;
		this.radiusTf.setSize(40, this.radiusTf._height);
		this.radiusTf.restrict = "0-9"
		this.radiusTf._visible = false;
		//Create an input textfield for the radius
		this.numSidesTf = this.db.createClassObject(mx.controls.TextInput, "numSide_ti", startDepth++);
		this.numSidesTf._x = startX+170;
		this.numSidesTf._y = startY+100;
		this.numSidesTf.setSize(40, this.numSidesTf._height);
		this.numSidesTf.restrict = "0-9"
		this.numSidesTf._visible = false;
		//Now create the URL TF
		var urlLabelTf:TextField = Utils.createText(false, "URL", this.db, startDepth++, startX, startY+125, 0, styleObj).tf;
		urlLabelTf._visible = false;
		//Create an input textfield
		this.urlTf = this.db.createClassObject(mx.controls.TextInput, "url_ti", startDepth++);
		this.urlTf._x = startX+55;
		this.urlTf._y = startY+125;
		this.urlTf.setSize(100, this.urlTf._height);
		this.urlTf._visible = false;
		//Now, we draw a combo box which lists all the series Names
		seriesName_cb = this.db.createClassObject(mx.controls.ComboBox, "seriesName_cb", startDepth++);
		//Set the position
		seriesName_cb._x = startX+55;
		seriesName_cb._y = startY+23;
		//Set the size
		seriesName_cb.setSize(seriesName_cb.width, seriesName_cb.height);
		// Create listener object for shape_cb
		var cbSeriesListener:Object = new Object();
		//Store the class Reference
		var classRef:Object = this;
		// Create event handler function.
		cbSeriesListener.change = function (evt_obj:Object) {
			 classRef.dsSeriesName = evt_obj.target.selectedItem.label;
		}
		//Loop to add all the seriesnames for the dataset series
		for(i=0;i<this.seriesNameArr.length;i++) {
			seriesName_cb.addItem({data:i, label:this.seriesNameArr[i].sn+ "," + this.seriesNameArr[i].dsId});
		}
		//Set the default seriesname depending upon the first element of dropdown
		this.dsSeriesName = seriesName_cb.selectedItem.label;		
		// Add event listener.
		seriesName_cb.addEventListener("change", cbSeriesListener);
		//Draw a different ComboBox for the different shapes available to Node
		shapes_cb = this.db.createClassObject(mx.controls.ComboBox, "shapes_cb", startDepth++);
		//Set the position
		shapes_cb._x = startX+55;
		shapes_cb._y = startY+75;
		//Set the size of the comboBox
		shapes_cb.setSize(shapes_cb.width, shapes_cb.height);
		//Add all the different shapes available for the Nodes
		shapes_cb.addItem({data:1, label:"Rectangle"});
		shapes_cb.addItem({data:2, label:"Circle"});
		shapes_cb.addItem({data:3, label:"Polygon"});
		//Draw a checkBox for accepting Image parameters
		this.image_ch = this.db.createClassObject(mx.controls.CheckBox, "image_ch", startDepth++, {label:'Image', selected:false});
		this.image_ch._x = startX+170;
		this.image_ch._y = startY+75;
		//Register the listener
		this.listenerObject.click = function(eventObject:Object) {
			//Negate the visibility
			 urlLabelTf._visible = !urlLabelTf._visible;
			 classRef.urlTf._visible = !classRef.urlTf._visible;
		};
		this.image_ch.addEventListener("click", this.listenerObject);
		//Set the default nodeShape as First element of DropDown
		this.nodeShape = "Rectangle";
		// Create listener object for shape_cb
		var cbListener:Object = new Object();
		// Create event handler function.
		cbListener.change = function (evt_obj:Object) {
			 classRef.nodeShape = evt_obj.target.selectedItem.label;
			 if(evt_obj.target.selectedItem.data == 1)  {
				 //Hide and show as per the selected shape
				 classRef.heightTf._visible = true;
				 classRef.widthTf._visible = true;
				 classRef.radiusTf._visible = false;
				 classRef.numSidesTf._visible = false;
				 widthLabelTf._visible = true;
				 heightLabelTf._visible = true;
				 radiusLabelTf._visible = false;
				 numSideLabelTf._visible = false;
			 }
			 else if(evt_obj.target.selectedItem.data == 2 || evt_obj.target.selectedItem.data == 3 ){
				 classRef.heightTf._visible = false;
				 classRef.widthTf._visible = false;
				 classRef.radiusTf._visible = true;
				 classRef.numSidesTf._visible = false;
				 numSideLabelTf._visible = false;
				 if(evt_obj.target.selectedItem.data == 3) {
					 classRef.numSidesTf._visible = true;
					 numSideLabelTf._visible = true;
				 }
				 widthLabelTf._visible = false;
				 heightLabelTf._visible = false;
				 radiusLabelTf._visible = true;
			 }
		}
		// Add event listener.
		shapes_cb.addEventListener("change", cbListener);
	}
	/**
	  * onClick method Handles the event to process once the Ok Button is pressed
	*/
	private function onClick():Void {
		//Error Flag
		var errFlag:Boolean = false;
		//Loop Variable
		var i:Number;
		//If there is no error we dispatch the event to its parent class
		//Check the id entered with the already stored ID's
		for(i=0;i<this.idArr.length;i++) {
			//Check
			if(idArr[i] == this.idTf.text) {
				//Set the flag
				errFlag = true;
				//Break
				break;
			}
		}
		// If error found then show the error label
		if(errFlag) { 
			//Make the error TF visible
			this.errorTf._visible = true;
			//Make any other error TF false
			this.errorInvalidTf._visible = false;
		}
		else if(this.idTf.text == "") {
			//Make the error TF visible
			this.errorInvalidTf._visible = true;
			//Make any other error TF false
			this.errorTf._visible = false;
		}
		else {
			//Check if the width and height of the node is given some value
			if(this.widthTf.text == "") {
				this.widthTf.text = "30";
			}
			if(this.heightTf.text == "") {
				this.heightTf.text = "30";
			}
			if(this.radiusTf.text == "") {
				this.radiusTf.text = "20";
			}
			//Dispatch information to parent class to update the required arrays(Connectors, data )
			this.dispatchEvent({type:"onNodeUpdate", target:this, id:this.idTf.text, dsSeriesName:this.dsSeriesName, nodeH:this.heightTf.text, nodeW:this.widthTf.text, nodeR:this.radiusTf.text, nodeShape:this.nodeShape, nodeName:this.nameTf.text, numSide:this.numSidesTf.text, url:this.urlTf.text, imageNode: this.image_ch.selected});
			//Hide the dialog box
			this.destroy();
		}
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
		this.btnOk.hide();
		this.btnCancel.hide();
	}
	/**
	 * destroy method MUST be called whenever you wish to delete this class's
	 * instance.
	*/
	public function destroy():Void {
		//remove all the listeners
		this.image_ch.removeEventListener("click", this.listenerObject);
		//Remove Components
		this.shapes_cb.destroyObject();
		this.seriesName_cb.destroyObject();
		//Remove textfields
		this.idTf.destroyObject();
		this.nameTf.destroyObject();
		this.widthTf.destroyObject();
		this.heightTf.destroyObject();
		this.radiusTf.destroyObject();
		this.numSidesTf.destroyObject();	
		this.image_ch.destroyObject();	
		//remove textfields
		this.errorInvalidTf.removeTextField();
		this.errorTf.removeTextField();
		//Delete the movieclip
		this.db.removeMovieClip();
		this.btnOk.destroy();
		this.btnOk.removeEventListener("click", this.btnListener);
		this.btnCancel.destroy();
		this.btnCancel.removeEventListener("click", this.btnCancelListener);
	}
}
