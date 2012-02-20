<?php

/*
This is an unauthenticated photos action
*/

class PhotosController extends Zend_Controller_Action
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

                    $uploaddir = '/var/www/html/smurf.grep-r.com/auto/public';
                    $img_url = '/pictures/' . $idKey . '-' .basename($_FILES['userfile']['tmp_name'] . '.' . basename($_FILES['userfile']['type'] ) );;

                    $uploadfile = $uploaddir . $img_url;

                    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
                            //      echo "File is valid, and was successfully uploaded.\n";
                            echo "Uploaded File: " . $uploadfile;

                           //
                           // Put this information into the Database
                           //
                           Zend_Loader::loadClass( 'Generic' );

                           $generic_db = new Generic();

                           $data = array();

                           $data['user_id_seq'] = $this->user_id_seq;
                           $data['event_id_seq'] = $idKey;
                           $data['type'] = 'guests';
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

                    } else {
                            //      echo "Possible file upload attack!\n";
                    }
        //    print_r($_FILES);
            }
        }
    }
    public function commentonlyAction(){
        // Displays the facebook comment box with the given params

	$server = $this->_request->getParam( 'location' );
        $image_url = $this->_request->getParam( 'url' );
        $width = $this->_request->getParam( 'width' );

        $this->view->showView = false;

        if( is_numeric( $width ) &&
                $server != '' &&
                $image_url != '' ){

                $this->view->showView = true;

                $this->view->server = $server;
                $this->view->image_url = $image_url;
                $this->view->width = $width;
        }

    }
    public function photoAction(){
        // Displays the photo and facebook comment box with the given params

        $server = $this->_request->getParam( 'location' );
        $image_url = $this->_request->getParam( 'url' );
        $width = $this->_request->getParam( 'width' );

	$this->view->showView = false;

	if( is_numeric( $width ) && 
		$server != '' &&
		$image_url != '' ){

		$this->view->showView = true;

        	$this->view->server = $server;
        	$this->view->image_url = $image_url;
        	$this->view->width = $width;
    	}
    }
    public function fbAction(){
        // Displays the photo and facebook comment box with the given params

        $server = $this->_request->getParam( 'location' );
        $image_url = $this->_request->getParam( 'url' );
        $width = $this->_request->getParam( 'width' );
	$fb_session = $this->_request->getParam( 'session' );

        $this->view->showView = false;

        if( is_numeric( $width ) &&
                $server != '' &&
                $image_url != '' ){

                $this->view->showView = true;

                $this->view->server = $server;
                $this->view->image_url = $image_url;
                $this->view->width = $width;
		$this->view->fb_session = $fb_session;
        }
    }


}
