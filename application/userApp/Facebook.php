<?php
// This class gets the list of events that the facebook id that is passed in is invited to

class Facebook
{

public function inviteOneUser( $request_vars ){
// Invites one user id to an event

	$facebook_user_id = $request_vars->getParam( 'new_fb_id' );
	$user_id_seq = $request_vars->getParam( 'user_id' );
	$event_id_seq = $request_vars->getParam( 'event_id' );

	if( is_numeric( $facebook_user_id ) && is_numeric( $user_id_seq ) && is_numeric( $event_id_seq ) ){

		Zend_Loader::loadClass( 'Utilities' );

                $utilitites = new Utilities();

//		if( $utilitites->recordBelongsToUser( $facebook_user_id, 'event_info', 'event_id_seq', $event_id_seq ) ){

			Zend_Loader::loadClass('Generic');

			$generic_db = new Generic();

			$data = array();

			// Add this data into the event table
			$data['user_id_seq'] = $user_id_seq;
			$data['event_id_seq'] = $event_id_seq;
			$data['datetime_created'] = 'NOW()';
			$data['datetime_modified'] = 'NOW()';
			$data['invited_user_fb_uid'] = $facebook_user_id;

			$fb_invite_list_id_seq = $generic_db->save( 'facebook_invite_list', $data );

//		}
	}
}
public function unInviteOneUser( $request_vars ){
// Removes one user from an event

        $facebook_user_id = $request_vars->getParam( 'new_fb_id' );
        $event_id_seq = $request_vars->getParam( 'event_id' );

        if( is_numeric( $facebook_user_id )  && is_numeric( $event_id_seq ) ){

                Zend_Loader::loadClass( 'Utilities' );

                $utilitites = new Utilities();

//              if( $utilitites->recordBelongsToUser( $facebook_user_id, 'event_info', 'event_id_seq', $event_id_seq ) ){

                        Zend_Loader::loadClass('Generic');

                        $generic_db = new Generic();
//echo 'delete from facebook_invite_list where event_id_seq='.$event_id_seq.' and invited_user_fb_uid=' . $facebook_user_id;

                        $fb_invite_list_id_seq = $generic_db->customQuery( 'facebook_invite_list', 'delete from facebook_invite_list where event_id_seq='.$event_id_seq.' and invited_user_fb_uid=' . $facebook_user_id );

//              }
        }
}


}

?>
