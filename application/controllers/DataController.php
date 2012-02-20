<?php
class DataController extends Zend_Controller_Action
{

     private $client_id_seq;
     private $user_id_seq;
     private $acl;

     public function init()
     {
     }
     public function preDispatch(){
     }
     public function indexAction(){
	//this method NEEDS at least 2 parameters "class" and "method".
	//It will use the "class" parameter to init the class and then call that "method"
	//in that class.

	//It will print out whatever that method returns

//echo $this->getRequest()->getControllerName() . ' -- ' . $this->getRequest()->getActionName() . '<br>';

		$class = $this->_request->getParam( 'class' );
		$method = $this->_request->getParam( 'method' );

		try{
//turning off the rights checking for now. 4/21
			//check if the user has rights to run this action
//			if( $this->acl->hasRights( $this->getRequest()->getControllerName() . '/' . $this->getRequest()->getActionName() . '/class/' . $class . '/method/' . $method ) ){

				Zend_Loader::loadClass( $class );

				$anObject = new $class( $this->client_id_seq, $this->user_id_seq );

				if( method_exists( $anObject, $method ) ){

					$returnVal =  $anObject->$method( $this->_request );

					$this->view->data = $returnVal;

				}
				else{
					$this->view->data = 'invalid method';

				}

/*			}
			else{
			//user has no rights to this action

				$data = array();

				$data['rights'] = 'none';
				$data['message'] = 'Not authorized to run this action';

				echo json_encode( $data );;
			}
*/

		} catch( Exception $e ){

			echo 'Caught exception: ',  $e->getMessage(), "<br/>";

			//$this->view->data = "invalid class";
		}

     }

}
