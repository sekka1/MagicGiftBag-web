<?php

class GetTemplate
{

public function __construct( ){

}

public function getTemplateUsed( $request_vars ){
        // Get the event info and returns it in a json format

        Zend_Loader::loadClass( 'Generic' );

	$returnVal = '';

        $generic_db = new Generic();

        $event_id_seq = $request_vars->getParam( 'id' );

        // Get the information and return it to the view
        if( is_numeric( $event_id_seq ) ){
		$results = $generic_db->customQuery( 'template_used', 'SELECT * FROM template_used WHERE event_id_seq = ' . $event_id_seq );

                $returnVal = json_encode( $results );
        }

	return $returnVal;
}

public function getAvailableTemplates( $request_vars ){
        // Get the event info and returns it in a json format

        Zend_Loader::loadClass( 'Generic' );

        $returnVal = '';

        $generic_db = new Generic();

        $event_id_seq = $request_vars->getParam( 'id' );

        // Get the information and return it to the view
        if( is_numeric( $event_id_seq ) ){
		$results = $generic_db->customQuery( 'templates_available', 'SELECT * FROM templates_available' );

                $returnVal = json_encode( $results );
        }

        return $returnVal;
}
public function edit( $request_vars ){

	$returnVar = '';

        Zend_Loader::loadClass( 'Generic' );

        $generic_db = new Generic();

        $event_id_seq = $request_vars->getParam( 'id' );
	$user_id_seq = $request_vars->getParam( 'user_id' );

        Zend_Loader::loadClass( 'Utilities' );

        $utilities = new Utilities();

        // Add the new event
        if (is_numeric( $event_id_seq ) &&
                $utilities->recordBelongsToUser( $user_id_seq, 'event', 'event_id_seq', $event_id_seq ) ) {

                Zend_Loader::loadClass( 'Generic' );

                $generic_db = new Generic();

                $data = array();

                // This is the new template that the user has picked
                $template_available_page_id_seq = $request_vars->getParam( 'used' );

                // Add this data into the event table
                $data['template_available_page_id_seq'] = $template_available_page_id_seq;
                $data['datetime_modified'] = 'NOW()';

                $arbitrary_page_id_seq = $generic_db->edit( 'template_used', $event_id_seq, $user_id_seq, $data, 'event_id_seq' );

		$returnVar = $arbitrary_page_id_seq;
        }

	return $returnVar;
}

}

?>
