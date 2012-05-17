<?php

/**
 * jp_admin_utilities.php
 * Description: wpCareers wordpress plugin
 * @author Mohammad Forgani
 * @copyright Copyright 2010, Mohamad Forghanian
 * @version 1.0
 * @link http://www.forgani.com
 */


if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('WPCareers: You are not allowed to call this page directly.'); }


function process_utilities(){
	global $_GET, $_POST, $wpdb, $table_prefix, $wpca_adm_page_name, $PHP_SELF;
	if (!isset($_GET["admin_action"])) $_GET["admin_action"]='main';
	
	$tables = array($table_prefix."wpj_job", $table_prefix."wpj_resume", 
		$table_prefix."wpj_categories", $table_prefix."wpj_res_categories",
		$table_prefix."wpj_type", $table_prefix."wpj_price");

	print wpcareers_admin_menu();
	switch ($_GET["admin_action"]){
		case "main":
		?>
		<div class="wrap">
		<script language=javascript>
		<!--
		function uninstallwpcareers_(y){
			if (confirm("Are you sure you want to Uninstall the wpCareers?\n")){
				document.location.href = y;
			}
		}
		//-->
		</script>
		<p>
		<h3>Uninstall</h3>
		Just make sure you create backups before you drops the wpCareers Database tables.

		<p style="color: red">
		<strong>WARNING</strong><br />
		Once uninstalled, this cannot be undone. You should use a Database Backup plugin of WordPress to back up all the data first.
		</p>
		<p style="color: red">
			<strong>The following WordPress Options/Tables will be DELETED</strong><br />
		</p>
		<table class="widefat">
			<thead>
				<tr>
				<th>WordPress Options</th>
				<th>WordPress Tables</th>
				</tr>
			</thead>
			<tr>
				<td valign="top">
				<ol>
				<?php	echo '<li>wpCareers</li>'."\n"; ?>
				</ol>
				</td>
				<td valign="top" class="alternate">
				<ol>
				<?php
					foreach($tables as $tables) {
						echo '<li>'.$tables.'</li>'."\n";
					}
				?>
				</ol>
				</td>
			</tr>
		</table>

		<br /><br />
		<a href="javascript:uninstallwpcareers_('<?php echo $PHP_SELF;?>?page=wpcareers_utilities&admin_action=uninstall')">Uninstall wpCareers from the Database?</a>
		</p></div>
	  <?php
		break;
		case "uninstall":
			$msg = wpcareers_uninstall_db();
			echo $msg;
			$deactivate_url = 'plugins.php?action=deactivate&amp;plugin=wpcareers/jp_control.php';
			if(function_exists('wp_nonce_url')) {
				$deactivate_url = wp_nonce_url($deactivate_url, 'deactivate-plugin_wpcareers/jp_control.php');
			}
			echo '<div class="wrap">';
			echo '<h2>Uninstall WPCareers</h2>';
			echo '<p><strong>'.sprintf(__('<a href="%s">Click Here</a> To Finish The Uninstallation And WPCareers Will Be Deactivated Automatically.', 'wpcareers'), $deactivate_url).'</strong></p>';
			echo '</div>'; 
			die();
		break;
	}
}

	
function wpcareers_uninstall_db(){
	global $wpdb, $table_prefix;
	$msg = "<span style='width:200px;'>Deleting Options... </span><br /><div id='message' class='updated fade'><p>";
	
	$arr = array('wpcareers');
	foreach ($arr as &$value) {
		$ref = delete_option($value);
		if($ref) {
			$msg .=  '<font color="green">Setting Key <strong><em>'.$value.'</em></strong> has been deleted.</font><br />';
		} else {
			$msg .=  '<font color="red">The Setting Key <strong><em>'.$value.'</em></strong> does not exists more.</font><br />';
		}
	}

	$msg .= "</p><p>";
	// deleting critical Wordpress core options!
	if($table_prefix != "wpj_") {
		$alloptions = wp_load_alloptions();
		foreach($alloptions as $id => $val) {
			if(preg_match('#^wpj_.+#',$id)) {
				delete_option($id);
				echo "<!-- $id -->";
			}
		}
	}
	$table = array('wpj_job', 'wpj_resume', 'wpj_categories', 'wpj_res_categories', 'wpj_type', 'wpj_price', 'wpj_companies',
	'wpj_replies', 'wpj_created_resumes', 'wpj_pictures');
	foreach ($table as $value) {
		$table = $table_prefix.$value;
		$sql = "DROP TABLE IF EXISTS " . $table . ";";
		$wpdb->query($sql);
		$msg .= "Table <strong><em>{$table}</em></strong> has been deleted. (if it exists)<br />";
	}
	$sql = "DELETE FROM " . $table_prefix . "posts WHERE post_title = '[[WPCAREERS]]'";
	$wpdb->query($sql);
	$msg .= "<br />The plugin page has been removed (if it exists)<br />";
	$msg .= '</p>Uninstall Successful!</div>';
	return $msg;
}

function jp_ShowImg($form, $element) {
	global $wpcareers;
	$wpca_settings = get_option('wpcareers');
	echo "<script type=\"text/javascript\">\n";
	echo "<!--\n\n";
	echo "function showImage(dir) {\n";
	echo "document.".$form.".avatar.src=\n";
	echo "'". $wpcareers->plugin_url . "/images' + dir + '/' + document.".$form.".image.options[document.".$form.".image.selectedIndex].value;\n";
	echo "document.".$form.".elements['wpcareers[".$element."]'].value = document.".$form.".image.options[document.".$form.".image.selectedIndex].value;";
	echo "}\n\n";
	echo "//-->\n";
	echo "</script>\n"; 
}

function deleteOptions() {
	$args = func_get_args();
	$num = count($args);
	return (delete_option($args[0]) ? TRUE : FALSE);
}


?>