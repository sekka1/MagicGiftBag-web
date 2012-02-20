<?php
/*
This class looks for images to convert to a thumbnail

*/

class convertToThumbnail
{

public function __construct( $client_id_seq, $user_id_seq ){


}
public function convert( $something ){

	Zend_Loader::loadClass('Generic');

	$generic_db = new Generic();

	// Pull up items that needs to be converted
	$results = $generic_db->customQuery( 'photos', 'SELECT * FROM photos WHERE thumb_url="/pictures/place-holder.png" LIMIT 5' );

	foreach( $results as $aResult ){
	// These items will need to be converted

		$image_uri = '/var/www/html/smurf.grep-r.com/auto/public' . $aResult['image_url'];
		$thumbFile = '/var/www/html/smurf.grep-r.com/auto/public' . str_replace( ".", "-thumb.", $aResult['image_url'] );

		// Convert
		system("/usr/bin/convert $image_uri -resize 90x100 $thumbFile");
		
		// Update DB of the new file name
		$data = array();

		$data['thumb_url'] = str_replace( ".", "-thumb.", $aResult['image_url'] );
		$data['datetime_modified'] = 'NOW()';

		$id_seq = $generic_db->edit_noauth( 'photos', $aResult['photos_id_seq'], $data, 'photos_id_seq' );
		//$id_seq = $generic_db->edit( 'photos', $aResult['photos_id_seq'], $aResult['user_id_seq'], $data, 'photos_id_seq' );
	}
}


}

?>
