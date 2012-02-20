<?php

class iqalias
{

	var $xl_reader;  // This is an Spreadsheet_Excel_Reader object holding the excel sheet(s)
			// This class is from: http://sourceforge.net/docman/display_doc.php?docid=22092&group_id=99160

	var $all_flows; // A 2D array holding all unique flows and the data
			// 1D - name of the flow
			// 2D - array holding the data

	var $spreadsheet_location = '/tmp/uploads/';

	var $spreadsheet_filename; // filename of the spreadsheet 

	var $formData; // This is the information the user is passing in to map the fields and other attribs

	var $type; // Type of alias the user wants: ip, qam, etc

	var $output_alias_dir = '/tmp/uploads/output/';

public function __construct(){

	include( '/var/www/html/zend1.grep-r.com/auto/application/userApp/Excel/reader.php' );

	// Make the Memory limit bigger for this run so that it can suck in the entire excel sheet
        ini_set("memory_limit","150M");

	$this->xl_reader = new Spreadsheet_Excel_Reader();

	$this->all_flows = array();
	
}
public function get_spreadsheet( $target_path_and_filename  ){
// Opens and reads the excel sheet(s) and puts it into the variable

// Input should be the full path to the file name

        $this->xl_reader->read( $target_path_and_filename );

}
public function parse_users_input_into_flows( $filename, $formData ){
// Given the user's selection of which fields map to which column.  This function will
// go through each row of the excel sheets and put each unique row into the $all_flows 
// array.

	$this->formData = $formData;

	// Get the spreadsheet
	$this->get_spreadsheet( $this->spreadsheet_location . $filename );

	$sheet_count = 0;  // Counter for each sheet loop

	// Loop through the excel sheet and put channel flow data into the $all_flows array	
	foreach( $this->xl_reader->sheets as $aSheet ){

		// If user picked to use this sheet then process it
		if( $this->use_sheet( $sheet_count, $formData ) ){

			//Loop through the sheet's rows
			for( $r = 1; ($r <= $aSheet['numRows']); $r++){

				// Check if this row exist in the array
                		if( isset( $aSheet['cells'][$r] ) ){

					// Check if this row has been marked as a header row by user
					if( ! $this->a_header_row( $sheet_count, $r, $formData ) ){


						// Check if this row has all the fields per the user map 	
						if( $this->row_is_ok_per_usermap( $sheet_count, $aSheet['numCols'], $aSheet['cells'][$r], $formData ) ){

							// Concat the field value into the name the user wants
							$flow_name = $this->get_flow_name_from_row_data( $sheet_count, $aSheet['cells'][$r], $formData );

							// Pus this flow data into the $all_flows array
							$this->push_data_to_all_flows( $sheet_count, $flow_name, $aSheet['cells'][$r] );

						}

					}

				}
			}
			
		}		

		//print_r( $this->all_flows );

		$sheet_count++;
	}

	// Will output the user picked choices
	//print_r( $formData );
	//print_r( $this->all_flows );
}

public function output_flows_to_alias_file(){
// Output the $all_flows array out to a file or the screen

	//print_r( $this->formData );

	//print_r( $this->all_flows );

	$alias_output = $this->output_alias_header();

	foreach( $this->all_flows as $flow_name => $flow_val ){

		$alias_output .= $this->output_alias_flow_line( $flow_name, $this->formData, $flow_val );	

		$alias_output .= $this->output_alias_flow_channels_line( $flow_name, $this->formData, $flow_val );
	}	

	return $alias_output;
}

private function output_alias_flow_line( $flow_name, $formData, $array_of_data ){
// There is only one of these line for each flow

//print_r( $array_of_data );
//print_r( $formData );

	// Check if the user wants to use a source IP and source port, if not set to "no"
	if( $formData['usermapfield_sheet-'.$array_of_data['sheet_num'].'_usesource'] != "no" ){
		$sourceIP = $array_of_data['data'][0][$this->get_user_field_mapped_column_number( $array_of_data['sheet_num'], 'sourceIP', $formData )];
	} else {
		$sourceIP = "no";
	}

	$flow_line = '';

	if( $this->type == 'ip' ){
	// For IP Alias output
		$destIP = $array_of_data['data'][0][$this->get_user_field_mapped_column_number( $array_of_data['sheet_num'], 'destIP', $formData )];
		$destPort = $array_of_data['data'][0][$this->get_user_field_mapped_column_number( $array_of_data['sheet_num'], 'destPort', $formData )];
	
		$flow_line = "Video\t".$flow_name."\t".$sourceIP."\t".$destIP."\tNo\t".$destPort."\tOn\tStandard Transport at IP\tNo\tprogramDefault\t255.255.255.255\t255.255.255.255\tYes\tNo\tNo\tNo\tNo\tNo\tNo\tNo\tNo\t1\t3\n";
	} elseif( $this->type == 'qam' ){
	// For QAM Alias Output

		$eia = $array_of_data['data'][0][$this->get_user_field_mapped_column_number( $array_of_data['sheet_num'], 'eia', $formData )];

		$flow_line = "RF\t".$flow_name."\t0.0.0.".$eia."\t0.0.0.".$eia."\tNo\tNo\tOff\ttunerDefault\tNo\tprogramDefault\t255.255.255.255\t255.255.255.255\tYes\tNo\tNo\tNo\tNo\tNo\tNo\tNo\t".$eia."\n";
	}

	return $flow_line;
}

private function output_alias_flow_channels_line( $flow_name, $formData, $array_of_data ){
// There can be one or more of each channel line for a flow line
// For a single transport stream flow, there will be only one entry.  But for a multi transport
// stream flow, one dest multicast flow can have multiple channels in it

	$flow_channel_count = count( $array_of_data['data'] ); // THe number of channels associated with this flow

	$channel_flows_line = "";

	for( $i=0; $i < $flow_channel_count; $i++ ){

		$channelNumber = $this->get_user_field_mapped_column_number( $array_of_data['sheet_num'], 'channelNumber', $formData );

		$channelName = $this->get_user_field_mapped_column_number( $array_of_data['sheet_num'], 'channelName', $formData );

		$channelAliasNumber = $this->get_user_field_mapped_column_number( $array_of_data['sheet_num'], 'channelAliasNumber', $formData );

		// Only use the number if it found the mapping.  If it didnt find the mapping it probably means the 
		// user didnt map this field to anything
		if( $channelNumber >= 0 )
			$channelNumber = $array_of_data['data'][$i][$channelNumber];

		if( $channelName >= 0 )
			$channelName = $array_of_data['data'][$i][$channelName];

		if( $channelAliasNumber >= 0 ) 	
			$channelAliasNumber = $array_of_data['data'][$i][$channelAliasNumber];

		// Setup flow to a user specified alarm template if a match is found
		$program_alarm_template = $this->program_alarm_template_replace( $channelName );

		if( $this->type == 'ip' ){

			// Set the default program alarm if the replace filter didnt change it
			if( $program_alarm_template == '' )
				$program_alarm_template = 'Standard Video Service at IP';

			$channel_flows_line .= "Video\t".$flow_name."\tNo\tNo\tNo\tNo\tNo\t".$program_alarm_template."\tNo\tprogramVideo\tNo\tNo\tNo\tNo\t".$channelNumber."\t".$channelName."\t".$channelAliasNumber."\tDeviceRef\t0_0.0:0.0\tprogramDefault\tNo\t0\n";
		} elseif ( $this->type == 'qam' ){

			// Set the default program alarm if the replace filter didnt change it
                        if( $program_alarm_template == '' )
                                $program_alarm_template = 'programDefault';

			$channel_flows_line .= "Video\t".$flow_name."\tNo\tNo\tNo\tNo\tNo\tNo\tNo\t".$program_alarm_template."\tNo\tNo\tNo\tNo\t".$channelNumber."\t".$channelName."\t".$channelAliasNumber."\t\t0_0.0:0.0\t\tNo\n";
		}
	}

	return $channel_flows_line;
}

private function get_user_field_mapped_column_number( $sheet_num, $field_name, $formData ){
// This function returns the column number given a sheet number, a field name and the mapped fields
// the users has choosen. Given a field name it will find which field the user has mapped it to the column
// number

	$column_num = -1;

	$number_of_fields = 300; // This is the max number of columns that a user can use in the spreadsheet

	for( $i=1; $i <= $number_of_fields; $i++ ){

		if( isset( $formData['usermapfield_sheet-'.$sheet_num.'_field-'.$i] ) ){

			if( $formData['usermapfield_sheet-'.$sheet_num.'_field-'.$i] == $field_name ){

				$column_num = $i;
			}
		}
	}

	return $column_num;
}

private function output_alias_header(){

	if( $this->type == 'ip' ){
		$header = "version(J)\tname(1)\tsourceIp(2)\tdestIp(3)\tsrcPort(4)\tdestPort(5)\tigmpStatus(6)\talarmTemplate(7)\tVLANTCI(8)\tpayloadTemplate(9)\tsrcIpMask(10\tdestIpMask(11)\tBroadcast(12)\tMACforARPReply(13)\tchannelNumber(15)\tchannelName(14)\tchannelAliasNumber(18)\tdeviceRef(22)\tchannelOffPeriod(32)\tchannelOffAirTemplate(33)\tRTP SSRC(35)\tIGMP\tSets(31)\tPorts(34)\n";
	} elseif( $this->type == 'qam' ){
		$header = "version(J)\tname(1)\tsourceIp(2)\tdestIp(3)\tsrcPort(4)\tdestPort(5)\tigmpStatus(6)\talarmTemplate(7)\tVLANTCI(8)\tpayloadTemplate(9)\tsrcIpMask(10)\tdestIpMask(11)\tBroadcast(12)\tMACforARPReply(13)\tchannelNumber(15)\tchannelName(14)\tchannelAliasNumber(18)\tdeviceRef(22)\tchannelOffPeriod(32)\tchannelOffAirTemplate(33)\tchannel(21)\n";
		$header .= "Video\tNone	No\tNo\tNo\tNo\tOff\ttunerDefault\tNo\tprogramDefault\t255.255.255.255\t255.255.255.255\tNo\tNo\tNo\tNo\tNo\tNo\tNo\tNo\tNo\n";
	}

	return $header;
}

private function push_data_to_all_flows( $sheet_num, $flown_name, $array_of_data ){
// Puts the data given to it into the $all_flows array.  It checks to see if the
// name of the flow is already there.  If it is not it creates that array else
// it just pushes the data into it.

	if( ! isset( $this->all_flows[$flown_name] ) ){

		$this->all_flows[$flown_name] = array();
		$this->all_flows[$flown_name]['data'] = array();

		$this->all_flows[$flown_name]['sheet_num'] = $sheet_num;

		array_push( $this->all_flows[$flown_name]['data'], $array_of_data ); 

	} else {

		array_push( $this->all_flows[$flown_name]['data'], $array_of_data );
	}
}

private function get_flow_name_from_row_data( $sheet_num, $array_of_data, $formData ){
// Based on the user input of what the field name should be.  This goes into the $formData
// to see which field the user picked that they wanted to name and then it goes into the
// $array_of_data which is ususually the row data to extract the values to create the name

	$flow_name = '';

	$max_number_of_name_concatenations = 30;

	for( $i=1; $i < $max_number_of_name_concatenations; $i++){

		if( isset( $formData['flowname_'.$i] ) ){

			if( $formData['flowname_'.$i] != "not used" ){

			if( $formData['flowname_'.$i] == 'sheet' ){

				$flow_name .= $this->xl_reader->boundsheets[$sheet_num]['name'];
			} else {

				$flow_name .= $array_of_data[$this->map_column_letter_to_seq_number( $formData['flowname_'.$i] )];
			}

			$flow_name .= $formData['flow_cat_'.$i];

			}

		}

	}

	return $flow_name;
}

private function map_column_letter_to_seq_number( $column_letter ){
// returns the map of a letter to a number
// example a = 1, b = 2, aa = 27

	$count = 1;

	$mapped_number = 0;

	foreach( range( 'a', 'z' ) as $letter ){

		if( $letter == $column_letter ){

			$mapped_number = $count;
		}
		
		$count++;
	}

	return $mapped_number;

}

private function row_is_ok_per_usermap( $sheet_num, $num_of_columns, $array_of_data, $formData ){
// Checks the $array_of_data (a 1D array) to see if all the fields the user has
// mapped is in there and set.  If any field is not there or not set then it will
// return false

// Also checks if the data type for the field is correct also

	$total_num_of_fields = count( $array_of_data );	

	$is_field_missing = false;

	for( $i=1; $i <= $num_of_columns; $i++){

		if( $formData['usermapfield_sheet-' . $sheet_num . '_field-' . $i] != 'not used' ){

			if( ! isset( $array_of_data[$i] ) 
				|| ( $array_of_data[$i] == '' ) 
				|| ! $this->check_data_type_is_ok( $formData['usermapfield_sheet-' . $sheet_num . '_field-' . $i], $array_of_data[$i] ) 
				){

				$is_field_missing = true;
			}
		}
	}

	return ! $is_field_missing;

}

private function a_header_row( $sheet_num, $row_num, $formData ){
// Determins if the user selected this sheet/row as a header or not from the $formData input

	$header_row_flag = 'header_sheet-' . $sheet_num . '_r-' . $row_num;

	$is_header_row = false; 

	if( isset( $formData[$header_row_flag] ) ){

		if( $formData[$header_row_flag] == 'on' ){

			$is_header_row = true;
		}
	}

	return $is_header_row;
}

private function use_sheet( $sheet_number, $formData ){

	// Var holding the "use sheet flag
	$use_sheet_flag = 'use_sheet_' . $sheet_number;

	$use_sheet = false;

	if( $formData[$use_sheet_flag] == 'true' ){

		$use_sheet = true;
	}

	return $use_sheet;
}

private function check_data_type_is_ok( $data_type, $value ){
// Check if the given input data and the type is correct
// For example ip address should be in a n.n.n.n format

// Returns true if its is ok, false if it is not

	$is_ok = false;

//echo "data_type: " . $data_type . " | value: " . $value;

	if( $data_type == 'sourceIP' || $data_type == 'destIP' ){

		if( eregi( "^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$", $value ) || $value == 'no' ){

			$is_ok = true;
		}
	}
	if( $data_type == 'destPort' ){

		if( eregi( "^[0-9]{1,5}$", $value ) || $value == 'no' ){

			$is_ok = true;
		}
	}
	else{
	// Unknow datatype, let it pass

		$is_ok = true;
	}

//echo " | <b>" . $is_ok . "</b><br/>";
	return $is_ok;
}
public function transport_alarm_template_replace( $channelName ){
// Uses the search and replace input provided by the user to set the alarm templates for individual flows

	$max_num_of_replace = 10;

	$set_alarm_templates_to = '';

	for( $i=0; $i<$max_num_of_replace; $i++ ){

		if( isset( $this->formData['alarmtemplate_search_'.$i] ) ){

			if( eregi( $this->formData['alarmtemplate_search_'.$i], $channelName ) ){
			// Channel name matched.

				$set_alarm_templates_to = $this->formData['alarmtemplate_set_'.$i];
			}
		}
	}
	return $set_alarm_templates_to;
}
public function program_alarm_template_replace( $channelName ){
// Uses the search and replace input provided by the user to set the alarm templates for individual flows

        $max_num_of_replace = 10;

        $set_alarm_templates_to = '';

        for( $i=0; $i<$max_num_of_replace; $i++ ){

                if( isset( $this->formData['program_alarmtemplate_search_'.$i] ) && 
			$this->formData['program_alarmtemplate_search_'.$i] != '' ){

                        if( eregi( $this->formData['program_alarmtemplate_search_'.$i], $channelName ) ){
                        // Channel name matched.

                                $set_alarm_templates_to = $this->formData['program_alarmtemplate_set_'.$i];
                        }
                }
        }

        return $set_alarm_templates_to;
}
public function output_alias( $alias, $output_to, $filename ){

	if( $output_to  == 'raw' ){
		echo $alias;
	} elseif(  $output_to == 'file' ){

		$file_name = $this->output_alias_dir.$filename;

		if( !file_exists( $file_name ) ){

			file_put_contents( $file_name.'.txt', $alias );
		} else {
		// Append something unquie to the file name and reoutput

			$file_name = $this->get_unique_filename( $file_name );
	
			file_put_contents( $file_name.'.txt', $alias );

			$this->output_file_to_browser( $file_name.'.txt' );
		}
	}
}
public function output_file_to_browser( $file_name ){
// $file_name is the full path to the file

	$dir_files = scandir( $this->output_alias_dir );

	set_time_limit(0);

	$file_path = $file_name;

	$this->output_file($file_path, $file_name, 'text/plain');

}
function output_file($file, $name, $mime_type='')
{
 /*
 This function takes a path to a file to output ($file), 
 the filename that the browser will see ($name) and 
 the MIME type of the file ($mime_type, optional).
 
 If you want to do something on download abort/finish,
 register_shutdown_function('function_name');
 */
 if(!is_readable($file)) die('File not found or inaccessible!');
 
 $size = filesize($file);
 $name = rawurldecode($name);
 
 /* Figure out the MIME type (if not specified) */
 $known_mime_types=array(
 	"pdf" => "application/pdf",
 	"txt" => "text/plain",
 	"html" => "text/html",
 	"htm" => "text/html",
	"exe" => "application/octet-stream",
	"zip" => "application/zip",
	"doc" => "application/msword",
	"xls" => "application/vnd.ms-excel",
	"ppt" => "application/vnd.ms-powerpoint",
	"gif" => "image/gif",
	"png" => "image/png",
	"jpeg"=> "image/jpg",
	"jpg" =>  "image/jpg",
	"php" => "text/plain"
 );
 
 if($mime_type==''){
	 $file_extension = strtolower(substr(strrchr($file,"."),1));
	 if(array_key_exists($file_extension, $known_mime_types)){
		$mime_type=$known_mime_types[$file_extension];
	 } else {
		$mime_type="application/force-download";
	 };
 };
 
 @ob_end_clean(); //turn off output buffering to decrease cpu usage
 
 // required for IE, otherwise Content-Disposition may be ignored
 if(ini_get('zlib.output_compression'))
  ini_set('zlib.output_compression', 'Off');
 
 header('Content-Type: ' . $mime_type);
 header('Content-Disposition: attachment; filename="'.$name.'"');
 header("Content-Transfer-Encoding: binary");
 header('Accept-Ranges: bytes');
 
 /* The three lines below basically make the 
    download non-cacheable */
 header("Cache-control: private");
 header('Pragma: private');
 header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
 
 // multipart-download and download resuming support
 if(isset($_SERVER['HTTP_RANGE']))
 {
	list($a, $range) = explode("=",$_SERVER['HTTP_RANGE'],2);
	list($range) = explode(",",$range,2);
	list($range, $range_end) = explode("-", $range);
	$range=intval($range);
	if(!$range_end) {
		$range_end=$size-1;
	} else {
		$range_end=intval($range_end);
	}
 
	$new_length = $range_end-$range+1;
	header("HTTP/1.1 206 Partial Content");
	header("Content-Length: $new_length");
	header("Content-Range: bytes $range-$range_end/$size");
 } else {
	$new_length=$size;
	header("Content-Length: ".$size);
 }
 
 /* output the file itself */
 $chunksize = 1*(1024*1024); //you may want to change this
 $bytes_send = 0;
 if ($file = fopen($file, 'r'))
 {
	if(isset($_SERVER['HTTP_RANGE']))
	fseek($file, $range);
 
	while(!feof($file) && 
		(!connection_aborted()) && 
		($bytes_send<$new_length)
	      )
	{
		$buffer = fread($file, $chunksize);
		print($buffer); //echo($buffer); // is also possible
		flush();
		$bytes_send += strlen($buffer);
	}
 fclose($file);
 } else die('Error - can not open file.');
 
die();
}	

public function get_unique_filename( $file_name ){
// $file_name should be a full path to a file
// Checks if the file exist and if it does it appends _# to it and check
// again until it doesnt exist

	$number = 1;

	$does_exist = true;

	$new_file_name = $file_name;

	while( $does_exist ){

		$temp_filename = $file_name . '_' . $number;

		if( file_exists( $temp_filename ) ){

			$number++;
		} else {
		// Found file that does not exist
	
			$new_file_name = $temp_filename;

			$does_exist = false;
		}
	}

	return $new_file_name;

}

}
/*
Format of: $formData
Array
(
    [submit] => submit
    [filename] => 1650651727-Cham-Car-Ft-Ldn-Green-Waynes_CA_WS_05-03-09.xls
    [use_sheet_0] => true
    [flowname_1] => sheet
    [flowname_2] => f
    [usermapfield_sheet-0_field-1] => not used
    [usermapfield_sheet-0_field-2] => channelName
    [usermapfield_sheet-0_field-3] => channelAliasNumber
    [usermapfield_sheet-0_field-4] => not used
    [usermapfield_sheet-0_field-5] => not used
    [usermapfield_sheet-0_field-6] => sourceIP
    [usermapfield_sheet-0_field-7] => destPort
    [usermapfield_sheet-0_field-8] => destIP
    [usermapfield_sheet-0_field-9] => channelNumber
    [usermapfield_sheet-0_field-10] => not used
    [usermapfield_sheet-0_field-11] => not used
    [usermapfield_sheet-0_field-12] => not used
    [usermapfield_sheet-0_field-13] => not used
    [usermapfield_sheet-0_field-14] => not used
    [usermapfield_sheet-0_field-15] => not used
    [header_sheet-0_r-1] => on
    [header_sheet-0_r-2] => on
    [header_sheet-0_r-3] => on
    [header_sheet-0_r-4] => on
    [header_sheet-0_r-5] => on
    [use_sheet_1] => false
    [usermapfield_sheet-1_field-1] => not used
    [usermapfield_sheet-1_field-2] => not used
    [usermapfield_sheet-1_field-3] => not used
    [usermapfield_sheet-1_field-4] => not used
    [usermapfield_sheet-1_field-5] => not used
    [usermapfield_sheet-1_field-6] => not used
    [usermapfield_sheet-1_field-7] => not used
    [usermapfield_sheet-1_field-8] => not used
    [usermapfield_sheet-1_field-9] => not used
    [usermapfield_sheet-1_field-10] => not used
    [usermapfield_sheet-1_field-11] => not used
    [usermapfield_sheet-1_field-12] => not used
    [usermapfield_sheet-1_field-13] => not used
    [usermapfield_sheet-1_field-14] => not used
    [usermapfield_sheet-1_field-15] => not used
}

Format of: $array_of_data
Array
(
    [1] => ShopNBC
    [2] => SNBC
    [3] => 97
    [4] => ADS_DM 01
    [5] => sem04dCAR
    [6] => 69.240.5.226
    [7] => 32501
    [8] => 239.28.0.225
    [9] => 9
    [10] => GigE-1
    [11] => QAM 1     (1A)
    [12] => 9
    [13] => 77
    [14] => 543.00
)


*/

?>
