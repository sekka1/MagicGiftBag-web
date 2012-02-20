<?php

class GetGiftRegistry
{

public function __construct(){


}
public function getInfo( $request_vars ){
        // Gets all the gift registry for an event_id_seq

        Zend_Loader::loadClass( 'Generic' );

        $returnVal = '';

        $generic_db = new Generic();

        $event_id_seq = $request_vars->getParam( 'id' );

        // Get the information and return it to the view
        if( is_numeric( $event_id_seq ) ){
                $results = $generic_db->customQuery( 'gift_registry', 'SELECT * FROM gift_registry WHERE event_id_seq = '.$event_id_seq );

                $returnVal = json_encode( $results );
        }

        return $returnVal;
}
public function create( $request_vars ){

	$returnVar = '';

	$event_id_seq = $request_vars->getParam( 'id' );
	$user_id_seq = $request_vars->getParam( 'user_id' );

	if( is_numeric( $event_id_seq ) && is_numeric( $user_id_seq ) ){

		Zend_Loader::loadClass( 'Utilities' );

        	$utilities = new Utilities();

		if( $utilities->recordBelongsToUser( $user_id_seq, 'event', 'event_id_seq', $event_id_seq ) ){

			Zend_Loader::loadClass( 'Generic' );

			$generic_db = new Generic();

			$data = array();

			$name = $request_vars->getParam( 'name' );
			$url = $request_vars->getParam( 'url' );

			// Add this data into the event table
			$data['user_id_seq'] = $user_id_seq;
			$data['event_id_seq'] = $event_id_seq;
			$data['name'] = $name;
			$data['url'] = $url;

			$id_seq = $generic_db->save( 'gift_registry', $data );

			$returnVar = $id_seq;
		}
	}

	return $returnVar;
}
public function edit( $request_vars ){

	$returnVar = '';

        $event_id_seq = $request_vars->getParam( 'id' );
        $user_id_seq = $request_vars->getParam( 'user_id' );
	$gift_registry_id_seq = $request_vars->getParam( 'gift_id' );

        if( is_numeric( $event_id_seq ) && is_numeric( $user_id_seq ) && is_numeric( $gift_registry_id_seq ) ){

                Zend_Loader::loadClass( 'Utilities' );

                $utilities = new Utilities();

                if( $utilities->recordBelongsToUser( $user_id_seq, 'gift_registry', 'gift_registry_id_seq', $gift_registry_id_seq ) ){

                        Zend_Loader::loadClass( 'Generic' );

                        $generic_db = new Generic();
        
                        $data = array();

                        $name = $request_vars->getParam( 'name' );
                        $url = $request_vars->getParam( 'url' );

                        // Add this data into the event table
                        $data['user_id_seq'] = $user_id_seq;
                        $data['event_id_seq'] = $event_id_seq;
                        $data['name'] = $name;
                        $data['url'] = $url;

                        $id_seq = $generic_db->edit( 'gift_registry', $gift_registry_id_seq, $user_id_seq, $data, 'gift_registry_id_seq' );

                        $returnVar = $id_seq;
                }
        }

        return $returnVar;
}
public function delete( $request_vars ){

	$returnVar = '';

        $event_id_seq = $request_vars->getParam( 'id' );
        $user_id_seq = $request_vars->getParam( 'user_id' );
	$gift_registry_id_seq = $request_vars->getParam( 'gift_id' );

        if( is_numeric( $event_id_seq ) && is_numeric( $user_id_seq ) && is_numeric( $gift_registry_id_seq ) ){

                Zend_Loader::loadClass( 'Utilities' );

                $utilities = new Utilities();

                if( $utilities->recordBelongsToUser( $user_id_seq, 'gift_registry', 'gift_registry_id_seq', $gift_registry_id_seq ) ){

			Zend_Loader::loadClass( 'Generic' );

                        $generic_db = new Generic();

			$generic_db->remove( 'gift_registry', $gift_registry_id_seq, $user_id_seq, 'gift_registry_id_seq' );
		}

	}
}

}//end class

?>
