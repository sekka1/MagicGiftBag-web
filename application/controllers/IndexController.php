<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
    public function indexAction()
    {
//	header("Location: /events"); /* Redirect browser */
/*        // action body
	$this->view->title = "My Albums"; 
        $this->view->headTitle($this->view->title, 'PREPEND'); 
        $albums = new Model_DbTable_Albums(); 
        $this->view->albums = $albums->fetchAll();
*/
    }
    public function oneAction()
    {

    }
    public function twoAction()
    {

    }
    public function eventsAction()
    {

    }
    public function unauthAction(){

	// Sets which template you want for the output
        $this->view->templateType = 'none';

        $auth = Zend_Auth::getInstance();

        if(!$auth->hasIdentity()){
                 $this->_redirect('/customers');
        } else {
                echo $auth->getIdentity();
                //print_r ($auth);
        }
    }
    public function fbcheckinsAction(){
 
    }
    public function fblikesAction(){
    }
    public function fblikes2Action(){

        $user_id = $this->_request->getParam( 'user' );

        $this->view->user_id = $user_id;
    }
    public function fblikes3Action(){

        $user_id = $this->_request->getParam( 'user' );
        $fb_session = $this->_request->getParam( 'session' );

        $this->view->user_id = $user_id;
        $this->view->fb_session = $fb_session;
    }
    public function simplestoreAction(){

    }
    public function amzecsAction(){

   }
   public function amazonecsAction(){


   }
   public function amzecs2Action(){
   }
   public function amzecs3Action(){

 	$category = $this->_request->getParam( 'category' );
	$search = $this->_request->getParam( 'search' );

	$this->view->category = $category;
	$this->view->search = $search;

   }
   public function channelAction(){
        // This is a file for FB javascript plugin for cross domain browsing and social plugins

   }
}
