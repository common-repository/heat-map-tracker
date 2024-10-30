<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker
 * http://heatmaptracker.com
 */
if(!isset($_GET['viberdebug']))
error_reporting(E_ERROR);
if(!is_user_logged_in() && current_user_can('manage_options')) die("Only admin can access this section");
?>
<!doctype html>
<html lang="en">
<head>
<title>HeatMapTracker View</title>
<?php
	$this->includeCSS();
	$this->includeJS();
	$option = $this -> OPTIONS;
?>
<style>
	body{
		padding: 0;
		margin: 0;
		font-family: "HelveticaNeue-Light","Helvetica Neue Light","Helvetica Neue",sans-serif !important;
		font-size: 13px;
	}
</style>
</head>
<body>
<script type="text/javascript">
	function alerter (text, type) {
		var cls = ""
		switch (type){
			case 1: cls = "alert-error";
				break;
			case 2:  cls = "alert-success";
				break;
			case 3:  cls = "alert-info";
				break;
		}
		
		setTimeout(function () { jQuery("iframe", top.document).height(jQuery(document).height()) }, 100);
		return jQuery('<div class="alert alert-block '+cls+' fade in"><button type="button" class="close" data-dismiss="alert">&times;</button>'+text+'</div>');
	}
	jQuery(document).ready(function(){
		
		jQuery("iframe", top.document).height(jQuery(document).height());
		//alerts
		jQuery(".alert .close").click(function(){jQuery(this).parent().hide()});
		
		//popover
		jQuery('.help-ico').popover({'placement': 'right'});
		jQuery('.url-info').popover({'placement': 'right'});
		
		//onoff buttons
		jQuery('.onoff button').click(function(){
			if(!jQuery(this).hasClass("active")){
				if(jQuery(this).hasClass("btn-on")){
					jQuery(this).addClass("btn-success")
					jQuery(this).parent().find(".btn-off").removeClass("btn-danger");
				}
				if(jQuery(this).hasClass("btn-off")){
					jQuery(this).addClass("btn-danger")
					jQuery(this).parent().find(".btn-on").removeClass("btn-success");
				}
			}
		})
		
		//all pages/special page buttons
		jQuery('.all-special button').click(function(){
			if(!jQuery(this).hasClass("active")){
				jQuery(this).parent().find("button").removeClass("btn-success");
				jQuery(this).addClass("btn-success")
				
				if(jQuery(this).hasClass("btn-special")){
					jQuery(".pagesposts").removeAttr("disabled");
				} else {
					jQuery(".pagesposts").attr("disabled","");
				}
			}
		})
		
		//save settings
		jQuery('.save-button').click(function(){
			//validating
			var pagepostErr = (jQuery('.opt_record_all').hasClass("active"))?false:jQuery('.opt_record_special').val() == null;
			if(pagepostErr){
				jQuery(".form-alerter").append(alerter("Please, choose at least one page or post",1));
				jQuery('.opt_record_special').focus();
				return false;
			}

			//post var
			var post = {};
			post.opt_record_status = 		jQuery('.opt_record_status .active').attr("data-value");
			post.opt_record_all =			jQuery('.opt_record_all').hasClass("active");
			post.opt_record_special =		(jQuery('.opt_record_all').hasClass("active"))?"":jQuery('.opt_record_special').val();
			post.opt_record_mousemove =		jQuery('.opt_record_mousemove .active').attr("data-value");
			post.opt_record_pagescroll =	jQuery('.opt_record_pagescroll .active').attr("data-value");
			post.opt_record_interval =		jQuery('.opt_record_interval').val();
			post.opt_record_kill_session =	jQuery('.opt_record_kill_session').val();
			post.opt_record_user =			jQuery('.opt_record_user').val();
			post.opt_record_tz =			jQuery('.opt_record_tz').val();
			post.viber_action = "save";
						
			//sending
        	jQuery(this).button('loading')
        	jQuery.post('<?php echo $this -> PLUGIN_API ?>/?vibersettings',post, function(data) {
				jQuery(this).button('reset');
				jQuery(".form-alerter").append(alerter(data,3));
				jQuery('.save-button').button('reset');
			});

		})
		
		//heatmap urls
		jQuery('.heat-urls').change(function(){
			if(!jQuery('.url-info').next().hasClass('popover') && !jQuery('.heat-urls').find(":selected").text() == ""){
				jQuery('.url-info').click();
			}
			if(jQuery('.url-info').next().hasClass('popover') && jQuery('.heat-urls').find(":selected").text() == ""){
				jQuery('.url-info').click();				
			}
			if(!jQuery('.heat-urls').find(":selected").text() == ""){
				
				if(jQuery('.url-info').next().hasClass('popover')){
      					jQuery('.url-info').next().find('.popover-title').html("Loading title..")
      					jQuery('.url-info').next().find('.popover-content').html("Loading description..")
      			}
				
				jQuery.get(jQuery('.heat-urls').find(":selected").val())
					.done(function(data) {
	
     					var matches = data.match(/<title>(.*?)<\/title>/);
      					var spUrlTitle = (matches!=null)?(matches[1].substr(0,40-1)+(matches[1].length>40?'..':'')):"No title";
     					var matches2 = data.match(/description="(.*?)"/);
      					var descr = (matches2!=null)?(matches2[1].substr(0,40-1)+(matches2[1].length>80?'..':'')):"No description";
      
      					if(jQuery('.url-info').next().hasClass('popover')){
      						jQuery('.url-info').next().find('.popover-title').html(spUrlTitle)
      						jQuery('.url-info').next().find('.popover-content').html(descr)
      					}
					})
					.fail(function() { 
						if(jQuery('.url-info').next().hasClass('popover')){
      						jQuery('.url-info').next().find('.popover-title').html("Can't load page'")
      					}
					})
			}
		});
		//datepickers
		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1; //January is 0!
		var yyyy = today.getFullYear();
		if(dd<10){dd='0'+dd} if(mm<10){mm='0'+mm} 
		var _today = yyyy+'-'+mm+'-'+dd;
		var __today = yyyy+'-'+mm+'-'+dd;
		//day
		var dayago  = new Date(today.getTime() - 1 * 24 * 60 * 60 * 1000);
		dd = dayago.getDate();
		mm = dayago.getMonth()+1; //January is 0!
		yyyy = dayago.getFullYear();
		if(dd<10){dd='0'+dd} if(mm<10){mm='0'+mm}
		var _dayago = yyyy+'-'+mm+'-'+dd;
		var __dayago = yyyy+'-'+mm+'-'+dd;
		//week
		var weekago  = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
		dd = weekago.getDate();
		mm = weekago.getMonth()+1; //January is 0!
		yyyy = weekago.getFullYear();
		if(dd<10){dd='0'+dd} if(mm<10){mm='0'+mm}
		var _weekago = yyyy+'-'+mm+'-'+dd;
		var __weekago = yyyy+'-'+mm+'-'+dd;
		//month
		var monthago  = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
		dd = monthago.getDate();
		mm = monthago.getMonth()+1; //January is 0!
		yyyy = monthago.getFullYear();
		if(dd<10){dd='0'+dd} if(mm<10){mm='0'+mm}
		var _monthago = yyyy+'-'+mm+'-'+dd;
		var __monthago = yyyy+'-'+mm+'-'+dd;
		
		var  _from =  _dayago,  _to =  _today;
		var __from = __dayago, __to = __today;
		
		jQuery('.from-date-heatmap').attr("data-date",_weekago).find("span").html(_weekago);
		jQuery('.to-date-heatmap').attr("data-date",_today).find("span").html(_today);
				
		jQuery('#dp4').datepicker()
				.on('changeDate', function(ev){
					jQuery(this).find("span").html(jQuery(this).data('date'));
					_from = jQuery(this).data('date');
					jQuery('#dp4').datepicker('hide');
				});
		jQuery('#dp5').datepicker()
				.on('changeDate', function(ev){
					jQuery(this).find("span").html(jQuery(this).data('date'));
					jQuery('#dp5').datepicker('hide');
					_to = jQuery(this).data('date');
		});
				
		jQuery(".btn-d-day").click(function(){		_from = _dayago;  _to = _today});
		jQuery(".btn-d-week").click(function(){		_from = _weekago;  _to = _today});
		jQuery(".btn-d-month").click(function(){	_from = _monthago ; _to = _today});
		
		var _map = "click";
		var _click_opts = jQuery('#click_urls').html();
		var _move_opts = jQuery('#move_urls').html();
		var _scroll_opts = jQuery('#scroll_urls').html();
		
		jQuery('#move_urls').remove();
		jQuery('#scroll_urls').remove();
		
		jQuery(".btn-h-click").click(function(){		_map = "click"; jQuery('.heat-urls').html("");  jQuery('.heat-urls').append('<optgroup label="By URL">'+_click_opts+'</optgroup>') });
		jQuery(".btn-h-move").click(function(){			_map = "mmove"; jQuery('.heat-urls').html("");  jQuery('.heat-urls').append('<optgroup label="By URL">'+_move_opts+'</optgroup>') });
		jQuery(".btn-h-scroll").click(function(){		_map = "scroll"; jQuery('.heat-urls').html("");  jQuery('.heat-urls').append('<optgroup label="By URL">'+_scroll_opts+'</optgroup>') });
		
		
		jQuery(".btn-d button").click(function(){
			setTimeout(function(){ 
				if(jQuery(".btn-d-range").hasClass("active")){
					jQuery(".date-range-buttons button").removeAttr("disabled");
				} else {
					jQuery(".date-range-buttons button").attr("disabled","");
				}
			},100);
		})
		//manage data
		jQuery('#dp6').datepicker()
				.on('changeDate', function(ev){
					jQuery(this).find("span").html(jQuery(this).data('date'));
					__from = jQuery(this).data('date');
					jQuery("#manage_data_form input[name$='from']").val(__from);
					jQuery('#dp6').datepicker('hide');
				});
		jQuery('#dp7').datepicker()
				.on('changeDate', function(ev){
					jQuery(this).find("span").html(jQuery(this).data('date'));
					jQuery('#dp7').datepicker('hide');
					__to = jQuery(this).data('date');
					jQuery("#manage_data_form input[name$='to']").val(__to);
		});
		
		jQuery(".btn-md button").click(function(){
			setTimeout(function(){ 
				if(jQuery(".btn-md-range").hasClass("active")){
					jQuery(".date-md-range-buttons button").removeAttr("disabled");
				} else {
					jQuery(".date-md-range-buttons button").attr("disabled","");
				}
			},100);
		})
		
		jQuery("#manage_data_form input[name$='from']").val(__from);
		jQuery("#manage_data_form input[name$='to']").val(__to);
		jQuery("#manage_data_form input[name$='what']").val("clicks");
		
		jQuery(".btn-md-day").click(function(){		__from = __dayago;  __to = __today
			jQuery("#manage_data_form input[name$='from']").val(__from);
		jQuery("#manage_data_form input[name$='to']").val(__to);
		});
		jQuery(".btn-md-week").click(function(){	__from = __weekago;  __to = __today
			
		jQuery("#manage_data_form input[name$='from']").val(__from);
		jQuery("#manage_data_form input[name$='to']").val(__to);});
		jQuery(".btn-md-month").click(function(){	__from = __monthago ; __to = __today
			
		jQuery("#manage_data_form input[name$='from']").val(__from);
		jQuery("#manage_data_form input[name$='to']").val(__to);
		});
				
		jQuery(".btn-sessions").click(function(){ 		jQuery("#manage_data_form input[name$='what']").val("sessions"); });
		jQuery(".btn-clicks").click(function(){			jQuery("#manage_data_form input[name$='what']").val("clicks"); });
		jQuery(".btn-eye").click(function(){ 			jQuery("#manage_data_form input[name$='what']").val("eye"); });
		jQuery(".btn-scroll").click(function(){ 		jQuery("#manage_data_form input[name$='what']").val("scroll"); });
		jQuery(".btn-popular").click(function(){		jQuery("#manage_data_form input[name$='what']").val("popular"); });
		
		//generate heatmap
		jQuery(".generate-heatmap").click(function(){
			//validate form
			if(jQuery('.heat-urls').find(":selected").val() == undefined){
				jQuery(".heat-alerter").append(alerter("Please, choose at least one page or post",1));
				jQuery('.heat-urls').focus();
				return false;
			}
			
			var get = {}
			get.url = jQuery('.heat-urls').find(":selected").val();
			get.from = _from;
			get.to = _to;
			get.map = _map;
			get.layout = jQuery(".btn-layout").find(".active").attr("data-value");
			
			jQuery("#heatmap_form input[name$='url']").val(get.url);
			jQuery("#heatmap_form input[name$='from']").val(get.from);
			jQuery("#heatmap_form input[name$='to']").val(get.to);
			jQuery("#heatmap_form input[name$='layout']").val(get.layout);
			jQuery("#heatmap_form input[name$='map']").val(get.map);
			jQuery("#heatmap_form input[name$='uniq']").val(Math.random());
			jQuery("#heatmap_form").submit();
			
		});
		jQuery("#loader").animate({ opacity: 0 }, 100);
		jQuery(".tabbable").animate({ opacity: 1 }, 100);
	
		//popular pages
		<?php
		global $wpdb;
		//manage data
		if(isset($_POST['manage']) && $_POST['manage'] == "tables"){
			
			switch($_POST['what']){
				case 'clicks':
					$table2 = $wpdb->prefix.$option['dbtable_name_clicks'];
					$wpdb->get_results("DELETE FROM $table2 WHERE  date >= '$_POST[from]' AND date <= '$_POST[to]'");
					break;
				case 'eye':
					$table2 = $wpdb->prefix.$option['dbtable_name_mmove'];
					$wpdb->get_results("DELETE FROM $table2 WHERE  date >= '$_POST[from]' AND date <= '$_POST[to]'");
					break;
			}
			
		}
		?>
		
	
	jQuery('a[data-toggle="tab"]').on('shown', function (e) {
				jQuery("iframe", top.document).height(200)
 				setTimeout(function() {
					jQuery("iframe", top.document).height(jQuery(document).height()+10)
				}, 100);
	})
	
	
	jQuery(".fldsubmitLicense").click(function(){
		jQuery("#register-form .alert").remove();
		//validate form
		if(jQuery('#fldLicense').val() == ""){
			jQuery("#register-form").prepend(alerter("Please, enter your key",1));
			jQuery('#fldLicense').focus();
			return false;
		}
		
		var post = {}
		post.fldTask = 'register';
		post.fldLicense = jQuery('#fldLicense').val();
		
       	jQuery(this).button('loading')
		jQuery.post('<?php echo $this -> PLUGIN_API ?>/?viberregister',post, function(data) {
			if(data.indexOf('Successfully') != -1){
				jQuery("#register-form").prepend(alerter(data,2));
				setTimeout(function(){ top.location.reload(); })
			} else {
				jQuery("#register-form").prepend(alerter(data,1));
			}
			jQuery('.fldsubmitLicense').button('reset');
		});
	
	});
	
	
	//unregister
	jQuery(".fldsubmitLicenseU").click(function(){
		jQuery("#register-form .alert").remove();
		
		var post = {}
		post.unregister = '1';
		
       	jQuery(this).button('loading')
		jQuery.post('<?php echo $this -> PLUGIN_API ?>/?viberregister',post, function(data) {
			if(data.indexOf('Successfully') != -1){
				jQuery("#register-form").prepend(alerter(data,2));
				setTimeout(function(){ top.location.reload(); })
			} else {
				jQuery("#register-form").prepend(alerter(data,1));
			}
			jQuery('.fldsubmitLicense').button('reset');
		});
	
	});
	
	
	jQuery(".mem_limit_howto_show").click(function(){
		jQuery(".mem_limit_howto").show();
		setTimeout(function () { jQuery("iframe", top.document).height(jQuery(document).height()) }, 100);
	});
	
})
</script>
<div class="wrap viber-boot">
	<span class="pull-right textalign-right"><sub style="font-size: 10px">v. <?php echo $option['version']; ?></sub></span>
	<h2><img src="<?php echo $this -> PLUGIN_URL ?>images/viber-logo.png" height="64" width="170" /> <span id="loader" style="line-height: 40px;" ><img src="<?php echo $this -> PLUGIN_URL ?>images/loader.gif" height="9" width="36" /></span>
	</h2>
	
	<?php
	if($this->MEMORY_ERROR):
	?>
	<div class="alert">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<strong>Notice:</strong>
		<p>We recommend to setup 128M or more for the <strong><em>memory_limit</em></strong> PHP configuration directive. <a href="#" class="mem_limit_howto_show">How to change memory_limit?</a></p>
		<div class="mem_limit_howto" style="display: none;">
			<p>3 Ways to Increase PHP Memory Limit:</p>
			<ul>
				<li>
					<h4>Edit wp-config:</h4>
					<p><pre>define('WP_MEMORY_LIMIT', '128M');</pre></p>
				</li>
				<li>
					<h4>Edit php.ini:</h4>
					<p><pre>memory_limit = 128M ;</pre></p>
				</li>
				<li>
					<h4>Edit .htaccess:</h4>
					<p><pre>php_value memory_limit 128M;</pre></p>
				</li>
			</ul>
		</div>
	</div>
	<?php
	endif;
	?>	
	<br/>
	<div class="tabbable" style="opacity: 0;">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#pane3" data-toggle="tab">Heat Maps</a>
			</li>
			<li>
				<a href="#pane1" data-toggle="tab">Settings</a>
			</li>
			<li>
				<a href="#pane5" data-toggle="tab">Manage Data</a>
			</li>
			<li>
				<a href="http://heatmaptracker.com/" target="_blank" style="color: #f00">Get Pro Version</a>
			</li>
			<li class="dropdown pull-right">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">Help <b class="caret"></b></a>
                <ul class="dropdown-menu">
					<li>
						<a href="<?php echo admin_url() ?>?vibervideos" >Help Videos</a>
					</li>
                </ul>
              </li>
		</ul>
		<div class="tab-content">
			<div id="pane1" class="tab-pane">
				<div class="form-alerter"></div>
				<form action="#" method="POST" class="form-horizontal" id="player_settings_form">
					<?php $defaults = $this->OPTIONS; ?>
					<div class="control-group">
						<label class="control-label">Enable record</label>
						<div class="controls">
							<div class="btn-group onoff opt_record_status" data-toggle="buttons-radio">
								<button type="button" class="btn btn-on <?php print(($defaults["opt_record_status"])?'active btn-success':''); ?> btn-small" data-value="1">
									Enabled
								</button>
								<button type="button" class="btn btn-off <?php print((!$defaults["opt_record_status"])?'active btn-danger':''); ?> btn-small" data-value="0">
									Disabled
								</button>
							</div>
							<a class="help-ico" data-trigger="hover" rel="popover" data-original-title="Enable record" data-content="Enable HeatMapTracker to track info from your pages">lnk</a>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label"> Don't record this user</label>
						<div class="controls">
							<select  class="input-large pagesposts opt_record_user">
								<option value="-1">Choose user</option>
								<?php  $ulists = get_users();
								foreach ($ulists as $usr) {
									$checked = ($usr -> ID==$defaults['opt_record_user'])?'selected="selected"':'';
									$optn = '<option value="' . $usr -> ID . '"  '.$checked.' >';
									$optn .= $usr -> user_login;
									$optn .= '</option>';
									echo $optn;
								}
								?>
							</select>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label"> Timezone <?php echo date_default_timezone_get(); ?></label>
						<div class="controls">
							<select  class="input-large pagesposts opt_record_tz">
								<option value="-1">Choose Timezone</option>
								<?php  
									 $zonelist = array('Kwajalein' => -12.00, 'Pacific/Midway' => -11.00, 'Pacific/Honolulu' => -10.00, 'America/Anchorage' => -9.00, 'America/Los_Angeles' => -8.00, 'America/Denver' => -7.00, 'America/Tegucigalpa' => -6.00, 'America/New_York' => -5.00, 'America/Caracas' => -4.30, 'America/Halifax' => -4.00, 'America/St_Johns' => -3.30, 'America/Argentina/Buenos_Aires' => -3.00, 'America/Sao_Paulo' => -3.00, 'Atlantic/South_Georgia' => -2.00, 'Atlantic/Azores' => -1.00, 'Europe/Dublin' => 0, 'Europe/Belgrade' => 1.00, 'Europe/Minsk' => 2.00, 'Asia/Kuwait' => 3.00, 'Asia/Tehran' => 3.30, 'Asia/Muscat' => 4.00, 'Asia/Yekaterinburg' => 5.00, 'Asia/Kolkata' => 5.30, 'Asia/Katmandu' => 5.45, 'Asia/Dhaka' => 6.00, 'Asia/Rangoon' => 6.30, 'Asia/Krasnoyarsk' => 7.00, 'Asia/Brunei' => 8.00, 'Asia/Seoul' => 9.00, 'Australia/Darwin' => 9.30, 'Australia/Canberra' => 10.00, 'Asia/Magadan' => 11.00, 'Pacific/Fiji' => 12.00, 'Pacific/Tongatapu' => 13.00);
									 foreach ($zonelist as $zkey=>$zvalue) {
										$checked = ($defaults['opt_record_tz']==$zkey)?'selected="selected"':'';
										$optn = '<option value="' . $zkey . '"  '.$checked.' >';
										$optn .= $zkey." (".$zvalue.")";
										$optn .= '</option>';
										echo $optn;
									}
								?>
							</select>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">Record actions</label>
						<div class="controls">
							<div class="btn-group all-special" data-toggle="buttons-radio">
								<button type="button" class="opt_record_all btn btn-small <?php print(($defaults["opt_record_all"] == "true")?'active btn-success':''); ?> btn-all" data-value="1">
									On all posts and pages
								</button>
								<button type="button" class="btn btn-small <?php print((!$defaults["opt_record_all"] == "true")?'active btn-success':''); ?> btn-special" data-value="0">
									On special page or/and post
								</button>
							</div>
							<a class="help-ico" data-trigger="hover" rel="popover" data-original-title="Record actions" data-content="Specify page, where you want to track actions. Use CTRL or SHIFT to select multiple pages/posts">lnk</a>
						</div>
						<br />
						<div class="controls">
							<select <?php print(($defaults["opt_record_all"] == "true")?'disabled':''); ?> class="input-xlarge pagesposts opt_record_special" multiple="multiple" size="10" style="width:600px !important">
								<optgroup label="Pages">
										<?php
											$pages = get_pages();
											foreach ($pages as $page) {
												$checked = (in_array($page -> ID, $defaults['opt_record_special']))?'selected="selected"':'';
												$optn = '<option value="' . $page -> ID . '"  '.$checked.' >';
												$optn .= $page -> post_title;
												$optn .= '</option>';
												echo $optn;
											}
											?>
										</optgroup>
										<optgroup label="Posts">
											<?php
											$posts = get_posts( array( 'numberposts' => '999' ));
											foreach ($posts as $page) {
												$checked = (in_array($page -> ID, $defaults['opt_record_special']))?'selected="selected"':'';
												$optn = '<option value="' . $page -> ID . '" '.$checked.' >';
												$optn .= $page -> post_title;
												$optn .= '</option>';
												echo $optn;
											}
											?>
										</optgroup>
							</select>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label"> Record mouse movement</label>
						<div class="controls">
							<div class="btn-group onoff opt_record_mousemove" data-toggle="buttons-radio">
								<button type="button" class="btn btn-on <?php print(($defaults["opt_record_mousemove"])?'active btn-success':''); ?>  btn-small" data-value="1">
									On
								</button>
								<button type="button" class="btn btn-off <?php print((!$defaults["opt_record_mousemove"])?'active btn-danger':''); ?> btn-small" data-value="0">
									Off
								</button>
							</div>
							<a class="help-ico" data-trigger="hover" rel="popover" data-original-title="Record mouse movement" data-content="record all mouse coordinates by mousemove event">lnk</a>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label"> Record page scroll</label>
						<div class="controls">
							<div class="btn-group onoff opt_record_pagescroll" data-toggle="buttons-radio">
								<button type="button" class="btn btn-on <?php print(($defaults["opt_record_pagescroll"])?'active btn-success':''); ?> btn-small" data-value="1">
									On
								</button>
								<button type="button" class="btn btn-off <?php print((!$defaults["opt_record_pagescroll"])?'active btn-danger':''); ?> btn-small" data-value="0">
									Off
								</button>
							</div>
							<a class="help-ico" data-trigger="hover" rel="popover" data-original-title="Record page scroll" data-content="Record page scroll changes">lnk</a>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">Send interval</label>
						<div class="controls">
							<div class="input-append input-mini">
								<input class="span1 opt_record_interval" min="1" max="10" step="1" value="<?php print($defaults["opt_record_interval"]); ?>" type="number">
								<span class="add-on">sec</span>
							</div>
							<a class="help-ico more-to-right" data-trigger="hover" rel="popover" data-original-title="Send interval" data-content="Send messages with recorded data to the database in the specified interval">lnk</a>
							<a class="help-ico att" data-trigger="hover" rel="popover" data-original-title="Important Note" data-content="If you have 100 users at the same time on your website, your server will receive over 100 requests each N seconds. For the low load use a small value for sending interval. For higher load website use value more than 5">lnk</a>							
						</div>
					</div>

					<div class="control-group">
						<label class="control-label">Session delay</label>
						<div class="controls">
							<div class="input-append input-mini">
								<input class="span1 opt_record_kill_session" min="50" max="1000" step="50" value="<?php print($defaults["opt_record_kill_session"]); ?>" type="number">
								<span class="add-on">sec</span>
							</div>
							<a class="help-ico more-to-right" data-trigger="hover" rel="popover" data-original-title="Session delay" data-content="If the user will be inactive in the next XX seconds you may say that his previous session expired and the next time new session will be created">lnk</a>
						</div>
					</div>

					<div class="form-actions">
						<button type="button" class="btn btn-primary save-button" data-loading-text="Saving...">
							Save changes
						</button>
					</div>

				</form>

			</div>
			<div id="pane3" class="tab-pane active">
				<div class="heat-alerter"></div>
				<form action="<?php echo $this -> PLUGIN_API ?>" method="GET" class="form-horizontal" id="heatmap_form" target="_blank">
					<input name="url" value="" type="hidden" />
					<input name="from" value="" type="hidden" />
					<input name="to" value="" type="hidden" />
					<input name="layout" value="" type="hidden" />
					<input name="viberheatmap" value="" type="hidden" />
					<input name="map" value="" type="hidden" />
					<input name="uniq" value="" type="hidden" />
					
					<div class="control-group">
						<label class="control-label">Heat Map Type</label>
						<div class="controls">
							<div class="btn-group all-special btn-heatmap" data-toggle="buttons-radio">
								<button type="button" class="btn btn-success active btn-small btn-h-click" data-value="clicks">
									Clicks
								</button>
								<button type="button" class="btn btn-small btn-h-move" data-value="eyetracking">
									Eye-tracking
								</button>
							</div>
							<a class="help-ico" data-trigger="hover" rel="popover" data-original-title="Heat Map Type" data-content="Choose heat map type to generate">lnk</a>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Available Heat Maps</label>
						<div class="controls">
							<select class="input-xlarge heat-urls" size="10" style="width: 600px !important;">
										<?php
											global $wpdb;
											$table2 = $wpdb->prefix.$defaults['dbtable_name_clicks'];
											$urls = $wpdb->get_results( 
												"
												SELECT DISTINCT `page_url`
												FROM $table2 
												"
											);
											$urlArray1 = array();
											foreach ($urls as $key => $value) {
												$urlArray1[] = $value->page_url;
											}
											
											$table3 = $wpdb->prefix.$defaults['dbtable_name_mmove'];
											$urls = $wpdb->get_results( 
												"
												SELECT DISTINCT `page_url`
												FROM $table3 
												"
											);
											$urlArray2 = array();
											foreach ($urls as $key => $value) {
												$urlArray2[] = $value->page_url;
											}
										
										?>	
										
										<optgroup label="By URL" id="click_urls">
										<?php
											foreach ($urlArray1 as $key => $value) { ?>
			         							<option value="<?php echo $value ?>"><?php echo $value ?></option>
											<?php }
			         						?>
										</optgroup>
										
										<optgroup label="By URL" id="move_urls">
										<?php
											foreach ($urlArray2 as $key => $value) { ?>
			         							<option value="<?php echo $value ?>"><?php echo $value ?></option>
											<?php }
			         						?>
										</optgroup>
							</select>
							<a href="#" class="url-info" rel="popover" title="A Title" data-content="And here's some amazing content. It's very engaging. right?">&nbsp</a>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Data Source</label>
						<div class="controls">
							<div class="btn-group all-special btn-d" data-toggle="buttons-radio">
								<button type="button" class="btn active btn-success btn-small btn-d-day" data-value="2">
									Last Day
								</button>
								<button type="button" class="btn btn-small btn-d-week" data-value="2">
									Last Week
								</button>
								<button type="button" class="btn btn-small btn-d-month" data-value="2">
									Last Month
								</button>
								<button type="button" class="btn btn-small btn-d-range" data-value="2">
									Date Range
								</button>
							</div>
							<a class="help-ico" data-trigger="hover" rel="popover" data-original-title="Data Source" data-content="For the better analyzing heat map you can choose date range">lnk</a>
						</div>
						<br/>
						<div class="controls date-range-buttons">
							<button disabled type="button" class="btn btn-small from-date-heatmap" id="dp4" data-date-format="yyyy-mm-dd" data-date="2012-02-20">
								<strong>From</strong> <span>2012-02-20</span>
							</button>
							<button disabled type="button" class="btn btn-small to-date-heatmap"  id="dp5" data-date-format="yyyy-mm-dd" data-date="2012-02-23">
								<strong>To</strong> <span>2012-02-23</span>
							</button>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Page layout</label>
						<div class="controls">
							<div class="btn-group all-special btn-layout" data-toggle="buttons-radio">
								<button type="button" class="btn btn-small" data-value="left">
									Left Aligned
								</button>
								<button type="button" class="btn active btn-success btn-small" data-value="center">
									Centered
								</button>
								<button type="button" class="btn btn-small" data-value="right">
									Right Aligned
								</button>
							</div>
							<a class="help-ico" data-trigger="hover" rel="popover" data-original-title="Page layout" data-content="How page is aligned relative to the browser window size">lnk</a>
						</div>
					</div>
					<div class="form-actions">
						<button type="button" class="btn btn-primary generate-heatmap" data-loading-text="Checking data...">
							Generate Heat Map
						</button>
					</div>
				</form>
			</div>
			
			<div id="pane5" class="tab-pane">
				<form action="" method="post" class="form-horizontal" id="manage_data_form">
					<input name="manage" value="tables" type="hidden" />
					<input name="from" value="" type="hidden" />
					<input name="to" value="" type="hidden" />
					<input name="what" value="" type="hidden" />
					<input name="page" value="<?php echo $_GET['page']; ?>" type="hidden" />
					
					<?php
					
					global $wpdb;
					$table2 = $wpdb->prefix.$defaults['dbtable_name_clicks'];
					$table3 = $wpdb->prefix.$defaults['dbtable_name_mmove'];
			        $query = 'SELECT TABLE_SCHEMA AS "Database", TABLE_NAME AS "Table",
			ROUND(((DATA_LENGTH + INDEX_LENGTH - DATA_FREE) / 1024 / 1024),2) AS Size 
			FROM INFORMATION_SCHEMA.TABLES where TABLE_SCHEMA like "%'.DB_NAME.'%" and (TABLE_NAME = "'.$table2.'" OR TABLE_NAME = "'.$table3.'")';
			
			        $size_res = $wpdb->get_results($query);
					$total = 0;
					foreach ( $size_res as $size ) 
					{
						$total +=$size->Size;
					}
					?>
					
					
					
					<div class="control-group">
						<label class="control-label">Data Total Size</label>
						<div class="controls">
							<div class="btn-group btn-layout">								
							<button disabled type="button" class="btn btn-small" disabled >
								<strong><?php echo $total;?> MB</strong>
							</button>
							</div>
							<a class="help-ico" data-trigger="hover" rel="popover" data-original-title="Data Total Size" data-content="Size in MB of all HeatMapTracker MySQL tables">lnk</a>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Tables</label>
						<div class="controls">
							<div class="btn-group btn-group-vertical all-special btn-layout" data-toggle="buttons-radio">
								<button type="button" class="btn btn-small active btn-success btn-clicks" data-value="left" style="width:150px;">
									Clicks Heatmap
								</button>
								<button type="button" class="btn btn-small btn-eye" data-value="right" style="width:150px;">
									Eye-tracking Heatmap
								</button>
							</div>
							<a class="help-ico" data-trigger="hover" rel="popover" data-original-title="Tables" data-content="All HeatMapTracker tables. Please select table to delete data">lnk</a>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Date Range</label>
						<div class="controls">
							<div class="btn-group all-special btn-md" data-toggle="buttons-radio">
								<button type="button" class="btn active btn-success btn-small btn-md-day" data-value="2">
									Last Day
								</button>
								<button type="button" class="btn btn-small btn-md-week" data-value="2">
									Last Week
								</button>
								<button type="button" class="btn btn-small btn-md-month" data-value="2">
									Last Month
								</button>
								<button type="button" class="btn btn-small btn-md-range" data-value="2">
									Date Range
								</button>
							</div>
							<a class="help-ico" data-trigger="hover" rel="popover" data-original-title="Date Range" data-content="Choose date range to delete data from the selected table">lnk</a>
						</div>
						<br/>
						<div class="controls date-md-range-buttons">
							<button disabled type="button" class="btn btn-small from-date-heatmap" id="dp6" data-date-format="yyyy-mm-dd" data-date="2012-02-20">
								<strong>From</strong> <span>2012-02-20</span>
							</button>
							<button disabled type="button" class="btn btn-small to-date-heatmap"  id="dp7" data-date-format="yyyy-mm-dd" data-date="2012-02-23">
								<strong>To</strong> <span>2012-02-23</span>
							</button>
						</div>
					</div>
					<div class="form-actions">
						<button type="submit" class="btn btn-primary save-button">
							Delete
						</button>
					</div>
				</form>
			</div>
			
			<div id="pane6" class="tab-pane">
				<a href="http://heatmaptracker.com/codecanyon-registration/" class="btn btn-large" target="_blank">Click here to register on our site for auto update!</a>
			</div>
		</div>
	</div>
</div>
</body>
</html>