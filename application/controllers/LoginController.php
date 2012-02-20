<?php

class LoginController extends Zend_Controller_Action
{
    public function preDispatch()
    {
/*
        if (Zend_Auth::getInstance()->hasIdentity()) {
            // If the user is logged in, we don't want to show the login form;
            // however, the logout action should still be available
            if ('logout' != $this->getRequest()->getActionName()) {
                $this->_helper->redirector('index', 'index');
            }
        } else {
            // If they aren't, they can't logout, so that action should
            // redirect to the login form
            if ('logout' == $this->getRequest()->getActionName()) {
                $this->_helper->redirector('index');
            }
        }
*/
    }
    public function indexAction()
    {
        //$this->view->form = $this->getForm();

	//$form->getElement('referer')->setValue($this->_request->getParam('f'));
    }
    public function getForm()
    {
        return new Form_Login(array(
            'action' => '/login/process?f=' . $this->_request->getParam( 'f' ),
            'method' => 'post',
        ));
	
    }
    public function getAuthAdapter(array $params)
    {
        // Leaving this to the developer...
        // Makes the assumption that the constructor takes an array of
        // parameters which it then uses as credentials to verify identity.
        // Our form, of course, will just pass the parameters 'username'
        // and 'password'.

	$dbAdapter = new Zend_Db_Adapter_Pdo_Mysql( array('dbname' => 'Smurf', 'password' => 'sunshine', 'username' => 'root' ) );
	
	$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

	$authAdapter
            ->setTableName('Users')
            ->setIdentityColumn('username')
            ->setCredentialColumn('password')
            ->setCredentialTreatment('MD5(?)')
        ;

	$authAdapter
            ->setIdentity($params['username'])
            ->setCredential($params['password'])
        ;

	return $authAdapter;

    }
    public function processAction()
    {

        $request = $this->getRequest();

        // Check if we have a POST request
        if (!$request->isPost()) {
            return $this->_helper->redirector('index');
        }

        // Get our form and validate it
        $form = $this->getForm();
        if (!$form->isValid($request->getPost())) {
            // Invalid entries
            $this->view->form = $form;
            return $this->render('index'); // re-render the login form
        }

        // Get our authentication adapter and check credentials
        $adapter = $this->getAuthAdapter($form->getValues());
        $auth    = Zend_Auth::getInstance();
//print_r( $adapter );
        $result  = $auth->authenticate($adapter);
        if (!$result->isValid()) {
            // Invalid credentials
            $form->setDescription('Invalid credentials provided');
            $this->view->form = $form;
            return $this->render('index'); // re-render the login form
        }

        // We're authenticated! Redirect to the home page
	// Page user tried to goto when unauthed send them back here
	$referer = $this->_request->getParam( 'f' );

	if( $referer == '' ){
		// Send to generic page
		$this->_helper->redirector('index', 'index');
	} else {
		// Send to the page the user wanted before authing
		$this->_redirect( $referer );
	}

    }
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('index'); // back to login page
    }
}

?>
