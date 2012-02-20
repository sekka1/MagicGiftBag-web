<?php
class CustomersController extends Zend_Controller_Action
{

     private $client_id_seq;
     private $user_id_seq;
     private $acl;
     private $auth;

     public function init()
     {
//this function is called by the constructor so what every goes on here
//will be available in all actions

//	$this->view->baseUrl = $this->_request->getBaseUrl();

//	$this->view->baseUrl = $this->_request->getBaseUrl();
//	$this->view->user = Zend_Auth::getInstance()->getIdentity();

//	$this->client_id_seq = Zend_Auth::getInstance()->getIdentity()->client_id_seq;
//	$this->user_id_seq = Zend_Auth::getInstance()->getIdentity()->id;


	Zend_Loader::loadClass('Zend_Json');
     }
     public function preDispatch(){

	// Authentication Piece
        $this->auth = Zend_Auth::getInstance();

        if(!$this->auth->hasIdentity()){
                 $this->_redirect( '/login?f=' . $this->_request->getRequestUri() );
        } else {
                // User is valid and logged in
                //echo $auth->getIdentity();
                //print_r ($auth);
        }
     }
     public function indexAction(){

	$this->view->user = $this->auth->getIdentity();
     }
     public function indexgetAction(){

	Zend_Loader::loadClass( 'Generic' );

	$generic_db = new Generic();

	$results = $generic_db->customQuery( 'Company', 'select * from Company' );

	$this->view->results = json_encode( $results );
     }
     public function indexaddAction(){

	Zend_Loader::loadClass( 'Generic' );

	$customer_id = $this->_request->getParam( 'id' );

        $generic_db = new Generic();

        $data = array();

        // Put data into the $data array in the format that the generic save wants
        $data['name'] = $this->_request->getParam( 'name' );
        $data['contact'] = $this->_request->getParam( 'contact' );
        $data['address'] = $this->_request->getParam( 'address' );
        $data['city'] = $this->_request->getParam( 'city' );
        $data['state'] = $this->_request->getParam( 'state' );
        $data['zip'] = $this->_request->getParam( 'zip' );
        $data['phone_number'] = $this->_request->getParam( 'phone_number' );
        $data['datetime_created'] = 'NOW()';
        $data['datetime_modified'] = 'NOW()';

        if( $this->_request->getParam( 'customer_id_seq' ) > 0 ){
        // Update the record

                $row_id_seq = $this->_request->getParam( 'customer_id_seq' );

                // Clear out all blank fields
                $edit_only = array(); // Holds only values that are going to be edited	
		
		foreach( $data as $key=>$aVal ){
                        if( $aVal != '' ){
                        // Add to $edit_only
                                $edit_only[$key] = $aVal;
                        }
                }

                $generic_db->edit_noauth( 'Company', $row_id_seq, $edit_only, 'company_id_seq' );
        }
        else {
        // Add a new record
                $generic_db->save( 'Company', $data );
        }
     }
     public function softwareAction(){

	$this->view->customer_id_seq = $this->_request->getParam( 'id' );
	$this->view->name = $this->_request->getParam( 'name' );
     }
     public function softwaregetAction(){

	Zend_Loader::loadClass( 'Generic' );

	$customer_id = $this->_request->getParam( 'id' );

        $generic_db = new Generic();

        $results = $generic_db->customQuery( 'Software', 'select * from Software WHERE company_id_seq = ' . $customer_id );

	$this->view->results = json_encode( $results );
     }
     public function softwareaddAction(){

	Zend_Loader::loadClass( 'Generic' );

	$customer_id = $this->_request->getParam( 'id' );

        $generic_db = new Generic();

	$data = array();

	// Put data into the $data array in the format that the generic save wants
	$data['company_id_seq'] = $customer_id;
	$data['equipment'] = $this->_request->getParam( 'equipment' );
	$data['serial_number'] = $this->_request->getParam( 'serial_number' );
	$data['installed'] = $this->_request->getParam( 'installed' );
	$data['site_name'] = $this->_request->getParam( 'site_name' );
	$data['site_address'] = $this->_request->getParam( 'site_address' );
	$data['probe_name'] = $this->_request->getParam( 'probe_name' );
	$data['management_ip'] = $this->_request->getParam( 'management_ip' );
	$data['firmware_version'] = $this->_request->getParam( 'firmware_version' );
	$data['uptime'] = $this->_request->getParam( 'uptime' );
	$data['notes'] = $this->_request->getParam( 'notes' );
	$data['datetime_created'] = 'NOW()';
	$data['datetime_modified'] = 'NOW()'; 

	if( $this->_request->getParam( 'id_seq' ) > 0 ){
	// Update the record
		
		$row_id_seq = $this->_request->getParam( 'id_seq' );

		// Clear out all blank fields
		$edit_only = array(); // Holds only values that are going to be edited

		foreach( $data as $key=>$aVal ){
			if( $aVal != '' ){
			// Add to $edit_only
				$edit_only[$key] = $aVal;
			}
		}

		$generic_db->edit_noauth( 'Software', $row_id_seq, $edit_only, 'id_seq' ); 
	}
	else {
	// Add a new record
		$generic_db->save( 'Software', $data );
	}
     }
     public function softwaredeleteAction(){

	$id_seq = $this->_request->getParam( 'id_seq' );

	Zend_Loader::loadClass( 'Generic' );

        $generic_db = new Generic();

	$generic_db->remove_noauth( 'Software', $id_seq, 'id_seq' );

     }
     public function probeAction(){

        $this->view->customer_id_seq = $this->_request->getParam( 'id' );
	$this->view->name = $this->_request->getParam( 'name' );
     }
     public function probegetAction(){

        Zend_Loader::loadClass( 'Generic' );

        $customer_id = $this->_request->getParam( 'id' );

        $generic_db = new Generic();

        $results = $generic_db->customQuery( 'Probes', 'select * from Probes WHERE company_id_seq = ' . $customer_id );

        $this->view->results = json_encode( $results );
     }
     public function probeaddAction(){
        Zend_Loader::loadClass( 'Generic' );

        $customer_id = $this->_request->getParam( 'id' );

        $generic_db = new Generic();

        $data = array();

        // Put data into the $data array in the format that the generic save wants
        $data['company_id_seq'] = $customer_id;
        $data['equipment'] = $this->_request->getParam( 'equipment' );
        $data['serial_number'] = $this->_request->getParam( 'serial_number' );
        $data['installed'] = $this->_request->getParam( 'installed' );
        $data['site_name'] = $this->_request->getParam( 'site_name' );
        $data['site_address'] = $this->_request->getParam( 'site_address' );
        $data['probe_name'] = $this->_request->getParam( 'probe_name' );
        $data['management_ip'] = $this->_request->getParam( 'management_ip' );
        $data['firmware_version'] = $this->_request->getParam( 'firmware_version' );
        $data['uptime'] = $this->_request->getParam( 'uptime' );
        $data['notes'] = $this->_request->getParam( 'notes' );
        $data['datetime_created'] = 'NOW()';
        $data['datetime_modified'] = 'NOW()';

        if( $this->_request->getParam( 'id_seq' ) > 0 ){
        // Update the record

                $row_id_seq = $this->_request->getParam( 'id_seq' );

                // Clear out all blank fields
                $edit_only = array(); // Holds only values that are going to be edited

                foreach( $data as $key=>$aVal ){
                        if( $aVal != '' ){
                        // Add to $edit_only
                                $edit_only[$key] = $aVal;
                        }                
		}
                $generic_db->edit_noauth( 'Probes', $row_id_seq, $edit_only, 'id_seq' );
        }
        else {
        // Add a new record
                $generic_db->save( 'Probes', $data );
        }
     }
     public function probedeleteAction(){

        $id_seq = $this->_request->getParam( 'id_seq' );

        Zend_Loader::loadClass( 'Generic' );

        $generic_db = new Generic();

        $generic_db->remove_noauth( 'Probes', $id_seq, 'id_seq' );

     }
     public function extraAction(){

        $this->view->customer_id_seq = $this->_request->getParam( 'id' );
	$this->view->name = $this->_request->getParam( 'name' );
     }
     public function extragetAction(){

        Zend_Loader::loadClass( 'Generic' );

        $customer_id = $this->_request->getParam( 'id' );

        $generic_db = new Generic();

        $results = $generic_db->customQuery( 'Extra_Services', 'select * from Extra_Services WHERE company_id_seq = ' . $customer_id );

        $this->view->results = json_encode( $results );
     }
     public function extraaddAction(){
        Zend_Loader::loadClass( 'Generic' );

        $customer_id = $this->_request->getParam( 'id' );

        $generic_db = new Generic();

        $data = array();

        // Put data into the $data array in the format that the generic save wants
        $data['company_id_seq'] = $customer_id;
        $data['type_of_service'] = $this->_request->getParam( 'type_of_service' );
        $data['explanation_of_service'] = $this->_request->getParam( 'explanation_of_service' );
        $data['design_document_script_filename'] = $this->_request->getParam( 'design_document_script_filename' );
        $data['datetime_created'] = 'NOW()';
        $data['datetime_modified'] = 'NOW()';

        if( $this->_request->getParam( 'id_seq' ) > 0 ){
        // Update the record

                $row_id_seq = $this->_request->getParam( 'id_seq' );

                // Clear out all blank fields
                $edit_only = array(); // Holds only values that are going to be edited

                foreach( $data as $key=>$aVal ){
                        if( $aVal != '' ){
                        // Add to $edit_only
                                $edit_only[$key] = $aVal;
                        }
                }
                $generic_db->edit_noauth( 'Extra_Services', $row_id_seq, $edit_only, 'id_seq' );
        }
        else {
        // Add a new record
                $generic_db->save( 'Extra_Services', $data );        
	}
     }
     public function extradeleteAction(){

        $id_seq = $this->_request->getParam( 'id_seq' );

        Zend_Loader::loadClass( 'Generic' );

        $generic_db = new Generic();

        $generic_db->remove_noauth( 'Extra_Services', $id_seq, 'id_seq' );

     }

}
