<?php

class PhotosguestsController extends Zend_Controller_Action
{

    private $username;
    private $user_id_seq;

    public function init()
    {
        /* Initialize action controller here */

    }
     public function preDispatch(){
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
    public function indexAction()
    {
	//print( $this->client_id_seq . ' - ' . $this->user_id_seq . '<br/>' );
    }
    public function uploadformAction(){


    }
    public function uploadAction(){

	if( isset( $_FILES['userfile'] ) ){

	    $idKey = $this->_request->getParam( 'id' );

	    if( isset( $idKey ) && is_numeric( $idKey ) ){

		    $uploaddir = '/tmp/smurf_photos/';
		    $uploadfile = $uploaddir . basename($_FILES['userfile']['tmp_name'] . '.' . basename($_FILES['userfile']['type'] ) );

		    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
			    //	    echo "File is valid, and was successfully uploaded.\n";
			    echo "Uploaded File: " . $uploadfile;

			   //
			   // Put this information into the Database
			   //
			   Zend_Loader::loadClass( 'Generic' );

			   $generic_db = new Generic();

                	   $data = array();

			   //$data['user_id_seq'] = $this->user_id_seq;
			   $data['event_id_seq'] = $idKey;
			   $data['image_uri'] = $uploadfile;
			   $data['being_processed'] = '1';
			   $data['datetime_created'] = 'NOW()';
			   $data['datetime_modified'] = 'NOW()';

			   $guests_photo_album_id_seq = $generic_db->save( 'guests_photo_album', $data );

		    } else {
			    //	    echo "Possible file upload attack!\n";
		    }
	//    print_r($_FILES);
	    }
	}
    }
}
