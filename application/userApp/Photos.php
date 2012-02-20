<?php

class Photos
{

public function __construct(){


}
public function delete( $request_vars ){

	$picture_path = '/var/www/html/smurf.grep-r.com/auto/public';

	$returnVar = '';

        $event_id_seq = $request_vars->getParam( 'id' );
        //$user_id_seq = $request_vars->getParam( 'user_id' );
	$photo_id_seq = $request_vars->getParam( 'photo_id' );

        if( is_numeric( $event_id_seq ) && is_numeric( $photo_id_seq ) ){

                Zend_Loader::loadClass( 'Utilities' );

                $utilities = new Utilities();

	/////////////////////////
	// Not checking if the user owns the photo b/c guest upload will not
	// have an user_id_seq associated with it.  THis is a little dangerous
	// b/c someone can just go in and start deleteing stuff via the URL
	////////////////////////
                //if( $utilities->recordBelongsToUser( $user_id_seq, 'photos', 'photos_id_seq', $photo_id_seq ) ){

			Zend_Loader::loadClass( 'Generic' );

                        $generic_db = new Generic();

			// Get a list of the photos and paths so that the system can go and delete those photos also
                	$results = $generic_db->customQuery( 'photos', 'SELECT * FROM photos WHERE event_id_seq = ' . $event_id_seq . ' AND photos_id_seq = ' . $photo_id_seq );

			foreach( $results as $aResult ){

				// Delete Thumb nails
				unlink( $picture_path . $aResult['thumb_url'] );

				// Delete orig image
				unlink( $picture_path . $aResult['image_url'] );

				$generic_db->remove_noauth( 'photos', $photo_id_seq, 'photos_id_seq' );
			}
		//}

	}
}

}//end class

?>
