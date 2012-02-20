<?php

class test
{

public function __construct( $client_id_seq, $user_id_seq ){


}
public function good( $something ){

	Zend_Loader::loadClass('Generic');

	$data = array(
			'artist' => 'generic',
			'title' => 'hahaha'
		);

	$generic = new Generic();

	$cred_id_seq = $generic->save( 'albums', $data );


	return 'returned from good...OK! ' . $cred_id_seq;
}
public function test( $request_vars ){
	
	// To get the request param
	//$request_vars->getParam( 'caption' );

	return 'working!';
}


}

?>
