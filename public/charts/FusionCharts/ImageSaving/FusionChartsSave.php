<?php
//Version 1.1: 
//	Fixed error where pixels were being ignored. Was happening because
//	we were checking for if($c) instead of if($c!="")
//Version 1.0: 
//	Initial Release

//Do not report errors
error_reporting(0);

// Get the width and height of chart 
$width = (int)$_POST['width'];
$height = (int)$_POST['height'];

//Background color of chart
$bgcolor = $_POST['bgcolor'];

//String of compressed data
$cdata = $_POST['data'];

//If width or height has not been specified, die
if (!($width or $height)){
	die("Width and height of chart has not been provided.");
}

// Split the data into rows using ; as separator
$rows = explode(";", $cdata);

// Create image
$chart = imagecreatetruecolor($width, $height);

// Detect the background color
if (!$bgcolor){
	$bgcolor = 0xFFFFFF;
}else{
	$bgcolor = intval($bgcolor, 16);
}
// Set the background color
imagefill($chart, 0, 0, $bgcolor);

// Iterate through all the rows
for($i= 0; $i<count($rows); $i++){
	// Parse all the pixels in this row
	$pixels = explode(",", $rows[$i]);
	// Horizontal pixel count
	$ri = 0;
	// Iterate through the pixels
	for($j=0; $j<count($pixels); $j++){
        // Split the pixel into color and repeat value
		$thispix = explode("_", $pixels[$j]);
		// Reference to color
		$c = $thispix[0];
		// Reference to repeat factor
		$r = (int)$thispix[1];		
		//If color is not empty (i.e., not background pixel)		
		if ($c!=""){					
			if (strlen($c)<6){
				//If the hexadecimal code is less than 6 characters, pad with 0
				$c = str_pad($c, 6, '0', STR_PAD_LEFT);
			}
			//Convert value from HEX to RGB
            $rr = hexdec(substr($c, 0, 2));
            $gg = hexdec(substr($c, 2, 2));
            $bb = hexdec(substr($c, 4, 2));
			// Allocate the color
            $ca = imagecolorallocate($chart, $rr, $gg, $bb);			
			//Now, set the pixels
			for ($k=1; $k<=$r; $k++){
				// Set the pixel
				imagesetpixel($chart, $ri, $i, $ca);
				//Increment horizontal row count
				$ri++;						
			}
		}else{
			//Just increment horizontal index
			$ri = $ri + $r;
		}
    }
}

// JPEG Header
header('Content-type:image/jpeg');
// Force download box if not IE
header('Content-Disposition: attachment; filename="FusionCharts.jpg"');

// JPEG Output with 100 quality
imagejpeg($chart, "", 100);

// Destroy chart image
imagedestroy($chart);
?> 