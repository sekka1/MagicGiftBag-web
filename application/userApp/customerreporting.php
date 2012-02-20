<?php

class customerreporting
{

public function __construct( $client_id_seq, $user_id_seq ){


}
public function probe_type_pie( $request_vars ){
// This function generates the xml data for a breakout of Probes by the type they are

	$caption = $request_vars->getParam( 'caption' );
	$subcaption = $request_vars->getParam( 'subcaption' );
	$xAxisName = $request_vars->getParam( 'xAxisName' );
	$yAxisName = $request_vars->getParam( 'yAxisName' );
	$numberPrefix = $request_vars->getParam( 'numberPrefix' );

	Zend_Loader::loadClass('Generic');

	$generic = new Generic();

	$query = 'SELECT Probes.equipment, COUNT( Probes.equipment ) as count
			FROM Probes
			GROUP BY Probes.equipment';

	$data = $generic->customQuery( 'Probes', $query ); 

	$xml_output = $this->generate_pie_xml( $data, 'equipment', 'count', $caption, $subcaption, $xAxisName, $yAxisName, $numberPrefix );

	return $xml_output;
}
public function software_type_pie( $request_vars ){
// This function generates the xml data for a breakout of software by the type they are

        $caption = $request_vars->getParam( 'caption' );
        $subcaption = $request_vars->getParam( 'subcaption' );
        $xAxisName = $request_vars->getParam( 'xAxisName' );
        $yAxisName = $request_vars->getParam( 'yAxisName' );
        $numberPrefix = $request_vars->getParam( 'numberPrefix' );

        Zend_Loader::loadClass('Generic');

        $generic = new Generic();

        $query = 'SELECT Software.equipment, COUNT( Software.equipment ) as count
                        FROM Software
                        GROUP BY Software.equipment';

        $data = $generic->customQuery( 'Software', $query );
        $xml_output = $this->generate_pie_xml( $data, 'equipment', 'count', $caption, $subcaption, $xAxisName, $yAxisName, 
$numberPrefix );

        return $xml_output;
}
public function generate_pie_xml( $data, $label, $value, $caption, $subcaption, $xAxisName, $yAxisName, $numberPrefix ){
// This funciont generates the xml data that is fed into the charts.  Usually you will have the chart make an
// ajax call to a funciton to return this type of data for a pie chart

// Take in an array in the form of
//  [0] => Array
//        (
//            [equipment] => ASI
//            [count] => 3
//        )

	$output = '<chart caption="'.$caption.'" subcaption="'.$subcaption.'" xAxisName="'.$xAxisName.'" yAxisName="'.$yAxisName.'" numberPrefix="'.$numberPrefix.'">';

	foreach( $data as $aVal ){
	
		$output .= '<set label="'. $aVal[$label] .'" value="'. $aVal[$value].'" />';
	}

	$output .= '</chart>';

	return $output;

}
// End of Class
}

?>
