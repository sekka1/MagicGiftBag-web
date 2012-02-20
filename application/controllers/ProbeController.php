<?php

class ProbeController extends Zend_Controller_Action
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
        $this->view->pageTitle = 'Probe Automation';

    }
    public function adduserAction(){

	// Sets which template you want for the output
        $this->view->templateType = 'flex';
	// Template Variables
        $this->view->pageTitle = 'Probe Automation - Add User';

	if ($this->getRequest()->isPost()) {

		$probe_ip = $this->_request->getParam( 'probe-ip' );
		$probe_login = $this->_request->getParam( 'probe-username' );
		$probe_pass = $this->_request->getParam( 'probe-password' );
		$new_user = $this->_request->getParam( 'new-user' );
		$new_pass = $this->_request->getParam( 'new-pass' );
		$new_group = $this->_request->getParam( 'group' );

		$command = '/opt/probe-web-automation/IQ/add_user.pl \''.$probe_ip.'\' \''.$probe_login.'\' \''.$probe_pass.'\' \''.$new_user.'\' \''.$new_pass.'\' \''.$new_group.'\'';

		$last_line = system( $command );

		echo "<br/><br/>" . $last_line . "<br/><br/>";

	}
    }
    public function editpatmdimlrAction(){

        // Sets which template you want for the output
        $this->view->templateType = 'flex';
        // Template Variables
        $this->view->pageTitle = 'Probe Automation - Change MDI-MLR Value';

        if ($this->getRequest()->isPost()) {

                $probe_ip = $this->_request->getParam( 'probe-ip' );
                $probe_login = $this->_request->getParam( 'probe-username' );
                $probe_pass = $this->_request->getParam( 'probe-password' );
                $mdi_mlr_value = $this->_request->getParam( 'mdi-mlr-value' );

                $command = '/opt/probe-web-automation/IQ/change_pat_mdi_mlr.pl \''.$probe_ip.'\' \''.$probe_login.'\' \''.$probe_pass.'\' \''.$mdi_mlr_value.'\'';

                $last_line = system( $command );

                echo "<br/><br/>" . $last_line . "<br/><br/>";
        }
    }

}
