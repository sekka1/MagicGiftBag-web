<?php

class identity
{

public function __construct( ){


}
public function getUserIDSeq( $username ){

	Zend_Loader::loadClass('Generic');

	$generic = new Generic();

	$query = 'select user_id_seq from Users WHERE username="' . $username . '"';

	$data = $generic->customQuery( 'event_info', $query );

	$user_id_seq = $data[0]['user_id_seq'];

	return $user_id_seq;
}

}

?>
