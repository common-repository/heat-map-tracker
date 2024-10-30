<?php
/*
 * Spy Analytics
 * (c) 2013. Aleksej Sytnik
 */
?>
<?php
if(!is_user_logged_in() && current_user_can('manage_options')) die("Only admin can access this section");
if (isset($_POST['viber_action']) && $_POST['viber_action'] == 'save') {
	$option = $this->OPTIONS;
	$option['opt_record_status'] 		= ($_POST['opt_record_status'])?true:false;
	$option['opt_record_all'] 			= $_POST['opt_record_all'];
	if($option['opt_record_all'] == "false")
		$option['opt_record_special'] 	= $_POST['opt_record_special'];
	else
		$option['opt_record_special'] 	= array();
	$option['opt_record_mousemove'] 	= $_POST['opt_record_mousemove'];
	$option['opt_record_pagescroll'] 	= $_POST['opt_record_pagescroll'];
	$option['opt_record_interval'] 		= $_POST['opt_record_interval'];
	$option['opt_record_kill_session'] 	= $_POST['opt_record_kill_session'];
	$option['opt_record_user'] 			= $_POST['opt_record_user'];
	if(isset($_POST['opt_record_tz']) && $_POST['opt_record_tz'] != "-1")
		$option['opt_record_tz'] 			= $_POST['opt_record_tz'];
	update_option($this->OPTION_NAME, $option);
	echo "Settings saved";
}
?>