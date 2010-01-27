<?php

/**
 * wpj_admin_structure.php
 * Description: wpCareers wordpress plugin
 * This file handles the Administration of the Categories
 * @author Mohammad Forgani
 * @copyright Copyright 2010, Mohamad Forghanian
 * @version 1.0
 * @link http://www.forgani.com
 **/


function process_structure(){
	global $_GET, $_POST, $table_prefix, $PHP_SELF, $wpdb, $lang, $wpcareers;
	$wpca_settings = get_option('wpcareers');
		$view = true;
	if (!isset($_GET['c_id'])) $_GET['c_id']=0;
	$id = $_GET['c_id']*1;
	
	print wpcareers_admin_menu();
	
	if (isset($_GET['admin_action'])){
		switch ($_GET['admin_action']){
			case "saveJobCategory":
				$title=$wpdb->escape($_POST['wpcareers']['c_title']);
				$sort=$wpdb->escape($_POST['wpcareers']['c_sort']);
				$cp_id=$wpdb->escape($_POST['wpcareers']['cp_id']);
				$catImg=$wpdb->escape($_POST['image']);
				$affprice=$wpdb->escape($_POST['wpcareers']['c_affprice']);
				if ($id==0){
					$check = $wpdb->get_var("SELECT c_title FROM {$table_prefix}wpj_categories WHERE c_title= '".$title."'");
					if (!$check) {
						$sql = "INSERT INTO {$table_prefix}wpj_categories (c_title, c_img, c_sort, c_affprice, cp_id) values ('$title',					'".$catImg."','".$sort."',	'".$affprice."','".$cp_id."') ";
						$wpdb->query($sql);
						$msg ="Category Saved.";
					} else {
						$msg ="Category EXSITS!";
					}
				} else {
					$sql = "UPDATE {$table_prefix}wpj_categories 
					SET c_title ='".$title."', 
					c_img = '".$catImg."',
					cp_id = '".$cp_id."', 
					c_sort = '".$sort."',
					c_affprice='".$affprice ."' 
					WHERE c_id = '".($_GET['c_id']*1)."'";
					$wpdb->query($sql);
					$msg ="Category Saved.";
				}
			break;
			case "saveResCategory":
				$title=$wpdb->escape($_POST['wpcareers']['rc_title']);
				$sort=$wpdb->escape($_POST['wpcareers']['rc_sort']);
				$rcp_id=$wpdb->escape($_POST['wpcareers']['rcp_id']);
				if ($id==0){
					$check = $wpdb->get_var("SELECT c_title FROM {$table_prefix}wpj_res_categories WHERE rc_title= '".$title."'");
					if (!$check) {
						$sql = "INSERT INTO {$table_prefix}wpj_res_categories (rc_title, rc_img, rc_sort, rc_affprice) values ('$title',
						'".$wpdb->escape($_POST['image'])."','".$sort."',
						'".$wpdb->escape($_POST['wpcareers']['rc_affprice']). "') ";
						$wpdb->query($sql);
						$msg ="Category Saved.";
					} else {
						$msg ="Category EXSITS!";
					}
				} else {
					$sql = "UPDATE {$table_prefix}wpj_resume SET rc_title = '".$wpdb->escape(stripslashes($_POST['wpcareers']['rc_title'])).
					"', rc_img = '".$wpdb->escape(stripslashes($_POST['image'])).
					"', rc_sort = '".$wpdb->escape(stripslashes($_POST['wpcareers']['rc_sort'])).
					"', rc_affprice = '".$wpdb->escape(stripslashes($_POST['wpcareers']['rc_affprice']))."' WHERE rc_id = '".($_GET['rc_id']*1)."'";
					$wpdb->query($sql);
					$msg ="Category Saved.";
				}
			break;
			case "editJobCategory":
					wpj_job_edit_category($id);
					$view = false;
			break;
			case "editResCategory":
					wpj_res_edit_category($id);
					$view = false;
			break;
			case "viewJobFromCategory":
					wpj_job_view_from_category($id);
					$view = false;
			break;
			case "viewResFromCategory":
					wpj_res_view_from_category($id);
					$view = false;
			break;
			case "deleteJobCategory":
				if ($id<>0) {
					$wpdb->query("DELETE FROM {$table_prefix}wpj_categories WHERE c_id = '".($id)."'");
					$wpdb->query("DELETE FROM {$table_prefix}wpj_job WHERE lc_id = '".($id)."'");
				}
			break;
			case "deleteResCategory":
				if ($id<>0) {
					$wpdb->query("DELETE FROM {$table_prefix}wpj_res_categories WHERE rc_id = '".($id)."'");
					$wpdb->query("DELETE FROM {$table_prefix}wpj_job WHERE lc_id = '".($id)."'");
				}
			break;
		}
	}
	if (isset($msg) && $msg!=''){
		?>
		<div id="message" class="updated fade"><?php echo $msg;?></div>
		<?php
	}
	if ($view) wpj_view_category(0);
	echo '</div>';
}




function wpj_job_view_from_category($lcid){
	global $_GET, $_POST, $table_prefix, $wpdb, $lang, $_FILES, $wpcareers;

	$wpca_settings=get_option('wpcareers');
	$sql="SELECT * FROM {$table_prefix}wpj_job WHERE lc_id=".$lcid;
	if (!isset($lcid) || $lcid == 0 ){
		$sql="SELECT * FROM {$table_prefix}wpj_job";
	}
	$results=$wpdb->get_results($sql); 
	for ($i=0; $i<count($results); $i++){
		$result=$results[$i];
		$sql="SELECT c_title FROM {$table_prefix}wpj_categories WHERE c_id=".$lcid;
		$category=$wpdb->get_var($sql);
		$title = $result->l_title;
		$jobs[]=array (
			'title'=>$title,
			'lid'=>$result->l_id,
			'town'=>$result->l_town,
			'valid'=>$result->l_valid,
			'contactinfo'=>$result->l_contactinfo,
			'date'=>$result->l_date,
			'desctext'=>$result->l_desctext,
			'email'=>$result->l_email,
			'photo'=>$result->l_photo,
			'company'=>$result->l_company,
			'viewjob'=>$viewJoblink);
	}
	?>
	<P>
	<div class="wrap"><h2><?php echo $lang['JH_CATEGORY'] ?></h2><p>
		<div class="wpca">
		 <fieldset><legend>&nbsp;</legend>
		 <input type="button" value="<?php echo $lang['J_ADDCATPRINC'] ?>" onclick="document.location.href='<?php echo $PHP_SELF;?>?page=wpcareers_structure&admin_action=editJobCategory&c_id=0';">
		 <P>
		 <table>
			<tr>
				<th><img border=0 src="<?php echo $wpcareers->plugin_url; ?>/images/edit.png"> Title</th>
				<th width="150">Local</th>
				<th>Status</th>
				<th>Delete</th>
			</tr>
			<?php
			for ($i=0; $i<count($jobs); $i++){
				 $job = $jobs[$i];
				 $id = $job[lid];
				 $title = $job[title];
				echo '<tr>'; 
					echo '<td style="background-color:#E9E9E9">&nbsp;'.$space;
					$viewJoblink=wpcareers_create_link("jview", array("name"=>$title, "id"=>$id));
					?>
					<?php echo $viewJoblink; ?></td>
					<td style=""><?php echo $job[town]; ?></td>
					<td style="background-color:#E9E9E9"><?php echo $job[valid]; ?></td>
					<td><a style="text-decoration: none;" href="javascript:deleteCategory('<?php echo rawurlencode($job[title] . " " . $job[town] );?>', '<?php echo $PHP_SELF;?>?page=wpcareers_structure&admin_action=deleteJobCategory&c_id=<?php echo $id;?>');"><img border=0 src="<?php echo $wpcareers->plugin_url; ?>'/images/delete.png"></a></td>
				</tr>
				<?php
			}
			?>
			</table>
			<br></fieldset>
		</div>
	</div>
	<?php
} //wpj_job_view_from_category



function wpj_res_view_from_category($rcid){
	global $_GET, $_POST, $table_prefix, $wpdb, $lang, $_FILES, $wpcareers;
	$wpca_settings=get_option('wpcareers');
	$sql="SELECT * FROM {$table_prefix}wpj_resume WHERE rc_id=".$rcid;
	if (!isset($rcid) || $rcid == 0 ){
		$sql="SELECT * FROM {$table_prefix}wpj_resume";
	}
	$results=$wpdb->get_results($sql); 
	for ($i=0; $i<count($results); $i++){
		$result=$results[$i];
		$sql="SELECT rc_title FROM {$table_prefix}wpj_res_categories WHERE rc_id=".$rcid;
		$category=$wpdb->get_var($sql);
		$title = $result->r_title;
		$resumes[]=array (
			'title'=>$title,
			'rid'=>$result->r_id,
			'town'=>$result->r_town,
			'date'=>$result->r_date,
			'desctext'=>$result->r_desctext,
			'email'=>$result->r_email,
			'photo'=>$photo,
			'upload'=>$result->r_upload,
			'name'=>$result->r_name,
			'viewResume'=>$viewResume);
	}
	?>
	<P>
	<div class="wpca">
	<fieldset><legend><?php echo $lang['JH_CATEGORY'] ?></legend>
	<input type="button" value="<?php echo $lang['J_ADDCATPRINC'] ?>" onclick="document.location.href='<?php echo $PHP_SELF;?>?page=wpcareers_structure&admin_action=editResCategory&c_id=0';">
	<P>
	<table>
	<tr>
		<th><img border=0 src="<?php echo $wpcareers->plugin_url; ?>/images/edit.png"> Title</th>
		<th width="150">Local</th>
		<th>Status</th>
		<th>Delete</th>
		</tr>
		<?php
		for ($i=0; $i<count($resumes); $i++){
			$res = $resumes[$i];
			$id = $res[rid];
			$title = $res[title];
			echo '<tr>'; 
			echo '<td style="background-color:#E9E9E9">&nbsp;'.$space;
			$viewReslink=wpcareers_create_link("rview", array("name"=>$title, "id"=>$id));
			?>
			<?php echo $viewReslink; ?></td>
			<td style=""><?php echo $res[town]; ?></td>
			<td style="background-color:#E9E9E9"><?php echo $res[valid]; ?></td>
			<td><a style="text-decoration: none;" href="javascript:deleteCategory('<?php echo rawurlencode($res[title] . " " . $res[town] );?>', '<?php echo $PHP_SELF;?>?page=wpcareers_structure&admin_action=deleteResCategory&c_id=<?php echo $id;?>');"><img border=0 src="<?php echo $wpcareers->plugin_url; ?>/images/delete.png"></a></td></tr>
			<?php
		}
		?>
		</table>
		<br></fieldset>
	</div>
	<?php
} //wpj_res_view_from_category


function wpj_job_edit_category($id) {
	global $_GET, $_POST, $table_prefix, $PHP_SELF, $wpdb, $lang, $wpcareers;
	$wpca_settings = get_option('wpcareers');
	jp_ShowImg('edit_category', 'c_img');
	$wpj_categories = $wpdb->get_row("SELECT * FROM {$table_prefix}wpj_categories WHERE c_id = '".($id)."'", ARRAY_A);
	?>
	<P>
	<div class="wpca">
	<form method="post" id="edit_category" name="edit_category" 
		action="<?php echo $PHP_SELF;?>?page=wpcareers_structure&admin_action=saveJobCategory&c_id=<?php echo $id; ?>">
	<fieldset>
	<legend><?php echo $lang['J_MODIFCAT'] ?></legend>
	<table border=0><tr><td width="120"><?php echo $lang['J_CATNAME'] ?></td><td style="background-color:#fff">
	<input type="text" size="40" name="wpcareers[c_title]" value="<?php echo $wpj_categories['c_title'];?>">&nbsp; <?php echo $lang['J_IN']; ?>&nbsp;
	<?php
	$sql = "SELECT * FROM {$table_prefix}wpj_categories WHERE cp_id=0";
	$key = $wpj_categories['cp_id'];
	$jcategories = $wpdb->get_results($sql);
	echo '<select name="wpcareers[cp_id]">';
	echo "\n<option value='0'>--</option>\n";
	for ($i=0; $i<count($jcategories); $i++){
		$jcategorie = $jcategories[$i];
		$title = $jcategorie->c_title;
		$id = $jcategorie->c_id;
		if ($key == $id) {
			echo "\n<option value='$id' selected='selected'>$title</option>\n";
		} else {
			echo "\n<option value='$id'>$title</option>\n";
		}
	}
	echo "\n</select></td></tr>";
	?>
	<tr><td><?php echo $lang['J_IMGCAT']; ?></td><td>
	<?php	echo "<select name=\"image\" onChange=\"showImage('/')\">";
	$rep = $wpcareers->plugin_dir . '/images';
	$handle=opendir($rep);
	while ($file = readdir($handle)) {
		$filelist[] = $file;
	}
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

	echo "</select>&nbsp;&nbsp;<img src=\"". $wpcareers->plugin_url."/images/".$wpj_categories['c_img']. "\" name=\"avatar\" align=\"absmiddle\"></td></tr><tr><td>&nbsp;</td><td style='background-color:#fff'>".$lang['J_REPIMGCAT']." /wp-content/plugins/wpcareers/images/</td></tr>";
	
	echo "<tr><td>".$lang['J_DISPLPRICE']." </td><td style='background-color:#fff'><input type=\"radio\" name=\"wpcareers[c_affprice]\" value=\"1\" checked>".$lang['J_YES']."&nbsp;&nbsp; <input type=\"radio\" name=\"wpcareers[c_affprice]\" value=\"0\">".$lang['J_NO']." (".$lang['J_INTHISCAT'].")</td></tr>";
	echo "<tr><td>".$lang['J_ORD']." </td><td style='background-color:#fff'><input type=\"text\" name=\"wpcareers[c_sort]\" size=\"4\" value=\"".$wpj_categories['c_sort']. "\"></td></tr><tr><td><td style='background-color:#fff'><br /><input type=\"submit\" value=\"".$lang['J_SUBMIT']."\"></td> &nbsp;&nbsp;</tr>";
	?>
	</table></form><br>
	</fieldset><br />
	</div>
	<?php
} 




function wpj_res_edit_category($id) {
	global $_GET, $_POST, $table_prefix, $PHP_SELF, $wpdb, $lang, $wpcareers;
	$wpca_settings = get_option('wpcareers');
	jp_ShowImg('edit_resume', 'c_img');
	$sql = "SELECT * FROM {$table_prefix}wpj_res_categories WHERE rc_id = ". $id;
	$wpj_categories = $wpdb->get_row( $sql, ARRAY_A);
	?>
	<P>
	<div class="wpca">
	<form method="post" id="edit_resume" name="edit_resume" 
		action="<?php echo $PHP_SELF;?>?page=wpcareers_structure&admin_action=saveResCategory&c_id=<?php echo $id; ?>">
	<fieldset>
	<legend><?php echo $lang['J_MODIFCAT'] ?></legend>
	<table border=0><tr><td width="120"><?php echo $lang['J_CATNAME'] ?></td><td style="background-color:#fff">
	<input type="text" size="40" name="wpcareers[rc_title]" value="<?php echo $wpj_categories['rc_title'];?>">&nbsp; <?php echo $lang['J_IN']; ?>&nbsp;
	<?php
	$sql = "SELECT * FROM {$table_prefix}wpj_res_categories WHERE rcp_id=0";
	$key = $wpj_categories['rcp_id'];
	$jcategories = $wpdb->get_results($sql);
	echo '<select name="wpcareers[rcp_id]">';
	echo "\n<option value='0'>--</option>\n";
	for ($i=0; $i<count($jcategories); $i++){
		$jcategorie = $jcategories[$i];
		$title = $jcategorie->rc_title;
		$id = $jcategorie->rc_id;
		if ($key == $id) {
			echo "\n<option value='$id' selected='selected'>$title</option>\n";
		} else {
			echo "\n<option value='$id'>$title</option>\n";
		}
	}
	echo "\n</select></td></tr>";
	?>
	<tr><td><?php echo $lang['J_IMGCAT']; ?></td><td>
	<?php	echo "<select name=\"image\" onChange=\"showImage('/')\">";
	$rep = $wpcareers->plugin_dir . "/images/";
	$handle=opendir($rep);
	while ($file = readdir($handle)) {
		$filelist[] = $file;
	}
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

	echo "</select>&nbsp;&nbsp;<img src=\"". $wpcareers->plugin_url . "/images/".$wpj_categories['rc_img']. "\" name=\"avatar\" align=\"absmiddle\"></td></tr><tr><td>&nbsp;</td><td style='background-color:#fff'>".$lang['J_REPIMGCAT']." /wp-content/plugins/wpcareers/images/</td></tr>";
	
	echo "<tr><td>".$lang['J_DISPLPRICE']." </td><td><input type=\"radio\" name=\"wpcareers[rc_affprice]\" value=\"1\" checked>".$lang['J_YES']."&nbsp;&nbsp; <input type=\"radio\" name=\"wpcareers[rc_affprice]\" value=\"0\">".$lang['J_NO']." (".$lang['J_INTHISCAT'].")</td></tr>";
	echo "<tr><td>".$lang['J_ORD']." </td><td><input type=\"text\" name=\"wpcareers[rc_sort]\" size=\"4\" value=\"".$wpj_categories['rc_sort']. "\"></td></tr><tr><td><td style='background-color:#fff'><br /><input type=\"submit\" value=\"".$lang['J_SUBMIT']."\"></td> &nbsp;&nbsp;</tr>";
	?>
	</table></form><br>
	</fieldset><br />
	</div>
	<?php
} 





function wpj_view_category($id) {
	global $_GET, $_POST, $table_prefix, $PHP_SELF, $wpdb, $lang, $wpcareers;
	$wpca_settings = get_option('wpcareers');
	$categoy_status = array('active'=>'Open','inactive'=>'Closed','readonly'=>'Read-Only');
	?>
	<img src="<?php echo $wpcareers->plugin_url; ?>/images/delete.png"><?php echo $lang['J_DELJOBCAT']; ?><p>
	<P>
	<div class="wpca">
	<fieldset><legend><?php echo $lang['J_CATEGORY'] ?></legend>
	<input type="button" value="<?php echo $lang['J_ADDCATPRINC'] ?>" onclick="document.location.href='<?php echo $PHP_SELF;?>?page=wpcareers_structure&admin_action=editJobCategory&c_id=0';">
	<P>
	<table>
		<tr style="background-color:#ccc">
			<th width="500"><img border=0 src="<?php echo $wpcareers->plugin_url; ?>/images/edit.png"> Title</th>
			<th width="150">Number of links</th>
			<th>Delete</th>
			</tr>
			<?php wpj_job_cats($id,0,'c_title', 'ASC');?>
	</table>
	<br></fieldset>
	<p><hr></p>
	<fieldset><legend><?php echo $lang['R_CATEGORY'] ?></legend>
	<input type="button" value="<?php echo $lang['J_ADDCATPRINC'] ?>" onclick="document.location.href='<?php echo $PHP_SELF;?>?page=wpcareers_structure&admin_action=editResCategory&c_id=0';">
	<P>
	<table>
		<tr style="background-color:#ccc">
			<th width="500"><img border=0 src="<?php echo $wpcareers->plugin_url; ?>/images/edit.png"> Title</th>
			<th width="150">Number of links</th>
			<th>Delete</th>
		</tr>
		<?php wpj_res_cats($id,0,'rc_title', 'ASC');?>
	</table></td>
	</tr></table>
	<br></fieldset>
	</div>

	<?php
} // wpj_view_category


function wpj_list_cats($parent, $lev, $exclude, $selected) { 
	global $table_prefix, $wpdb;
	$out = ""; 
	if ($lev == 0) {
		echo "\n"; 
	}
	$space = ""; 
	for ($x = 0; $x < $lev; $x++) { 
		$space .= "&nbsp;&nbsp;&nbsp;-&nbsp;"; 
	} 
	$categories = $wpdb->get_results("SELECT * FROM {$table_prefix}wpj_categories WHERE cp_id = '$parent' ORDER BY c_title ASC"); 
	for ($i=0; $i<count($categories); $i++){
		$category = $categories[$i];
		$res = $wpdb->get_row("SELECT * FROM {$table_prefix}wpj_job WHERE lc_id = '".$category->c_id."'", ARRAY_A); 
		$linksNum = count($res);
		$id = $category->c_id; 
		$title = $category->c_title;
		$sel = ""; 
		if($id == $selected){$sel = " selected ";}
		if($id != $exclude){echo "<option $sel value=\"$id\">$space$title ($linksNum)</option>";} 
		$out = wpj_list_cats($id, $lev + 1, $exclude, $selected);	
	}
	return $out; 
} 



function wpj_job_cats($parent, $lev, $orderby, $how) { 
	global $table_prefix, $wpdb, $PHP_SELF, $wpcareers;
	$out = "";
	$space = "";
	?>
	<script language=javascript>
		<!--
		function deleteCategory(x, y){
			if (confirm("Are you sure you wish to delete the Category\n" + x)){
				document.location.href = y;
			}
		} //-->
	</script>
	<?php
	if($lev > 0) {
		for($x=0;$x<$lev;$x++){$space .= "&nbsp;&nbsp;&nbsp;&nbsp;";}
	} elseif ($lev == 0) {print "\n";}
	$sql = "SELECT * FROM {$table_prefix}wpj_categories WHERE cp_id = $parent ORDER BY $orderby $how";
	$categories = $wpdb->get_results($sql);
	for ($i=0; $i<count($categories); $i++){
		$category = $categories[$i];
		$linksCnt = $wpdb->get_row("SELECT count(lc_id) as count FROM {$table_prefix}wpj_job WHERE lc_id=".$category->c_id, ARRAY_A);
		$linksNum = $linksCnt['count'];
		$id = $category->c_id;
		$title = $category->c_title;
		echo '<tr>';
		echo '<td style="background-color:#E9E9E9">&nbsp;'.$space;
		?>	
		<a style="text-decoration: none;" href="<?php echo $PHP_SELF;?>?page=wpcareers_structure&admin_action=editJobCategory&c_id=<?php echo $id;?>"><?php echo ($title); ?></a>
		<?php
		echo '<td style="background-color:#E9E9E9">(<a href="'.$PHP_SELF.'?page=wpcareers_structure&admin_action=viewJobFromCategory&c_id='.$category->c_id.'">'.$linksNum.'</a>)</td>';
		?>
		<td style="background-color:#E9E9E9"><a style="text-decoration: none;" href="javascript:deleteCategory('<?php echo rawurlencode($category->c_title." " .$category->c_name );?>', '<?php echo $PHP_SELF;?>?page=wpcareers_structure&admin_action=deleteJobCategory&c_id=<?php echo $id;?>');"><img border=0 src="<?php echo $wpcareers->plugin_url; ?>/images/delete.png"></a></td></tr>
		<?php $print = wpj_job_cats($id,$lev + 1,'c_title', 'ASC');
	}
	return $out;
} // wpj_job_cats


function wpj_res_cats($parent, $lev, $orderby, $how) { 
	global $table_prefix, $wpdb, $PHP_SELF, $wpcareers;
	$out = "";
	if($lev == 0){print "\n";}
	$space = "";
	?>
	<script language=javascript>
		<!--
		function deleteCategory(x, y){
			if (confirm("Are you sure you wish to delete the Category\n" + x)){
				document.location.href = y;
			}
		} //-->
	</script>
	<?php
	for($x=0;$x<$lev;$x++){$space .= "&nbsp;&nbsp;&nbsp;&nbsp;";}
	$sql = "SELECT * FROM {$table_prefix}wpj_res_categories WHERE rcp_id = $parent ORDER BY $orderby $how";
	$categories = $wpdb->get_results($sql);
	
	for ($i=0; $i<count($categories); $i++){
		$category = $categories[$i];
		$sql = "SELECT count(rc_id) as count FROM {$table_prefix}wpj_resume WHERE rc_id=".$category->rc_id;
		$linksCnt = $wpdb->get_row($sql, ARRAY_A);
		$linksNum = $linksCnt['count'];
		$id = $category->rc_id;
		$title = $category->rc_title;
		echo '<tr>';
		echo '<td style="background-color:#E9E9E9">&nbsp;'.$space;
		?>	
		<a style="text-decoration: none;" href="<?php echo $PHP_SELF;?>?page=wpcareers_structure&admin_action=editResCategory&c_id=<?php echo $id;?>"><?php echo ($title); ?></a>
		<?php
		echo '<td style="background-color:#E9E9E9">(<a href="'.$PHP_SELF.'?page=wpcareers_structure&admin_action=viewResFromCategory&c_id=' .$category->rc_id. '">'. $linksNum .'</a>)</td>';
		?>
		<td style="background-color:#E9E9E9"><a style="text-decoration: none;" href="javascript:deleteCategory('<?php echo rawurlencode($category->rc_title . " " . $category->rc_name );?>', '<?php echo $PHP_SELF;?>?page=wpcareers_structure&admin_action=deleteResCategory&c_id=<?php echo $id;?>');"><img border=0 src="<?php echo $wpcareers->plugin_url; ?>/images/delete.png"></a></td></tr>
		<?php
		$print = wpj_res_cats($id,$lev + 1, 'rc_title', 'ASC'); 
	}
	return $out; 
} // wpj_res_cats

?>