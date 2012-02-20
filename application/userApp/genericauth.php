<?php
/*
 This is a generic auth class.  It is used for minor authorization from the web url.
*/

class genericauth
{

	var $auth_string; 

public function __construct( ){

	$this->auth_string = 'videomonitoring';
}
public function is_authorized( $string ){

	$is_authorized = false;

	if( $this->auth_string == $string ){
		$is_authorized = true;
	}

	return $is_authorized;
}


}

?>
