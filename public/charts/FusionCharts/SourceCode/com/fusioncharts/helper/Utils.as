 /**
* @class Utils
* @author InfoSoft Global (P) Ltd. www.InfoSoftGlobal.com
* @version 3.0
*
* Copyright (C) InfoSoft Global Pvt. Ltd. 2005-2006

* Utils class helps us bunch a group of utility functions
*/
import com.fusioncharts.extensions.StringExt;
class com.fusioncharts.helper.Utils 
{
	/**
	* Since Utils class is just a grouping of utility functions,
	* we do not want any instances of it (as all methods wil be static).
	* So, we declare a private constructor
	*/
	private function Utils ()
	{
		//Private constructor to avoid creation of instances
		
	}
	/**
	* getFirstValue method is is used to return the first non-null
	* non-undefined non-empty value in a list of values provided to
	* this method. To this method you can pass a list of values like
	* a,b,c,d,e in a preferential order and this method will return the
	* first non-null non-undefined value in this list.
	*/
	public static function getFirstValue ()
	{
		for (var i = 0; i < arguments.length; i ++)
		{
			if (arguments [i] != null && arguments [i] != undefined && arguments [i] != "")
			{
				return arguments [i];
			}
		}
		return "";
	}
	/**
	* getFirstNumber method is is used to return the first non-null
	* non-undefined non-empty NUMBER in a list of values provided to
	* this method. To this method you can pass a list of numbers like
	* a,b,c,d,e in a preferential order and this method will return the
	* first non-null non-undefined NUMBER in this list.
	*/
	public static function getFirstNumber () : Number 
	{
		for (var i = 0; i < arguments.length; i ++)
		{
			if (arguments [i] != null && arguments [i] != undefined && arguments [i] != "" && isNaN (Number (arguments [i])) == false)
			{
				return Number (arguments [i]);
			}
		}
		return 0;
	}
	/**
	* createText method creates a text field on the chart based
	* on the parameters provided.
	*	@param		simulationMode	Whether we're simulating the text field creation
	*								to get width & height. Or, if we're actually
	*								rendering it. In simulation mode, we do not
	*								re-position the textfield to optimize the flow.
	*	@param		targetMC	Movie clip inside which we've to
	*							create the text field.
	*	@param		strText		Text to be shown in the text field
	*	@param		depth		Depth at which the text field is to
	*							be created
	*	@param		xPos		x-position of the text field
	*	@param		yPos		y-position of the text field
	*	@param		rotation	Numerical value of rotation
	*	@param		objStyle	Object containing style properties for
	*							the text field.
	*							This object should necessarily contain
	*							the following parameters:
	*				align		Horizontal alignment. Possible values:
	*							"left", "center" or "right"
	*				vAlign		Vertical alignment of text. Possible values:
	*							"top", "middle" or "bottom"
	*				bold		Boolean value indicating whether the text
	*							would be bold.
	*				italic		Boolean value indicating whether the text
	*							would be italic.
	*				underline	Boolean value indicating whether the text
	*							would be underline.
	*				font		Font face for the text.
	*				size		Font size for the text
	*				color		Color in RRGGBB format for the text.
	*				isHTML		Boolean value indicating whether text would
	*							be rendered as HTML.
	*				leftMargin	Left margin for text (in pixels)
	*				letterSpacing	Numerical value indicating the spacing
	*								between two letters
	*				bgColor		Hex Color RRGGBB or undefined value indicating
	*							the background color of the text field. If undefined,
	*							that means text field shouldn't have a background.
	*				borderColor	Hex Color RRGGBB or undefined value indicating
	*							border color of text field.
	*				wrap		Boolean value indicating whether we need to wrap
	*							labels
	*				width		If we've to wrap, width of textbox
	*				height		If we've to wrap, height of text box
	*	@return				An object cotaining reference to the text field that it
	*							creates and dimensions
	*/
	public static function createText (simulationMode : Boolean, strText : String, targetMC : MovieClip, depth : Number, xPos : Number, yPos : Number, rotation : Number, objStyle : Object, wrap : Boolean, width : Number, maxHeight : Number) : Object 
	{
		//First up, we create a text format object and set the properties
		var tFormat : TextFormat = new TextFormat ();
		//Font properties
		tFormat.font = objStyle.font;
		tFormat.size = objStyle.size;
		tFormat.color = parseInt (objStyle.color, 16);
		//Text decoration
		tFormat.bold = objStyle.bold;
		tFormat.italic = objStyle.italic;
		tFormat.underline = objStyle.underline;
		//Margin and spacing
		tFormat.leftMargin = objStyle.leftMargin;
		tFormat.letterSpacing = objStyle.letterSpacing;
		//Extract align and vAlign Properties from style Obj
		var alignPos : String = objStyle.align;
		var vAlignPos : String = objStyle.vAlign;
		//Adjust alignment position for 315 rotation
		alignPos = (rotation == 315) ? "RIGHT" : alignPos;
		//Create the actual text field object now.
		var tf : TextField;
		//a & b are undefined variables
		//We want the initial text field size to be of flexible size as we're
		//not wrapping. So we do not define the width and height here
		//Based on wrap, we create the textfield
		if (wrap == true)
		{
			//Get text extent for this text.
			var reqHeightExt : Object = tFormat.getTextExtent (strText, width);
			//Create the textfield with minimum required width & height
			tf = targetMC.createTextField ("Text_" + depth, depth, xPos, yPos, width, Math.min (reqHeightExt.textFieldHeight, maxHeight));
			//Set align position for text format in case of wrapping
			tFormat.align = alignPos;
			//Set wordwrap
			tf.wordWrap = true;
		} else 
		{
			var a, b;
			tf = targetMC.createTextField ("Text_" + depth, depth, xPos, yPos, a, b);
			//Set align position
			tf.autoSize = alignPos;
		}
		//Set the properties
		tf.multiLine = true;
		tf.selectable = false;
		//Set the border color if required
		if (objStyle.borderColor != "")
		{
			tf.border = true;
			tf.borderColor = parseInt (objStyle.borderColor, 16);
		}
		//Set the background color if required
		if (objStyle.bgColor != "")
		{
			tf.background = true;
			tf.backgroundColor = parseInt (objStyle.bgColor, 16);
		}
		//Whether HTML text or not?
		tf.html = objStyle.isHTML;
		//If HTML text, we need to convert <BR> to /n.
		if (objStyle.isHTML)
		{
			strText = StringExt.replace (strText, "<BR>", "\n");
			strText = StringExt.replace (strText, "&lt;BR&gt;", "\n");
		}
		//Set the text
		if (objStyle.isHTML)
		{
			//If it's HTML text, set as htmlText
			tf.htmlText = strText;
		} else 
		{
			//Else, set as plain text
			tf.text = strText;
		}
		//Apply the text format
		tf.setTextFormat (tFormat);
		var originalH = tf._height;
		//Now, depending on the rotation angle, set the embedding of fonts
		if (rotation != null && rotation != undefined && rotation != 0)
		{
			//Set embedFonts to true
			tf.embedFonts = true;
			//Set rotation
			tf._rotation = rotation;
		}
		//We now re-position the text field if not in simulation mode
		if ( ! simulationMode)
		{
			//------------------------------------------------------------------//
			//If the rotation angle is 0,null or undefined i.e., the
			//text is horizontal, we just need to adjust the vertical
			//alignment.
			if (rotation == 0 || rotation == null || rotation == undefined)
			{
				switch (vAlignPos.toUpperCase ())
				{
					case "TOP" :
					//Top(of the ypos mid line
					//        TEXT HERE
					//---------MID LINE---------
					//       (empty space)
					tf._y = tf._y - (tf._height);
					break;
					case "MIDDLE" :
					//       (empty space)
					//---------TEXT HERE---------
					//       (empty space)
					tf._y = tf._y - (tf._height / 2);
					break;
					case "BOTTOM" :
					//Right is equivalent to bottom
					//       (empty space)
					//---------MID LINE---------
					//         TEXT HERE
					//No need to change - already at this position
					break;
				}
				//If in wrap mode, we need to horizontal align too
				if (wrap == true)
				{
					switch (alignPos.toUpperCase ())
					{
						case "LEFT" :
						//Nothing to do - pre-left aligned
						break;
						case "CENTER" :
						tf._x = tf._x - (tf._width / 2);
						break;
						case "RIGHT" :
						tf._x = tf._x - (tf._width);
						break;
					}
				}
			} else if (rotation == 270)
			{
				//Now, adjust the y orientation of the text
				switch (alignPos.toUpperCase ())
				{
					case "LEFT" :
					//Adjust y-position based on vAlignPos
					switch (vAlignPos.toUpperCase ())
					{
						case "TOP" :
						//Nothing
						break;
						case "MIDDLE" :
						tf._y = tf._y + (tf._height / 2);
						break;
						case "BOTTOM" :
						tf._y = tf._y + tf._height;
						break;
					}
					break;
					case "CENTER" :
					tf._x = tf._x - (tf._width / 2);
					//Adjust y-position based on vAlignPos
					switch (vAlignPos.toUpperCase ())
					{
						case "TOP" :
						tf._y = tf._y - (tf._height / 2);
						break;
						case "MIDDLE" :
						break;
						case "BOTTOM" :
						tf._y = tf._y + (tf._height / 2);
						break;
					}
					break;
					case "RIGHT" :
					tf._x = tf._x - (tf._width);
					//Adjust y-position based on vAlignPos
					switch (vAlignPos.toUpperCase ())
					{
						case "TOP" :
						tf._y = tf._y - tf._height;
						break;
						case "MIDDLE" :
						tf._y = tf._y - (tf._height / 2);
						break;
						case "BOTTOM" :
						break;
					}
					break;
				}
			} else if (rotation == 315)
			{
				//If the rotation angle is 315, it can only be for x-axis
				//names in the chart. So we directly alter the x and y position
				//irrespective of alignment position specified.
				var root2 = 1.42;
				tf._x -= (originalH / root2) / 2;
				//Minus 4 to avoid gutter space
				tf._y -= 4;
			}
		}
		//------------------------------------------------------------------//
		//Create an object which we'll return
		var rtnObj : Object = new Object ();
		//Set 3 properties of the temporary object
		//width, height, tf
		rtnObj.width = tf._width;
		rtnObj.height = tf._height;
		//For fonts not included
		if (rtnObj.height <= 4)
		{
			rtnObj.height = objStyle.font * 2;
		}
		//Set the text field
		rtnObj.tf = tf;
		//Delete the temporary objects
		delete tFormat;
		delete tf;
		//Return this object
		return rtnObj;
	}
}
