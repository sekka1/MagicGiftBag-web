<?php

class TemplateController extends Zend_Controller_Action
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
    public function editAction()
    {
        Zend_Loader::loadClass( 'Generic' );

        $generic_db = new Generic();

        $event_id_seq = $this->_request->getParam( 'id' );

        Zend_Loader::loadClass( 'Utilities' );

        $utilities = new Utilities();

        // Add the new event
        if ($this->getRequest()->isPost() && is_numeric( $event_id_seq ) &&
                $utilities->recordBelongsToUser( $this->user_id_seq, 'event', 'event_id_seq', $event_id_seq ) ) {

		$this->_request->setParam( 'user_id', $this->user_id_seq );

		Zend_Loader::loadClass( 'GetTemplate' );

		$getTemplate = new GetTemplate();

		$getTemplate->edit( $this->_request );

		$this->_redirect( '/events/options/id/' . $event_id_seq );
        }

        // Get the information and return it to the view
	// Getting the user's template information and all available templates.  This will be displayed
	// on the screen for the users
        if( is_numeric( $event_id_seq ) ){
                $results = $generic_db->customQuery( 'template_used', 'SELECT * FROM template_used WHERE user_id_seq='.$this->user_id_seq.' AND event_id_seq = ' . $event_id_seq );

                $this->view->results = $results;

		$template_available = $generic_db->customQuery( 'templates_available', 'SELECT * FROM templates_available' );

		$this->view->template_available = $template_available;

                $this->view->event_id_seq = $event_id_seq;
        }
    }
}
