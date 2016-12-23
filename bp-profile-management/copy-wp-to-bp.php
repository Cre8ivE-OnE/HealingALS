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
    // ##  Read the message sent by the MemberPress
	// ############################################
	$userId = 199; //$message['data']['id'];
	
	$currentUser = get_userdata($userId);
	$website = $currentUser->user_url;
	$first_name = get_user_meta ($userId, 'first_name', true);
	$last_name = get_user_meta ($userId, 'last_name', true);
	
	$alsConnectionOther = get_user_meta ($userId, 'mepr_other_connection_with_als', true);
	$alsDiagnosisDate = get_user_meta ($userId, 'mepr_diagnosis_date', true);
	$alsHowFoundOther = get_user_meta ($userId, 'mepr_other_location_on_how_you_found_out_about_us', true);
	$alsWhereLive = get_user_meta ($userId, 'mepr_where_do_you_live_or_are_located', true);
	$alsImportantInfo = get_user_meta ($userId, 'mepr_what_are_the_most_important_things_you_are_looking_for_from_this_membership_site', true);
	
	$current_user = get_userdata($userId);	
	$alsForumEmail = $current_user->user_email;
	
	
	echo $first_name . "<br/><br/>";
	echo $last_name . "<br/><br/>";
	echo $alsConnectionOther . "<br/><br/>";
	echo $alsDiagnosisDate . "<br/><br/>";
	echo $alsForumEmail . "<br/><br/>";
	echo $alsHowFoundOther . "<br/><br/>";
	echo $alsWhereLive . "<br/><br/>";
	echo $alsImportantInfo . "<br/><br/>";
	echo $website . "<br/><br/>";
	
	$allMeta = get_user_meta( $userId );
	echo "<br/><br/>";
	print_r($allMeta);
	echo "<br/><br/>";
	
	// ############################################
    // ##  Map Memberpress data over to xProfile Data
	// ############################################	
	$alsConnectionData = $allMeta['mepr_connection_with_als'][0];
	$alsConnection = array();
	if (strpos($alsConnectionData, 'pals') !== false) 
	{$alsConnection[] = "PALS";}
	if (strpos($alsConnectionData, 'pcst') !== false) 
	{$alsConnection[] = "PALS Team (Core Support Team - inner circle advisor to a PALS)";}
	if (strpos($alsConnectionData, 'care') !== false) 
	{$alsConnection[] = "Caregiver (more than 10 hours per week)";}
	if (strpos($alsConnectionData, 'fals') !== false) 
	{$alsConnection[] = "FALS";}
	if (strpos($alsConnectionData, 'mp') !== false) 
	{$alsConnection[] = "Medical Professional";}
	if (strpos($alsConnectionData, 'ond') !== false) 
	{$alsConnection[] = "OND (Other neurological diagnosis)";}
	if (strpos($alsConnectionData, 'snd') !== false) 
	{$alsConnection[] = "SND (Neurological symptoms, but no diagnosis)";}
	if (strpos($alsConnectionData, 'other') !== false) 
	{$alsConnection[] = "Other (Please add in explanation below.)";}

	$alsHowFoundData = $allMeta['mepr_how_did_you_find_out_about_us'][0];
	$alsHowFound = array();
	if (strpos($alsHowFoundData, 'internet') !== false) 
	{$alsHowFound[] = "Internet Search";}
	if (strpos($alsHowFoundData, 'friend') !== false) 
	{$alsHowFound[] = "Referred by Friend";}
	if (strpos($alsHowFoundData, 'otherhow') !== false) 
	{$alsHowFound[] = "Other (Please add in explanation below.)";}

//$a = serialize($alsHowFound);
//$b = serialize($alsConnection);
	//print_r($a);
	//echo "<br/><br/>";
print_r($alsConnectionData);
echo "<br/><br/>";
print_r($alsHowFoundData);
echo "<br/><br/>";
	
	
	
	//$results = wp_update_user(array( 'ID' => $userId, 'user_url' => 'http://google.com' ));
	
	global $bp;	
	echo "xProfile Data:<br/><br/>";
	$meh1 = xprofile_get_field_data(17, $userId);
	print_r($meh1);

//Array mappings
//xprofile_set_field_data(6, $userId,  $alsConnection);
//xprofile_set_field_data(17, $userId,  $alsHowFound);

//xprofile_set_field_data(16, $userId,  $alsConnectionOther);
//xprofile_set_field_data(15, $userId,  $alsDiagnosisDate);
//xprofile_set_field_data(5, $userId,  $alsForumEmail);
//xprofile_set_field_data(21, $userId,  $alsHowFoundOther);
//xprofile_set_field_data(22, $userId,  $alsWhereLive);
//xprofile_set_field_data(23, $userId,  $alsImportantInfo);

//$custom_field = xprofile_get_field_data(15, $userId);
//echo "<br/><br/>Diagnosis Date : " . $custom_field;

?>