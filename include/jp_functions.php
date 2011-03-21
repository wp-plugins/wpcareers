<?php

/**
 * File Name: jp_functions.php
 * Description: This file is part of wpCareers wordpress plugin
 * @author Mohammad Forgani
 * @copyright Copyright 2008, Oh Jung-Su
 * @version 1.0
 * @link http://www.forgani.com
 */


if (!isset($_SESSION)) @session_start(); 
if (!defined('AUTOLOAD_SMARTY')) { define('AUTOLOAD_SMARTY', true); }

if (AUTOLOAD_SMARTY) {
	// include required files for Smarty
	require_once(dirname(__FILE__)	. '/Smarty/Smarty.class.php');
}

/**
 * Class constructor
 * Define template engine configuration settings
 */
class wpca_smarty_compiler_class extends WPCA_Smarty { 
	function wpca_smarty_compiler_class($cache = true, $cache_lifetime = 0){
		global $wpcareers;
		$this->Smarty();
		$this->template_dir = $wpcareers->template_dir;
		$this->compile_dir = $wpcareers->compile_dir;
		$this->config_dir = $wpcareers->config_dir;
		$this->cache_dir = $wpcareers->cache_dir;
		$this->caching = $cache;
		$this->cache_lifetime = $cache_lifetime;
	}
}

function wpcareers_rewrite_rules_wp($wp_rewrite){
	global $wp_rewrite;
	$wpca_settings = get_option('wpcareers');
	$wpca_slug = $wpca_settings['wpcareers_slug'];
	$wpca_rules = array(
	$wpca_slug.'/([^/\(\)]*)/?([^/\(\)]*)/?([^/\(\)]*)/?' => '/'. $wpca_slug.'/index.php?pagename='. $wpca_slug.'&op=$matches[1]&id=$matches[2]&parent=$matches[3]');
	$wp_rewrite->rules = $wpca_rules + $wp_rewrite->rules;
}

function wpcareers_query_vars($vars){
	$vars[] = 'op';
	$vars[] = 'id';
	$vars[] = 'orderby';
	$vars[] = 'who';
	return $vars;
}

function wpcareers_excerpt_text($length, $text){
	$text = strip_tags(wpcareers_create_post_html($text));
	if(strlen($text)>$length){
	$ret_strpos = strpos($text, ' ', $length);
	$ret = substr($text, 0, $ret_strpos)." ...";
	} else{
	$ret = $text;
	}
	return $ret;
}

function wpcareers_page_handle_title($title){
	global $wpj_breadcrumbs;
	if ($wpj_breadcrumbs==""){
	$sidebar = 0;
	$wpj_breadcrumbs = wpcareers_get_breadcrumbs($sidebar);
	}
	return str_replace("[[WPCAREERS]]", $wpj_breadcrumbs, $title);
}

function wpcareers_page_handle_pagetitle($title){
	global $wpj_pagetitle;
	return str_replace("[[WPCAREERS]]", "WPCareers", $title);
}

function wpcareers_page_handle_content($content){
	if (preg_match('/\[\[WPCAREERS\]\]/', $content)){
	wpcareers_process();
	return "";
	} else {
	return $content;
	}
}

function wpcareers_page_handle_titlechange($title){
	global $wpj_breadcrumbs;
	$sidebar = 0;
	$wpj_breadcrumbs = wpcareers_get_breadcrumbs($sidebar);
	$wpca_settings = get_option('wpcareers');
	$title = str_replace($wpj_breadcrumbs, $wpca_settings["page_title"], $title);
	$title = str_replace("[[WPCAREERS]]", $wpca_settings["page_title"], $title);
	return $title;
}

function wpcareers_get_breadcrumbs($sidebar){
	global $_GET, $_POST, $table_prefix, $wpdb, $_SERVER;
	$g_action = get_query_var("op");
	$id = get_query_var("id");
	$parent = get_query_var("parent");
	if (basename($_SERVER['PHP_SELF'])!='index.php'){
		return "[[WPCAREERS]]";
		} else {
			$wpca_settings = get_option('wpcareers');
		if (!isset($_POST['search_terms']) && $sidebar=0) {
			$g_action = "sidebar";
		} elseif (!isset($_POST['search_terms'])) {
			$g_action = $g_action;
		} else {
			$g_action = "search";
		} 
		switch ($g_action){
			default:
			case "index":
			return '<strong class="wpj_breadcrumb">'. $wpca_settings['page_title'].'</strong>';
			break;
		}
	}
}

function wpcareers_create_link($action, $vars){
	global $wpdb, $table_prefix, $wp_rewrite, $wpcareers;

	$wpca_settings = get_option('wpcareers');
	$page_info = $wpcareers->get_pageinfo();
	$page_id = $page_info['ID'];
	if($wp_rewrite->using_permalinks()) $delim = "?";
	else $delim = "&amp;";
	
	$page = get_permalink($page_id);

	$main_link = $page . $delim;
	if (isset($vars['name'])) $name = trim($vars["name"]);
	if (isset($vars['id'])) $id = trim($vars["id"]);
	switch ($action){
	case "index":
		return "<a href=\"" . $main_link."/\">" . $name."</a>";
	break;
	case "indexLink":
		return $main_link;
	break;
	case "jpost":
	case "rpost":
		return "<a href=\"" . $main_link . "op=" . $action . "\">" . $name . "</a>";
	break;
	case "category":
	case "jlist":
	case "rlist":
	case "jview":
	case "rview":
	case "jmodify":
	case "rmodify":
	case "jdelete":
	case "rdelete":
		return "<a href=\"" . $main_link ."op=" . $action . "&amp;id=" . $id. "\">" . $name . "</a>";
	break;
	case "jdeleteform":
	case "rdeleteform":
		preg_match('/([^.]+)form/', $action, $matches);
		return $main_link."op=" . $matches[1] . "&amp;id=" . $id;
	break;
	case "jpostform":
	case "rpostform":
	case "searchform":
	case "jsendform":
	case "rsendform":
		preg_match('/([^.]+)form/', $action, $matches);
		return $main_link."op=" . $matches[1];
	break;
	case "jsend":
	case "rsend":
		return "<a style=\"color:green\" href=\"" . $main_link."op=" . $action . "&amp;id=" . $id."\">" . $name."</a>";
	break;
	}
}


function wpcareers_process(){
	global $wp_rewrite, $_GET, $_POST, $wpdb, $message;
	$page_id = get_query_var('page_id');
	$action = get_query_var('op');
	$wpca_settings = get_option('wpcareers');
	if (isset($_GET['id'])) $id = $_GET['id'];
	switch ($action){
	default:
	case "main":
		wpcareers_display_index($message);
	break;
	case "search":
		wpcareers_display_search($message);
	break;
	case "jpost":
		wpcareers_post_job($message, 0);
	break;
	case "rpost":
		wpcareers_post_resume($message, 0);
	break;
	case "jlist":
		if ($_GET['id']) wpcareers_list_jobs($message, $id);
	break;
	case "rlist":
		if ($_GET['id']) wpcareers_list_resumes($message, $id);
	break;
	case "jview":
		if ($_GET['id']) wpcareers_view_job($message);
	break;
	case "rview":
		if ($_GET['id']) wpcareers_view_resume($message);
	break;
	case "jmodify":
		if ($_GET['id']) wpcareers_post_job($message, 1);
	break;
	case "rmodify":
		if ($_GET['id']) wpcareers_post_resume($message, 1);
	break;
	case "jdelete":
		if ($_GET['id']) wpcareers_delete_job($message);
	break;
	case "rdelete":
		if ($_GET['id']) wpcareers_delete_resume($message);
	break;
	case "jsend":
		wpcareers_send_job($message);
	break;
	case "rsend":
		wpcareers_send_resume($message);
	break;
	}
}


function jp_create_navigation($id,$links,$addJobLink, $desc){
	global $table_prefix, $wpdb, $tpl;
	$again="";
	$sql="SELECT * FROM {$table_prefix}wpj_categories WHERE c_id=" . $id;
	$result=$wpdb->get_results($sql); 
	for ($i=0; $i<count($result); $i++){
		$row=$result[$i];
		$name=trim($row->c_title);
		$jp_job=wpcareers_create_link("category", array("name"=>$name, "id"=>$row->c_id, "parent"=>$row->cp_id));
		$links=$jp_job . ":" . $links;
		if ($row->cp_id>0)
			$addJobLink=wpcareers_create_link("postJobLink", array("name"=>"Post a job", "id"=>$row->c_id, "parent"=>$row->cp_id));
		$again=jp_create_navigation($row->cp_id, $links, $addurl, $row->c_description);
	}
	if ($id <> "0") {
		return $again;
	} else {
		$out=array($links, $addJobLink, $desc);
		return $out;
	}
}


function jp_getHtml($error) {
   global $wpdb, $wp_query, $lang;
   $message .= '<p>Please enter your information here. We will send you a new password</p>';
	if ($error) { 
		$message .= "<div id='login_error'><p>$error</p></div>"; 
	} 
   $message .= '<form name="lostpass" action="wp-login.php" method="post" id="lostpass">';
	$message .= '<p><input type="hidden" name="action" value="retrievepassword" />';
	$message .= '<label>Username:</label> <input type="text" name="user_login" id="user_login" value="" size="20" tabindex="1" />';
	$message .= '<br /><p><label>E-mail:</label> <input type="text" name="email" id="email" value="" size="25" tabindex="2" />';
	$message .= '<p><label for="captcha">'. $lang['J_COMFIMATION'] .'</label> ';
   $message .= '<img id="siimage" alt="ConfirmCode" align="middle" src="'.get_bloginfo('wpurl') .'/wp-content/plugins/wpcareers/include/jp_securimage_show.php?sid='. md5(time()) .'" />';
	$message .= '<br><span class ="smallTxt">'. $lang["J_VERIFICATION"] .'</span></p>';
	$message .= '<p><lable></lable> <input type="text" name="wpcareers[jp_captcha]" id="wpcareers[jp_captcha]" size="10"></p>';
	$message .= '</p><p class="submit"> <input type="submit" name="submit" id="submit" value="Retrieve Password" tabindex="3" /></p>';
	$message .= '</form><ul>';
	$message .= '<li><a href="'. $home . '" title="Are you lost?">Home</a></li>';
	$message .= '<li><a href="'. get_bloginfo('wpurl') .'/wp-register.php" title="Register">Register</a></li>';
   $message .= '<input type="hidden" name="redirect_to" value="'.$home.'" />';
   $message .= '</ul>';
   return $message;
}
      
function wpcareers_do_login(){
	global $wpdb, $error, $wp_query, $_REQUEST, $_GET, $_POST, $lang, $wpcareers, $wp_rewrite, $wpcareers;
	$tpl = wpcareers_display_header($message);
	if (!is_array($wp_query->query_vars))	$wp_query->query_vars = array();
	$wpca_settings = get_option('wpcareers');
   $securimage = new jp_securimage();
	$message = '';
	$error = '';
	$home = wpcareers_create_link("indexLink", 'undef');
   // TODO
	if ($wp_rewrite->get_page_permastruct() != '') {
		$selflink = get_option('home') . '/' . $wpca_settings['slug'];
	} else {
		# fixed for xampp
		$page_info = $wpcareers->get_pageinfo();
		$page_id = $page_info['ID'];
		$selflink = get_option('home') . "/?page_id=" . $page_id;
	}
   // TODO
	$selflink = get_option('home');
	if (!empty($_REQUEST['action'])) { 
		$action = $_REQUEST['action'];
	} else {
		wp_redirect( $selflink );
		exit();
	}
	nocache_headers();
	
	switch($action) {
	//logout
	case "logout":
		wp_clearcookie();
		do_action('wp_logout');
		nocache_headers();
		wp_redirect( $home );
		exit();
	break;
	case 'lostpassword':
		//do_action('lost_password');
		get_header();
		$message .= jp_getHtml($error);
		$tpl->assign('title', 'Retrieve Password');
		$tpl->assign('form', $message);
		$tpl->display('register.tpl');
		die();
	break;
	case 'retrievepassword':
		get_header();
		$user_data = get_userdatabylogin($_POST['user_login']);
		// redefining user_login ensures we return the right case in the email
		$user_login = $user_data->user_login;
		$user_email = $user_data->user_email;
		if (!$user_email || $user_email != $_POST['email']) {
			$message .= sprintf(__('<div class="error"><ul><li>Sorry, that user does not seem to exist in our database.<br />Perhaps you have the wrong username or e-mail address? <a href="%s">Try again</a>.</li></ul></div>'), 'wp-login.php?action=lostpassword');
		} 
		do_action('retrieve_password', $user_login);
		// Generate something random for a password... md5'ing current time with a rand salt
		$key = substr( md5( uniqid( microtime() ) ), 0, 50);
		// now insert the new pass md5'd into the db
		$wpdb->query("UPDATE $wpdb->users SET user_activation_key = '$key' WHERE user_login = '$user_login'");
		$mail = __('Someone has asked to reset the password for the following site and username.') . "\r\n\r\n";
		$mail .= get_option('siteurl') . "\r\n\r\n";
		$mail .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
		$mail .= __('To reset your password visit the following address, otherwise just ignore this email and nothing will happen.') . "\r\n\r\n";
		$mail .= get_settings('siteurl') . "/wp-login.php?action=resetpass&key=$key\r\n";
		$m = wp_mail($user_email, sprintf(__('[%s] Password Reset'), get_settings('blogname')), $mail);
		$message .= "<div id=\"login\">\n";
		if ($m == false) {
			$message .= ('<span class="jp_error"><h3>Problem</h3></span>');
			$message .= '<p>' . __('The e-mail could not be sent.') . "</p>\n";
		} else {
		$message .= "<h1>Success!</h1>";
			$message .= '<p>' . sprintf(__("The e-mail was sent successfully to %s's e-mail address."), $user_login) . '<br />';
			$message .= "<a href='wp-login.php' title='" . __('Check your e-mail first, of course') . "'>" . __('Click here to login!') . '</a></p>';
		} 
		$message .= "</div>\n";
		$tpl->assign('title', 'Retrieve Password');
		$tpl->assign('form', $message);
		$tpl->display('register.tpl');
		die();
	break;
	case 'resetpass' :
		$message .= "<div id=\"login\">\n";
		$key = preg_replace('/a-z0-9/i', '', $_GET['key']);
		if ( empty($key) ) {
			_e('<span class="jp_error"><h3>Problem</h3></span>');
			_e('Sorry, that key does not appear to be valid.');
			$message .= "</div>\n";
			$tpl->assign('title', 'Retrieve Password');
			$tpl->assign('form', $message);
			$tpl->display('register.tpl');
			die();
		} 
		$user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE user_activation_key = '$key'");
		if ( !$user ) {
			_e('<span class="jp_error"><h3>Problem</h3></span>');
			_e('Sorry, that key does not appear to be valid.');
			$message .= "</div>\n";
			$tpl->assign('title', 'Retrieve Password');
			$tpl->assign('form', $message);
			$tpl->display('register.tpl');
			die();
		} 
		do_action('password_reset');
		$new_pass = substr( md5( uniqid( microtime() ) ), 0, 7);
		$wpdb->query("UPDATE $wpdb->users SET user_pass = MD5('$new_pass'), user_activation_key = '' WHERE user_login = '$user->user_login'");
		wp_cache_delete($user->ID, 'users');
		wp_cache_delete($user->user_login, 'userlogins');	
		$message	.= sprintf(__('Username: %s'), $user->user_login) . "&nbsp;";
		$message .= sprintf(__('Password: %s'), $new_pass) . "&nbsp;";
		$message .= get_settings('siteurl') . "/wp-login.php\r\n";
		$m = wp_mail($user->user_email, sprintf(__('[%s] Your new password'), get_settings('blogname')), $message);
		if ($m == false) {
			$message .= ('<span class="jp_error"><h3>Problem</h3></span>');
			$message .= '<p>' . __('The e-mail could not be sent.') . "<br /></p>\n";
		} else {
         $message .= ('<h1>Success!</h1>');
			$message .= '<p>' . sprintf(__('Your new password is in the mail.'), $user_login) . '<br />';
			$message .= "<a href='wp-login.php' title='" . __('Check your e-mail first, of course') . "'>" . __('Click here to login!') . '</a></p>';
			// send a copy of password change notification to the admin
			$message .= sprintf(__('Password Lost and Changed for user: %s'), $user->user_login) . "\r\n";
			wp_mail(get_settings('admin_email'), sprintf(__('[%s] Password Lost/Change'), get_settings('blogname')), $message);
		} 
		$message .= "</div>\n";
		$tpl->assign('title', 'Retrieve Password');
		$tpl->assign('form', $message);
		$tpl->display('register.tpl');
		die();
	break;
	case 'login':
		$user_login = '';
		$user_pass = '';
		$using_cookie = false;
		if( $_POST ) {
			$user_login = $_POST['log'];
			$user_login = sanitize_user( $user_login );
			$user_pass	= $_POST['pwd'];
			$rememberme = $_POST['rememberme'];
			$redirect_to = $_POST['redirect_to'];
		} else {
			if (function_exists('wp_get_cookie_login')) {
				$cookie_login = wp_get_cookie_login();
				if ( !empty($cookie_login) ) {
					$using_cookie = true;
					$user_login = $cookie_login['login'];
					$user_pass = $cookie_login['password'];
				}
			} elseif ( !empty($_COOKIE) ) {
            if ( !empty($_COOKIE[USER_COOKIE]) )
               $user_login = $_COOKIE[USER_COOKIE];
				if ( !empty($_COOKIE[PASS_COOKIE]) ) {
					$user_pass = $_COOKIE[PASS_COOKIE];
					$using_cookie = true;
				}
			}
		}
		do_action('wp_authenticate', array(&$user_login, &$user_pass));
		if ( $user_login && $user_pass ) {
			$user = new WP_User(0, $user_login);
			if ( wp_login($user_login, $user_pass, $using_cookie) ) {
				if ( !$using_cookie ) wp_setcookie($user_login, $user_pass, false, '', '', $rememberme);
				do_action('wp_login', $user_login);
				wp_redirect($selflink);
				exit;
			} else {
				if ( $using_cookie ) {
               $error = __('<strong><span style="color:red">Error</span></strong>: Your session has expired.');
            }
			}
		} else if ( $user_login || $user_pass ) {
			$error = __('<strong><span style="color:red">Error</span></strong>: The password or login field is empty.');         
		}
      get_header();
      $message .= jp_getHtml($error);
      $tpl->assign('title', 'Login');
      $tpl->assign('form', $message);
      $tpl->display('register.tpl');
      die();
	break;
	default:
		wp_redirect($selflink);
	break;
	} 
	exit();
} 


function wpcareers_do_register(){
	global $wpdb, $errors, $user_ID, $wp_query, $lang, $wpcareers;

	$tpl = wpcareers_display_header($message);
	$home = wpcareers_create_link("indexLink", 'undef');
   $securimage = new jp_securimage();

	if (!is_array($wp_query->query_vars)) $wp_query->query_vars = array();
	switch( $_REQUEST["action"] ) {
	case 'register':
		require_once( ABSPATH . WPINC . '/registration-functions.php');
		$user_login = sanitize_user( $_POST['user_login'] );
		$user_email = $_POST['user_email'];
		$errors = array();
		if ( $user_login == '' )
			$errors['user_login'] = __('<span class="jp_error">ERROR</span>: Please enter a username!');
		if ($user_email == '') {
			$errors['user_email'] = __('<span class="jp_error">ERROR</span>: Please type your e-mail address.');
		} else if (!is_email($user_email)) {
			$errors['user_email'] = __('<span class="jp_error">ERROR</span>: The email address isn&#8217;t correct.');
			$user_email = '';
		}
		if ( ! validate_username($user_login) ) {
			$errors['user_login'] = __('<span class="jp_error">ERROR</span>: This username is invalid. Please enter a valid username.');
			$user_login = '';
		} 
		if ( username_exists( $user_login ) )
			$errors['user_login'] = __('<span class="jp_error">ERROR</span>: This username is already registered, please choose another one.');
		$email_exists = $wpdb->get_row("SELECT user_email FROM $wpdb->users WHERE user_email = '$user_email'");
		if ( $email_exists) {
			get_header();
			$err = (__('<span class="jp_error">ERROR</span>: This email address is already registered, please supply another.'));
			$error .= "<div id='login_error'>$err</div>";
			$tpl->assign('title', 'Registration');
			$tpl->assign('form', $error);
			$tpl->display('register.tpl');
			die();
		}
			
		$hash = $_POST['capcc_captchakey'];
		$text =$_POST['capcc_captcha'];
      if (!$securimage->check($_POST['capcc_captchakey'])) {
        $errors['error']='<span class="jp_error">ERROR</span>: The key you are attempting to use has expired.';
      }

		if ( 0 == count($errors) ) {
			$password = substr( md5( uniqid( microtime() ) ), 0, 7);
			$user_id = wp_create_user( $user_login, $password, $user_email );
			if ( !$user_id )	
				$errors['user_id'] = sprintf(__('<span class="jp_error">ERROR</span>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !'), get_settings('admin_email'));
			else wp_new_user_notification($user_id, $password);
		} 
		if ( 0 == count($errors) ) {//continues after the break; 
			get_header();
			$message .= '<p>Username: <strong>' . wp_specialchars($user_login) . '</strong>';
			$message .= '&nbsp;Password: <strong>' . __('emailed to you') . '</strong><br />';
			$message .= 'E-mail: <strong>' . wp_specialchars($user_email) . '</strong></p>';
			$tpl->assign('title', 'Registration Complete');
			$tpl->assign('form', $message);
			$tpl->display('register.tpl');
			die();
		}
	default:
		get_header();
		$message = '';
		if ( isset($errors) ) : 
			$message .= '<div class="error">';
			foreach($errors as $error) $message .= "<br />$error</div>";
		endif;
		$message .= '<form style="margin-top: 20px" method="post" action="wp-register.php" id="registerform">';
		$message .= '<p><input type="hidden" name="action" value="register" />';
		$message .= '<label for="user_login">Username:</label> ';
		$message .= '<input type="text" name="user_login" id="user_login" size="20" maxlength="20" value="';
		$message .= wp_specialchars($user_login) .'"/><br /></p>';
		$message .= '<p><label for="user_email">E-mail:</label> ';
		$message .= '<input type="text" name="user_email" id="user_email" size="25" maxlength="100" value="';
		$message .= wp_specialchars($user_email) . '"/></p>';
		$message .= '<p>A password will be emailed to you.</p>';
		$message .= '<p><label for="captcha">'. $lang['J_COMFIMATION'] .'</label> ';
      $message .= '<img id="siimage" alt="ConfirmCode" align="middle" src="'.get_bloginfo('wpurl') .'/wp-content/plugins/wpcareers/include/jp_securimage_show.php?sid='. md5(time()) .'" />';
		$message .= '<br><span class ="smallTxt">'. $lang["J_VERIFICATION"] .'</span></p>';
		$message .= '<p><lable></lable> <input type="text" name="capcc_captchakey" id="capcc_captchakey" size="10"></p>';
		$message .= '<p class="submit"><input type="submit" value="Register" id="submit" name="submit" /></p>';
		$message .= '</form><ul>';
		$message .= '<li><a href="'. $home .'" title="Are you lost?">Home</a></li>';
		$message .= '<li><a href="'. get_bloginfo('wpurl') .'/wp-login.php?action=lostpassword" title="Password Lost and Found">Recover password?</a></li>';
		$message .= '</ul>';	
		$tpl->assign('title', 'Registration');
		$tpl->assign('form', $message);
		$tpl->display('register.tpl');
		die();
	break;
	case 'disabled':
		?>
		<div id="login">
		<h2><?php _e('Registration Disabled') ?></h2>
		<p><?php _e('User registration is currently not allowed.') ?><br />
		<a href="<?php echo get_settings('home'); ?>/" title="<?php _e('Go back to the blog') ?>"><?php _e('Home') ?></a></p>
		</div>
		<?php 
	break;
	} // switch
};




function get_jp_user_field(){
	global $wpdb, $table_prefix, $wpmuBaseTablePrefix, $wpca_user_field, $wp_version;
	if ($wpca_user_field == false){

	$sql = "SHOW COLUMNS FROM {$wpmuBaseTablePrefix}users";
	$tcols = $wpdb->get_results($sql, ARRAY_A);
	$cols = array();
	for ($i=0; $i<count($tcols); $i++){
 $cols[] = $tcols[$i]['Field'];
	}
	if (in_array("display_name", $cols)){
 $wpca_user_field = "display_name";
 $wp_version = "2";
	} elseif (in_array("user_nicename", $cols)){
 $wpca_user_field = "user_nicename";
 $wp_version = "WPMU";
	} else {
 $wpca_user_field = "nickname";
 $wp_version = "1";
	}
	}
	return $wpca_user_field;
}


function jp_check_permission(){
	global $postinfo, $user_level, $user_ID;
	
	$permission=0;
	// user loggedin
	if ($user_level && $user_level>=1) $permission=1;
	elseif ($user_ID) $permission=1;
	elseif ($userdata->wp_user_level >= 1) $permission=1;
	if (($permission && $user_ID==$postinfo['author']) ){
 $permission=5;
	} 
	// administrator
	if ($user_level && $user_level>=8) $permission=8;
	elseif ($userdata->wp_user_level >= 8) $permission=8;
	/*
	if (!$permission) {
 if (getenv('REMOTE_ADDR')==$postinfo['author_ip']) $permission=true;
	}
	*/
	return $permission;
}


# EMAIL ROUTINE 
function jp_send_email($mailto, $mailsubject, $mailtext, $from) {
	global $lang;
	$email_status=array();
	$email=wp_mail($mailto, $mailsubject, $mailtext, $from);
	if ($email == false) {
 $email_status[0]=false;
 $email_status[1]="The email could not be sent!";
	} else {
 $email_status[0]=true;
 $email_status[1]="The email sent successfully!";
	}
	return $email_status;
}

# NOTIFICATION EMAILS 
function jp_email_notifications($title, $description, $email, $id, $mode = 0){
	global $lang, $PHP_SELF;

	$wpca_settings=get_option('wpcareers');
	$out='';
	$eol="\r\n";
	
	# notify admin?
	$msg='';
	$msg .= sprintf(_('New Post, '.date("j-M-Y, l").' and is waiting for your Approval on your site %s:'), get_option('blogname')). $eol;
	$msg .= " Please visit the admin panel";
	$msg .= $eol."Title: " . $title;
	$msg .= $eol."Description: " . $description;
	$msg .= $eol."Email: " . $email;
	$msg .= $eol."Category: " . $category. $eol;
	# admin message
	$url = admin_url("admin.php?page=wpcareers_posts&admin_action=approvelinks");
	$msg .= $eol."Approve or delete it: " . $url. $eol;
	//$msg.= $lang['_FROM'].': '. $subject. $eol;
	$email_status=jp_send_email(get_option('admin_email'), get_bloginfo('name').': ' . $lang['J_NEWPOST'] , $msg, '');
	return $email_status;
}


// function that echo's the textarea/whatever for post input
function create_description($content="", $field, $form){
	global $wpdb, $table_prefix, $wp_filesystem;
	$wpca_settings = get_option('wpcareers');
	if (!isset($wpca_settings['edit_style'])) $wpca_settings['edit_style']= 'plain';
	echo '<script type="text/javascript" src="' . JP_PLUGIN_URL .  '/include/js/jquery.limit.js"></script>';
	?>
	<script type='text/javascript'>
 var intMaxLength="<?php echo $wpca_settings['excerpt_length'] ?>";
 $(document).ready(function() {
  $('#contactinfo').keyup(function() {
  	var len = this.value.length;
  	if (len >= intMaxLength) {
   this.value = this.value.substring(0, intMaxLength);
  	}
  	$('#charLeft').text(intMaxLength - len);
  });
 });
	</script>
	<?php
	switch ($wpca_settings['edit_style']){
 case "plain":
 default:
 	echo "<textarea name='wpcareers[" . $field."]' id='wpcareers[" . $field."]' cols='80' rows='15'>".str_replace("<", "&lt;", $content)."</textarea>";
 break;
 case "tinymce":
 	$theme="advanced";
 	if (isset($wpca_settings['editor_toolbar_basic']) && $wpca_settings['editor_toolbar_basic']=='y') $theme="simple";
 ?>
 <script type="text/javascript">
 var myTheme ="<?php echo $theme; ?>";
 
 tinyMCE.init({
  mode: "exact",
  theme: myTheme,
  elements : "mceEditor",
  width : "500",
  height : "200",
  theme_advanced_buttons1: "bold,italic,underline,|,strikethrough,|,bullist,numlist,|,undo,redo,|,removeformat,|, formatselect,underline,justifyfull,forecolor,|,pastetext,pasteword,removeformat,|,outdent,indent,|,undo,redo",
  theme_advanced_buttons2:"",
  theme_advanced_buttons3: "",
  theme_advanced_toolbar_location: "top",
  theme_advanced_toolbar_align: "left",
  theme_advanced_statusbar_location: "none",
  theme_advanced_resizing: false,
  onchange_callback	 : "tinyMceOnChange",
  handle_event_callback : "tinyMceEventHandler"
 });
 var _form = "<?php echo $form ?>";
 var intMaxLength="<?php echo $wpca_settings['excerpt_length'] ?>";
 var tinyMceBuffers = new Object();
 var tinyMceCharCounts = new Object();
 function tinyMceOnChange(inst){ tinyMceCheckContentLength(inst.id,intMaxLength); }
 function tinyMceEventHandler(e){
 	switch (e.type) {
  case 'keyup': tinyMceOnChange(tinyMCE.activeEditor); break;
 	}
 	return true;
 }
 // Strips all html tags from a given string, leaving only plain text
 function stripHtmlTags(strContent) { return strContent.replace(/(<([^>]+)>)/ig, ""); }
 function tinyMceCheckContentLength(strEditorId, intMaxLength) {
 	var editorInstance = tinyMCE.get(strEditorId);
 	if (editorInstance == null || editorInstance	== undefined) { alert('NO EDITOR'); }
 	var contentContainer = editorInstance.getBody();
 	if (contentContainer == null || contentContainer == undefined) { alert('NO CONTENT CONTAINER'); }
 	var strContent = contentContainer.innerHTML;
 	var intContentLength = strContent.length;
 	var intCharCount = stripHtmlTags(strContent).length;
 	if (intContentLength <= intMaxLength) {
  tinyMceBuffers[strEditorId] = strContent;
  tinyMceCharCounts[strEditorId] = intCharCount;
 	} else {
  var bm = editorInstance.selection.getBookmark(); // Stores a bookmark of the current selection
  editorInstance.setContent((tinyMceBuffers[strEditorId]) ? tinyMceBuffers[strEditorId] : strContent.substring(0, intMaxLength - 10));
  var intDelta = intCharCount - tinyMceCharCounts[strEditorId];
  if (bm['start'] && bm['start'] > intDelta) {
  	bm['start'] -= intDelta;
  	bm['end'] = bm['start'];
  }
  editorInstance.selection.moveToBookmark(bm); // Restore the selection bookmark
  alert('You have exceeded the maximum size for this text and we have undone your last change.');
 	}
 }
 </script>
 <?php
 if ($field)
 	echo '<textarea name="wpcareers['.$field.']" id="mceEditor" rows="8" cols="60">'. htmlentities($content) .'</textarea><br />';
 ?>
 <SPAN class="smallTxt" id="msgCounter">Maximum of <SCRIPT language="javascript">document.write(intMaxLength);</SCRIPT> characters allowed</SPAN><BR/>
 <?php
	break;
	}
}
//
// Validate an e-mail address
//
function jp_is_valid_phone($phone) {
	$phone = substr($phone, 1); // delete the "+"
	$number = trim(preg_replace('/\(|\)|\-|\+|\s/', '', $phone));
	if (!is_numeric($number)){
 return false;
	}
	return true;
}

function jp_is_valid_number($number, $max_len) {
	if ( ! ereg("^([0-9]+)$", $number) ) {
 $msg = "$number is not a number!";
	} elseif ( strlen($number) > $max_len ) {
 $msg = "$number too long. Must be less than $max_len";
	} else {
 $msg = false;
	}
	return $msg;
}

function jp_remove_weblink($text){
	$start = '<a\s[^>]*href='; # start of A link
	$mail_q = '[\'"]mailto:([^\'"]+)[\'"]'; # quoted mailto
	$mail_u = 'mailto:([^\s>]+)'; # unquoted mailto
	$link_q = '[\'"](h?[ft]tp:[^\'"]+)[\'"]'; # quoted http or ftp link
	$link_u = '(h?[ft]tp:[^\s>]+)'; # unquoted http or ftp link
	$end = '[^>]*>(.+)<\/a>'; # end of A link
	$search = array("/$start(?:$mail_q|$mail_u|$link_q|$link_u)$end/iU", '/<a\s[^>]*>(.*)<\/a>/iU'); # local file or other non-match
	$replace = array('\5', '\1');
	return(preg_replace($search, $replace, $text));
}



?>