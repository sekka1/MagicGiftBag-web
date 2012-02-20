<?php

class ConvertController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
    public function preDispatch(){

	//
	// Authorization block.  If the auth string is not passed in the
	// url the user will not be able to access this page
	Zend_Loader::loadClass( 'genericauth' );

	$auth = new genericauth();

	$auth_string = $this->_request->getParam( 'auth' );

	if( ! $auth->is_authorized( $auth_string ) ){
	// Forward to some un auth page
		header('Location: /index/unauth');
	}
    }
    public function indexAction()
    {
	// Sets which template you want for the output
        $this->view->templateType = 'flex';
        // Template Variables
        $this->view->pageTitle = 'Alias File Convertion Tool';

    }
    public function testAction()
    {

	// Sets which template you want for the output
	$this->view->templateType = 'flex';

	$this->view->title = "index here"; 

	ini_set("memory_limit","50M");

        include( '/var/www/html/auto/application/userApp/Excel/reader.php' );

	$xl_reader = new Spreadsheet_Excel_Reader();
	
	//$xl_reader->read( '/var/www/html/auto/application/userApp/Excel/excel1.xls' );
	$xl_reader->read( '/tmp/uploads/974261830-Changed_excel_saveas_type_Naples-Modified-ADS---ReMux-_070709.xls' );

	echo "First tab's name: " . $sheetname = $xl_reader->boundsheets[0]['name'] . "<br/>";
	//echo "Second tab's name: " . $sheetname = $xl_reader->boundsheets[0]['name'] . "<br/>";

	print_r( $xl_reader->boundsheets );
	print_r( $xl_reader->sheets );

    }
    public function qamAction()
    {

	// Sets which template you want for the output
        $this->view->templateType = 'flex';
	// Template Variables
	$this->view->pageTitle = 'Create QAM Alias File';

    }
    public function ipAction()
    {

        // Sets which template you want for the output
        $this->view->templateType = 'flex';
        // Template Variables
        $this->view->pageTitle = 'Create IP Alias File';

    }
    public function sanatizeuploadAction()
    {

        // Sets which template you want for the output
        $this->view->templateType = 'flex';
        // Template Variables
        $this->view->pageTitle = 'Sanatize an Alias File';

    }
    public function uploadAction()
    {

	$redirect_destination = $this->_request->getParam( 'destination' );
	$type = $this->_request->getParam( 'type' ); // The type of alias file that the user wants

	// Upload file location
	$target_path = "/tmp/uploads/";

	$basename = rand() . "-" . str_replace( " ", "-", basename( $_FILES['uploadedfile']['name'] ) );

	// Concat orig file name with the path
	$target_path = $target_path . $basename;

	if( move_uploaded_file( $_FILES['uploadedfile']['tmp_name'], $target_path ) ) {

		echo "The file ".  basename( $_FILES['uploadedfile']['name']) . " has been uploaded to " . $target_path;
	} else {
		echo "file upload Failed";
	}

	if( $redirect_destination == '' ){
		// This is the standard alias file picker location

		// Redirect user back to page that will process the uploaded file
		header("Location: /convert/pickfields/auth/videomonitoring/type/".$type."/didUpload/yes/filename/" . $basename  ); /* Redirect browser */
	} elseif ( $redirect_destination == 'sanatize' ){
		// This is the location to sanatize a file for iregular characters

		header("Location: /convert/sanatize/auth/videomonitoring/didUpload/yes/filename/" . $basename );
	}

    }
    public function pickfieldsAction()
    {

	// Sets which template you want for the output
        $this->view->templateType = 'flex'; // This is the template based on the kubruk design

	$this->view->pageTitle = "Choose Field Mapping";

	$type = $this->_request->getParam( 'type' ); // Type of alias file user wants
	$didUpload = $this->_request->getParam( 'didUpload' );
	$filename = $this->_request->getParam( 'filename' );

	// Upload file location
	$target_path = "/tmp/uploads/";

	// Make the Memory limit bigger for this run so that it can suck in the entire excel sheet
	ini_set("memory_limit","150M");

	include( '/var/www/html/zend1.grep-r.com/auto/application/userApp/Excel/reader.php' );

	$xl_reader = new Spreadsheet_Excel_Reader();

	$xl_reader->read( $target_path . $filename );

	// Put the excel array into the view
	$this->view->sheets = $xl_reader->sheets;
	$this->view->boundsheets = $xl_reader->boundsheets;
	$this->view->filename = $filename;
	$this->view->type = $type;


    }
    public function processfieldsAction()
    {

	if ($this->getRequest()->isPost()) {

		// Load class that will parse out the user input
		Zend_Loader::loadClass( 'iqalias' );

		$iqalias = new iqalias();

		$iqalias->type = $this->_request->getParam( 'type' );

		$formData = $this->getRequest()->getPost();

		// Based on the user input parse the different columns into flows
		$iqalias->parse_users_input_into_flows( $formData['filename'], $formData );		

		// Output the flows to a file
		$alias_output = $iqalias->output_flows_to_alias_file();

		// Output Alias per users settings
		$iqalias->output_alias( $alias_output, $this->_request->getParam( 'output_to' ), $this->_request->getParam( 'file_name' ) );
	}	
    }
    public function igmpsetcalAction(){

	$this->view->templateType = 'flex';
        // Template Variables
        $this->view->pageTitle = 'IGMP Set Calculator';

	$this->view->pick = true; // If set to true this means the user is tryiing to pick the sets not calculating it

	if ($this->getRequest()->isPost()) {

		$this->view->pick = false;

		// Add up all the groups picked by the user
		$group_sum = $this->_request->getParam( 'all_group' ) + $this->_request->getParam( 'set_1' )	+ $this->_request->getParam( 'set_2' ) + $this->_request->getParam( 'set_3' ) + $this->_request->getParam( 'set_4' ) + $this->_request->getParam( 'set_5' ) + $this->_request->getParam( 'set_6' ) + $this->_request->getParam( 'set_7' ) + $this->_request->getParam( 'set_8' ) + $this->_request->getParam( 'set_9' ) + $this->_request->getParam( 'set_10' ) + $this->_request->getParam( 'set_11' ) + $this->_request->getParam( 'set_12' ) + $this->_request->getParam( 'set_13' ) + $this->_request->getParam( 'set_14' ) + $this->_request->getParam( 'set_15' );

		$this->view->igmp_number = $group_sum;

	}
    }
    public function sanatizeAction(){

	$this->view->templateType = 'flex';

	$this->view->pageTitle = 'Sanatize a File';

    }

}
