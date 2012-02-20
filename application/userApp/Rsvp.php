<?php

class Rsvp
{

public function __construct( ){

}

public function getone( $request_vars ){
	// Given an event id and a guest id (facebook id) it will return the 
	// RSVP info for that one guest

        Zend_Loader::loadClass( 'Generic' );

	$returnVal = '';

        $generic_db = new Generic();

        $event_id_seq = $request_vars->getParam( 'id' );
	$guest_id_seq = $request_vars->getParam( 'guest_id' );

        // Get the information and return it to the view
        if( is_numeric( $event_id_seq ) && is_numeric( $guest_id_seq ) ){
                $results = $generic_db->customQuery( 'rsvp', 'SELECT * FROM rsvp WHERE event_id_seq = '. $event_id_seq . ' AND guest_id_seq = ' . $guest_id_seq );

                $returnVal = json_encode( $results );
        }

	return $returnVal;
}
public function edit( $request_vars ){
// This does the edit and adding a new row if none are in the DB. 

	$returnVal = '';

	Zend_Loader::loadClass( 'Generic' );

        $generic_db = new Generic();

	$event_id_seq = $request_vars->getParam( 'id' );
        $guest_id_seq = $request_vars->getParam( 'guest_id' );
	$rsvp_id_seq = $request_vars->getParam( 'rsvp_id' );

        // Get the information and return it to the view
        if( is_numeric( $event_id_seq ) && is_numeric( $guest_id_seq ) ){

		if( $rsvp_id_seq > 0 ){
		// User has an entry in DB already Edit this one

			$data['response'] = $request_vars->getParam( 'response' );
			$data['guests'] = $request_vars->getParam( 'guests' );
			$data['message'] = $request_vars->getParam( 'message' );
			$data['guest_id_seq'] = $guest_id_seq;

			$returnVal = $generic_db->edit_noauth( 'rsvp', $rsvp_id_seq, $data, 'rsvp_id_seq' );

		} else {
		// Add a new entry for this user
			
			$data['response'] = $request_vars->getParam( 'response' );
                        $data['guests'] = $request_vars->getParam( 'guests' );
                        $data['message'] = $request_vars->getParam( 'message' );
                        $data['guest_id_seq'] = $guest_id_seq;
			$data['event_id_seq'] = $event_id_seq;
			$data['datetime_created'] = 'NOW()';
			$data['datetime_modified'] = 'NOW()';
			
			$returnVal = $generic_db->save( 'rsvp', $data );
		}
	}

	return $returnVal;
}


}

?>
