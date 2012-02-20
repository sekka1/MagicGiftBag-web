<?php
//////////////////////////////////////////////////////
// This controller is here to facilitate the tuning of the Gift Engine
//
// With function such as add and delete stuff from the manipulation tables
// Hot products, persona types, etc
/////////////////////////////////////////////////////


class TuningController extends Zend_Controller_Action
{

    private $username;
    private $user_id_seq;
    private $fbAuth;
    private $personaAdmin;

    public function init()
    {
        /* Initialize action controller here */

    }
     public function preDispatch(){

        // Authentication with FB
        Zend_Loader::loadClass('fbAuth');

        $this->fbAuth = new fbAuth();

        if( !$this->fbAuth->hasIdentity() ){
                $this->_redirect( '/login?f=' . $this->_request->getRequestUri() );
        } else {
                // User is valid and logged in
                $this->user_id_seq = $this->fbAuth->getUID();

                // Only allow these FB users to this controller
                $user_profile = $this->fbAuth->facebook->api('/me');

                $allowedUIDs = array( '566708666', '3420169', '30901818', '1133764889', '32701479' );
                //$allowedUIDs = array( 'zzzz', 'zz' );

                if( ! in_array( $user_profile['id'], $allowedUIDs ) )
                    $this->_redirect( '/' );

        }

        Zend_Loader::loadClass('PersonaAdmin');

        $this->personaAdmin = new PersonaAdmin();
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
        // Pass the facebook object for the view to use
        $this->view->facebook = $this->fbAuth->facebook;
    }
    public function augmentsearchAction()
    {
        // Get the augmented search list
        Zend_Loader::loadClass('EngineTuning'); 

        $engineTuning = new EngineTuning();

        $webAction = $this->_request->getParam( 'webAction' );

        // Add New Item
        if( $webAction != '' ){

            if( $webAction == 'add_searchTerm' ){

                $orig_text = $this->_request->getParam( 'orig_text' );
                $change_to = $this->_request->getParam( 'change_to' );

                $engineTuning->addWordManip( 'augmentSearchTerm', $orig_text, $change_to );
            }
            if( $webAction == 'delete' ){

                $spot_to_delete = $this->_request->getParam( 'spot' );

                $engineTuning->deleteWordManip( 'augmentSearchTerm', $spot_to_delete ); 
            }

            $this->_helper->redirector('augmentsearch');
        } 

        $listContent = json_decode( $engineTuning->getFile( 'augmentSearchTerm' ), 1 );

        $this->view->listContent = $listContent; 

    }
    public function jsonconverterAction(){

        $list = $this->_request->getParam( 'list' );

        $listArray = array();

        if( $list != '' ){

            $split_list = preg_split( '/,/', $list );
    
            foreach( $split_list as $anItem ){

                array_push( $listArray, trim( $anItem ) );
            }

            //print_r( $listArray );

            $this->view->list = json_encode( $listArray );
        }
    }
    public function personatypeAction(){
        //List all the Persona Types

        $results = $this->personaAdmin->personatTypeGetAll();

        $this->view->results = $results;
    }
    public function personatypeaddAction(){

        $this->personaAdmin->personaTypeAdd( $this->_request ); 

        $this->_helper->redirector('personatype');
    }
    public function personatypedeleteAction(){

        $this->personaAdmin->personaTypeDelete( $this->_request );

        $this->_helper->redirector('personatype');
    }
    public function wordsAction(){

        $this->view->results = $this->personaAdmin->wordGetAll( $this->_request );
        $this->view->persona_type_id_seq = $this->_request->getParam( 'persona_type_id_seq' );
        $this->view->name = $this->_request->getParam( 'name' );
    }
    public function wordaddAction(){

        $this->personaAdmin->wordAdd( $this->_request );

        header('Location: http://www.magicgiftbag.com/tuning/words/persona_type_id_seq/'. $this->_request->getParam( 'persona_type_id_seq' ));
    }
    public function worddeleteAction(){

        $this->personaAdmin->wordDelete( $this->_request );

        header('Location: http://www.magicgiftbag.com/tuning/words/persona_type_id_seq/'. $this->_request->getParam( 'persona_type_id_seq' ));
    }
    public function productsAction(){

        $this->view->results = $this->personaAdmin->productGetAll( $this->_request );
        $this->view->persona_type_id_seq = $this->_request->getParam( 'persona_type_id_seq' );
        $this->view->name = $this->_request->getParam( 'name' );
    }
    public function productaddAction(){

        $this->personaAdmin->productAdd( $this->_request );

        header('Location: http://www.magicgiftbag.com/tuning/products/persona_type_id_seq/'. $this->_request->getParam( 'persona_type_id_seq' ));
    }
    public function productdeleteAction(){

        $this->personaAdmin->productDelete( $this->_request );

        header('Location: http://www.magicgiftbag.com/tuning/products/persona_type_id_seq/'. $this->_request->getParam( 'persona_type_id_seq' ));
    }
}
