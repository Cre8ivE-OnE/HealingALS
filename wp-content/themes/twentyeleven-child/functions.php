<?php

// ########################################################################################
// ##  Map Memberpress data over to xProfile Data
// ########################################################################################
add_action('edit_user_profile_update', 'update_wp_to_xprofile');
add_action('personal_options_update', 'update_wp_to_xprofile');

add_action('edit_user_profile', 'update_user_wp_to_xprofile');
add_action('show_user_profile', 'update_user_wp_to_xprofile');

function update_user_wp_to_xprofile($user) {
	do_action(update_wp_to_xprofile($user->ID));
}

function update_wp_to_xprofile($userId) {
	if ( current_user_can('edit_user',$userId) ) {
		global $bp;	
		$currentUser = get_userdata($userId);
		$website = $currentUser->user_url;
		$first_name = get_user_meta ($userId, 'first_name', true);
		$last_name = get_user_meta ($userId, 'last_name', true);
		
		$alsConnectionOther = get_user_meta ($userId, 'mepr_other_connection_with_als', true);
		
		$WPDiagnosisDate = strtotime(get_user_meta ($userId, 'mepr_diagnosis_date', true));
		$alsDiagnosisDate = date('Y-m-d',$WPDiagnosisDate);
		
		$alsHowFoundOther = get_user_meta ($userId, 'mepr_other_location_on_how_you_found_out_about_us', true);
		$alsWhereLive = get_user_meta ($userId, 'mepr_where_do_you_live_or_are_located', true);
		$alsImportantInfo = get_user_meta ($userId, 'mepr_what_are_the_most_important_things_you_are_looking_for_from_this_membership_site', true);
		
		$current_user = get_userdata($userId);	
		$alsForumEmail = $current_user->user_email;
		
		$allMeta = get_user_meta( $userId );
		$alsConnectionData = $allMeta['mepr_connection_with_als'][0];
		$alsConnection = array();
		if (strpos($alsConnectionData, 'pals') !== false) 
		{$alsConnection = "PALS (Person diagnosed w ALS)";}
		if (strpos($alsConnectionData, 'cals') !== false) 
		{$alsConnection = "CALS (Caregiver/Spouse/Partner)";}
		if (strpos($alsConnectionData, 'fals') !== false) 
		{$alsConnection = "FALS (Family or friend of PALS)";}
		if (strpos($alsConnectionData, 'mp') !== false)  
		{$alsConnection = "MP (Medical Professional)";}
		if (strpos($alsConnectionData, 'other') !== false) 
		{$alsConnection = "Other  (Please add explanation below.)";}

		$alsHowFoundData = $allMeta['mepr_how_did_you_find_out_about_us'][0];
		$alsHowFound = array();
		if (strpos($alsHowFoundData, 'internet') !== false) 
		{$alsHowFound[] = "Internet Search";}
		if (strpos($alsHowFoundData, 'friend') !== false) 
		{$alsHowFound[] = "Referred by Friend";}
		if (strpos($alsHowFoundData, 'otherhow') !== false) 
		{$alsHowFound[] = "Other (Please add in explanation below.)";}

		// ############################################
		// ##  Map Wordpress data over to xProfile Data
		// ############################################	
		//Array mappings
		$a = serialize($alsConnection);
		$b = serialize($alsHowFound);
		xprofile_set_field_data(6, $userId,   $alsConnection );
		xprofile_set_field_data(17, $userId,  $alsHowFound);

		xprofile_set_field_data(2, $userId,  $website);
		xprofile_set_field_data(3, $userId,  $first_name);
		xprofile_set_field_data(4, $userId,  $last_name);
		xprofile_set_field_data(16, $userId,  $alsConnectionOther);
		xprofile_set_field_data(15, $userId,  $alsDiagnosisDate);
		xprofile_set_field_data(5, $userId,   $alsForumEmail);
		xprofile_set_field_data(21, $userId,  $alsHowFoundOther);
		xprofile_set_field_data(22, $userId,  $alsWhereLive);
		xprofile_set_field_data(23, $userId,  $alsImportantInfo);
	}
}


// ########################################################################################
// ##  Map xProfile data over to MemberPress Data
// ########################################################################################
add_action('xprofile_updated_profile', 'update_xprofile_to_wp');

function update_xprofile_to_wp($userId) {
    if ( current_user_can('edit_user',$userId) ) {
		// ############################################
		// ##  Read the xProfile Data
		// ############################################
		//$userId = 199;
		global $bp;

		$website = xprofile_get_field_data(2, $userId);
		$first_name = xprofile_get_field_data(3, $userId);
		$last_name = xprofile_get_field_data(4, $userId);
		
		$alsConnectionData = xprofile_get_field_data(6, $userId);
		$alsHowFoundData = xprofile_get_field_data(17, $userId);

		$alsConnectionOther = xprofile_get_field_data(16, $userId);
		$alsDiagnosisDate = xprofile_get_field_data(15, $userId);
		$alsForumEmail = xprofile_get_field_data(5, $userId);
		$alsHowFoundOther = xprofile_get_field_data(21, $userId);
		$alsWhereLive = xprofile_get_field_data(22, $userId);
		$alsImportantInfo = xprofile_get_field_data(23, $userId);

		echo $website . "<br/><br/>";
		echo $first_name . "<br/><br/>";
		echo $last_name . "<br/><br/>";
		print_r($alsConnectionData);
		echo "<br/><br/>";
		print_r($alsHowFoundData);
		echo "<br/><br/>";
		echo $alsConnectionOther . "<br/><br/>";
		echo $alsDiagnosisDate . "<br/><br/>";
		echo $alsForumEmail . "<br/><br/>";
		echo $alsHowFoundOther . "<br/><br/>";
		echo $alsWhereLive . "<br/><br/>";
		echo $alsImportantInfo . "<br/><br/>";
		
		
		// ############################################
		// ##  Map xProfile data over to Wordpress Data
		// ############################################	
		$alsConnection = array();
		if ('PALS (Person diagnosed w ALS)' == $alsConnectionData)
		{ $alsConnection[] = "pals";}
		if ('CALS (Caregiver/Spouse/Partner)' == $alsConnectionData)
		{$alsConnection = "cals";}
		if ('FALS (Family or friend of PALS)' == $alsConnectionData)
		{$alsConnection = "fals";}
		if ('MP (Medical Professional)' == $alsConnectionData)
		{$alsConnection = "mp";}
		if ('Other  (Please add explanation below.)' == $alsConnectionData)
		{$alsConnection = "other";}

		$alsHowFound = array();
		if (in_array('Internet Search', $alsHowFoundData, true)) {
			$alsHowFound["internet"] = "on";
		}
		if (in_array('Referred by Friend', $alsHowFoundData, true)) {
			$alsHowFound["friend"] = "on";
		}
		if (in_array('Other (Please add in explanation below.)', $alsHowFoundData, true)) {
			$alsHowFound["otherhow"] = "on";
		}
		
		$a = serialize($alsHowFound);
		$b = serialize($alsConnection);

		echo "test  " . $a . "<br/><br/>";
		echo "test  " . $b . "<br/><br/>";

		update_user_meta ($userId, 'first_name', $first_name);
		update_user_meta ($userId, 'last_name', $last_name);
		update_user_meta ($userId, 'dislpay_name', $first_name . " " . $last_name);
		update_user_meta( $userId, 'mepr_how_did_you_find_out_about_us', $alsHowFound);
		update_user_meta( $userId, 'mepr_connection_with_als', $alsConnection);

		update_user_meta( $userId, 'mepr_other_connection_with_als', $alsConnectionOther);
		update_user_meta( $userId, 'mepr_diagnosis_date', $alsDiagnosisDate);
		update_user_meta( $userId, 'mepr_other_location_on_how_you_found_out_about_us', $alsHowFoundOther);
		update_user_meta( $userId, 'mepr_where_do_you_live_or_are_located', $alsWhereLive);
		update_user_meta( $userId, 'mepr_what_are_the_most_important_things_you_are_looking_for_from_this_membership_site', $alsImportantInfo);
		
		wp_update_user(array( 'ID' => $userId, 'user_email' => $alsForumEmail ));
		wp_update_user( array( 'ID' => $userId, 'user_url' => $website ) );
	}
}
?>