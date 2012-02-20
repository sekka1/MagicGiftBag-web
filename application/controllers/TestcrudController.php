<?php
class TestcrudController extends Zend_Controller_Action
{
    private $fbAuth;

     public function init()
     {
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
                                                                                                            
     }                                                                                 
     public function indexAction(){

     }
     public function testAction(){

        // Pass the facebook object for the view to use                                                     
        $this->view->facebook = $this->fbAuth->facebook;

     }
     public function authAction(){

    }
}
