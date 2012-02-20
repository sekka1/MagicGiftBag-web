<?php
import_request_variables( "gp", "input_" );

//Test Parameters
$username = urlencode( $input_username );
$password = urlencode( $input_password );
$start_time = urlencode( $input_start_time );
$end_time = urlencode( $input_end_time );
$device = urlencode( $input_device );
$test = urlencode( $input_test );

//Chart Parameters
$data_source = urlencode( $input_data_source );
$caption = $input_caption;
$subcaption = "";
$labelstep = $input_labelstep;

if( $data_source == "traverse" ){

    $url = "http://demonkitty/automation/traverse/getResults.php?username=$username&password=$password&start_time=$start_time&end_time=$end_time&device=$device&test=$test";

    $fp = fopen( $url, "r" );

    $meta_data = stream_get_meta_data( $fp );

    $page_content = stream_get_contents( $fp );

///////////

    $line = split( "::::", $page_content );

    $output = "";

    // Get the First and Last Date Time Stamp for use as the title
    $date_in_result_Start = '';
    $date_in_result_End = '';

    $number_of_results_count = count( $line );

    // Outputting Category Section
    $output .= '<categories>';

    for( $i=0; $i<$number_of_results_count; $i++){

        // Split the lines by the | delimitor
        $temp_split = split( '\|', $line[$i] );

        // Form the date label output as MM/DD/YYYY
        $date_label = substr( $temp_split[6], 4, 2 ) . "/" . substr( $temp_split[6], 6, 2 ) . " T" . substr( $temp_split[6], 8, 2 ) . ":" . substr( $temp_split[6], 10, 2 );

        // Output Category Labels, the X axis
        $output .= "<category label='" . $date_label . "'/>";
    
        // Grabbing the Start and End time of the data Set.  To be used in the Title Caption
        if( $i == 0 ){
            $date_in_result_Start = $temp_split[6];
        }
        if( $i == $number_of_results_count -2 ){
            $date_in_result_End = $temp_split[6];
        }
    }

    $output .= "</categories>";

    // Parse Out date string to be used in the Chart's subcaption
    $start_year = substr( $date_in_result_Start, 0, 4 );
    $start_month = substr( $date_in_result_Start, 4, 2 );
    $start_day = substr( $date_in_result_Start, 6, 2 );
    $start_hour = substr( $date_in_result_Start, 8, 2 );   
    $start_minute = substr( $date_in_result_Start, 10, 2 );
    $end_year = substr( $date_in_result_End, 0, 4 );
    $end_month = substr( $date_in_result_End, 4, 2 );
    $end_day = substr( $date_in_result_End, 6, 2 );
    $end_hour = substr( $date_in_result_End, 8, 2 );
    $end_minute = substr( $date_in_result_End, 10, 2 );

    $caption = "Device: " . $device . " Test: " . $test;

    $subcaption =  "From: " . $start_month . "/" . $start_day . "/" . $start_year . " - " . $start_hour . ":" . $start_minute . " to " . $end_month . "/" . $end_day . "/" . $end_year . " - " . $end_hour . ":" . $end_minute; 

    // Output the Chart Header now that we have the subcation info prepended to the "Catgory" output section 
    $output = "<chart caption='$caption' subcaption='$subcaption' lineThickness='1' showValues='0' formatNumberScale='0' anchorRadius='2' divLineAlpha='20' divLineColor='CC3300' divLineIsDashed='1' showAlternateHGridColor='1' alternateHGridAlpha='5' alternateHGridColor='CC3300' shadowAlpha='40' numvdivlines='5' chartRightMargin='35' bgColor='FFFFFF,CC3300' bgAngle='270' bgAlpha='10,10' labelStep='$labelstep' labelDisplay='ROTATE' slantLabels='1'>" . $output;

    // Outputting DataSet Max Value
    $output .= "<dataset seriesName='Max' color='1D8BD1' anchorBorderColor='1D8BD1' anchorBgColor='1D8BD1'>";
    foreach( $line as $aTest ){

        $temp_split = split( '\|', $aTest );

        $output .= "<set value='" . $temp_split[10] . "'/>";
    }

    $output .= "</dataset>";


    // Outputting DataSet Avg Value
    $output .= "<dataset seriesName='Avg' color='F1683C' anchorBorderColor='F1683C' anchorBgColor='F1683C'>";
    foreach( $line as $aTest ){        

        $temp_split = split( '\|', $aTest );

        $output .= "<set value='" . $temp_split[8] . "'/>";
    }

    $output .= "</dataset>";

    // Outputting DataSet Min Value
    $output .= "<dataset seriesName='Min' color='2AD62A' anchorBorderColor='2AD62A' anchorBgColor='2AD62A'>";
    foreach( $line as $aTest ){        

        $temp_split = split( '\|', $aTest );

        $output .= "<set value='" . $temp_split[9] . "'/>";
    }

    $output .= "</dataset>";


    $output .= "<styles>                
        <definition>
                         
            <style name='CaptionFont' type='font' size='12'/>

        </definition>
        <application>

            <apply toObject='CAPTION' styles='CaptionFont' />
            <apply toObject='SUBCAPTION' styles='CaptionFont' />
        </application>
    </styles>";
    $output .= "</chart>";

///////////

    echo $output;

    fclose( $fp );
}

?>
