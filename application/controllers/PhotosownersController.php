<?php

class PhotosownersController extends Zend_Controller_Action
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

	$this->view->id = $this->_request->getParam( 'id' );
    }
    public function uploadAction(){

	if( isset( $_FILES['userfile'] ) ){

	    $idKey = $this->_request->getParam( 'id' );

	    if( isset( $idKey ) && is_numeric( $idKey ) ){

		    $uploaddir = '/var/www/html/smurf.grep-r.com/auto/public';
		    $img_url = '/pictures/' . $idKey . '-' .basename($_FILES['userfile']['tmp_name'] . '.' . basename($_FILES['userfile']['type'] ) );;

		    $uploadfile = $uploaddir . $img_url;

		    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
			    //	    echo "File is valid, and was successfully uploaded.\n";
			    echo "Uploaded File: " . $uploadfile;

			   //
			   // Put this information into the Database
			   //
			   Zend_Loader::loadClass( 'Generic' );

			   $generic_db = new Generic();

                	   $data = array();

			   $data['user_id_seq'] = $this->user_id_seq;
			   $data['event_id_seq'] = $idKey;
			   $data['type'] = 'owners';
			   $data['image_url'] = $img_url;
			   $data['thumb_url'] = '/pictures/place-holder.png';
			   //$data['being_processed'] = '1';
			   $data['server_path'] = $uploaddir;
			   $data['server_location'] = 'wedvite.us';
			   //$data['user_tags'] = '';
			   //$data['being_processed'] = 0;
			   $data['datetime_created'] = 'NOW()';
			   $data['datetime_modified'] = 'NOW()';

			   $owners_photo_album_id_seq = $generic_db->save( 'photos', $data );

			   $this->_redirect( '/events/options/id/' . $idKey );

		    } else {
			    //	    echo "Possible file upload attack!\n";
		    }
	//    print_r($_FILES);
	    }
	}
    }
}
