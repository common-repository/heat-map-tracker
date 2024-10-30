<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker
 * http://heatmaptracker.com
 */
?>
<?php
//secure check $_POST variables
$_POST["user"] = ViberFN::viber_secure($_POST["user"]);

//save spy data to db
if(isset($_POST['user'])){
    	
    $option = $this->OPTIONS;
	global $wpdb;
		

//if(isset($this -> OPTIONS['opt_record_tz'])) date_default_timezone_set($this -> OPTIONS['opt_record_tz']);
		
//stack the data
$sessions = array();
$click_arr = array();
$mmove_arr = array();
$scroll_arr = array();
$popular_arr = array();
if(isset($_POST['data'])) {
	$data = json_decode(base64_decode($_POST['data']));
	foreach ($data as $key => $value) {
		
		if(!isset($sessions[$key]["time"])) $sessions[$key]["time"] = 0;
		
		foreach ($value as $kk => $vv) {//pages lvl
			
			$scroll_arr[$kk]["height"] = 0;
			$scroll_arr[$kk]["maxscroll"] = 0;
			$popular_arr[$kk] = $option["opt_record_interval"];
			foreach ($vv as $kkk => $vvv) {//event lvl
				foreach ($vvv as $kkkk => $vvvv) {//events arr lvl
					if($kkk != "responsetive")
						if($sessions[$key]["time"] < (int)($vvvv[0])) $sessions[$key]["time"] = (int)($vvvv[0]);
					if($kkk == "mouse_click"){
						if(isset($click_arr[$kk]))
								$click_arr[$kk] .= "|".$vvvv[2]." ".$vvvv[3]." ".$vvvv[6];
							else
								$click_arr[$kk] = "|".$vvvv[2]." ".$vvvv[3]." ".$vvvv[6];
					}
					if($kkk == "mouse_move"){
						if(isset($mmove_arr[$kk]))
								$mmove_arr[$kk] .= "|".$vvvv[1]." ".$vvvv[2]." ".$vvvv[3];
							else
								$mmove_arr[$kk] = "|".$vvvv[1]." ".$vvvv[2]." ".$vvvv[3];
					}
					if($kkk == "page_scroll"){
						if($scroll_arr[$kk]["maxscroll"] < $vvvv[1]) $scroll_arr[$kk]["maxscroll"] = $vvvv[1];
					}
					if($kkk == "window_size"){
						if($scroll_arr[$kk]["height"] < $vvvv[1]) $scroll_arr[$kk]["height"] = $vvvv[1];
					}
				}
			}
			$sessions[$key]['data'][]=array($kk=>$vv);
		}
	}
}
		//put clicks to DB
		$table2 = $wpdb->prefix.$option['dbtable_name_clicks'];
		foreach ($click_arr as $key => $value) {
		
			$clicks = $wpdb->get_row("SELECT * FROM $table2 WHERE `page_url` = '$key' ORDER BY `id` DESC LIMIT 1");
			if(!$clicks){
				$q = "INSERT INTO `".$table2."` (`page_url`,`click_data`,`date`) VALUES ('".$key."','".$value."', NOW())";
				$wpdb->query($q);
			} else {
				if($clicks->click_data != ""){
					$clickStr = $clicks->click_data;
				}
				if(strlen($clickStr) > 600 || date("m.d.y") != date("m.d.y", strtotime($clicks->date))){
					$q = "INSERT INTO `".$table2."` (`page_url`,`click_data`,`date`) VALUES ('".$key."','".$value."', NOW())";
					$wpdb->query($q);
				} else {
					$clickStrMerged = $clickStr.$value;
					$q = "UPDATE `".$table2."` SET  `click_data` = '".$clickStrMerged."' WHERE `page_url` = '".$key."' ORDER BY `id` DESC LIMIT 1 ";
					$wpdb->query($q);
				}
			}
		}
		
		//put mmove to DB
		$table3 = $wpdb->prefix.$option['dbtable_name_mmove'];
		foreach ($mmove_arr as $key => $value) {
		
			$clicks = $wpdb->get_row("SELECT * FROM $table3 WHERE `page_url` = '$key' ORDER BY `id` DESC LIMIT 1");
			if(!$clicks){
				$q = "INSERT INTO `".$table3."` (`page_url`,`mmove_data`,`date`) VALUES ('".$key."','".$value."', NOW())";
				$wpdb->query($q);
			} else {
				if($clicks->mmove_data != ""){
					$clickStr = $clicks->mmove_data;
				}
				if(strlen($clickStr) > 600 || date("m.d.y") != date("m.d.y", strtotime($clicks->date))){
					$q = "INSERT INTO `".$table3."` (`page_url`,`mmove_data`,`date`) VALUES ('".$key."','".$value."', NOW())";
					$wpdb->query($q);
				} else {
					$clickStrMerged = $clickStr.$value;
					$q = "UPDATE `".$table3."` SET  `mmove_data` = '".$clickStrMerged."' WHERE `page_url` = '".$key."' ORDER BY `id` DESC LIMIT 1 ";
					$wpdb->query($q);
				}
			}
		}
}//end if
?>