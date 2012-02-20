<?php

/*
This is an unauthenticated photos action

This controller is here for the Wedding comments icon on the phone app.  To get a clean page for each wedding the FB comment
needs to be able to actually get that page.  In this controller there is a catch all that will just render any page and 
provides a unique FB comment for every wedding based on the UID of the event.

*/

class CommentsController extends Zend_Controller_Action
{

    private $username;
    private $user_id_seq;

    public function init()
    {
        /* Initialize action controller here */

    }
     public function preDispatch(){

// Not using authentication in this controller b/c the file uploads will not be by authenticated users
// from the mobile app
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
    public function __call($method, $args)
    {
        if ('Action' == substr($method, -6)) {
            // If the action method was not found, render the error
            // template
            return $this->render('comment');

	    // Forward to another page
            //return $this->_forward('index');
        }

        // all other methods throw an exception
        throw new Exception('Invalid method "'
                            . $method
                            . '" called',
                                500);

    }

}
