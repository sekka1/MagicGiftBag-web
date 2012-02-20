<?php

class ReportingController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
	header("Location: /convert"); /* Redirect browser */
/*        // action body
	$this->view->title = "My Albums"; 
        $this->view->headTitle($this->view->title, 'PREPEND'); 
        $albums = new Model_DbTable_Albums(); 
        $this->view->albums = $albums->fetchAll();
*/
    }
    public function pie2dAction(){

	$this->view->input_chart = 'Pie2D.swf';//$this->_request->getParam( 'chart' );

	$this->view->data_url = $this->_request->getParam( 'data' );//'/data/index/class/customerreporting/method/probe_type_pie';

	// width and height of the entire chart
	$this->view->width = $this->_request->getParam( 'width' );
	$this->view->height = $this->_request->getParam( 'height' );
    }
    public function pie3dAction(){

        $this->view->input_chart = 'Pie3D.swf';//$this->_request->getParam( 'chart' );

        $this->view->data_url = $this->_request->getParam( 'data' );//'/data/index/class/customerreporting/method/probe_type_pie';

        // width and height of the entire chart
        $this->view->width = $this->_request->getParam( 'width' );
        $this->view->height = $this->_request->getParam( 'height' );
    }
}
