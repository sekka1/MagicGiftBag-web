<?php

class EventsController extends Zend_Controller_Action
{

    private $username;
    private $user_id_seq;

    public function init()
    {
        /* Initialize action controller here */

    }
     public function preDispatch(){

	// Authentication with FB
	Zend_Loader::loadClass('fbAuth');

	$fbAuth = new fbAuth();

	if( !$fbAuth->hasIdentity() ){
		$this->_redirect( '/login?f=' . $this->_request->getRequestUri() );
	} else {
		// User is valid and logged in
		$this->user_id_seq = $fbAuth->getUID();
	}
/*
        // Authentication Piece
        $this->auth = Zend_Auth::getInstance();

        if(!$this->auth->hasIdentity()){
                 $this->_redirect( '/login?f=' . $this->_request->getRequestUri() );
        } else {
                // User is valid and logged in
                $this->username = $this->auth->getIdentity();

		// Get the user_id_seq for this user
		Zend_Loader::loadClass('identity');

        	$identity = new Identity();

		$this->user_id_seq = $identity->getUserIDSeq( $this->username );
        }
*/
     }
/*
    public function __call($method, $args)
    {
        if ('Action' == substr($method, -6)) {
            // If the action method was not found, render the error
            // template
            return $this->render('error');

	    // Forward to another page
	    //return $this->_forward('index');
        }
 
        // all other methods throw an exception
        throw new Exception('Invalid method "'
                            . $method
                            . '" called',
				500);

    }
*/
    public function indexAction()
    {
	//print( $this->client_id_seq . ' - ' . $this->user_id_seq . '<br/>' );
    }
    public function createAction()
    {

	// Add the new event
	if ($this->getRequest()->isPost()) {

		Zend_Loader::loadClass( 'Generic' );

		$generic_db = new Generic();

		$data = array();
	
		$name = $this->_request->getParam( 'name' );
		$description = $this->_request->getParam( 'description' );

		// Add this data into the event table
		$data['user_id_seq'] = $this->user_id_seq;
		$data['name'] = $name;
		$data['description'] = $description;
		$data['datetime_created'] = 'NOW()';
        	$data['datetime_modified'] = 'NOW()';

		$event_id_seq = $generic_db->save( 'event', $data );

		// Add a record for this event into the event_info table
		$data = array();

		$data['user_id_seq'] = $this->user_id_seq;
		$data['event_id_seq'] = $event_id_seq;
		$data['name'] = '';
		$data['description'] = '';
		$data['datetime_created'] = 'NOW()';
                $data['datetime_modified'] = 'NOW()';

		$generic_db->save( 'event_info', $data );

		// Add a record for this event to set it to the default template
		$data = array();

		$data['user_id_seq'] = $this->user_id_seq;
                $data['event_id_seq'] = $event_id_seq;
		$data['template_available_page_id_seq'] = 1;
		$data['datetime_created'] = 'NOW()';
                $data['datetime_modified'] = 'NOW()';

		$generic_db->save( 'template_used', $data );

		// Add a record in for the About Us Icon
		$data = array();
	
		$data['user_id_seq'] = $this->user_id_seq;
		$data['event_id_seq'] = $event_id_seq;
		$data['name'] = 'About Us';
		$data['description'] = 'How we met+-+-+-+-+-+-Answer+-+-+-+-+-+-One weird fact+-+-+-+-+-+-Answer+-+-+-+-+-+-Our favoriate Activity+-+-+-+-+-+-Answer+-+-+-+-+-+-Something we would like to share+-+-+-+-+-+-Answer';
		$data['datetime_created'] = 'NOW()';
                $data['datetime_modified'] = 'NOW()';

		$generic_db->save( 'arbitrary_page', $data );

		// Add a record in for the Family Icon
		$data = array();

		$data['user_id_seq'] = $this->user_id_seq;
                $data['event_id_seq'] = $event_id_seq;
                $data['name'] = 'Family';
		$data['description'] = 'Family 1s Name+-+-+-+-+-+-Family 1s blurb+-+-+-+-+-+-Family 2s Name+-+-+-+-+-+-Family 2s blurb';
                $data['datetime_created'] = 'NOW()';
                $data['datetime_modified'] = 'NOW()';

		$generic_db->save( 'arbitrary_page', $data );

		// Forward user to the events page
		$this->_redirect( '/events/options/id/' . $event_id_seq );
	}

    }
    public function listAction()
    {
	// List all the events that this user has

	Zend_Loader::loadClass( 'Generic' );

	$generic_db = new Generic();

	$results = $generic_db->customQuery( 'event', 'SELECT * FROM event WHERE user_id_seq = ' . $this->user_id_seq );

	$this->view->results = $results;
    }
    public function listplainAction()
    {
        // List all the events that this user has

        Zend_Loader::loadClass( 'Generic' );

        $generic_db = new Generic();

        $results = $generic_db->customQuery( 'event', 'SELECT * FROM event WHERE user_id_seq = ' . $this->user_id_seq );

        $this->view->results = $results;
    }
    public function optionsAction()
    {
        // This view will list all the options that a user can do to their Event


	$event_id_seq = $this->_request->getParam( 'id' );

	Zend_Loader::loadClass( 'Utilities' );

	$utilities = new Utilities();

	// Check if this id belongs to this user
	if( $utilities->recordBelongsToUser( $this->user_id_seq, 'event', 'event_id_seq', $event_id_seq ) ){

		$this->view->doesBelong = true;
		$this->view->event_id_seq = $event_id_seq;
	} else {
		$this->view->doesBelong = false;
	}

    }
    public function optionsplainAction()
    {
        // This view will list all the options that a user can do to their Event

	// This page dont have the skin on it.  Just all html

        $event_id_seq = $this->_request->getParam( 'id' );

        Zend_Loader::loadClass( 'Utilities' );

        $utilities = new Utilities();

        // Check if this id belongs to this user
        if( $utilities->recordBelongsToUser( $this->user_id_seq, 'event', 'event_id_seq', $event_id_seq ) ){

                $this->view->doesBelong = true;
                $this->view->event_id_seq = $event_id_seq;
        } else {
                $this->view->doesBelong = false;
        }

    }
    public function deleteAction()
    {
	// Deletes all record of this event in all the various tables and pictures on the file system

	$picture_path = '/var/www/html/smurf.grep-r.com/auto/public';

        $event_id_seq = $this->_request->getParam( 'id' );

        Zend_Loader::loadClass( 'Utilities' );

        $utilities = new Utilities();

	Zend_Loader::loadClass( 'Generic' );

        $generic_db = new Generic();

        // Check if this id belongs to this user
        if( $utilities->recordBelongsToUser( $this->user_id_seq, 'event', 'event_id_seq', $event_id_seq ) ){

		$generic_db->remove( 'event', $event_id_seq, $this->user_id_seq, 'event_id_seq' );	

		$generic_db->remove( 'event_info', $event_id_seq, $this->user_id_seq, 'event_id_seq' );
	
		$generic_db->remove( 'facebook_invite_list', $event_id_seq, $this->user_id_seq, 'event_id_seq' );

		$generic_db->remove( 'gift_registry', $event_id_seq, $this->user_id_seq, 'event_id_seq' );

		$generic_db->remove( 'template_used', $event_id_seq, $this->user_id_seq, 'event_id_seq' );

		// Get a list of the photos and paths so that the system can go and delete those photos also
		$results = $generic_db->customQuery( 'photos', 'SELECT * FROM photos WHERE event_id_seq = ' . $event_id_seq );

		foreach( $results as $aResult ){

			// Delete Thumb nails
			unlink( $picture_path . $aResult['thumb_url'] ); 
		
			// Delete orig image
			unlink( $picture_path . $aResult['image_url'] );


		}	

		$generic_db->remove( 'photos', $event_id_seq, $this->user_id_seq, 'event_id_seq' );

        } 

	 $this->_redirect( '/events/list' );
    }


}
