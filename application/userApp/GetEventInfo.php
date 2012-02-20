<?php

class GetEventInfo
{

public function __construct( ){

}

public function getInfo( $request_vars ){
        // Get the event info and returns it in a json format

        Zend_Loader::loadClass( 'Generic' );

	$returnVal = '';

        $generic_db = new Generic();

        $event_id_seq = $request_vars->getParam( 'id' );

        // Get the information and return it to the view
        if( is_numeric( $event_id_seq ) ){
                $results = $generic_db->customQuery( 'event_info', 'SELECT * FROM event_info WHERE event_id_seq = '.$event_id_seq );

		// Adding this in here b/c we dont currently know our app store's url prior to submitting it.  This is a work around
		// we are using to be able to change the url after submiting and posting the url to people's FB wall to download the app
		$data['app_url'] = 'http://itunes.apple.com/us/app/wedvite/id457757757?ls=1&mt=8';

		array_push( $results, $data );

                $returnVal = json_encode( $results );
        }

	return $returnVal;
}
public function edit( $request_vars ){
// Save settings for an event info

	$returnVal = '';

	Zend_Loader::loadClass( 'Generic' );

        $generic_db = new Generic();

        $event_id_seq = $request_vars->getParam( 'id' );	
	$facebook_user_id = $request_vars->getParam( 'user_id' );
	
	// Save the edited information
        if ( is_numeric( $event_id_seq ) && is_numeric( $facebook_user_id ) ) {

                Zend_Loader::loadClass( 'Utilities' );

                $utilitites = new Utilities();

		// Check if this event_id_seq belongs to this user before doing anything
		if( $utilitites->recordBelongsToUser( $facebook_user_id, 'event_info', 'event_id_seq', $event_id_seq ) ){

			$data = array();

			$name = $request_vars->getParam( 'name' );
			$description = $request_vars->getParam( 'description' );
			$location_geo_lat = $request_vars->getParam( 'location_geo_lat' );
			$location_geo_long = $request_vars->getParam( 'location_geo_long' );
			$host = $request_vars->getParam( 'host' );
			$phone = $request_vars->getParam( 'phone' );
			$location_name = $request_vars->getParam( 'location_name' );
			$address = $request_vars->getParam( 'address' );
			$city = $request_vars->getParam( 'city' );
			$state = $request_vars->getParam( 'state' );
			$zip = $request_vars->getParam( 'zip' );
			$when = $request_vars->getParam( 'when' );
			$to = $request_vars->getParam( 'to' );
			$message = $request_vars->getParam( 'message' );

			$coordinates_array = $utilitites->convertToCoordinates( $address . ',' . $city . ',' . $state );

			$data['name'] = $name;
			$data['description'] = $description;
			$data['location'] = $address . ',' . $city . ',' . $state;
			$data['location_geo_lat'] = $coordinates_array[1];
			$data['location_geo_long'] = $coordinates_array[2];
			$data['host'] = $host;
			$data['phone'] = $phone;
			$data['location_name'] = $location_name;
			$data['address'] = $address;
			$data['city'] = $city;
			$data['state'] = $state;
			$data['zip'] = $zip;
			$data['when'] = $when;
			$data['to'] = $to;
			$data['message'] = $message;
			$data['datetime_modified'] = 'NOW()';

			$returnVal = $generic_db->edit( 'event_info', $event_id_seq, $facebook_user_id, $data, 'event_id_seq' );

		}

	}

	return $returnVal;

}


}

?>
