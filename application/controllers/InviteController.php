<?php

class InviteController extends Zend_Controller_Action
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

    }
    public function facebookAction()
    {

	$event_id_seq = $this->_request->getParam( 'id' );

	$this->view->event_id_seq = $event_id_seq;

	Zend_Loader::loadClass( 'Generic' );

	$generic_db = new Generic();

	// Add the FB users that the user picked to invite
	if ($this->getRequest()->isPost()) {

		$invite_list = $this->_request->getParam( 'friends' );

		$results = array();

		if( is_numeric( $event_id_seq ) ){

			// Delete all invited guess entries before inserting the new list in.  This way there wont be any duplicates
			$generic_db->remove( 'facebook_invite_list', $event_id_seq, $this->user_id_seq, 'event_id_seq' );

			foreach( $invite_list as $anEntry ){

				$data = array();

				// Add this data into the event table
				$data['user_id_seq'] = $this->user_id_seq;
				$data['event_id_seq'] = $event_id_seq;
				$data['datetime_created'] = 'NOW()';
				$data['datetime_modified'] = 'NOW()';	
				$data['invited_user_fb_uid'] = $anEntry;

				$fb_invite_list_id_seq = $generic_db->save( 'facebook_invite_list', $data );

			}
		}

		$this->_redirect( '/events/options/id/' . $event_id_seq );
	} 

	if( is_numeric( $event_id_seq ) ){

		// Get the list of invited guess to this event
                $results = $generic_db->customQuery( 'facebook_invite_list', 'SELECT * FROM facebook_invite_list WHERE user_id_seq = '.$this->user_id_seq.' AND event_id_seq = '.$event_id_seq );

		$this->view->results = $results;
		
	}

    }
}
