<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker
 * http://heatmaptracker.com
 */
?>
<?php
	//detect user info
	$broosArr = ViberFN::browser_detection("full");
	$broos =  ViberFN::browser_detection("os")." ".ViberFN::browser_detection("os_number");					

	if(ViberFN::browser_detection("browser") != "ie"){
		$broarr = ViberFN::browser_detection(ViberFN::browser_detection("browser")."_version");
		$broos .= "; ".$broarr[0]." ".$broarr[1];
	} else {
		$broos .= "; ie ".ViberFN::browser_detection("number");
	}
	
	//get user real IP
	$uip = ViberFN::getRealIp();
	
	//wp user detection
	$current_user = wp_get_current_user();
	if ( 0 == $current_user->ID ) {
		$reguser = "guest";
	} else {
		$reguser = $current_user->ID;
	}
	
	//secure check $_GET variables
	$_GET = array_map(array(ViberFN,'viber_secure'), $_GET);
	$option = $this->OPTIONS;
	header("Content-type: application/javascript");
	//check page we want to record
	if(!$option['opt_record_status']) die('//empty js file');
	//check user we do not wan't to record
	if($option['opt_record_user'] == $reguser) die('//empty js file');
	if(!$option['opt_record_all'] && !in_array($_GET['i'], $option['opt_record_special'])) die('//empty js file');
?>


//<script>	

function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

	
	//here you can change coockies name
	var viber_cookie_name = "viber";

	//functions to work with cookies
	function setViberData(e,t,n){
		localStorage.setItem(e, t);
	}
	function getViberData(e){
		return localStorage[e];
	}
	
	function getByteSize(s) {
    	return encodeURIComponent('<q></q>' + s).length;
	}
	
	//viber_serialize/viber_unserialize array function
	// implement JSON.stringify serialization
JSONstringify =  function (obj) {
	var t = typeof (obj);
	if (t != "object" || obj === null) {
		// simple data type
		if (t == "string") obj = '"'+obj+'"';
		return String(obj);
	}
	else {
		// recurse array or object
		var n, v, json = [], arr = (obj && obj.constructor == Array);
		for (n in obj) {
			v = obj[n]; t = typeof(v);
			if (t == "string") v = '"'+v+'"';
			else if (t == "object" && v !== null) v = JSONstringify(v);
			json.push((arr ? "" : '"' + n + '":') + String(v));
		}
		return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
	}
};
// implement JSON.parse de-serialization
JSONparse = function (str) {
	if (str === "") str = '""';
	eval("var p=" + str + ";");
	return p;
};

//<script>
var END_OF_INPUT = -1;
var base64Chars = new Array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9','+','/');
var reverseBase64Chars = new Array();
for (var i=0; i < base64Chars.length; i++){
    reverseBase64Chars[base64Chars[i]] = i;
}
var base64Str;
var base64Count;
function setBase64Str(str){
    base64Str = str;
    base64Count = 0;
}
function readBase64(){    
    if (!base64Str) return END_OF_INPUT;
    if (base64Count >= base64Str.length) return END_OF_INPUT;
    var c = base64Str.charCodeAt(base64Count) & 0xff;
    base64Count++;
    return c;
}
function encodeBase64(str){
    setBase64Str(str);
    var result = '';
    var inBuffer = new Array(3);
    var lineCount = 0;
    var done = false;
    while (!done && (inBuffer[0] = readBase64()) != END_OF_INPUT){
        inBuffer[1] = readBase64();
        inBuffer[2] = readBase64();
        result += (base64Chars[ inBuffer[0] >> 2 ]);
        if (inBuffer[1] != END_OF_INPUT){
            result += (base64Chars [(( inBuffer[0] << 4 ) & 0x30) | (inBuffer[1] >> 4) ]);
            if (inBuffer[2] != END_OF_INPUT){
                result += (base64Chars [((inBuffer[1] << 2) & 0x3c) | (inBuffer[2] >> 6) ]);
                result += (base64Chars [inBuffer[2] & 0x3F]);
            } else {
                result += (base64Chars [((inBuffer[1] << 2) & 0x3c)]);
                result += ('=');
                done = true;
            }
        } else {
            result += (base64Chars [(( inBuffer[0] << 4 ) & 0x30)]);
            result += ('=');
            result += ('=');
            done = true;
        }
        lineCount += 4;
        if (lineCount >= 76){
            result += ('\n');
            lineCount = 0;
        }
    }
    return result;
}

	function viber_serialize(arr){
		//uncompressed
		var _srz = JSON.stringify(arr);
		return _srz;
	}
	function viber_unserialize(e){
		return JSONparse(e);
	}
	
	function isiOS(){
	    return (
	        //Detect iPhone
	        (navigator.platform.indexOf("iPhone") != -1) ||
	        //Detect iPod
	        (navigator.platform.indexOf("iPod") != -1) ||
	        //Detect iPad
	        (navigator.platform.indexOf("iPad") != -1)
	    );
	}

	function addr(el, str){
		var address = str;
		if(jQuery(el)[0].nodeName!='BODY')
			return addr(jQuery(el).parent(),address+jQuery(el).index()+",");
		else 
			return address;
	}
	
	function getBuff(sess, location, name){
		var src_buff = getViberData(viber_cookie_name+"_buff");
		if(src_buff != null) {
			buff = viber_unserialize(src_buff);
		}
		else buff = {};
			
		if(buff[sess] == undefined)
			buff[sess] = {};
					
		if(buff[sess][location] == undefined)
			buff[sess][location] = {};
					
		if(buff[sess][location][name] == undefined)
			buff[sess][location][name] = [];
		
		return buff;
	}
	
	isActive = true;
	//<script>
	
	
	var latest_update;
	 
	function initLogic(){
	jQuery(document).ready(function() {
		
		 jQuery.fn.getStyleObject = function(){
        var dom = this.get(0);
        var style;
        var returns = {};
        if(window.getComputedStyle){
            var camelize = function(a,b){
                return b.toUpperCase();
            }
            style = window.getComputedStyle(dom, null);
            for(var i=0;i < style.length;i++){
                var prop = style[i];
                var camel = prop.replace(/\-([a-z])/g, camelize);
                var val = style.getPropertyValue(prop);
                returns[camel] = val;
            }
            return returns;
        }
        if(dom.currentStyle){
            style = dom.currentStyle;
            for(var prop in style){
                returns[prop] = style[prop];
            }
            return returns;
        }
        return this.css();
    }
		//check if window/tab is active
   		jQuery(window).focus(function() { isActive = true;});
    	jQuery(window).blur(function() { isActive = false;});
		//dont record in iframe
		if(top !== self) return false;
		//get user id from the cookies
		//create new if not exist 
		var myVar = "<?php echo $uip."~".$broos."~".$reguser; ?>";
		//get session id from the cookies
		//create new if not exist 
		var session_data = getViberData(viber_cookie_name+"_session");
		if (session_data == undefined) {
			time = 0;
			session = [Math.floor((Math.random() * 1000000000) + 1), Math.floor(new Date().getTime()/1000),time, document.location.href];
			setViberData(viber_cookie_name+"_session", viber_serialize(session), 365);
		} else {
			var now = Math.floor(new Date().getTime()/1000);
			session = viber_unserialize(session_data);
			if((now-session[1]) > <?php print $option['opt_record_kill_session']; ?>){
				time = 0;
				session = [Math.floor((Math.random() * 1000000000) + 1), Math.floor(new Date().getTime()/1000),time, document.location.href];
				setViberData(viber_cookie_name+"_session", viber_serialize(session), 365);
			} else {
				if(session[3] != document.location.href)
					time = 0;
				else
					time = session[2];
				session = [session[0], Math.floor(new Date().getTime()/1000), session[2], document.location.href];
				setViberData(viber_cookie_name+"_session", viber_serialize(session), 365);
			}
		}
		var viber_lastmousex = 0, viber_lastmousey = 0, viber_lastscrollv = 0, viber_lastscrollh = 0, lastwinh = 0, lastwinw = 0;
		var viber_prevmousex = 0, viber_prevmousey = 0, prevscrollv = 0, prevscrollh = 0, prevwinh = 0, prevwinw = 0;
		var mouse_move, mouse_click, page_scroll;
		var sendwhen = <?php print $option['opt_record_interval']; ?>*1000;
		var interval = 100;
		var sending = false;
		var location = document.location.href;
		//timer for sending recorded actions to db
		//<script>
		
 		function sendData(){
		//break when inactive current window/tab
			now = Math.floor(new Date().getTime()/1000);
			sending = true;
						
			//get recorded data from the cookies
			var send_buff =  getViberData(viber_cookie_name+"_buff") || "";
			
			var send_obj = viber_unserialize(send_buff);
			jQuery.each( send_obj, function( key, value ) {
				  jQuery.each( value, function( kkey, vvalue ) {
				  		if(send_obj[key][kkey]["window_size"] == undefined)
				  			send_obj[key][kkey]["window_size"] = [["0.8",jQuery(window).height(),jQuery(window).width()]];
					});
			});
			//if we have new actions from the users, send it	
			if(!(send_buff.length < 5) && isActive){
				jQuery.post("<?php echo $this -> PLUGIN_API; ?>?viberdata=&callback=?", {
					"user" : myVar,
					"data" : encodeBase64(viber_serialize(send_obj))
				},function(){},'json');
					buff = {}; setViberData(viber_cookie_name+"_buff",viber_serialize(buff));

				//set empty arrays to the cookies
				sending = false;
				latest_update = Math.floor(new Date().getTime()/1000);
			} else {
			//if we do not have new actions from the user,
			//check session live time to create new if need
				var session_data = getViberData(viber_cookie_name+"_session");
				if (session_data == undefined) {
					time = 0;
					session = [Math.floor((Math.random() * 1000000000) + 1), Math.floor(new Date().getTime()/1000),time, document.location.href];
					setViberData(viber_cookie_name+"_session", viber_serialize(session), 365);
				} else {
					var now = Math.floor(new Date().getTime()/1000);
					session = viber_unserialize(session_data);
					if((now-session[1]) > <?php print $option['opt_record_kill_session']; ?>){
						time = 0;
						session = [Math.floor((Math.random() * 1000000000) + 1), Math.floor(new Date().getTime()/1000),time, document.location.href];
						setViberData(viber_cookie_name+"_session", viber_serialize(session), 365);
					} else {
						session = [session[0], latest_update ,session[2], document.location.href];
						setViberData(viber_cookie_name+"_session", viber_serialize(session));
					}
				}
				sending = false;
			}
		
		}
		
		
		setInterval(function() {
			sendData();
		}, sendwhen)
		//record window size
		prevwinw = prevwinh = 0;
		lastwinh = jQuery(window).height();
		lastwinw = jQuery(window).width();
		//timer for record user actions
		setInterval(function() {
			//get session id
			var cur_sess_data = getViberData(viber_cookie_name+"_session");
			var cur_sess = viber_unserialize(cur_sess_data);
			
			//if data was changed add it to array,
			//in this way we have optimized sending
			var mmove_iterate = 0;
			if((viber_prevmousex != viber_lastmousex || viber_prevmousey != viber_lastmousey) && !sending && <?php print ($option['opt_record_mousemove'])?'true':'false'; ?>){
				
				
				var buff = getBuff(cur_sess[0],location,"mouse_move");
				if(mmove_iterate == 0) { mmove_iterate = 0;
					buff[cur_sess[0]][location]["mouse_move"].push([time.toFixed(1),viber_lastmousex,viber_lastmousey,jQuery(window).width()]);
					setViberData(viber_cookie_name+"_buff",viber_serialize(buff));
				} else mmove_iterate--;
				
				viber_prevmousex = viber_lastmousex;
				viber_prevmousey = viber_lastmousey;
			}
			
			if((prevscrollv != viber_lastscrollv || prevscrollh != viber_lastscrollh) && !sending && <?php print ($option['opt_record_pagescroll'])?'true':'false'; ?>){
				
				
				var buff = getBuff(cur_sess[0],location,"page_scroll");
				buff[cur_sess[0]][location]["page_scroll"].push([time.toFixed(1),viber_lastscrollv,viber_lastscrollh]);
				setViberData(viber_cookie_name+"_buff",viber_serialize(buff));
				
				prevscrollv = viber_lastscrollv;
				prevscrollh = viber_lastscrollh;
			}
			
			if((prevwinw != lastwinw || prevwinh != lastwinh) && !sending){
				
				var buff = getBuff(cur_sess[0],location,"window_size");
				buff[cur_sess[0]][location]["window_size"].push([time.toFixed(1),lastwinh,lastwinw]);
				setViberData(viber_cookie_name+"_buff",viber_serialize(buff));

				prevwinw = lastwinw;
				prevwinh = lastwinh;
			}
			time += (interval/1000);
			cur_sess[2] = time;
			setViberData(viber_cookie_name+"_session",viber_serialize(cur_sess));
		}, interval)
		
		//mouse position
		jQuery("body").mousemove(function(e) {
			viber_lastmousex = e.pageX;
			viber_lastmousey = e.pageY;
		});
		
		//scroll
		jQuery(window).scroll(function(e) {
			viber_lastscrollv = jQuery(document).scrollTop();
			viber_lastscrollh = jQuery(document).scrollLeft();
		})
		
		//window resize
		jQuery(window).resize(function() {
			lastwinh = jQuery(window).height();
			lastwinw = jQuery(window).width();
		});
				
		
		//mouse clicks
		if(!isiOS())
		jQuery("body").mousedown(function(event) {
			if(!sending){
				//get session id
				var cur_sess_data = getViberData(viber_cookie_name+"_session");
				var cur_sess = viber_unserialize(cur_sess_data);
				
				var buff = getBuff(cur_sess[0],location,"mouse_click");
				
				buff[cur_sess[0]][location]["mouse_click"].push([time.toFixed(1), event.which, viber_lastmousex, viber_lastmousey, viber_lastscrollv, viber_lastscrollh,jQuery(window).width()]);
				
				setViberData(viber_cookie_name+"_buff",viber_serialize(buff));	
			}
		});
		//touch in devices
		if(isiOS())
		$('body').bind( "touchstart", function(e){
			if(!sending){
				//get session id
				var cur_sess_data = getViberData(viber_cookie_name+"_session");
				var cur_sess = viber_unserialize(cur_sess_data);
				
				var buff = getBuff(cur_sess[0],location,"mouse_click");
					
				buff[cur_sess[0]][location]["mouse_click"].push([time.toFixed(1), event.which, e.touches[0].pageX,  e.touches[0].pageY, viber_lastscrollv, viber_lastscrollh,jQuery(window).width()]);
				
				setViberData(viber_cookie_name+"_buff",viber_serialize(buff));	
			}
		});
	});
	}//initLogic()
function waitForJQuery() {
	if (typeof jQuery != 'undefined') { // JQuery is loaded!
		initLogic();
		return;
	}
	setTimeout(waitForJQuery, 100); // Check 0,1 a second
	return;
}
function checkjQuery() {
	if (typeof jQuery == 'undefined') { 
		var script = document.createElement('script');
		script.type = "text/javascript";
		script.src = "//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js";
		document.getElementsByTagName('head')[0].appendChild(script);
		waitForJQuery();
	} else {
		initLogic();
	}
}
function viberbindReady(handler){

	var called = false

	function ready() { // (1)
		if (called) return
		called = true
		handler()
	}

	if ( document.addEventListener ) { 
		document.addEventListener( "DOMContentLoaded", function(){
			ready()
		}, false )
	} else if ( document.attachEvent ) { 

		if ( document.documentElement.doScroll && window == window.top ) {
			function tryScroll(){
				if (called) return
				if (!document.body) return
				try {
					document.documentElement.doScroll("left")
					ready()
				} catch(e) {
					setTimeout(tryScroll, 0)
				}
			}
			tryScroll()
		}

		document.attachEvent("onreadystatechange", function(){

			if ( document.readyState === "complete" ) {
				ready()
			}
		})
	}

    if (window.addEventListener)
        window.addEventListener('load', ready, false)
    else if (window.attachEvent)
        window.attachEvent('onload', ready)
}

viberreadyList = []

function onVReady(handler) {

	if (!viberreadyList.length) {
		viberbindReady(function() {
			for(var i=0; i<viberreadyList.length; i++) {
				viberreadyList[i]()
			}
		})
	}
	viberreadyList.push(handler)
}

onVReady(function(){   
    checkjQuery();
});