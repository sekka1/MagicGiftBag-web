<?php

class GetPhotos
{

public function __construct( ){

}

public function getInfo( $request_vars ){
        // Get the event info and returns it in a json format

        Zend_Loader::loadClass( 'Generic' );

	$returnVal = '';

        $generic_db = new Generic();

        $event_id_seq = $request_vars->getParam( 'id' );

        // Get the information and return it to the view
        if( is_numeric( $event_id_seq ) ){
                $results = $generic_db->customQuery( 'event_info', 'SELECT * FROM event_info WHERE event_id_seq = '.$event_id_seq );

                $returnVal = json_encode( $results );
        }

	return $returnVal;
}
public function getPhotoDummyOwners( $request_vars ){

	$json[0]['owners_photo_album_id_seq'] = 10;
	$json[0]['photo_url'] = 'http://smurf.grep-r.com/test_smurf_pics/a.jpeg';
	$json[1]['owners_photo_album_id_seq'] = 11;
        $json[1]['photo_url'] = 'http://smurf.grep-r.com/test_smurf_pics/b.jpeg';
	$json[2]['owners_photo_album_id_seq'] = 12;
        $json[2]['photo_url'] = 'http://smurf.grep-r.com/test_smurf_pics/c.jpeg';
	$json[3]['owners_photo_album_id_seq'] = 13;
        $json[3]['photo_url'] = 'http://smurf.grep-r.com/test_smurf_pics/d.jpeg';
	$json[4]['owners_photo_album_id_seq'] = 14;
        $json[4]['photo_url'] = 'http://smurf.grep-r.com/test_smurf_pics/e.jpeg';
	$json[5]['owners_photo_album_id_seq'] = 15;
        $json[5]['photo_url'] = 'http://smurf.grep-r.com/test_smurf_pics/f.jpeg';
	$json[6]['owners_photo_album_id_seq'] = 16;
        $json[6]['photo_url'] = 'http://smurf.grep-r.com/test_smurf_pics/g.jpeg';
	$json[7]['owners_photo_album_id_seq'] = 17;
        $json[7]['photo_url'] = 'http://smurf.grep-r.com/test_smurf_pics/h.jpeg';
	$json[8]['owners_photo_album_id_seq'] = 18;
        $json[8]['photo_url'] = 'http://smurf.grep-r.com/test_smurf_pics/i.jpeg';
	$json[9]['owners_photo_album_id_seq'] = 19;
        $json[9]['photo_url'] = 'http://smurf.grep-r.com/test_smurf_pics/j.jpeg';
	$json[10]['owners_photo_album_id_seq'] = 20;
        $json[10]['photo_url'] = 'http://smurf.grep-r.com/test_smurf_pics/k.jpeg';
	$json[11]['owners_photo_album_id_seq'] = 21;
        $json[11]['photo_url'] = 'http://smurf.grep-r.com/test_smurf_pics/l.jpeg';	
	$json[12]['owners_photo_album_id_seq'] = 22;
        $json[12]['photo_url'] = 'http://smurf.grep-r.com/test_smurf_pics/m.jpeg';
	$json[13]['owners_photo_album_id_seq'] = 23;
        $json[13]['photo_url'] = 'http://smurf.grep-r.com/test_smurf_pics/n.jpeg';
	$json[14]['owners_photo_album_id_seq'] = 24;
        $json[14]['photo_url'] = 'http://smurf.grep-r.com/test_smurf_pics/o.jpeg';
	$json[15]['owners_photo_album_id_seq'] = 25;
        $json[15]['photo_url'] = 'http://smurf.grep-r.com/test_smurf_pics/p.jpeg';
	$json[15]['owners_photo_album_id_seq'] = 26;
        $json[15]['photo_url'] = 'http://smurf.grep-r.com/test_smurf_pics/q.jpeg';
	$json[16]['owners_photo_album_id_seq'] = 27;
        $json[16]['photo_url'] = 'http://smurf.grep-r.com/test_smurf_pics/r.jpeg';
	$json[17]['owners_photo_album_id_seq'] = 28;
        $json[17]['photo_url'] = 'http://smurf.grep-r.com/test_smurf_pics/s.jpeg';
	$json[18]['owners_photo_album_id_seq'] = 29;
        $json[18]['photo_url'] = 'http://smurf.grep-r.com/test_smurf_pics/t.jpeg';

	return json_encode( $json );
}
public function getAllEventPhotos( $request_vars ){
        // Get the event info and returns it in a json format

        Zend_Loader::loadClass( 'Generic' );

        $returnVal = '';

        $generic_db = new Generic();

        $event_id_seq = $request_vars->getParam( 'id' );

        // Get the information and return it to the view
        if( is_numeric( $event_id_seq ) ){
		$results = $generic_db->customQuery( 'photos', 'SELECT * FROM photos WHERE event_id_seq = "'.$event_id_seq . '" ORDER BY datetime_created desc' );

		$results = $this->checkThumbNailsExistsAndReplaceWithTempImage( $results );

                $returnVal = json_encode( $results );
        }

        return $returnVal;
}
public function getAllOwnersPhotos( $request_vars ){
        // Get the event info and returns it in a json format

        Zend_Loader::loadClass( 'Generic' );

        $returnVal = '';

        $generic_db = new Generic();

        $event_id_seq = $request_vars->getParam( 'id' );
	$type = 'owners';

        // Get the information and return it to the view
        if( is_numeric( $event_id_seq ) && ( $type != '' ) ){
                $results = $generic_db->customQuery( 'photos', 'SELECT * FROM photos WHERE event_id_seq = "'.$event_id_seq .'" AND type = "' . $type . '"' );

                $returnVal = json_encode( $results );
        }

        return $returnVal;
}
public function getOnePhoto( $request_vars ){

	Zend_Loader::loadClass( 'Generic' );

        $returnVal = '';

        $generic_db = new Generic();

        $event_id_seq = $request_vars->getParam( 'id' );
	$photos_id_seq = $request_vars->getParam( 'photo' );

        // Get the information and return it to the view
        if( is_numeric( $event_id_seq ) && is_numeric( $photos_id_seq ) ){
		$results = $generic_db->customQuery( 'photos', 'SELECT * FROM photos WHERE event_id_seq = "'.$event_id_seq .'" AND photos_id_seq = "' . $photos_id_seq . '"' );

                $returnVal = json_encode( $results );
        }

        return $returnVal;
}
public function getAllGuestsPhotos( $request_vars ){
        // Get the event info and returns it in a json format

        Zend_Loader::loadClass( 'Generic' );

        $returnVal = '';
        $generic_db = new Generic();

        $event_id_seq = $request_vars->getParam( 'id' );
        $type = 'guests';

        // Get the information and return it to the view
        if( is_numeric( $event_id_seq ) && ( $type != '' ) ){
                $results = $generic_db->customQuery( 'photos', 'SELECT * FROM photos WHERE event_id_seq = "'.$event_id_seq 
.'" AND type = "' . $type . '"' );

                $returnVal = json_encode( $results );
        }

        return $returnVal;
}
public function checkThumbNailsExistsAndReplaceWithTempImage( $results_array ){
// Check the thumb_url array spot and check if this file is actually there or not.
// If it is not there replace it with a temp image place holder

	for( $i=0; $i<count( $results_array ); $i++ ){

		if( !file_exists( '../public/'.$results_array[$i]['thumb_url'] ) ){
			$results_array[$i]['thumb_url'] = '/pictures/place-holder.png';
		} 
	}

	return $results_array;
}

}

?>
