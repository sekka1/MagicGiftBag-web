 /**
* @class ErrorBar
* @author InfoSoft Global (P) Ltd. www.InfoSoftGlobal.com
* @version 3.0
*
* Copyright (C) InfoSoft Global Pvt. Ltd. 2005-2006
*
* ErrorBar extends the movie clip class and represents a single
* errorBar to be drawn on the chart. It takes in the required
* parameters (dimensions) and draws a errorBar.
*/
//Import required classes
import com.fusioncharts.extensions.MathExt;
import com.fusioncharts.extensions.DrawingExt;
class com.fusioncharts.core.chartobjects.ErrorBar extends MovieClip 
{
	//Instance variables
	//Movie clip container
	private var mc : MovieClip;
	//Width and height
	private var w : Number;
	private var h : Number;
	//errorBar cosmetics
	private var lineColor : String;
	private var lineAlpha : Number;
	private var lineThickness : Number;
	//Flag whether we need to draw the lower half of the errorBar
	private var drawBottomHalf : Boolean;
	/**
	* Constructor method. Takes in parameters and stores
	* in local variables.
	*/
	function ErrorBar (target : MovieClip, w : Number, h : Number, drawBottomHalf : Boolean, lineColor : String, lineAlpha : Number, lineThickness : Number)
	{
		//Store parameters in instance variables
		this.mc = target;
		this.w = w;
		//Negate to cancel Flash's reverse Y Co-ordinate system
		this.h = - h;
		//lineColor
		this.lineColor = lineColor;
		//lineAlpha
		this.lineAlpha = lineAlpha;
		//lineThickness
		this.lineThickness = lineThickness;
		//drawBottomHalf of errorBar
		this.drawBottomHalf = ( ! drawBottomHalf);
	}
	/**
	* draw method draws the errorBar
	*/
	public function draw () : Void 
	{
		//Set line style of errorBar
		this.mc.lineStyle (this.lineThickness, parseInt (this.lineColor, 16) , this.lineAlpha);
		//Draw upper T
		this.mc.lineTo (0, h);
		this.mc.lineTo ( - w / 2, h);
		this.mc.moveTo (0, h);
		this.mc.lineTo (w / 2, h);
		if (drawBottomHalf)
		{
			this.mc.moveTo (0, 0);
			//Draw lower T
			this.mc.lineTo (0, - h);
			this.mc.lineTo ( - w / 2, - h);
			this.mc.moveTo (0, - h);
			this.mc.lineTo (w / 2, - h);
		}
		//Draw 0 alpha hit area for tool tip
		//We do so only if line thickness < 3, else the hit areas would overlap
		if (this.lineThickness < 3)
		{
			this.mc.lineStyle (Number (this.lineThickness) + 2, parseInt (this.lineColor, 16) , 0);
			//Draw upper T
			this.mc.lineTo (0, h);
			this.mc.lineTo ( - w / 2, h);
			this.mc.moveTo (0, h);
			this.mc.lineTo (w / 2, h);
		}
	}
}
