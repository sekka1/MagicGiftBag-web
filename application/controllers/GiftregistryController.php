<?php

class GiftRegistryController extends Zend_Controller_Action
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

		Zend_Loader::loadClass( 'Utilities' );

		$utilitites = new Utilities();

		$data = array();
	
		$name = $this->_request->getParam( 'name' );
		$description = $this->_request->getParam( 'url' );


		$data['name'] = $name;
		$data['url'] = $url;

		$generic_db->edit( 'gift_registry', $event_id_seq, $this->user_id_seq, $data, 'event_id_seq' );

		$this->_redirect( '/events/options/id/'.$event_id_seq );
	}

	// Get the information and return it to the view
	if( is_numeric( $event_id_seq ) ){
		$results = $generic_db->customQuery( 'event_info', 'SELECT * FROM gift_registry WHERE user_id_seq='.$this->user_id_seq.' AND event_id_seq = '.$event_id_seq );

		$this->view->results = $results;
		$this->view->event_id_seq = $event_id_seq;
	}
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
               	$url = $this->_request->getParam( 'url' );

                // Add this data into the event table
                $data['user_id_seq'] = $this->user_id_seq;
                $data['event_id_seq'] = $event_id_seq;
                $data['name'] = $name;
                $data['url'] = $url;

                $arbitrary_page_id_seq = $generic_db->save( 'gift_registry', $data );

                //$this->_redirect( '/giftregistry/list/id/' . $event_id_seq );
        }

        $this->view->event_id_seq = $event_id_seq;

    }
/*
	public function createAction() {
		if($this->getRequest()->isPost()) { 
			Zend_Loader::loadClass( 'Generic' );
	                $generic_db = new Generic();

       	    		$data = array();

                	$name = $this->_request->getParam( 'name' );
                	$url = $this->_request->getParam( 'url' );
			$event_id_seq = $this->_request->getParam('id');
                	// Add this data into the event table
                	$data['user_id_seq'] = $this->user_id_seq;
			$data['event_id_seq'] = $this->event_id_seq;
                	$data['name'] = $name;
			$data['url'] = $url;
			$event_id_seq = $generic_db->save('gift_registry', $data);

			


		}


	}
*/
    public function getregistryAction()
    {
	// Get the event info and returns it in a json format

	Zend_Loader::loadClass( 'Generic' );

        $generic_db = new Generic();

        $event_id_seq = $this->_request->getParam( 'id' );

	// Get the information and return it to the view
        if( is_numeric( $event_id_seq ) ){
                $results = $generic_db->customQuery( 'event_info', 'SELECT * FROM gift_registry WHERE user_id_seq='.$this->user_id_seq.' AND event_id_seq = '.$event_id_seq );

                $this->view->results = json_encode( $results );
        }

    }
public function listAction()
    {
        // List all the gift_registry that this user has

        Zend_Loader::loadClass( 'Generic' );

        $generic_db = new Generic();

        $results = $generic_db->customQuery( 'event', 'SELECT * FROM gift_registry WHERE user_id_seq = ' . $this->user_id_seq );

        $this->view->results = $results;
    }
}//end of class
