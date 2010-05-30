<?php

/**
 * File Name: jp_admin_setup.php
 * Description: Component for wpCareers wordpress plugin
 * @author Mohammad Forgani
 * @copyright Copyright 2010, Mohamad Forghanian
 * @version 1.0
 * @link http://www.forgani.com
 * 
 * Last modified:  2010-01-17
 * Comments:
 */

class WP_Careers {
	// Sets the version number.
	var $version;
	var $menu_name = 'wpCareers';
	var $plugin_name = 'wpcareers';
	var $plugin_dir;
	var $plugin_url;
	var $template_dir;
	var $compile_dir;
	var $cache_dir;
	var $config_dir;
	var $public_url;
	var $public_dir;
	var $userId;
	var $password;

	function WP_Careers() {
		// initialize all the variables
		$this->plugin_url = get_option('siteurl').'/wp-content/plugins/wpcareers';
		$this->plugin_dir = JP_PLUGIN_DIR . '/';
		$this->version = VERSION;
		$this->template_dir = $this->plugin_dir . '/themes/default';
		$this->cache_dir = $this->plugin_dir . '/cache';
		$this->compile_dir = $this->plugin_dir . '/templates_c';
		$this->config_dir = $this->plugin_dir . '/include/Smarty/configs';
		$this->affiliate_id = get_option('wpca_affiliate_id');
		$this->userId = get_option('wpca_userId'); // TODO
		$this->password = get_option('wpca_password');  // TODO
		$this->public_dir = ABSPATH . 'wp-content/public/wpcareers/';
		$this->public_url = get_option('siteurl') . '/wp-content/public/wpcareers/';
      $this->cache_url = get_option('siteurl') . '/wp-content/plugins/wpcareers/cache/';
		
		/**
		* config_page() - Add WordPress action to show the admin configuration page
		*/
		$this->admin_pages = array(
			array('name'=>'wpcareers_structure','arg'=>'wpcareers_structure','prg'=>'process_structure'),
			array('name'=>'wpcareers_posts','arg'=>'wpcareers_posts','prg'=>'process_posts'),
			array('name'=>'wpcareers_utilities','arg'=>'wpcareers_utilities','prg'=>'process_utilities'),
		);

		/**
		* admin_page() - Show the admin page
		*/
		$this->admin_menu = array(
			array('name'=>'List Categories','arg'=>'wpcareers_structure'),
			array('name'=>'List Posts','arg'=>'wpcareers_posts'),
			array('name'=>'Utilities','arg'=>'wpcareers_utilities')
		);

		add_action('widgets_init', array(&$this, 'widget_init'));
		add_action('init', array(&$this, 'login_register_init'));
		add_action('admin_menu', array(&$this, 'add_admin_pages'));
		add_action('admin_head', array(&$this, 'add_admin_head'));
		add_action('wp_head', array(&$this, 'add_head'));
		add_action('admin_head', array(&$this, 'hide_dashboard'));
	}

	/**
	 * Adds the required css and js to the header for the search box and search results.
	 *  TODO
	 */  
	function add_head() {
		global $locale;
		$wpca_settings = get_option('wpcareers');
		echo "<link rel=\"stylesheet\" href=\"" . $this->plugin_url . "/themes/default/css/default.css\" type=\"text/css\" media=\"screen\">";
		if($wpca_settings['edit_style']==null || $wpca_settings['edit_style']=='plain') {
			// nothing
		} elseif($wpca_settings['edit_style']=='tinymce') {
			// activate these includes if the user chooses tinyMCE on the settings page
			$mce_path = get_option('siteurl');
			$mce_path .= '/wp-includes/js/tinymce/tiny_mce.js';
			echo '<script type="text/javascript" src="' . $mce_path . '"></script>';
		}
	}

	function tinymce() {
		wp_admin_css('thickbox');
		wp_print_scripts('jquery-ui-core');
		wp_print_scripts('jquery-ui-tabs');
		wp_print_scripts('post');
		wp_print_scripts('editor');
		if (function_exists('add_thickbox')) add_thickbox();
		wp_print_scripts('media-upload');
		if (function_exists('wp_tiny_mce')) wp_tiny_mce();
		// use the if condition because this function doesn't exist in version prior to 2.7
	}

	function add_admin_head() {
		?>
		<link rel="stylesheet" href="<?php echo $this->plugin_url; ?>/themes/default/css/admin.css" type="text/css" />
		<?php
		$this->tinymce();
	}
	
	function welcome() {
		print wpcareers_admin_menu();
		?>
		<div style="float:left;width:70%;">
		<div class="wrap">
		<h2>Welcome to Wordpress wpCareers (Job Portal)</h2>
		<p>The plugin allows you to build an online jobs/resume website, where the applicants will be able to search, update, add/remove or edit their resumes/profiles.
This plugin is for standalone WordPress sites.</p>
		<p>In addition, users can also add/delete/change the descriptions in addition to uploading images/photos.</p>
		<br />In the admin area the administrator will be able to:<br />
		<ul>
		<li>View and manage records in terms of add/modify/remove of entries </li>
		<li>Approve or deny the posts. </li>
		<li>Inactive Applicants Convert to Active</li>
		<li>Delete Users profile, Delete Employer profiles</li>
		</ul>
		<p><b>Important Note:</b> We strongly recommend you to use themes whose front page consisted only of one column, otherwise you should use our developed theme.
This plugin is for a standalone WordPress site.</p>
		<p>To install and configure this plugin, please click on <b><a href="<?php echo $PHP_SELF; ?>?page=wpcareers_settings">installation and configuration</a>.</b></p>
		This plugin is under active development. If you experience problems, please first make sure you have installed the latest version. <br />
		For remove the plugin please use the plugin Uninstaller utility.
		<p>&nbsp;</p>
		<?php
	}

	function process_option_settings() {
		global $_GET, $_POST, $wp_rewrite, $PHP_SELF, $wpdb, $table_prefix, $wp_version, $lang;
		
		$wpca_settings = get_option('wpcareers');
		if (isset($_GET['admin_action'])) {
			switch ($_GET['admin_action']){
				case "savesettings":
					foreach ($_POST['wpcareers'] as $k=>$v){
						$_POST['wpcareers'][$k] = stripslashes($v);
					}
					$_POST['wpcareers']['installed'] = 'y';
					$jp_new_slug = $_POST['wpcareers']['slug'];
					// new installation
					$page_info = $this->get_pageinfo();
					$page_title = $page_info['post_title'];

					if ( empty($page_title) ){
						$this->create_page();
					}
					$sql = "SELECT post_name FROM {$table_prefix}posts WHERE post_name = '".$wpdb->escape($jp_new_slug)."'";
					$check4update = $wpdb->get_results($sql);
					if($jp_new_slug != $wpca_settings['slug']) {
						if($check4update != $jp_new_slug) {
							$wpdb->query("UPDATE {$table_prefix}posts SET post_name = '".$jp_new_slug."' WHERE post_title = '[[WPCAREERS]]'");
							$msg ="Settings Updated.";
						} else {	
							$msg ="Settings Updated but a Slug exists with the name: ".$jp_new_slug."<br />"."Try Again.";
							$_POST['wpcareers']['slug'] = $wpca_settings['slug'];
						}
					}
					$wpca_settings = array();
					$wpca_settings = $_POST['wpcareers'];
					update_option('wpcareers', $wpca_settings);
					$wp_rewrite->flush_rules();
					if(!$wpdb->get_results("SHOW TABLES LIKE '%wpj_%'")) {
						echo "SHOW TABLES LIKE '%wpj_%'";
						$this->create_db();
					}
					$msg = "Settings Updated.";
				break;
			}
		}
		$this->set_default_option();
		$wpca_settings = get_option('wpcareers');
		echo '<div class="wrap">';
		if (isset($msg) && $msg!=''){echo "<div id='message' class='updated fade'>$msg</div>";}
	 
		$selflink = ($wp_rewrite->get_page_permastruct()=="")?"<a href=\"".get_bloginfo('url')."/index.php?pagename=".$wpca_settings['slug']."\">".get_bloginfo('url')."/index.php?pagename=".$wpca_settings['slug']."</a>":"<a href=\"".get_bloginfo('url')."/".$wpca_settings['slug']."/\">".get_bloginfo('url')."/".$wpca_settings['slug']."/</a>";
	 
		?>
		<div class="wrap">
		<h2>General Settings</h2>
		<?php
		print wpcareers_admin_menu();
		// dir setting checker
		$arr = array('templates_c', 'cache');
		foreach ($arr as $value) {
			$dir = $this->plugin_dir . $value. '/';
			if( ! is_writable( $dir ) || ! is_readable( $dir ) ) {
				echo "<BR /><BR /><fieldset><legend style='font-weight: bold; color: #900;'>".$lang['J_CHECKER']."</legend>";
				echo "<font color='#FF0000'>".$lang['J_DIRPERMS']."".$dir."</font><br />\n" ;
				echo "</fieldset>"; 
			}
		}
		$arr = array('images', 'resume');
		foreach ($arr as $value) {
			$dir = $this->public_dir . $value. '/';
			if( ! is_writable( $dir ) || ! is_readable( $dir ) ) {
				echo "<BR /><BR /><fieldset><legend style='font-weight: bold; color: #900;'>".$lang['J_CHECKER']."</legend>";
				echo "<font color='#FF0000'>".$lang['J_DIRPERMS']."".$dir."</font><br />\n" ;
				echo "</fieldset>";
			}
		}
		jp_ShowImg('process_settings', 'page_image');
		?>
		<p>

		<form name="process_settings" method="post" id="process_settings" action="<?php echo $PHP_SELF;?>?page=wpcareers&admin_action=savesettings">
			<table border="0" class="editform">
			<tr><th align="right">Version:</th>
				<td><?php echo $this->version;?></td>
			</tr>
			<tr><th align="right">WordPress Version:</th>
				<td><?php echo $wp_version;?></td>
			</tr>
			<tr>
				<th align="right">URL:</th>
				<td><?php echo $selflink;?></td>
			</tr>
			<tr>
				<th align="right">Slug:</th>
				<td><input type="text" size="30" name="wpcareers[slug]" value="<?php echo str_replace('"', "&quot;", stripslashes($wpca_settings['slug']));?>">
				</td>
			</tr>

			<tr>
				<th align="right" valign="top">Description in header:</th>
				<td><?php create_description($wpca_settings['description'], 'description', 'process_settings'); ?></td>
			</tr> 
			<tr>
				<th align="right">&nbsp;</th>
				<td><input type="checkbox" name="wpcareers[show_credits]" value="y"<?php echo ($wpca_settings['show_credits']=='y')?" checked":"";?>> Display credit line at the bottom of pages</td>
			</tr>
			<tr>
				<th align="right">Page Title:</th>
				<td><input type="text" size="40" name="wpcareers[page_title]" value="<?php echo $wpca_settings['page_title'];?>"></td>
			</tr>
			<?php $textarea=array ('tinymce' => 'HTML with TinyMCE (inline wysiwyg)','plain' => 'No HTML, No BBCode');?>
			<tr>
				<th align="right">Posting Style: </th>
				<td><select name="wpcareers[edit_style]">
				<?php
				foreach($textarea as $key=>$value)	{
					if ($key == $wpca_settings[edit_style]) {
						echo "\n<option value='$key' selected='selected'>$value</option>\n";
					} else {
						echo "\n<option value='$key'>$value</option>\n";
					}
				}
				?>
				</select></td>
			</tr>
			<tr><th align="right">The maximum number of characters:</th>
				<td><input type="text" size="4" name="wpcareers[excerpt_length]" value="<?php echo ($wpca_settings['excerpt_length']);?>" onchange="this.value=this.value*1;">
				</td>
			</tr>
			<tr><th align="right" valign="top">Top Image:</th>
				<td>
				<table><tr><td valign="top">
					<input type="hidden" name="wpcareers[page_image]" value="<?php echo $wpca_settings['page_image'];?>">
					<select name="image" onChange="showImage('/')">
					<?php
					$rep = $this->plugin_dir . "/images";
					$handle=opendir($rep);
					while ($file = readdir($handle)) {$filelist[] = $file;}
					asort($filelist);
					while (list ($key, $file) = each ($filelist)) {
						if (!ereg(".gif|.jpg|.png",$file)) {
							if ($file == "." || $file == "..") $a=1;
						} else {
							if ($file == $wpca_settings['page_image']) {
								echo "\n<option value=\"$file\" selected>$file</option>\n";
							} else {
								echo "\n<option value=\"$file\">$file</option>\n";
							}
						}
					}
					?>
					</select></td><td>&nbsp;&nbsp;
					<img src="<?php echo $this->plugin_url ?>/images/<?php echo $wpca_settings['page_image']; ?>" name="avatar" align="absmiddle"><br>
					<span class="smallTxt"><?php echo $lang['J_REPIMGCAT']; ?>/wp-content/plugins/wpcareers/images/</span>
				</td></tr></table>
				</td></tr>
			<tr>
				<th align="right"><?php echo $lang['J_APPROVE'];?></th>	
				<td><input type=checkbox name="wpcareers[approve]" value="y"<?php 
				if (!isset($wpca_settings['approve'])) $wpca_settings['approve']='y';
				echo ($wpca_settings['approve']=='y')?" checked":"";?>></td>
			</tr>
			<tr>
				<th align="right">Remove posts after X days:</th>
				<td><input name="wpcareers[expire]" id="wpcareers[expire]" size="5" value='<?php echo $wpca_settings['expire']; ?>'/></td>
			</tr>
			<tr>
			 <th align="right" valign="top"><label>Maximum size for images/logos: </label></th>
			 <td><input type="text" size="3" name="wpcareers[image_width]" value="<?php echo $wpca_settings['image_width'];?>"> X <input type="text" size="3" name="wpcareers[image_height]" value="<?php echo $wpca_settings['image_height'];?>"><br /><span class="smallTxt">example: 120 x120 pixel</span></td>
			</tr>
			<th align="right">Maximum limit for files:</th>
				<td><input name="wpcareers[file_max_upl]" id="wpcareers[file_max_upl]" size="3" value='<?php echo $wpca_settings['file_max_upl']; ?>'/>&nbsp;Kbyte</td>
			</tr> 
			<tr>
				<th align="right">Post only for registered users:</th>	
				<td><input type=checkbox name="wpcareers[must_login]" value="y"<?php echo ($wpca_settings['must_login']=='y')?" checked":"";?>></td>
			</tr>
			<tr>
				<th align="right">View only for registered users:</th>	
				<td><input type=checkbox name="wpcareers[show_all]" value="y"<?php echo ($wpca_settings['show_all']=='y')?" checked":"";?>></td>
			</tr>
			<tr>
				<th align="right"><?php echo $lang['J_COMFIMSHOW'];?></th>	
				<td><input type=checkbox name="wpcareers[confirmation_code]" value="y"<?php echo ($wpca_settings['confirmation_code']=='y')?" checked":"";?>></td>
			</tr>
			<tr>
				<th align="right">The last post's count:</th>
				<td><input type="text" size="2" name="wpcareers[new_links]" value="<?php echo ($wpca_settings['new_links']);?>" onchange="this.value=this.value*1;">
				</td></tr>
			<tr>
				<td colspan=2><p>&nbsp;</p><strong>Google AdSense for Open Directory Links</strong><hr /></td></tr>
				<?php
				//for upgrade versions
				if (!isset($wpca_settings['GADcolor_border'])) $wpca_settings['GADcolor_border']= 'FFFFFF';
				if (!isset($wpca_settings['GADcolor_link'])) $wpca_settings['GADcolor_link']= '0000FF';
				if (!isset($wpca_settings['GADcolor_bg'])) $wpca_settings['GADcolor_bg']= 'FFFFFF';
				if (!isset($wpca_settings['GADcolor_text'])) $wpca_settings['GADcolor_text']= '000000';
				if (!isset($wpca_settings['GADcolor_url'])) $wpca_settings['GADcolor_url']= 'FF0000';
				if (!isset($wpca_settings['GADposition'])) $wpca_settings['GADposition']= 'btn';
				if (!isset($wpca_settings['GADproduct'])) $wpca_settings['GADproduct']= 'link';
				if (!isset($wpca_settings['googleID'])) $wpca_settings['googleID'] = 'pub-2844370112691023';
				$GADpos = array ('top' => 'top','btn' => 'bottom', 'bth' => 'both','no' => 'none');
				?>
			<tr>
				<th align="right" valign="top"><a href='https://www.google.com/adsense/' target='google'>Google AdSense Account ID: </a></th>
				<td><input type='text' name='wpcareers[googleID]' id='wpcareers[googleID]' size='30' value="<?php echo $wpca_settings['googleID']; ?>" /><br>
				<span class="smallTxt"> example: no, pub-2844370112691023 or ...</span></td>
			</tr>
			<tr>
				<th align="right" valign="top">Google Ad Position: </th>
				<td>
				<select name="wpcareers[GADposition]" tabindex="1">
				<?php
				foreach($GADpos as $key=>$value)	{
					if ($key == $wpca_settings['GADposition']) {
						echo "\n<option value='$key' selected='selected'>$value</option>\n";
					} else {
						echo "\n<option value='$key'>$value</option>\n";
					}
				}
				?>
				</select>&nbsp;&nbsp;<span class="smallTxt">(If this value is assigned to 'none' then the Google Ads will not show up)</small>
				</td>
			</tr>
			<?php
				$wpca_settings['GADproduct'] = 'link';
				$lformats=array ('728x15'  => '728x15', '468x15' => '468x15');
			?>
			<tr>
			<th align="right" valign="top"><label>Link Format: </label></th>
			<td><select name="wpcareers[GADLformat]">
			<?php
				foreach($lformats as $key=>$value)	{
					if ($key == $wpca_settings['GADLformat']) {
						echo "\n<option value='$key' selected='selected'>$value</option>\n";
					} else {
						echo "\n<option value='$key'>$value</option>\n";
					}
				}
				?>
			</select></td>	
			</tr>
			<tr>
				<th align="right">Ad Colours: </th>
				<td>
					<table>
					<tr>
						<td>Border: </td>
						<td><input name="wpcareers[GADcolor_border]" id="wpcareers[GADcolor_border]" size="6" value='<?php echo $wpca_settings['GADcolor_border']; ?>'/></td>
						<td>Title/Link: </td>
						<td><input name="wpcareers[GADcolor_link]" id="wpcareers[GADcolor_link]" size="6" value='<?php echo $wpca_settings['GADcolor_link']; ?>'/></td>
						<td>Background: </td>
						<td><input name="wpcareers[GADcolor_bg]" id="wpcareers[GADcolor_bg]" size="6" value='<?php echo $wpca_settings['GADcolor_bg']; ?>'/></td>
						<td>Text: </td>
						<td><input name="wpcareers[GADcolor_text]" id="wpcareers[GADcolor_text]" size="6" value='<?php echo $wpca_settings['GADcolor_text']; ?>'/></td>
						<td>URL: </td>
						<td><input name="wpcareers[GADcolor_url]" id="wpcareers[GADcolor_url]" size="6" value='<?php echo $wpca_settings['GADcolor_url']; ?>'/>
						</td>
					</tr>
					</table>
				</td>
			</tr>
			<tr><td colspan=2><HR /></td></tr>
			<tr>
				<th>&nbsp;</th>
				<td><p><input type="submit" value="Update Settings"></p></td>
			</tr>
			</table>
		</form>
		</p></div>
		<?php
		echo '</div>';
	}

	function set_default_option() {
		global $wp_version;
		$wpca_settings = get_option('wpcareers');
		$wpca_settings['version'] = $this->version;
		if (!isset($wpca_settings['installed']) || $wpca_settings['installed']!='y'){
			$wpca_settings['installed'] = 'y';
			$wpca_settings['add_into_pages'] = 'y';
			$wpca_settings['show_credits'] = 'y';
			$wpca_settings['read_blog'] = 'y';
			$wpca_settings['slug'] = 'wpcareers';
			$wpca_settings['page_title'] = 'Job Portal';
			$wpca_settings['theme'] = 'default';
			$wpca_settings['display_titles'] = 'y';
			$wpca_settings['page_image'] = 'default.gif';
			$wpca_settings['display_last_links'] = 'y';
			$wpca_settings['display_last_post_link'] = 'y';
			$wpca_settings['last_links_num'] = 5;
			$wpca_settings['excerpt_length'] = 700;
			$wpca_settings['confirmation_code']="y";
			$wpca_settings['show_all']="n";
			$wpca_settings['must_login']="n";
			$wpca_settings['new_links']= 8;
			$wpca_settings['search_log'] = '25';
			$wpca_settings['description'] = 
'<p>Post your job in <strong>our free online wpCareers service</strong>.<br />You can browse jobs or search by job title and apply online.<br />Recruiters have also apportunity to add the avaliable jobs and update the job status.</p><br />If you have any question or feature request, please mail us.</p>';
			$wpca_settings['keywords'] = 'dummy'; 
			$wpca_settings['googleID'] = 'pub-2844370112691023';
			$wpca_settings['GADproduct'] = 'link';
			$wpca_settings['GADLformat'] = '468x15';
			$wpca_settings['GADtype'] = 'text';
			$wpca_settings['GADcolor_border']= 'FFFFFF';
			$wpca_settings['GADcolor_link']= '0000FF';
			$wpca_settings['GADcolor_bg']= 'E4F2FD';
			$wpca_settings['GADcolor_text']= '000000';
			$wpca_settings['GADcolor_url']= 'FF0000';
			$wpca_settings['GADposition'] = 'btn';
			$wpca_settings['expire'] = 360;
			
			$wpca_settings['file_max_upl']= 10;  // in kbye
			$wpca_settings['image_width'] = 120;
			$wpca_settings['image_height'] = 120;
			$wpca_settings['text_area_editor']= 'tinymce';
		}
		update_option('wpcareers', $wpca_settings);
	}

	/**#@+
	* Table SQL
	*
	* The table create statment
	*/
	function create_db() {
		global $wpdb, $table_prefix;
		
		$wpca_sql[$table_prefix.'wpj_job'] = "CREATE TABLE IF NOT EXISTS {$table_prefix}wpj_job (
			l_id int(11) NOT NULL auto_increment,
			lc_id int(11) NOT NULL default 0,
			l_title varchar(128) NOT NULL default '',
			l_status int(3) NOT NULL default 0,
			l_expire int(3) NULL,
			l_type varchar(128) NOT NULL default '',
			l_company varchar(128) NOT NULL default '',
			l_desctext text NOT NULL,
			l_requirements text NOT NULL,
			l_tel varchar(32) NULL,
			l_price varchar(128) NOT NULL default '',
			l_typeprice varchar(128) NOT NULL default '',
			l_contactinfo mediumtext NOT NULL,
			l_date varchar(64) NULL,
			l_email varchar(128) NOT NULL default '',
			l_submitter varchar(60) NOT NULL default '',
			l_usid varchar(6) NOT NULL default '',
			l_town varchar(128) NOT NULL default '',
			l_state varchar(128) NOT NULL default '',
			l_valid varchar(11) NOT NULL default '',
			l_photo varchar(128) NOT NULL default '',
			l_view int(5) NOT NULL default 0,
			l_author_ip varchar(16) NOT NULL default '',
			l_fax varchar(32) NULL,
			PRIMARY KEY (l_id)
			) TYPE=MyISAM;";
		
		$wpca_sql[$table_prefix.'wpj_resume'] = "CREATE TABLE IF NOT EXISTS {$table_prefix}wpj_resume (
			r_id int(11) NOT NULL auto_increment,
			rc_id int(11) NULL,
			r_name varchar(128) NOT NULL default '',
			r_title varchar(128) NOT NULL default '',
			r_status int(3) NOT NULL default,
			r_exp int(3) NULL,
			r_expire int(3) NULL,
			r_private int(3) NULL,
			r_tel varchar(32) NULL,
			r_salary varchar(128) NULL,
			r_contactinfo mediumtext  NOT NULL default '',
			r_typesalary varchar(128) NOT NULL default '',
			r_date varchar(64) NULL,
			r_email varchar(128) NOT NULL default '',
			r_submitter varchar(128) NOT NULL default '',
			r_desctext text NOT NULL default '',
			r_usid varchar(6) NOT NULL default '',
			r_town varchar(128) NULL,
			r_state varchar(128) NULL,
			r_valid varchar(11) NOT NULL default '',
			r_photo varchar(128) NULL,
			r_resume varchar(128) NULL,
			r_view int(5) NOT NULL default 0,
			r_author_ip varchar(32) NOT NULL default '',
			r_startDate varchar(64) NULL,
			r_fax varchar(32) NULL,
			PRIMARY KEY (r_id)
			) TYPE=MyISAM;";
		
		$wpca_sql[$table_prefix.'wpj_categories'] = "CREATE TABLE IF NOT EXISTS {$table_prefix}wpj_categories (
			c_id int(11) NOT NULL auto_increment,
			cp_id int(5) unsigned NOT NULL default 0,
			c_title varchar(128) NOT NULL default '',
			c_img varchar(150) NOT NULL default '',
			c_sort int(5) NOT NULL default 0,
			c_affprice int(5) NOT NULL default 0,
			PRIMARY KEY (c_id)
			) TYPE=MyISAM;";
		
		$wpca_sql[$table_prefix.'wpj_res_categories'] = "CREATE TABLE IF NOT EXISTS {$table_prefix}wpj_res_categories (
			rc_id int(11) NOT NULL auto_increment,
			rcp_id int(5) unsigned NOT NULL default 0,
			rc_title varchar(128) NOT NULL default '',
			rc_img varchar(150) NOT NULL default '',
			rc_sort int(5) NOT NULL default '0',
			rc_affprice int(5) NOT NULL default 0,
			PRIMARY KEY  (rc_id)
			) TYPE=MyISAM;";
		
		$wpca_sql[$table_prefix.'wpj_type'] = "CREATE TABLE IF NOT EXISTS {$table_prefix}wpj_type (
			t_id int(11) NOT NULL auto_increment,
			t_nom varchar(150) NOT NULL default '',
			PRIMARY KEY  (t_id)
			) TYPE=MyISAM;";
		
		
		$wpca_sql[$table_prefix.'wpj_price'] = "CREATE TABLE IF NOT EXISTS {$table_prefix}wpj_price (
			p_id int(11) NOT NULL auto_increment,
			p_nom varchar(150) NOT NULL default '',
			PRIMARY KEY  (p_id)
			) TYPE=MyISAM;";
		
		
		$wpca_sql[$table_prefix.'wpj_companies'] = "CREATE TABLE IF NOT EXISTS {$table_prefix}wpj_companies (
			c_id int(11) NOT NULL auto_increment,
			c_name varchar(128) NOT NULL default '',
			c_address varchar(128) NOT NULL default '',
			c_address2 varchar(128) NOT NULL default '',
			c_city varchar(128) NOT NULL default '',
			c_state varchar(128) NOT NULL default '',
			c_zip varchar(20) NOT NULL default '',
			c_phone varchar(32) NOT NULL default '',
			c_fax varchar(32) NOT NULL default '',
			c_url varchar(150) NOT NULL default '',
			c_img varchar(150) NOT NULL default '',
			c_usid varchar(6) NOT NULL default '',
			c_author_ip varchar(6) NOT NULL default '',
			c_author varchar(6) NOT NULL default '',
			c_contact text NOT NULL,
			c_date_added int(10) NOT NULL default 0,
			PRIMARY KEY (c_id),
			KEY c_name (c_name)
			) TYPE=MyISAM;";
		
		
		$wpca_sql[$table_prefix.'wpj_replies'] = "CREATE TABLE IF NOT EXISTS {$table_prefix}wpj_replies (
			rp_id int(11) NOT NULL default 0,
			rl_id int(11) NOT NULL default 0,
			rp_title varchar(128) NOT NULL default '',
			rp_date int(10) NOT NULL default 0,
			rp_submitter varchar(60) NOT NULL default '',
			rp_message text NOT NULL,
			rp_resume varchar(128) NOT NULL default '',
			rp_tele varchar(32) NOT NULL default '',
			rp_email varchar(128) NOT NULL default '',
			rp_usid int(11) NOT NULL default 0,
			PRIMARY KEY (rp_id),
			KEY lid (rl_id)
			) TYPE=MyISAM;";
		
		$wpca_sql[$table_prefix.'wpj_created_resumes'] = "CREATE TABLE IF NOT EXISTS {$table_prefix}wpj_created_resumes (
			cr_id int(11) NOT NULL auto_increment,
			cl_id int(11) NOT NULL default 0,
			cr_made_resume text NOT NULL,
			cr_date int(10) NOT NULL default 0,
			cr_usid int(11) NOT NULL default 0,
			PRIMARY KEY (cr_id),
			KEY cr_id (cr_id)
			) TYPE=MyISAM;";

		
		$wpca_sql[$table_prefix.'wpj_pictures'] = "CREATE TABLE IF NOT EXISTS {$table_prefix}wpj_pictures (
			p_img int(11) NOT NULL auto_increment,
			p_title varchar(255) NOT NULL,
			p_date_added int(10) NOT NULL default 0,
			p_date_modified int(10) NOT NULL default 0,
			pl_id int(11) NOT NULL default 0,
			p_uid_owner varchar(50) NOT NULL,
			p_url text NOT NULL,
			PRIMARY KEY (p_img)
			) TYPE=MyISAM;";
		
		
		$tabs = $wpdb->get_results("SHOW TABLES", ARRAY_A);
		$tables = array();
		for ($i=0; $i<count($tabs); $i++){
			$tables[] = $tabs[$i][0];
		}
		
		@reset($wpca_sql);
		while (list($k, $v) = @each($wpca_sql)){
			if (!@in_array($k, $tables)){
				echo " - create table: " .  $k . "<br />"; 
				$wpdb->query($v);
			}
		}
		// demo
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_categories VALUES (1, 0, 'Small Business and Website Designs', 'open.gif', 0, 1)");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_categories VALUES (3, 1, 'Telecommunication Systems', 'close.gif', 0, 1)");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_categories VALUES (4, 1, 'Computer Operator / Networking ', 'close.gif', 0, 1)");
		 $wpdb->query("INSERT INTO " . $table_prefix. "wpj_categories VALUES (15, 1, 'programming languages / platforms (Linux, Windows, Unix, etc.)', 'close.gif', 0, 1)");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_categories VALUES (5, 0, 'Web Development / Designer ', 'open.gif', 0, 1)");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_categories VALUES (6, 5, 'Graphic, Photography and Animation', 'close.gif', 0, 1)");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_categories VALUES (2, 5, 'Digital Media and Multimedia Design ', 'close.gif', 0, 1)");
		
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_categories VALUES (7, 0, 'Project Management and Solution Manager', 'open.gif', 0, 1)");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_categories VALUES (8, 7, 'Data Management and System Administrator', 'close.gif', 0, 1)");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_categories VALUES (9, 7, 'Sales Manager / Marketing & Promotion', 'close.gif', 0, 1)");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_categories VALUES (10, 7, 'Assistant Manager  / Operations Manager', 'close.gif', 0, 1)");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_categories VALUES (11, 7, 'Quality Control / Process Work Flow Analyst', 'open.gif', 0, 1)");

		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_categories VALUES (16, 0, 'Help Desk / Medical & Technician', 'open.gif', 0, 1)");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_categories VALUES (12, 16, 'Security / Production Support ', 'open.gif', 0, 1)");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_categories VALUES (13, 16, 'Customer Support & Service / Call Center', 'close.gif', 0, 1)");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_categories VALUES (14, 16, 'Develops and Maintains / Services & Training and Seminars ', 'open.gif', 0, 1)");

		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_res_categories VALUES (1, 0, 'E-Commerce / Internet Technologies, ', 'open.gif', 0, 1)");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_res_categories VALUES (2, 1, 'Engineering and Information Technology', 'open.gif', 0, 1)");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_res_categories VALUES (3, 1, 'IT Software / System Programming, ', 'close.gif', 0, 1)");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_res_categories VALUES (4, 1, 'IT Hardware / Telecommunication, ', 'close.gif', 0, 1)");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_res_categories VALUES (10, 1, 'Training and Seminars', 'close.gif', 0, 1)");

		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_res_categories VALUES (5, 0, 'Marketing & Promotion, etc...', 'open.gif', 0, 1)");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_res_categories VALUES (6, 5, 'Account & Finace / Tax', 'close.gif', 0, 1)");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_res_categories VALUES (7, 5, 'Coporate Planning / Consulting, etc...', 'close.gif', 0, 1)");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_res_categories VALUES (8, 5, 'Film & TV Produktion/ Werbung', 'open.gif', 0, 1)");

		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_res_categories VALUES (12, 0, 'Website Builder / Designer', 'open.gif', 0, 1)");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_res_categories VALUES (13, 12, 'Data Management and System Administrator', 'close.gif', 0, 1)");	

		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_type VALUES (1,'Full Time')");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_type VALUES (2,'Part Time')");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_type VALUES (3,'Intership')");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_price VALUES (1,'Hourly')");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_price VALUES (2,'Annual')");
		$wpdb->query("INSERT INTO " . $table_prefix. "wpj_price VALUES (3,'Yearly')");
	}

	function get_pageinfo() {
		global $wpdb, $table_prefix;
		return $wpdb->get_row("SELECT * FROM {$table_prefix}posts WHERE post_title = '[[WPCAREERS]]'", ARRAY_A);
	}

	function create_page() {
		global $wpdb, $table_prefix;
		$dt = date("Y-m-d");
		$sql = "INSERT INTO {$table_prefix}posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type) VALUES ('1', '$dt', '$dt', '[[WPCAREERS]]', '[[WPCAREERS]]',  '[[WPCAREERS]]', 'publish', 'closed', 'closed', '', 'wpcareers', '', '', '$dt', '$dt', '[[WPCAREERS]]', '0', '', '0', 'page')";
		$wpdb->query($sql);
		return $wpdb->get_row("SELECT * FROM {$table_prefix}posts WHERE post_title = '[[WPCAREERS]]'", ARRAY_A);
	}

	function add_admin_pages() {
		add_menu_page($this->menu_name , $this->menu_name ,'administrator', __FILE__, array(&$this, 'welcome'), 
				'../wp-content/plugins/wpcareers/images/wpj.jpg');
		add_submenu_page(__FILE__, 'wpcareers_settings', 'wpcareers_settings', 'administrator', 'wpcareers_settings', array(&$this, 'process_option_settings'));
		for ($i=0; $i<count($this->admin_pages); $i++){
			$link = $this->admin_pages[$i];
			add_submenu_page(__FILE__, $link['name'], $link['name'], 'administrator', $link['arg'], $link['prg']);
		}
		add_management_page($this->menu_name, $this->menu_name, 'administrator', $this->plugin_name, 'wpcareers_admin_page'); 
	}

	function login_register_init() {
		global $pagenow;
      $wpca_settings = get_option('wpcareers');
      if ($wpca_settings['installed'] == 'y') {
        switch ($pagenow) {
          case "wp-login.php":
              wpcareers_do_login();
          break;
          case "wp-register.php":
              wpcareers_do_register();
          break;
        }
      }
	}

	// TODO
	/**
	* registers the widget, the widget will die if wordpress version doesn't support
	* register_sidebar_widget
	*
	*/
	function widget_init() {
		// Check for required functions
		if (!function_exists('register_sidebar_widget'))
			die('sidebar function does not exist, this is required for use with this plugin');
			register_sidebar_widget('wpCareers Search',array(&$this, 'widget_wpca'));
	}


	/**
	* Hide Dashboard for subscriber level [edit_posts] 
	*/
	function hide_dashboard () {
		if (current_user_can('edit_posts')) {
			return;
		} else {
			global $menu, $submenu, $user_ID;
			$the_user = new WP_User($user_ID);
			reset($menu); $page = key($menu);
			while ((__('Dashboard') != $menu[$page][0]) && next($menu))
				$page = key($menu);
			if (__('Dashboard') == $menu[$page][0]) unset($menu[$page]);
				reset($menu); $page = key($menu);  
			while (!$the_user->has_cap($menu[$page][1]) && next($menu))
				$page = key($menu);  
			while ((__('Tools') != $menu[$page][0]) && next($menu))
				$page = key($menu);
			if (__('Tools') == $menu[$page][0]) unset($menu[$page]);
			if (preg_match('#wp-admin/?(index.php)?$#',$_SERVER['REQUEST_URI']) && ('index.php' != $menu[$page][2]))  
				wp_redirect(get_option('siteurl') . '/wp-admin/post-new.php');
		}
	}

	function ip_cleanUp() {
		$deleteTimeDiff= 5 * 60; // second
		if (!($dh = opendir($this->cache_dir)))
			echo 'Unable to open cache directory "' . $this->cache_dir . '"';
			$result = true;
			while ($file = readdir($dh)) {
				if (($file != '.') && ($file != '..')) {
					$file2 = $this->cache_dir . $file;
					if (isset($file2) && is_file($file2)) {
						$diff = mktime() - @filemtime($file2);
						if ($diff > $deleteTimeDiff) @unlink( $file2 );
					}
				}
			}
	}

}

?>