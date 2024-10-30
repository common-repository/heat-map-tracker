<?php
/*
 Plugin Name: HeatMapTracker Lite
 Plugin URI: http://heatmaptracker.com
 Description: Analyze your visitors via Mouse Click and Mouse Move Heat Maps
 Author: HeatMapTracker
 Author URI: http://heatmaptracker.com
 Version: 1.0.96
 */
if(!isset($_GET['viberdebug']))
error_reporting(E_ERROR);
if (!function_exists('add_action')) {
	echo 'Can\'t be called directly';
	exit ;
}

if (!class_exists('ViberPro_class')) {
	class ViberPro_class {

		var $PLUGIN_URL;
		var $PLUGIN_PATH;
		var $OPTION_NAME;
		var $OPTIONS;
		var $VIBER_INIT;
		var $MAIN_STR;
		var $MEMORY_ERROR;
		var $UpdateChecker;

		public function __construct() {
			$this -> PLUGIN_URL = plugin_dir_url( __FILE__ );
			$this -> PLUGIN_PATH = plugin_dir_path( __FILE__ );
			$this -> PLUGIN_API = admin_url('admin-ajax.php');
			$this -> OPTION_NAME = "viberspy_lite_plugin";
			$this -> OPTIONS = get_option($this -> OPTION_NAME);
			$this->MEMORY_ERROR = false;
			$GLOBALS["ViberPro_PLUGIN_PATH"] = $this -> PLUGIN_PATH;
			$GLOBALS["ViberPro_PLUGIN_URL"] = $this -> PLUGIN_URL;
			$GLOBALS["ViberPro_OPTION_NAME"] = $this -> OPTION_NAME;
		}

		#-------------------------------------------------------------------------------------------
		public function viberspy_install() {#install plugin
			#-------------------------------------------------------------------------------------------
			$option = $this -> OPTIONS;
			if (false === $option ) {
				$option = array();
				$option['version'] = "1.0.96";
				$option['dbtable_name'] = "hmaptracker";
				$option['dbtable_name_clicks'] = "hmaptracker_clicks";
				$option['dbtable_name_mmove'] = "hmaptracker_mmove";
				$option['opt_record_status'] = true;
				$option['opt_record_all'] = "true";
				$option['opt_record_special'] = array();
				$option['opt_record_mousemove'] = true;
				$option['opt_record_pagescroll'] = true;
				$option['opt_record_interval'] = 1;
				$option['opt_record_kill_session'] = 100;
				$option['opt_record_user'] = '-1';
				$option['opt_record_tz'] = date_default_timezone_get();

				add_option($this -> OPTION_NAME, $option);

				global $wpdb;

				$table_click = $wpdb -> prefix . $option['dbtable_name_clicks'];
				$structure2 = "CREATE TABLE IF NOT EXISTS $table_click (
						      id int(99) NOT NULL AUTO_INCREMENT,
						      date DATE NOT NULL,
						      page_url VARCHAR(500) DEFAULT '' NOT NULL,
						      click_data text,
						      UNIQUE KEY id (id),
						      KEY page_url (page_url),
  							  KEY date (`date`)
						    );";
				$wpdb -> query($structure2);

				$table_mmove = $wpdb -> prefix . $option['dbtable_name_mmove'];
				$structure3 = "CREATE TABLE IF NOT EXISTS $table_mmove (
						      id int(99) NOT NULL AUTO_INCREMENT,
						      date DATE NOT NULL,
						      page_url VARCHAR(500) DEFAULT '' NOT NULL,
						      mmove_data text,
						      UNIQUE KEY id (id),
						      KEY page_url (page_url),
  							  KEY date (`date`)
						    );";
				$wpdb -> query($structure3);

			}
		}

		#-------------------------------------------------------------------------------------------
		public function viberspy_uninstaller() {	#uninstall plugin
			#---------------------------------------------------------------------------------------
		}

		#-------------------------------------------------------------------------------------------
		public function viberspy_reinstall() {	#uninstall plugin
		#----------------------------------------------------------------------------
			
		}

		#-------------------------------------------------------------------------------------------
		public function build_analytics_menu() {	#build admin menu
			#-------------------------------------------------------------------------------------------
			$page = add_menu_page(__('HMapTracker', 'spy-main'), __('HMapTracker', 'spy-main'), 'manage_options', 'heatmaptracker', array(&$this,'bootPage'), 'div');
			wp_register_style('viberspy_style', $this -> PLUGIN_URL . 'css/style.css');
			wp_enqueue_style('viberspy_style');
		}

		#-------------------------------------------------------------------------------------------
		public function includeJS() {				#add files
			#-------------------------------------------------------------------------------------------
			global $wp_version;
			if ($wp_version <= 3.2) { 
			$prnt = "<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>";
			
			} else {
			$prnt = "<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js'></script>";
			}
			$prnt .= '<script type="text/javascript" src="'.$this -> PLUGIN_URL . 'js/bootstrap-datepicker.js"></script>';
			$prnt .= '<script type="text/javascript" src="'.$this -> PLUGIN_URL . 'js/jquery.flot.js"></script>';
			$prnt .= '<script type="text/javascript" src="'.$this -> PLUGIN_URL . 'js/jquery.flot.pie.js"></script>';
			$prnt .= '<script type="text/javascript" src="'.$this -> PLUGIN_URL . 'js/heatmap.js"></script>';
			$prnt .= '<script type="text/javascript" src="'.$this -> PLUGIN_URL . 'js/adminscripts.js"></script>';
			$prnt .= '<script type="text/javascript" src="'.$this -> PLUGIN_URL . 'js/bootstrap.min.js"></script>';
			echo $prnt;
		}

		#-------------------------------------------------------------------------------------------
		public function includeCSS() {				#add files
			#-------------------------------------------------------------------------------------------
			
			$prnt = '<link rel="stylesheet" type="text/css" media="all" href="'.$this -> PLUGIN_URL . 'css/style.css" />';
			$prnt .= '<link rel="stylesheet" type="text/css" media="all" href="'.$this -> PLUGIN_URL . 'css/flags.css" />';
			$prnt .= '<link rel="stylesheet" type="text/css" media="all" href="'.$this -> PLUGIN_URL . 'css/bootstrap.css" />';
			$prnt .= '<link rel="stylesheet" type="text/css" media="all" href="'.$this -> PLUGIN_URL . 'css/datepicker.css" />';
			$prnt .= '<link rel="stylesheet" type="text/css" media="all" href="'.$this -> PLUGIN_URL . 'css/adminstyles.css" />';
			echo $prnt;
		}

		#-------------------------------------------------------------------------------------------
		public function includePlayerCSS() {#add files
			#-------------------------------------------------------------------------------------------
			$this->includeCSS();
			echo '<link rel="stylesheet" type="text/css" media="all" href="'.$this -> PLUGIN_URL . 'css/player.css" />';
			
		}

		
		#-------------------------------------------------------------------------------------------
		public function bootPage() {#settings page
			#-------------------------------------------------------------------------------------------
			?>
			<iframe src="<?php echo admin_url() ?>?viberboot" style="width: 98%;"  scrolling="no" ></iframe>
			<?php
		}
		#-------------------------------------------------------------------------------------------
		public function adminViberspy() {
			#-------------------------------------------------------------------------------------------
			$this->viberspy_reinstall();
			if(isset($_GET["viberboot"])){
				require_once (dirname(__FILE__) . '/includes/markup/mk-boot-page.php');
				die();
			}
			if(isset($_GET["vibervideos"])){
				require_once (dirname(__FILE__) . '/includes/markup/mk-videos-page.php');
				die();
			}
			
		}
		#-------------------------------------------------------------------------------------------
		public function backendSpy() {#spy logic
			#-------------------------------------------------------------------------------------------
			$spyPlayerUrl = $this -> PLUGIN_URL . 'css/player.css';
			wp_register_style('spyViewSheets', $spyPlayerUrl);
			require_once (dirname(__FILE__) . '/includes/functions/fn-backend-processing.php');
			return true;
		}
		#-------------------------------------------------------------------------------------------
		public function spyHeatmap() {#heatmap page
			#-------------------------------------------------------------------------------------------
			require_once (dirname(__FILE__) . '/includes/markup/mk-heatmap-page.php');
			$this->includeCss();
		}
		#-------------------------------------------------------------------------------------------
		public function frontendSpy() {#spy logic
			#-------------------------------------------------------------------------------------------
			print '<script type="text/javascript" src="'. $this -> PLUGIN_API .'?viberjs=&i=' . get_the_ID() . '"></script>';
		}

		#-------------------------------------------------------------------------------------------
		public function init() {#spy logic
			#-------------------------------------------------------------------------------------------
			global $wp_version;
			require_once (dirname(__FILE__).'/includes/functions/fn-functions.php');
			if(isset($this -> OPTIONS['opt_record_tz'])) date_default_timezone_set($this -> OPTIONS['opt_record_tz']);
			#register hooks
			register_activation_hook(__FILE__, array(&$this,'viberspy_install'));
			register_deactivation_hook(__FILE__, array(&$this,'viberspy_uninstaller'));
			add_action('init', array(&$this,'backendSpy'));
			#build admin menu
			add_action('admin_menu', array(&$this,'build_analytics_menu'));
			#run main logic
			add_action('wp_head', array(&$this,'frontendSpy'));
			add_action('admin_init', array(&$this,'adminViberspy'));
		}

	}
	$_ViberPro_class = new ViberPro_class();
	$_ViberPro_class->init();
	
}
?>