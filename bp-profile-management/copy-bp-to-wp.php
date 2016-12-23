<?php
	// ############################################
    // ##  Load Wordpress Framework
	// ############################################
	# No need for the template engine
	define( 'WP_USE_THEMES', false );
	# Load WordPress Core
	// Assuming we're in a subdir: "~/wp-content/plugins/current_dir"
	require_once( '../wp-load.php' );
	
	// ############################################
    // ##  Read the xProfile Data
	// ############################################
    $userId = 199;
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
    // ##  Map Memberpress data over to xProfile Data
	// ############################################	
	$alsConnection = array();
	if (in_array('PALS', $alsConnectionData, true))
	{ $alsConnection["pals"] = "on";}
	if (in_array('PALS Team (Core Support Team - inner circle advisor to a PALS)', $alsConnectionData, true))
	{$alsConnection["pcst"] = "on";}
	if (in_array('Caregiver (more than 10 hours per week)', $alsConnectionData, true))
	{$alsConnection["care"] = "on";}
	if (in_array('FALS', $alsConnectionData, true))
	{$alsConnection["fals"] = "on";}
	if (in_array('Medical Professional', $alsConnectionData, true))
	{$alsConnection["mp"] = "on";}
	if (in_array('OND (Other neurological diagnosis)', $alsConnectionData, true))
	{$alsConnection["ond"] = "on";}
	if (in_array('SND (Neurological symptoms, but no diagnosis)', $alsConnectionData, true))
	{$alsConnection["snd"] = "on";}
	if (in_array('Other (Please add in explanation below.)', $alsConnectionData, true))
	{$alsConnection["other"] = "on";}

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


	update_user_meta( $userId, 'mepr_how_did_you_find_out_about_us', $alsHowFound);
	update_user_meta( $userId, 'mepr_connection_with_als', $alsConnection);

	update_user_meta( $userId, 'mepr_other_connection_with_als', $alsConnectionOther);
	update_user_meta( $userId, 'mepr_diagnosis_date', $alsDiagnosisDate);
	update_user_meta( $userId, 'mepr_other_location_on_how_you_found_out_about_us', $alsHowFoundOther);
	update_user_meta( $userId, 'mepr_where_do_you_live_or_are_located', $alsWhereLive);
	update_user_meta( $userId, 'mepr_what_are_the_most_important_things_you_are_looking_for_from_this_membership_site', $alsImportantInfo);
	
	wp_update_user(array( 'ID' => $userId, 'user_email' => $alsForumEmail ));
	wp_update_user( array( 'ID' => $userId, 'user_url' => $website ) );
?>