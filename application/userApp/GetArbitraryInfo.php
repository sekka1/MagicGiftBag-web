<?php

class GetArbitraryInfo 
{

public function __construct( ){

}

public function getInfo( $request_vars ){
// Get a list of all the Arbitrary pages for a certain event and returns it in a json format

	$returnVal = '';

        $event_id_seq = $request_vars->getParam( 'id' );

	if( is_numeric( $event_id_seq ) ){

		Zend_Loader::loadClass( 'Generic' );

		$generic_db = new Generic();

		$results = $generic_db->customQuery( 'arbitrary_page', 'SELECT * FROM arbitrary_page WHERE event_id_seq = ' . $event_id_seq );

		$data = array();

		// Insert how many characters are in each description field
		foreach( $results as $aResult ){
	
			$temp_split = preg_split( '/\+-\+-\+-\+-\+-\+-/', $aResult['description'] );

			//print_r( $temp_split );

			$count = 1;

			foreach( $temp_split as $anItem ){

				$data[$count] = strlen( $anItem );

				$count++;
			}

			array_push( $results, $data );
		}

		$returnVal = json_encode( $results );

	}

	return $returnVal;
}

public function edit( $request_vars ){
// Save settings for the arbitrary pages

	$returnVal = '';

	$arbitrary_page_id_seq = $request_vars->getParam( 'id' );
        $event_id_seq = $request_vars->getParam( 'event_id' );
        $facebook_user_id = $request_vars->getParam( 'user_id' );

	// Save the edited information
        if ( is_numeric( $event_id_seq ) && is_numeric( $facebook_user_id ) && is_numeric( $arbitrary_page_id_seq ) ){	

		Zend_Loader::loadClass( 'Utilities' );

        	$utilities = new Utilities();

		// Check to see if this record belongs to the user
		if( $utilities->recordBelongsToUser( $facebook_user_id, 'arbitrary_page', 'arbitrary_page_id_seq', $arbitrary_page_id_seq ) ){

			Zend_Loader::loadClass( 'Generic' );

	                $generic_db = new Generic();

        	        $data = array();

                	$name = $request_vars->getParam( 'name' );
                	$type = $request_vars->getParam( 'type' );

                	$description = '';

			// Added functionality to make the arbitrary pages a little bit more structured
			if( $type != '' && $type == 'aboutus' ){
				// The about us has 4 question and answer that should be put together and inserted into the database

				$q1 = $request_vars->getParam( 'q1' );
				$q2 = $request_vars->getParam( 'q2' );
				$q3 = $request_vars->getParam( 'q3' );
				$q4 = $request_vars->getParam( 'q4' );
				$a1 = $request_vars->getParam( 'a1' );
				$a2 = $request_vars->getParam( 'a2' );
				$a3 = $request_vars->getParam( 'a3' );
				$a4 = $request_vars->getParam( 'a4' );

				$description = $q1 . '+-+-+-+-+-+-' . $a1 . '+-+-+-+-+-+-' . $q2 . '+-+-+-+-+-+-' . $a2 . '+-+-+-+-+-+-' . $q3 . '+-+-+-+-+-+-' . $a3 . '+-+-+-+-+-+-' . $q4 . '+-+-+-+-+-+-' . $a4;

			}
			if( $type != '' && $type == 'family' ){

				$q1 = $request_vars->getParam( 'q1' );
				$q2 = $request_vars->getParam( 'q2' );
				$a1 = $request_vars->getParam( 'a1' );
				$a2 = $request_vars->getParam( 'a2' );

				$description = $q1 . '+-+-+-+-+-+-' . $a1 . '+-+-+-+-+-+-' . $q2 . '+-+-+-+-+-+-' . $a2;
			}

			// Add this data into the event table
			//$data['name'] = $name;
			$data['description'] = $description;
			$data['datetime_modified'] = 'NOW()';

			$arbitrary_page_id_seq = $generic_db->edit( 'arbitrary_page', $arbitrary_page_id_seq, $facebook_user_id, $data, 'arbitrary_page_id_seq' );

		}

	}

}

}

?>
