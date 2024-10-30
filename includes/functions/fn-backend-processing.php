<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker
 * http://heatmaptracker.com
 */
?>
<?php
//when we generate js
if(isset($_GET["viberjs"])){
    require_once(dirname(__FILE__).'/fn-js-processing.php');
	die();
}
//when we get data from js
if(isset($_GET["viberdata"])){
    require_once(dirname(__FILE__).'/fn-data-processing.php');
	die();
}
//heatmaps interface
if(isset($_GET["viberheatmap"])){
	if(!is_user_logged_in() && current_user_can('manage_options')) die("Only admin can access this section");
	wp_enqueue_script( 'jquery' );
	add_action( 'admin_print_styles', array(&$this,'includeCSS'));
	add_action( 'admin_print_scripts', array(&$this,'includeJS'));
    require_once(dirname(__FILE__).'/../markup/mk-heatmap-page.php');
	die();
}
//save settings
if(isset($_GET["vibersettings"])){
	if(!is_user_logged_in() && current_user_can('manage_options')) die("Only admin can access this section");
	require_once(dirname(__FILE__).'/fn-settings-processing.php');
	die();
}
?>