<?php

class EventinfoController extends Zend_Controller_Action
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
    // This function will be called to handle any Actions that is not defined in the
    // controller file.  Its one way to catch all Actions 
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
    public function editAction()
    {
	Zend_Loader::loadClass( 'Generic' );

	$generic_db = new Generic();

	$event_id_seq = $this->_request->getParam( 'id' );

	// Save the edited information
	if ($this->getRequest()->isPost() && is_numeric( $event_id_seq ) ) {


		Zend_Loader::loadClass( 'GetEventInfo' );

		$getEventInfo = new GetEventInfo();

		// Setting the user_id_seq
		$this->_request->setParam( 'user_id', $this->user_id_seq );

		// Saving the information
		$getEventInfo->edit( $this->_request );

		$this->_redirect( '/events/options/id/'.$event_id_seq );
	}

	// Get the information and return it to the view
	if( is_numeric( $event_id_seq ) ){
		$results = $generic_db->customQuery( 'event_info', 'SELECT * FROM event_info WHERE user_id_seq='.$this->user_id_seq.' AND event_id_seq = '.$event_id_seq );

		$this->view->results = $results;
		$this->view->event_id_seq = $event_id_seq;
	}
    }
    public function geteventinfoAction()
    {
	// Get the event info and returns it in a json format

	Zend_Loader::loadClass( 'Generic' );

        $generic_db = new Generic();

        $event_id_seq = $this->_request->getParam( 'id' );

	// Get the information and return it to the view
        if( is_numeric( $event_id_seq ) ){
                $results = $generic_db->customQuery( 'event_info', 'SELECT * FROM event_info WHERE user_id_seq='.$this->user_id_seq.' AND event_id_seq = '.$event_id_seq );

                $this->view->results = json_encode( $results );
        }

    }
}
