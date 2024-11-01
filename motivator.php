<?php
	/*
	Plugin Name: Social Share Motivator
	Plugin URI: https://www.thefreewindows.com/15816/reveal-posts-visitors-share-social-networks/
	Description: Social Share Motivator makes your pages hardly visible urging your visitors to share your blog on Facebook, Twitter or Google Plus, to enjoy easy access. You can check the <a href="https://www.thefreewindows.com/15816/reveal-posts-visitors-share-social-networks/" target="_blank">Home Page</a> for more information.
	Version: 4.9
	Author: TheFreeWindows
	Author URI: https://www.thefreewindows.com
	*/
	
	$wp_scripts = new WP_Scripts();

	register_activation_hook( __FILE__, 'set_up_options' );

	function set_up_options(){	
	  add_option('socialsharemotivatorpo_home', 'checked');
	  add_option('socialsharemotivatorpo_post', 'checked');
	  add_option('socialsharemotivatorpo_category', 'checked');
	  add_option('socialsharemotivatorpo_tag', 'checked');
	  add_option('socialsharemotivatorpo_page', 'checked');
	  add_option('socialsharemotivatorpo_archive', 'checked');  
	}

	if (!is_admin()) {
		wp_enqueue_script("jquery");
		wp_deregister_script('facebooksdk');
		wp_register_script('facebooksdk', 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2');
		wp_enqueue_script("facebooksdk");
		wp_deregister_script('twittersdk');
		wp_register_script('twittersdk', 'https://platform.twitter.com/widgets.js');
		wp_enqueue_script("twittersdk");
	}
	
	if (!class_exists('socialsharemotivator_class')) :
	// DEFINE PLUGIN ID
	define('SOCIAL_SHARE_MOTIVATOR_PLUGINOPTIONS_ID', 'socialsharemotivator-plugin-options');
	// DEFINE PLUGIN NICK
	define('SOCIAL_SHARE_MOTIVATOR_PLUGINOPTIONS_NICK', 'Social Share Motivator');
	
	class socialsharemotivator_class {
		
		function __construct() {
	if (is_admin()) {	
		add_action('wp_ajax_sosharemotivator', array(&$this, "sosharemotivator_callback"));
		add_action('wp_ajax_nopriv_sosharemotivator', array(&$this, "sosharemotivator_callback"));	
		add_action('admin_init', array(&$this, 'register'));
	    add_action('admin_menu', array(&$this, 'menu'));	
	} else {
		add_action("wp_head", array(&$this, "front_header"));
		add_action("wp_footer", array(&$this, "front_footer"));	
	}
		}
	
		public static function file_path($file)
		{
	return ABSPATH.'wp-content/plugins/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).$file;
		}
		
		public static function register()
		{
		register_setting(SOCIAL_SHARE_MOTIVATOR_PLUGINOPTIONS_ID.'_options', 'socialsharemotivatorpo_showCloseButton');
		register_setting(SOCIAL_SHARE_MOTIVATOR_PLUGINOPTIONS_ID.'_options', 'socialsharemotivatorpo_title');
		register_setting(SOCIAL_SHARE_MOTIVATOR_PLUGINOPTIONS_ID.'_options', 'socialsharemotivatorpo_url');
		register_setting(SOCIAL_SHARE_MOTIVATOR_PLUGINOPTIONS_ID.'_options', 'socialsharemotivatorpo_sitedesc');
		register_setting(SOCIAL_SHARE_MOTIVATOR_PLUGINOPTIONS_ID.'_options', 'socialsharemotivatorpo_post');
		register_setting(SOCIAL_SHARE_MOTIVATOR_PLUGINOPTIONS_ID.'_options', 'socialsharemotivatorpo_category');
		register_setting(SOCIAL_SHARE_MOTIVATOR_PLUGINOPTIONS_ID.'_options', 'socialsharemotivatorpo_tag');
		register_setting(SOCIAL_SHARE_MOTIVATOR_PLUGINOPTIONS_ID.'_options', 'socialsharemotivatorpo_page');
		register_setting(SOCIAL_SHARE_MOTIVATOR_PLUGINOPTIONS_ID.'_options', 'socialsharemotivatorpo_archive');
		register_setting(SOCIAL_SHARE_MOTIVATOR_PLUGINOPTIONS_ID.'_options', 'socialsharemotivatorpo_home');
		register_setting(SOCIAL_SHARE_MOTIVATOR_PLUGINOPTIONS_ID.'_options', 'socialsharemotivatorpo_singpages');
		register_setting(SOCIAL_SHARE_MOTIVATOR_PLUGINOPTIONS_ID.'_options', 'socialsharemotivatorpo_singposts');
		register_setting(SOCIAL_SHARE_MOTIVATOR_PLUGINOPTIONS_ID.'_options', 'socialsharemotivatorpo_singpagesdis');
		register_setting(SOCIAL_SHARE_MOTIVATOR_PLUGINOPTIONS_ID.'_options', 'socialsharemotivatorpo_singpostsdis');
		register_setting(SOCIAL_SHARE_MOTIVATOR_PLUGINOPTIONS_ID.'_options', 'socialsharemotivatorpo_colors');
		}
		/** function/method
		* Usage: hooking (registering) the plugin menu
		* Arg(0): null
		* Return: void
		*/
		public static function menu()
		{
	// Create menu tab
	add_options_page(SOCIAL_SHARE_MOTIVATOR_PLUGINOPTIONS_NICK.' Plugin Options', SOCIAL_SHARE_MOTIVATOR_PLUGINOPTIONS_NICK, 'manage_options', SOCIAL_SHARE_MOTIVATOR_PLUGINOPTIONS_ID, array('socialsharemotivator_class', 'options_page'));      
		}
		
		public static function options_page()
		{
	if (!current_user_can('manage_options'))
	{
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
		
	$plugin_id = SOCIAL_SHARE_MOTIVATOR_PLUGINOPTIONS_ID;
	// display options page
	include(self::file_path('options.php'));
		}
		
		/** function/method
		* Usage: filtering the content
		* Arg(1): string
		* Return: string
		*/
	  function front_header() {
	echo '<link type="text/css" rel="stylesheet" href="'.site_url().'/wp-content/plugins/'.basename(dirname(__FILE__)).'/css/faceboxmodal.css">';
	echo '<script type="text/javascript" src="'.site_url().'/wp-content/plugins/'.basename(dirname(__FILE__)).'/js/faceboxmodal.js"></script>';
		}
	  
		function sosharemotivator_callback() {
	global $wpdb;
	    $cookie_value = "0|0|0";	
	    if (!empty($_COOKIE["__socialsharemotivator"])){
		$cookie_value = $_COOKIE["__socialsharemotivator"];
	}
	    $cookies = explode("|", $cookie_value);
	switch ($_POST['network']) {
		case "facebook":
	        $cookie_value = "1|".$cookies[1]."|".$cookies[2];
	break;
	      case "twitter":
	        $cookie_value = $cookies[0]."|".$cookies[1]."|1";
	        break;
		default:
	break;
	}
	setcookie("__socialsharemotivator", "1", time()+3600*24*90, "/");
		}
	

		function front_footer() {
	global $wpdb;
	$showCategory = get_option('socialsharemotivatorpo_category');
	$showTag = get_option('socialsharemotivatorpo_tag');
	$showPost = get_option('socialsharemotivatorpo_post');
	$showPage = get_option('socialsharemotivatorpo_page');
	$showArchive = get_option('socialsharemotivatorpo_archive');
	$showHome = get_option('socialsharemotivatorpo_home');
	$showSingposts = get_option('socialsharemotivatorpo_singposts');
	$showSingpages =  get_option('socialsharemotivatorpo_singpages');
	$showSingpostsdis = get_option('socialsharemotivatorpo_singpostsdis');
	$showSingpagesdis =  get_option('socialsharemotivatorpo_singpagesdis');
		if (empty($showCategory) && is_category()) return;
		if (empty($showTag) && is_tag()) return;
		if (empty($showArchive) && is_archive()) return;
		if (empty($showHome) && (is_home() || is_front_page()) ) return; 
	    
		if ( ! is_array( $showSingposts ) )
	    $showSingposts = explode( ',', $showSingposts );
		if ( !is_single( $showSingposts ) && empty($showPost) && is_single() ) return;	
	        
		if ( ! is_array( $showSingpages ) )
	    $showSingpages = explode( ',', $showSingpages );	
		if ( !is_page( $showSingpages ) && empty($showPage) && is_page() ) return;
	
		if ( ! is_array( $showSingpostsdis ) )
	    $showSingpostsdis = explode( ',', $showSingpostsdis );
		if ( is_single( $showSingpostsdis ) && !empty($showPost) && is_single() ) return;	
	
		if ( ! is_array( $showSingpagesdis ) )
	    $showSingpagesdis = explode( ',', $showSingpagesdis );	
		if ( is_page( $showSingpagesdis ) && !empty($showPage) && is_page() ) return;
	
	$cookie_value = "";	
	if (!empty($_COOKIE["__socialsharemotivator"])){
		$cookie_value = $_COOKIE["__socialsharemotivator"];
	}
	$popupTitle = get_option('socialsharemotivatorpo_title');
	$colors = get_option('socialsharemotivatorpo_colors');
	$showClose = '';
	if (get_option('socialsharemotivatorpo_showCloseButton'))
		$showClose = 'jQuery(".popup").append(\'<a class="close" href="#"><img class="close_image" title="close" src="'.site_url().'/wp-content/plugins/'.basename(dirname(__FILE__)).'/images/closelabel.png"></a>\'); 
		jQuery("#facebox .close").click(jQuery.facebox.close);';
	if (get_option('socialsharemotivatorpo_url') != '') {
		$url = 'href="'.get_option('socialsharemotivatorpo_url').'"';
		$twitterUrl = 'data-url="'.get_option('socialsharemotivatorpo_url').'"';
	if (get_option('socialsharemotivatorpo_sitedesc') != '') 
		$twittersitedesc = 'data-text="'.get_option('socialsharemotivatorpo_sitedesc').'"';
	}
	
	if ($cookie_value != "1"){
		echo '	
		<div id="fb-root"></div>
		<script type="text/javascript">
	FB.XFBML.parse();
		</script>
		<script type="text/javascript">
		var sosharemotivator_use = false;
		FB.init();
		jQuery(document).ready(function() {
	FB.Event.subscribe("edge.create", function(href, widget) { 
		var data = {action: "sosharemotivator", network: "facebook"};
		jQuery.post("'.admin_url('admin-ajax.php').'", data, function(response) {
	if (sosharemotivator_use) location.reload();
		});
	});
	twttr.ready(function (twttr) {
		twttr.events.bind("tweet", function(event) {
	var data = {action: "sosharemotivator", network: "twitter"};
	jQuery.post("'.admin_url('admin-ajax.php').'", data, function(response) {
		if (sosharemotivator_use) location.reload();
	});
		});
	});
		});
		</script>


		<div id="sosharemotivator" style="display:none;">
	<div class="socialviral-box" style="'.$colors.';">                
	'.$popupTitle.'
	  <div class="ssm-socials">
		<div><fb:like layout="box_count" show_faces="false" '.$url.'></fb:like></div>
		<div><a class="twitter-share-button" '.$twittersitedesc.' data-count="vertical" '.$twitterUrl.'>Tweet</a></div>
	  </div>
	</div>
	<div class="ssm-author"><a href="https://www.thefreewindows.com/15816/reveal-posts-visitors-share-social-networks/" target="_blank">Powered by Social Share Motivator</a></div>	
	</div>
		<script type="text/javascript">	  
		  sosharemotivator_use = true;
		  jQuery(document).ready(function() {            
	  jQuery.facebox({div: "#sosharemotivator", loadingImage: "'.site_url().'/wp-content/plugins/'.basename(dirname(__FILE__)).'/images/loading.gif"});
	  '.$showClose.'	  
	});
		</script>
		';
	}
		}
	}
	
	$sosharemotivator = new socialsharemotivator_class();
	
	if (isset($sosharemotivator)) { 
		function plugin_settings_link($links) { 
	$settings_link = '<a href="options-general.php?page='.SOCIAL_SHARE_MOTIVATOR_PLUGINOPTIONS_ID.'">Settings</a> | <a href="https://www.thefreewindows.com/15875/social-share-motivator-fqa/">FQA</a>'; 
	array_unshift($links, $settings_link); return $links; 
		} 
		$plugin = plugin_basename(__FILE__); 
		add_filter("plugin_action_links_$plugin", 'plugin_settings_link'); 
	}
	
	endif;
	?>