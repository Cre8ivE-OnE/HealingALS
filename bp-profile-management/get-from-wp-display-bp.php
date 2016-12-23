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
	$userId = 199;
	
	$currentUser = get_userdata($userId);
	$website = $currentUser->user_url;
	$first_name = get_user_meta ($userId, 'first_name', true);
	$last_name = get_user_meta ($userId, 'last_name', true);
	$alsForumEmail = $currentUser->user_email;
	
	$allMeta = get_user_meta( $userId );
	$alsConnectionData = $allMeta['mepr_connection_with_als'][0];
	$alsConnectionOther = get_user_meta ($userId, 'mepr_other_connection_with_als', true);
	$alsDiagnosisDate = get_user_meta ($userId, 'mepr_diagnosis_date', true);
	$alsHowFoundData = $allMeta['mepr_how_did_you_find_out_about_us'][0];
	$alsHowFoundOther = get_user_meta ($userId, 'mepr_other_location_on_how_you_found_out_about_us', true);
	$alsWhereLive = get_user_meta ($userId, 'mepr_where_do_you_live_or_are_located', true);
	$alsImportantInfo = get_user_meta ($userId, 'mepr_what_are_the_most_important_things_you_are_looking_for_from_this_membership_site', true);
		
	
		
	echo "WORDPRESS VALUES:::";
	echo "<br/><br/>";
	echo "####################################################";
	echo "<br/><br/>";
	echo $first_name . "<br/><br/>";
	echo $last_name . "<br/><br/>";
	echo $alsForumEmail . "<br/><br/>";
	echo $alsConnectionData . "<br/><br/>";
	echo $alsConnectionOther . "<br/><br/>";
	echo $alsDiagnosisDate . "<br/><br/>";
	echo $alsHowFoundData . "<br/><br/>";
	echo $alsHowFoundOther . "<br/><br/>";
	echo $alsWhereLive . "<br/><br/>";
	echo $alsImportantInfo . "<br/><br/>";
	echo $website . "<br/><br/>";
	
	print_r($allMeta);
	echo "<br/><br/>";
	
	// ############################################
    // ##  Map Memberpress data over to xProfile Data
	// ############################################	
	if (strpos($alsConnectionData, 'pals') !== false) 
	{$alsConnection = "PALS (Person diagnosed w ALS)";}
	if (strpos($alsConnectionData, 'cals') !== false) 
	{$alsConnection = "CALS (Caregiver/Spouse/Partner)";}
	if (strpos($alsConnectionData, 'fals') !== false) 
	{$alsConnection = "FALS (Family/Friend)";}
	if (strpos($alsConnectionData, 'mp') !== false)  
	{$alsConnection = "MP (Medical Professional)";}
	if (strpos($alsConnectionData, 'other') !== false) 
	{$alsConnection = "Other  (Please add explanation below.)";}

	$alsHowFound = array();
	if (strpos($alsHowFoundData, 'internet') !== false) 
	{$alsHowFound[] = "Internet Search";}
	if (strpos($alsHowFoundData, 'friend') !== false) 
	{$alsHowFound[] = "Referred by Friend";}
	if (strpos($alsHowFoundData, 'otherhow') !== false) 
	{$alsHowFound[] = "Other (Please add in explanation below.)";}

	echo "<br/><br/>";
	echo "BUDDYPREE VALUES:::";
	echo "<br/><br/>";
	echo "####################################################";
	echo "<br/><br/>";
	//$a = serialize($alsHowFound);
	//$a = serialize($alsConnection);
	//print_r($a);
	//echo "<br/><br/>";
		
	print_r($alsConnection);
	echo "<br/><br/>";
	print_r($alsHowFound);
	echo "<br/><br/>";
		
	global $bp;	
	$meh1 = xprofile_get_field_data(6, $userId);
	echo "xProfile Data: ";
	print_r($meh1);	
	echo "<br/><br/>";
	$meh2 = xprofile_get_field_data(17, $userId);
	echo "xProfile Data: ";
	print_r($meh2);

?>