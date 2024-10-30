<?php
/*
 * HeatMapTracker
 * (c) 2013. HeatMapTracker
 * http://heatmaptracker.com
 */
?>
<?php

//error_reporting(E_ERROR);
function doGet($sURL, $asVars, $bRequireResult = FALSE)
			{
	$sURL .= '?' . http_build_query($asVars);
	$asResponse = @ wp_remote_get($sURL);
	$sResponse = @ $asResponse['body'];
	if ($bRequireResult) {
		if (empty($sResponse)) {
			sleep(1);
			$asResponse = @ wp_remote_get($sURL);
			$sResponse = @ $asResponse['body'];
			if ($bRequireResult) {
				if (empty($sResponse)) {
					sleep(2);
					$asResponse = @ wp_remote_get($sURL);
					$sResponse = @ $asResponse['body'];
				}
			}
		}
	}
	return trim($sResponse);	
}
class ViberFN {
//secure post variables
public static function viber_secure($string) {
	
		// Checking if this PHP installation automatically adds slashes, remove them if so
		(get_magic_quotes_gpc())?$string=stripslashes($string):$string=$string;
		
		$string = trim($string);
		$string = strip_tags($string);
		
		//$string = htmlspecialchars($string); - htmlentities() covers more characters
		$string = htmlentities($string);
		
		$string = @mysql_real_escape_string($string);
		return $string;
}
//seconds to hh:mm:ss
public static function sec2hms ($sec, $padHours = true) {

    $hms = "";
    
    // there are 3600 seconds in an hour, so if we
    // divide total seconds by 3600 and throw away
    // the remainder, we've got the number of hours
    $hours = intval(intval($sec) / 3600); 

    // add to $hms, with a leading 0 if asked for
    $hms .= ($padHours) 
          ? str_pad($hours, 2, "0", STR_PAD_LEFT). ':'
          : $hours. ':';
     
    // dividing the total seconds by 60 will give us
    // the number of minutes, but we're interested in 
    // minutes past the hour: to get that, we need to 
    // divide by 60 again and keep the remainder
    $minutes = intval(($sec / 60) % 60); 

    // then add to $hms (with a leading 0 if needed)
    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';

    // seconds are simple - just divide the total
    // seconds by 60 and keep the remainder
    $seconds = intval($sec % 60); 

    // add to $hms, again with a leading 0 if needed
    $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

    return $hms;
}
//get user ip
public static function getRealIp() {
	 if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}


public static function browser_detection( $which_test, $test_excludes='' ) {
	/*
	uncomment the global variable declaration if you want the variables to be available on
	a global level throughout your php page, make sure that php is configured to support
	the use of globals first!
	Use of globals should be avoided however, and they are not necessary with this script
	/*
	/*
	global $dom_browser, $safe_browser, $browser_user_agent, $browser_name, $s_browser, $ie_version, $true_msie_version, $browser_version_number, $mobile_test, $a_mobile_data, $os_number, $os_type, $b_repeat, $moz_type, $moz_version_number, $moz_rv, $moz_rv_full, $moz_release_date, $math_version_number, $ua_type, $webkit_type, $webkit_type_number;
	*/

	static $dom_browser, $safe_browser, $browser_user_agent, $browser_name, $s_browser, $ie_version, $true_msie_version, $browser_version_number, $mobile_test, $a_mobile_data, $os_number, $os_type, $b_repeat, $moz_type, $moz_version_number, $moz_rv, $moz_rv_full, $moz_release_date, $math_version_number, $ua_type, $webkit_type, $webkit_type_number;

	/*
	this makes the test only run once no matter how many times you call it since
	all the variables are filled on the first run through, it's only a matter of
	returning the the right ones
	*/
	if ( !$b_repeat )
	{
		//initialize all variables with default values to prevent error
		$dom_browser = false;
		$ua_type = 'bot';// default to bot since you never know with bots
		$safe_browser = false;
		$a_os_data = '';
		$os_number = '';
		$os_type = '';
		$browser_name = '';
		$browser_version_number = '';
		$math_version_number = '';
		$a_math_version_number = '';
		$ie_version = '';
		$true_msie_version = '';
		$mobile_test = '';
		$a_mobile_data = '';
		$a_moz_data = '';
		$moz_type = '';
		$moz_version_number = '';
		$moz_rv = '';
		$moz_rv_full = '';
		$moz_release_date = '';
		$a_unhandled_browser = '';
		$a_webkit_data = '';
		$webkit_type = '';
		$webkit_type_number = '';
		$b_success = false;// boolean for if browser found in main test
		$b_os_test = true;
		$b_mobile_test = true;

		// set the excludes if required
		if ( $test_excludes )
		{
			switch ( $test_excludes )
			{
				case '1':
					$b_os_test = false;
					break;
				case '2':
					$b_mobile_test = false;
					break;
				case '3':
					$b_os_test = false;
					$b_mobile_test = false;
					break;
				default:
					die( 'Error: bad $test_excludes parameter used: ' . $test_excludes );
					break;
			}
		}

		/*
		make navigator user agent string lower case to make sure all versions get caught
		isset protects against blank user agent failure. tolower also lets the script use
		strstr instead of stristr, which drops overhead slightly.
		*/
		$browser_user_agent = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';
		/*
		pack the browser type array, in this order
		the order is important, because opera must be tested first, then omniweb [which has safari
		data in string], same for konqueror, then safari, then gecko, since safari navigator user
		agent id's with 'gecko' in string.
		Note that $dom_browser is set for all  modern dom browsers, this gives you a default to use.

		array[0] = id string for useragent, array[1] is if dom capable, array[2] is working name
		for browser, array[3] identifies navigator useragent type

		Note: all browser strings are in lower case to match the strtolower output, this avoids
		possible detection errors

		Note: These are the navigator user agent types:
		bro - modern, css supporting browser.
		bbro - basic browser, text only, table only, defective css implementation
		bot - search type spider
		dow - known download agent
		lib - standard http libraries
		mobile - handheld or mobile browser, set using $mobile_test
		*/
		// known browsers, list will be updated routinely, check back now and then
		$a_browser_types = array(
			array( 'opera', true, 'op', 'bro' ),
			array( 'msie', true, 'ie', 'bro' ),
			// webkit before gecko because some webkit ua strings say: like gecko
			array( 'webkit', true, 'webkit', 'bro' ),
			// konq will be using webkit soon
			array( 'konqueror', true, 'konq', 'bro' ),
			// covers Netscape 6-7, K-Meleon, Most linux versions, uses moz array below
			array( 'gecko', true, 'moz', 'bro' ),
			array( 'netpositive', false, 'netp', 'bbro' ),// beos browser
			array( 'lynx', false, 'lynx', 'bbro' ), // command line browser
			array( 'elinks ', false, 'elinks', 'bbro' ), // new version of links
			array( 'elinks', false, 'elinks', 'bbro' ), // alternate id for it
			array( 'links2', false, 'links2', 'bbro' ), // alternate links version
			array( 'links ', false, 'links', 'bbro' ), // old name for links
			array( 'links', false, 'links', 'bbro' ), // alternate id for it
			array( 'w3m', false, 'w3m', 'bbro' ), // open source browser, more features than lynx/links
			array( 'webtv', false, 'webtv', 'bbro' ),// junk ms webtv
			array( 'amaya', false, 'amaya', 'bbro' ),// w3c browser
			array( 'dillo', false, 'dillo', 'bbro' ),// linux browser, basic table support
			array( 'ibrowse', false, 'ibrowse', 'bbro' ),// amiga browser
			array( 'icab', false, 'icab', 'bro' ),// mac browser
			array( 'crazy browser', true, 'ie', 'bro' ),// uses ie rendering engine

			// search engine spider bots:
			array( 'googlebot', false, 'google', 'bot' ),// google
			array( 'mediapartners-google', false, 'adsense', 'bot' ),// google adsense
			array( 'yahoo-verticalcrawler', false, 'yahoo', 'bot' ),// old yahoo bot
			array( 'yahoo! slurp', false, 'yahoo', 'bot' ), // new yahoo bot
			array( 'yahoo-mm', false, 'yahoomm', 'bot' ), // gets Yahoo-MMCrawler and Yahoo-MMAudVid bots
			array( 'inktomi', false, 'inktomi', 'bot' ), // inktomi bot
			array( 'slurp', false, 'inktomi', 'bot' ), // inktomi bot
			array( 'fast-webcrawler', false, 'fast', 'bot' ),// Fast AllTheWeb
			array( 'msnbot', false, 'msn', 'bot' ),// msn search
			array( 'ask jeeves', false, 'ask', 'bot' ), //jeeves/teoma
			array( 'teoma', false, 'ask', 'bot' ),//jeeves teoma
			array( 'scooter', false, 'scooter', 'bot' ),// altavista
			array( 'openbot', false, 'openbot', 'bot' ),// openbot, from taiwan
			array( 'ia_archiver', false, 'ia_archiver', 'bot' ),// ia archiver
			array( 'zyborg', false, 'looksmart', 'bot' ),// looksmart
			array( 'almaden', false, 'ibm', 'bot' ),// ibm almaden web crawler
			array( 'baiduspider', false, 'baidu', 'bot' ),// Baiduspider asian search spider
			array( 'psbot', false, 'psbot', 'bot' ),// psbot image crawler
			array( 'gigabot', false, 'gigabot', 'bot' ),// gigabot crawler
			array( 'naverbot', false, 'naverbot', 'bot' ),// naverbot crawler, bad bot, block
			array( 'surveybot', false, 'surveybot', 'bot' ),//
			array( 'boitho.com-dc', false, 'boitho', 'bot' ),//norwegian search engine
			array( 'objectssearch', false, 'objectsearch', 'bot' ),// open source search engine
			array( 'answerbus', false, 'answerbus', 'bot' ),// http://www.answerbus.com/, web questions
			array( 'sohu-search', false, 'sohu', 'bot' ),// chinese media company, search component
			array( 'iltrovatore-setaccio', false, 'il-set', 'bot' ),

			// various http utility libaries
			array( 'w3c_validator', false, 'w3c', 'lib' ), // uses libperl, make first
			array( 'wdg_validator', false, 'wdg', 'lib' ), //
			array( 'libwww-perl', false, 'libwww-perl', 'lib' ),
			array( 'jakarta commons-httpclient', false, 'jakarta', 'lib' ),
			array( 'python-urllib', false, 'python-urllib', 'lib' ),

			// download apps
			array( 'getright', false, 'getright', 'dow' ),
			array( 'wget', false, 'wget', 'dow' ),// open source downloader, obeys robots.txt

			// netscape 4 and earlier tests, put last so spiders don't get caught
			array( 'mozilla/4.', false, 'ns', 'bbro' ),
			array( 'mozilla/3.', false, 'ns', 'bbro' ),
			array( 'mozilla/2.', false, 'ns', 'bbro' )
		);

		//array( '', false ); // browser array template

		/*
		moz types array
		note the order, netscape6 must come before netscape, which  is how netscape 7 id's itself.
		rv comes last in case it is plain old mozilla. firefox/netscape/seamonkey need to be later
		*/
		$a_moz_types = array( 'camino', 'epiphany', 'firebird', 'flock', 'galeon', 'k-meleon', 'minimo', 'multizilla', 'phoenix', 'swiftfox', 'iceape', 'seamonkey', 'iceweasel', 'firefox', 'netscape6', 'netscape', 'rv' );

		/*
		webkit types, this is going to expand over time as webkit browsers spread
		konqueror is probably going to move to webkit, so this is preparing for that
		It will now default to khtml. gtklauncher is the temp id for epiphany, might
		change. Defaults to applewebkit, and will all show the webkit number.
		*/
		$a_webkit_types = array( 'arora', 'chrome', 'epiphany', 'gtklauncher', 'konqueror', 'midori', 'omniweb', 'safari', 'uzbl', 'applewebkit', 'webkit' );

		/*
		run through the browser_types array, break if you hit a match, if no match, assume old browser
		or non dom browser, assigns false value to $b_success.
		*/
		$i_count = count( $a_browser_types );
		for ( $i = 0; $i < $i_count; $i++ )
		{
			//unpacks browser array, assigns to variables
			$s_browser = $a_browser_types[$i][0];// text string to id browser from array

			if ( strstr( $browser_user_agent, $s_browser ) )
			{
				/*
				it defaults to true, will become false below if needed
				this keeps it easier to keep track of what is safe, only
				explicit false assignment will make it false.
				*/
				$safe_browser = true;

				// assign values based on match of user agent string
				$dom_browser = $a_browser_types[$i][1];// hardcoded dom support from array
				$browser_name = $a_browser_types[$i][2];// working name for browser
				$ua_type = $a_browser_types[$i][3];// sets whether bot or browser

				switch ( $browser_name )
				{
					// this is modified quite a bit, now will return proper netscape version number
					// check your implementation to make sure it works
					case 'ns':
						$safe_browser = false;
						$browser_version_number = ViberFN::get_item_version( $browser_user_agent, 'mozilla' );
						break;
					case 'moz':
						/*
						note: The 'rv' test is not absolute since the rv number is very different on
						different versions, for example Galean doesn't use the same rv version as Mozilla,
						neither do later Netscapes, like 7.x. For more on this, read the full mozilla
						numbering conventions here: http://www.mozilla.org/releases/cvstags.html
						*/
						// this will return alpha and beta version numbers, if present
						$moz_rv_full = ViberFN::get_item_version( $browser_user_agent, 'rv' );
						// this slices them back off for math comparisons
						$moz_rv = substr( $moz_rv_full, 0, 3 );

						// this is to pull out specific mozilla versions, firebird, netscape etc..
						$j_count = count( $a_moz_types );
						for ( $j = 0; $j < $j_count; $j++ )
						{
							if ( strstr( $browser_user_agent, $a_moz_types[$j] ) )
							{
								$moz_type = $a_moz_types[$j];
								$moz_version_number = ViberFN::get_item_version( $browser_user_agent, $moz_type );
								break;
							}
						}
						/*
						this is necesary to protect against false id'ed moz'es and new moz'es.
						this corrects for galeon, or any other moz browser without an rv number
						*/
						if ( !$moz_rv )
						{
							// you can use this if you are running php >= 4.2
							if ( function_exists( 'floatval' ) )
							{
								$moz_rv = floatval( $moz_version_number );
							}
							else
							{
								$moz_rv = substr( $moz_version_number, 0, 3 );
							}
							$moz_rv_full = $moz_version_number;
						}
						// this corrects the version name in case it went to the default 'rv' for the test
						if ( $moz_type == 'rv' )
						{
							$moz_type = 'mozilla';
						}

						//the moz version will be taken from the rv number, see notes above for rv problems
						$browser_version_number = $moz_rv;
						// gets the actual release date, necessary if you need to do functionality tests
						$moz_release_date = ViberFN::get_item_version( $browser_user_agent, 'gecko/' );
						/*
						Test for mozilla 0.9.x / netscape 6.x
						test your javascript/CSS to see if it works in these mozilla releases, if it does, just default it to:
						$safe_browser = true;
						*/
						if ( ( $moz_release_date < 20020400 ) || ( $moz_rv < 1 ) )
						{
							$safe_browser = false;
						}
						break;
					case 'ie':
						/*
						note we're adding in the trident/ search to return only first instance in case
						of msie 8, and we're triggering the  break last condition in the test, as well
						as the test for a second search string, trident/
						*/
						$browser_version_number = ViberFN::get_item_version( $browser_user_agent, $s_browser, true, 'trident/' );
						// construct the proper real number if it's in compat mode and msie 8.0
						if ( strstr( $browser_version_number, '7.' ) && strstr( $browser_user_agent, 'trident/4' ) )
						{
							// note that 7.0 becomes 8 when adding 1, but if it's 7.1 it will be 8.1
							$true_msie_version = $browser_version_number + 1;
						}
						// test for most modern msie instances
						if ( $browser_version_number >= 7 )
						{
							$ie_version = 'ie7x';
						}
						// then test for IE 5x mac, that's the most problematic IE out there
						elseif ( strstr( $browser_user_agent, 'mac') )
						{
							$ie_version = 'ieMac';
						}
						// this assigns a general ie id to the $ie_version variable
						elseif ( $browser_version_number >= 5 )
						{
							$ie_version = 'ie5x';
						}
						elseif ( ( $browser_version_number > 3 ) && ( $browser_version_number < 5 ) )
						{
							$dom_browser = false;
							$ie_version = 'ie4';
							// this depends on what you're using the script for, make sure this fits your needs
							$safe_browser = true;
						}
						else
						{
							$ie_version = 'old';
							$dom_browser = false;
							$safe_browser = false;
						}
						break;
					case 'op':
						$browser_version_number = ViberFN::get_item_version( $browser_user_agent, $s_browser );
						if ( $browser_version_number < 5 )// opera 4 wasn't very useable.
						{
							$safe_browser = false;
						}
						break;
					/*
					note: webkit returns always the webkit version number, not the specific user
					agent version, ie, webkit 583, not chrome 0.3
					*/
					case 'webkit':
						// note that this is the Webkit version number
						$browser_version_number = ViberFN::get_item_version( $browser_user_agent, $s_browser );
						// this is to pull out specific webkit versions, safari, google-chrome etc..
						$j_count = count( $a_webkit_types );
						for ( $j = 0; $j < $j_count; $j++ )
						{
							if ( strstr( $browser_user_agent, $a_webkit_types[$j] ) )
							{
								$webkit_type = $a_webkit_types[$j];
								// and this is the webkit type version number, like: chrome 1.2
								$webkit_type_number = ViberFN::get_item_version( $browser_user_agent, $webkit_type );
								// epiphany hack
								if ( $a_webkit_types[$j] == 'gtklauncher' )
								{
									$s_browser = 'Epiphany';
								}
								else
								{
									$s_browser = $a_webkit_types[$j];
								}
								break;
							}
						}
						break;
					default:
						$browser_version_number = ViberFN::get_item_version( $browser_user_agent, $s_browser );
						break;
				}
				// the browser was id'ed
				$b_success = true;
				break;
			}
		}

		//assigns defaults if the browser was not found in the loop test
		if ( !$b_success )
		{
			/*
			this will return the first part of the browser string if the above id's failed
			usually the first part of the browser string has the navigator useragent name/version in it.
			This will usually correctly id the browser and the browser number if it didn't get
			caught by the above routine.
			If you want a '' to do a if browser == '' type test, just comment out all lines below
			except for the last line, and uncomment the last line. If you want undefined values,
			the browser_name is '', you can always test for that
			*/
			// delete this part if you want an unknown browser returned
			$s_browser = substr( $browser_user_agent, 0, strcspn( $browser_user_agent , '();') );
			// this extracts just the browser name from the string, if something usable was found
			if ( $s_browser && preg_match( '/[^0-9][a-z]*-*\ *[a-z]*\ *[a-z]*/', $s_browser, $a_unhandled_browser ) )
			{
				$s_browser = $a_unhandled_browser[0];
				$browser_version_number = ViberFN::get_item_version( $browser_user_agent, $s_browser );
			}
			else
			{
				$s_browser = 'NA';
				$browser_version_number = 'NA';
			}

			// then uncomment this part
			//$s_browser = '';//deletes the last array item in case the browser was not a match
		}
		// get os data, mac os x test requires browser/version information, this is a change from older scripts
		if ( $b_os_test )
		{
			$a_os_data = ViberFN::get_os_data( $browser_user_agent, $browser_name, $browser_version_number );
			$os_type = $a_os_data[0];// os name, abbreviated
			$os_number = $a_os_data[1];// os number or version if available
		}
		/*
		this ends the run through once if clause, set the boolean
		to true so the function won't retest everything
		*/
		$b_repeat = true;
		/*
		pulls out primary version number from more complex string, like 7.5a,
		use this for numeric version comparison
		*/
		if ( $browser_version_number && preg_match( '/[0-9]*\.*[0-9]*/', $browser_version_number, $a_math_version_number ) )
		{
			$math_version_number = $a_math_version_number[0];
			//print_r($a_math_version_number);
		}
		if ( $b_mobile_test )
		{
			$mobile_test = ViberFN::check_is_mobile( $browser_user_agent );
			if ( $mobile_test )
			{
				$a_mobile_data = ViberFN::get_mobile_data( $browser_user_agent );
				$ua_type = 'mobile';
			}
		}
	}
	//$browser_version_number = $_SERVER["REMOTE_ADDR"];
	/*
	This is where you return values based on what parameter you used to call the function
	$which_test is the passed parameter in the initial browser_detection('os') for example call
	*/
	// assemble these first so they can be included in full return data
	$a_moz_data = array( $moz_type, $moz_version_number, $moz_rv, $moz_rv_full, $moz_release_date );
	$a_webkit_data = array( $webkit_type, $webkit_type_number, $browser_version_number );

	switch ( $which_test )
	{
		case 'safe':// returns true/false if your tests determine it's a safe browser
			/*
			you can change the tests to determine what is a safeBrowser for your scripts
			in this case sub rv 1 Mozillas and Netscape 4x's trigger the unsafe condition
			*/
			return $safe_browser;
			break;
		case 'ie_version': // returns ieMac or ie5x
			return $ie_version;
			break;
		case 'moz_version':// returns array of all relevant moz information
			return $a_moz_data;
			break;
		case 'webkit_version':// returns array of all relevant webkit information
			return $a_webkit_data;
			break;
		case 'dom':// returns true/fale if a DOM capable browser
			return $dom_browser;
			break;
		case 'os':// returns os name
			return $os_type;
			break;
		case 'os_number':// returns os number if windows
			return $os_number;
			break;
		case 'browser':// returns browser name
			return $browser_name;
			break;
		case 'number':// returns browser number
			return $browser_version_number;
			break;
		case 'full':// returns all relevant browser information in an array
			$a_full_data = array( $browser_name, $browser_version_number, $ie_version, $dom_browser, $safe_browser, $os_type, $os_number, $s_browser, $ua_type, $math_version_number, $a_moz_data, $a_webkit_data, $mobile_test, $a_mobile_data, $true_msie_version );
			// print_r( $a_full_data );
			return $a_full_data;
			break;
		case 'type':// returns what type, bot, browser, maybe downloader in future
			return $ua_type;
			break;
		case 'math_number':// returns numerical version number, for number comparisons
			return $math_version_number;
			break;
		case 'mobile_test':
			return $mobile_test;
			break;
		case 'mobile_data':
			return $a_mobile_data;
			break;
		case 'true_msie_version':
			return $true_msie_version;
			break;
		default:
			break;
	}
}

// gets which os from the browser string

public static function get_os_data ( $pv_browser_string, $pv_browser_name, $pv_version_number  )
{
	// initialize variables
	$os_working_type = '';
	$os_working_number = '';
	/*
	packs the os array. Use this order since some navigator user agents will put 'macintosh'
	in the navigator user agent string which would make the nt test register true
	*/
	$a_mac = array( 'intel mac', 'ppc mac', 'mac68k' );// this is not used currently
	// same logic, check in order to catch the os's in order, last is always default item
	$a_unix_types = array( 'freebsd', 'openbsd', 'netbsd', 'bsd', 'unixware', 'solaris', 'sunos', 'sun4', 'sun5', 'suni86', 'sun', 'irix5', 'irix6', 'irix', 'hpux9', 'hpux10', 'hpux11', 'hpux', 'hp-ux', 'aix1', 'aix2', 'aix3', 'aix4', 'aix5', 'aix', 'sco', 'unixware', 'mpras', 'reliant', 'dec', 'sinix', 'unix' );
	// only sometimes will you get a linux distro to id itself...
	$a_linux_distros = array( 'ubuntu', 'kubuntu', 'xubuntu', 'mepis', 'xandros', 'linspire', 'winspire', 'sidux', 'kanotix', 'debian', 'opensuse', 'suse', 'fedora', 'redhat', 'slackware', 'slax', 'mandrake', 'mandriva', 'gentoo', 'sabayon', 'linux' );
	$a_linux_process = array ( 'i386', 'i586', 'i686' );// not use currently
	// note, order of os very important in os array, you will get failed ids if changed
	$a_os_types = array( 'android', 'blackberry', 'iphone', 'ipad', 'ipod', 'palmos', 'palmsource', 'symbian', 'beos', 'os2', 'amiga', 'webtv', 'mac', 'nt', 'win', $a_unix_types, $a_linux_distros );

	//os tester
	$i_count = count( $a_os_types );
	for ( $i = 0; $i < $i_count; $i++ )
	{
		// unpacks os array, assigns to variable $a_os_working
		$os_working_data = $a_os_types[$i];
		/*
		assign os to global os variable, os flag true on success
		!strstr($pv_browser_string, "linux" ) corrects a linux detection bug
		*/
		if ( !is_array( $os_working_data ) && strstr( $pv_browser_string, $os_working_data ) && !strstr( $pv_browser_string, "linux" ) )
		{
			$os_working_type = $os_working_data;
			switch ( $os_working_type )
			{
				// most windows now uses: NT X.Y syntax
				case 'nt':
					if ( strstr( $pv_browser_string, 'nt 6.2' ) )// windows 7
					{
						/*$os_working_number = 6.1;
						$os_working_type = 'nt';*/
						$os_working_number = 8;
						$os_working_type = 'WIN';
						
					}
					elseif ( strstr( $pv_browser_string, 'nt 6.1' ) )// windows 7
					{
						/*$os_working_number = 6.1;
						$os_working_type = 'nt';*/
						$os_working_number = 7;
						$os_working_type = 'WIN';
						
					}
					elseif ( strstr( $pv_browser_string, 'nt 6.0' ) )// windows vista/server 2008
					{
						/*$os_working_number = 6.0;
						$os_working_type = 'nt';*/
						$os_working_type = 'WIN VISTA';
					}
					elseif ( strstr( $pv_browser_string, 'nt 5.2' ) )// windows server 2003
					{
						$os_working_number = 2003;
						$os_working_type = 'WIN SERV';
					}
					elseif ( strstr( $pv_browser_string, 'nt 5.1' ) || strstr( $pv_browser_string, 'xp' ) )// windows xp
					{
						$os_working_type = 'WIN XP';
					}
					elseif ( strstr( $pv_browser_string, 'nt 5' ) || strstr( $pv_browser_string, '2000' ) )// windows 2000
					{
						$os_working_number = 5.0;
					}
					elseif ( strstr( $pv_browser_string, 'nt 4' ) )// nt 4
					{
						$os_working_number = 4;
					}
					elseif ( strstr( $pv_browser_string, 'nt 3' ) )// nt 4
					{
						$os_working_number = 3;
					}
					break;
				case 'win':
					if ( strstr( $pv_browser_string, 'vista' ) )// windows vista, for opera ID
					{
						$os_working_number = 6.0;
						$os_working_type = 'nt';
					}
					elseif ( strstr( $pv_browser_string, 'xp' ) )// windows xp, for opera ID
					{
						$os_working_number = 5.1;
						$os_working_type = 'nt';
					}
					elseif ( strstr( $pv_browser_string, '2003' ) )// windows server 2003, for opera ID
					{
						$os_working_number = 5.2;
						$os_working_type = 'nt';
					}
					elseif ( strstr( $pv_browser_string, 'windows ce' ) )// windows CE
					{
						$os_working_number = 'ce';
						$os_working_type = 'nt';
					}
					elseif ( strstr( $pv_browser_string, '95' ) )
					{
						$os_working_number = '95';
					}
					elseif ( ( strstr( $pv_browser_string, '9x 4.9' ) ) || ( strstr( $pv_browser_string, 'me' ) ) )
					{
						$os_working_number = 'me';
					}
					elseif ( strstr( $pv_browser_string, '98' ) )
					{
						$os_working_number = '98';
					}
					elseif ( strstr( $pv_browser_string, '2000' ) )// windows 2000, for opera ID
					{
						$os_working_number = 5.0;
						$os_working_type = 'nt';
					}
					break;
				case 'mac':
					if ( strstr( $pv_browser_string, 'os x' ) )
					{
						$os_working_number = 'os x';
					}
					/*
					this is a crude test for os x, since safari, camino, ie 5.2, & moz >= rv 1.3
					are only made for os x
					*/
					elseif ( ( $pv_browser_name == 'saf' ) || ( $pv_browser_name == 'cam' ) ||
						( ( $pv_browser_name == 'moz' ) && ( $pv_version_number >= 1.3 ) ) ||
						( ( $pv_browser_name == 'ie' ) && ( $pv_version_number >= 5.2 ) ) )
					{
						$os_working_number = 10;
					}
					break;
				case 'iphone':
				case 'ipad':
					$os_working_number = "OS ".preg_replace("/(.*) os ([0-9]*)_([0-9]*)(.*)/","$2_$3", $pv_browser_string);
					break;
				default:
					break;
			}
			break;
		}
		/*
		check that it's an array, check it's the second to last item
		in the main os array, the unix one that is
		*/
		elseif ( is_array( $os_working_data ) && ( $i == ( $i_count - 2 ) ) )
		{
			$j_count = count($os_working_data);
			for ($j = 0; $j < $j_count; $j++)
			{
				if ( strstr( $pv_browser_string, $os_working_data[$j] ) )
				{
					$os_working_type = 'unix'; //if the os is in the unix array, it's unix, obviously...
					$os_working_number = ( $os_working_data[$j] != 'unix' ) ? $os_working_data[$j] : '';// assign sub unix version from the unix array
					break;
				}
			}
		}
		/*
		check that it's an array, check it's the last item
		in the main os array, the linux one that is
		*/
		elseif ( is_array( $os_working_data ) && ( $i == ( $i_count - 1 ) ) )
		{
			$j_count = count($os_working_data);
			for ($j = 0; $j < $j_count; $j++)
			{
				if ( strstr( $pv_browser_string, $os_working_data[$j] ) )
				{
					$os_working_type = 'lin';
					// assign linux distro from the linux array, there's a default
					//search for 'lin', if it's that, set version to ''
					$os_working_number = ( $os_working_data[$j] != 'linux' ) ? $os_working_data[$j] : '';
					break;
				}
			}
		}
	}

	// pack the os data array for return to main function
	$a_os_data = array( $os_working_type, $os_working_number );

	return $a_os_data;
}
/*
Function Info:
function returns browser number, gecko rv number, or gecko release date
function get_item_version( $browser_user_agent, $search_string, $substring_length )
$pv_extra_search='' allows us to set an additional search/exit loop parameter, but we
only want this running when needed
*/
public static function get_item_version( $pv_browser_user_agent, $pv_search_string, $pv_b_break_last='', $pv_extra_search='' )
{
	// 12 is the longest that will be required, handles release dates: 20020323; 0.8.0+
	$substring_length = 12;
	$start_pos = 0; // set $start_pos to 0 for first iteration
	//initialize browser number, will return '' if not found
	$string_working_number = '';

	/*
	use the passed parameter for $pv_search_string
	start the substring slice right after these moz search strings
	there are some cases of double msie id's, first in string and then with then number
	$start_pos = 0;
	this test covers you for multiple occurrences of string, only with ie though
	with for example google bot you want the first occurance returned, since that's where the
	numbering happens
	*/
	for ( $i = 0; $i < 4; $i++ )
	{
		//start the search after the first string occurrence
		if ( strpos( $pv_browser_user_agent, $pv_search_string, $start_pos ) !== false )
		{
			// update start position if position found
			$start_pos = strpos( $pv_browser_user_agent, $pv_search_string, $start_pos ) + strlen( $pv_search_string );
			/*
			msie (and maybe other userAgents requires special handling because some apps inject
			a second msie, usually at the beginning, custom modes allow breaking at first instance
			if $pv_b_break_last $pv_extra_search conditions exist. Since we only want this test
			to run if and only if we need it, it's triggered by caller passing these values.
			*/
			if ( !$pv_b_break_last || ( $pv_extra_search && strstr( $pv_browser_user_agent, $pv_extra_search ) ) )
			{
				break;
			}
		}
		else
		{
			break;
		}
	}
	/*
	this is just to get the release date, not other moz information
	also corrects for the omniweb 'v'
	*/
	if ( $pv_search_string != 'gecko/' )
	{
		if ( $pv_search_string == 'omniweb' )
		{
			$start_pos += 2;// handles the v in 'omniweb/v532.xx
		}
		else
		{
			$start_pos++;
		}
	}

	// Initial trimming
	$string_working_number = substr( $pv_browser_user_agent, $start_pos, $substring_length );

	// Find the space, ;, or parentheses that ends the number
	$string_working_number = substr( $string_working_number, 0, strcspn($string_working_number, ' );') );

	//make sure the returned value is actually the id number and not a string
	// otherwise return ''
	if ( !is_numeric( substr( $string_working_number, 0, 1 ) ) )
	{
		$string_working_number = '';
	}
	//$browser_number = strrpos( $pv_browser_user_agent, $pv_search_string );
	return $string_working_number;
}

/*
Special ID notes:
Novarra-Vision is a Content Transformation Server (CTS)
*/
public static function check_is_mobile( $pv_browser_user_agent )
{
	$mobile_working_test = '';
	/*
	these will search for basic mobile hints, this should catch most of them, first check
	known hand held device os, then check device names, then mobile browser names
	This list is almost the same but not exactly as the 4 arrays in function below
	*/
	$a_mobile_search = array(
	// os
	'android', 'epoc', 'linux armv', 'palmos', 'palmsource', 'windows ce', 'symbianos', 'symbian os', 'symbian',
	// devices
	'benq', 'blackberry', 'danger hiptop', 'ddipocket', 'iphone', 'kindle', 'lge-cx', 'lge-lx', 'lge-mx', 'lge vx', 'lge ', 'lge-', 'lg;lx', 'nintendo wii', 'nokia', 'palm', 'pdxgw', 'playstation', 'sagem', 'samsung', 'sec-sgh', 'sharp', 'sonyericsson', 'sprint', 'vodaphone', 'j-phone', 'n410', 'mot 24', 'mot-', 'htc-', 'htc_', 'sec-', 'sie-m', 'sie-s', 'spv ', 'smartphone', 'armv', 'midp', 'mobilephone',
	// browsers
	'avantgo', 'blazer', 'elaine', 'eudoraweb', 'iemobile',  'minimo', 'opera mobi', 'opera mini', 'netfront', 'opwv', 'polaris', 'semc-browser', 'up.browser', 'webpro', 'wms pie', 'xiino',
	// services
	'astel',  'docomo',  'novarra-vision', 'portalmmm', 'reqwirelessweb'
	);

	// then do basic mobile type search, this uses data from: get_mobile_data()
	$j_count = count( $a_mobile_search );
	for ($j = 0; $j < $j_count; $j++)
	{
		if ( strstr( $pv_browser_user_agent, $a_mobile_search[$j] ) )
		{
			$mobile_working_test = $a_mobile_search[$j];
			break;
		}
	}

	return $mobile_working_test;
}


/*
thanks to this page: http://www.zytrax.com/tech/web/mobile_ids.html
for data used here
*/
public static function get_mobile_data( $pv_browser_user_agent )
{
	$mobile_browser = '';
	$mobile_browser_number = '';
	$mobile_device = '';
	$mobile_os = ''; // will usually be null, sorry
	$mobile_os_number = '';
	$mobile_server = '';
	$mobile_server_number = '';

	// browsers, show it as a handheld, but is not the os
	$a_mobile_browser = array( 'avantgo', 'blazer', 'elaine', 'eudoraweb', 'iemobile',  'minimo', 'mobileexplorer', 'opera mobi', 'opera mini', 'netfront', 'opwv', 'polaris', 'semc-browser', 'up.browser', 'webpro', 'wms pie', 'xiino' );
	/*
	This goes from easiest to detect to hardest, so don't use this for output unless you
	clean it up more is my advice.
	*/
	$a_mobile_device = array( 'benq', 'blackberry', 'danger hiptop', 'ddipocket', 'iphone', 'kindle', 'lge-cx', 'lge-lx', 'lge-mx', 'lge vx', 'lg;lx', 'nintendo wii', 'nokia', 'palm', 'pdxgw', 'playstation', 'sagem', 'samsung', 'sec-sgh', 'sharp', 'sonyericsson', 'sprint', 'vodaphone', 'j-phone', 'n410', 'mot 24', 'mot-', 'htc-', 'htc_',  'lge ', 'lge-', 'sec-', 'sie-m', 'sie-s', 'spv ', 'smartphone', 'armv', 'midp', 'mobilephone' );
	// note: linux alone can't be searched for, and almost all linux devices are armv types
	$a_mobile_os = array( 'android', 'epoc', 'palmos', 'palmsource', 'windows ce', 'symbianos', 'symbian os', 'symbian', 'linux armv'  );

	// sometimes there is just no other id for the unit that the CTS type service/server
	$a_mobile_server = array( 'astel', 'docomo', 'novarra-vision', 'portalmmm', 'reqwirelessweb' );

	$k_count = count( $a_mobile_browser );
	for ( $k = 0; $k < $k_count; $k++ )
	{
		if ( strstr( $pv_browser_user_agent, $a_mobile_browser[$k] ) )
		{
			$mobile_browser = $a_mobile_browser[$k];
			// this may or may not work, highly unreliable
			$mobile_browser_number = ViberFN::get_item_version( $pv_browser_user_agent, $mobile_browser );
			break;
		}
	}
	$k_count = count( $a_mobile_device );
	for ( $k = 0; $k < $k_count; $k++ )
	{
		if ( strstr( $pv_browser_user_agent, $a_mobile_device[$k] ) )
		{
			$mobile_device = $a_mobile_device[$k];
			break;
		}
	}
	$k_count = count( $a_mobile_os );
	for ( $k = 0; $k < $k_count; $k++ )
	{
		if ( strstr( $pv_browser_user_agent, $a_mobile_os[$k] ) )
		{
			$mobile_os = $a_mobile_os[$k];
			// this may or may not work, highly unreliable
			$mobile_os_number = ViberFN::get_item_version( $pv_browser_user_agent, $mobile_os );
			break;
		}
	}
	$k_count = count( $a_mobile_server );
	for ( $k = 0; $k < $k_count; $k++ )
	{
		if ( strstr( $pv_browser_user_agent, $a_mobile_server[$k] ) )
		{
			$mobile_server = $a_mobile_server[$k];
			// this may or may not work, highly unreliable
			$mobile_server_number = ViberFN::get_item_version( $pv_browser_user_agent, $a_mobile_server );
			break;
		}
	}
	// just for cases where we know it's a mobile device already
	if ( !$mobile_os && ( $mobile_browser || $mobile_device || $mobile_server ) && strstr( $pv_browser_user_agent, 'linux' ) )
	{
		$mobile_os = 'linux';
		$mobile_os_number = ViberFN::get_item_version( $pv_browser_user_agent, 'linux' );
	}

	$a_mobile_data = array( $mobile_device, $mobile_browser, $mobile_browser_number, $mobile_os, $mobile_os_number, $mobile_server, $mobile_server_number );
	return $a_mobile_data;
}
}
?>