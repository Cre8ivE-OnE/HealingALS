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
		// $userId = $message['data']['id'];
		global $bp;

		$website = xprofile_get_field_data(2, $userId);
		$first_name = xprofile_get_field_data(3, $userId);
		$last_name = xprofile_get_field_data(4, $userId);
		$alsForumEmail = xprofile_get_field_data(5, $userId);
		$alsConnectionData = xprofile_get_field_data(6, $userId);
		$alsConnectionOther = xprofile_get_field_data(16, $userId);
		$alsDiagnosisDate = xprofile_get_field_data(15, $userId);
		$alsHowFoundData = xprofile_get_field_data(17, $userId);
		$alsHowFoundOther = xprofile_get_field_data(21, $userId);
		$alsWhereLive = xprofile_get_field_data(22, $userId);
		$alsImportantInfo = xprofile_get_field_data(23, $userId);

		echo "BUDDYPREE VALUES:::";
		echo "<br/><br/>";
		echo "####################################################";
		echo "<br/><br/>";
		echo $website . "<br/><br/>";
		echo $first_name . "<br/><br/>";
		echo $last_name . "<br/><br/>";
		echo $alsForumEmail . "<br/><br/>";
		echo $alsConnectionData . "<br/><br/>";
		echo $alsConnectionOther . "<br/><br/>";
		echo $alsDiagnosisDate . "<br/><br/>";
		print_r($alsHowFoundData); echo "<br/><br/>";
		echo $alsHowFoundOther . "<br/><br/>";
		echo $alsWhereLive . "<br/><br/>";
		echo $alsImportantInfo . "<br/><br/>";
		
		
		// ############################################
		// ##  Map Memberpress data over to xProfile Data
		// ############################################	
		$alsConnection = array();
		if ('PALS (Person diagnosed w ALS)' == $alsConnectionData)
		{ $alsConnection[] = "pals";}
		if ('CALS (Caregiver/Spouse/Partner)' == $alsConnectionData)
		{$alsConnection = "cals";}
		if ('FALS (Family/Friend)' == $alsConnectionData)
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
			
		echo "WORDPRESS VALUES:::";
		echo "<br/><br/>";
		echo "####################################################";
		echo "<br/><br/>";
		$a = serialize($alsConnection);
		$b = serialize($alsHowFound);
		print_r($alsConnection); echo "<br/><br/>";
		print_r($alsHowFound); echo "<br/><br/>";
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	?>