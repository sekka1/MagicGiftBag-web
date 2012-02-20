<?php

class ArbitrarypageController extends Zend_Controller_Action
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

	$event_id_seq = $this->_request->getParam( 'id' );

	Zend_Loader::loadClass( 'Utilities' );

        $utilities = new Utilities();

	// Add the new event
	if ($this->getRequest()->isPost() && is_numeric( $event_id_seq ) &&
		$utilities->recordBelongsToUser( $this->user_id_seq, 'event', 'event_id_seq', $event_id_seq ) ) {

		Zend_Loader::loadClass( 'Generic' );

		$generic_db = new Generic();

		$data = array();
	
		$name = $this->_request->getParam( 'name' );
		$description = $this->_request->getParam( 'description' );

		// Add this data into the event table
		$data['user_id_seq'] = $this->user_id_seq;
		$data['event_id_seq'] = $event_id_seq;
		$data['name'] = $name;
		$data['description'] = $description;
		$data['datetime_created'] = 'NOW()';
        	$data['datetime_modified'] = 'NOW()';

		$arbitrary_page_id_seq = $generic_db->save( 'arbitrary_page', $data );

		$this->_redirect( '/arbitrarypage/list/id/' . $event_id_seq );
	}

	$this->view->event_id_seq = $event_id_seq;

    }
    public function editAction()
    {

        $arbitrary_page_id_seq = $this->_request->getParam( 'id' );
	$event_id_seq = $this->_request->getParam( 'event_id' );

	Zend_Loader::loadClass( 'GetArbitraryInfo' );

	$getArbitraryInfo = new GetArbitraryInfo();

	Zend_Loader::loadClass( 'Generic' );

        $generic_db = new Generic();

	if( $this->getRequest()->isPost() && is_numeric( $arbitrary_page_id_seq ) && is_numeric( $event_id_seq ) ){

		// Setting the user_id_seq
                $this->_request->setParam( 'user_id', $this->user_id_seq );

		$getArbitraryInfo->edit( $this->_request );

		$this->_redirect( '/events/options/id/' . $event_id_seq );
	}

	// Get the information and return it to the view
        if( is_numeric( $arbitrary_page_id_seq ) ){
                $results = $generic_db->customQuery( 'arbitrary_page', 'SELECT * FROM arbitrary_page WHERE user_id_seq='.$this->user_id_seq.' AND arbitrary_page_id_seq = ' . $arbitrary_page_id_seq );

                $this->view->results = $results;
                $this->view->arbitrary_page_id_seq = $arbitrary_page_id_seq;
        }


    }
    public function listAction()
    {
	// List all the Arbitrary pages for given event_id_seq

	Zend_Loader::loadClass( 'Utilities' );

	$utilities = new Utilities();

	$event_id_seq = $this->_request->getParam( 'id' );

	if( $utilities->recordBelongsToUser( $this->user_id_seq, 'event', 'event_id_seq', $event_id_seq ) ){
	// Make sure the id passed in from the user belongs to this user

		if( is_numeric( $event_id_seq ) ){

			Zend_Loader::loadClass( 'Generic' );

			$generic_db = new Generic();

			$results = $generic_db->customQuery( 'arbitrary_page', 'SELECT * FROM arbitrary_page WHERE user_id_seq = ' . $this->user_id_seq . ' AND event_id_seq = ' . $event_id_seq );

			$this->view->results = $results;
			$this->view->event_id_seq = $event_id_seq;

		}
	}
    }
    public function getarbitrarypagesAction()
    {
	// Get a list of all the Arbitrary pages for a certain event and returns it in a json format

        Zend_Loader::loadClass( 'Utilities' );

        $utilities = new Utilities();

        $event_id_seq = $this->_request->getParam( 'id' );

        if( $utilities->recordBelongsToUser( $this->user_id_seq, 'event', 'event_id_seq', $event_id_seq ) ){
        // Make sure the id passed in from the user belongs to this user

                if( is_numeric( $event_id_seq ) ){

                        Zend_Loader::loadClass( 'Generic' );

                        $generic_db = new Generic();

                        $results = $generic_db->customQuery( 'arbitrary_page', 'SELECT * FROM arbitrary_page WHERE user_id_seq = ' . $this->user_id_seq . ' AND event_id_seq = ' . $event_id_seq );

                        $this->view->results = json_encode( $results );

                }
        }
    }
}
