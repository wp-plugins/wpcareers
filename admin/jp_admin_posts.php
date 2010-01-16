<?php

/**
 * jp_admin_posts.php
 * Description: * wpCareers wordpress plugin
 * This file handles the Administration of the Posts
 * @author Mohammad Forgani
 * @copyright Copyright 2010, Mohammad Forghanian
 * @version 1.0
 * @link http://www.forgani.com
 */

	
function process_posts() {
	global $_GET, $_POST, $table_prefix, $PHP_SELF, $wpdb, $lang, $wpca_adm_page_name;
	$wpca_settings=get_option('wpcareers');
	$loadpage=true;
	
	print wpcareers_admin_menu();
	
	?>
	<div class="wrap"><h2>wpCareers - <?php echo $lang['J_ACCESADMIN']; ?></h2><p>
	<div class='wpca'>
	<input type="button" value="Main" onclick="document.location.href='<?php echo $PHP_SELF;?>?page=wpcareers_structure&admin_action=jviewFromCategory&id=0';">
	<input type="button" value="<?php echo $lang['J_WAIT']; ?>" onclick="document.location.href='<?php echo $PHP_SELF;?>?page=wpcareers_posts&admin_action=list';">

	<?php
	if (!isset($_GET['admin_action'])) $_GET['admin_action']= 'list';
	
	wpcareers_approve_post($_GET['id']*1, $_GET['admin_action']);
	$loadpage=false;

	if(isset($msg) && $msg!='')
		echo '<div id="message" class="updated fade">' . $msg . '</div>';
	if($loadpage==true) 
		wpcareers_admin_main_job(0, 0);
	echo '</div></div>';
}


function wpcareers_approve_post($id, $action) {
	global $_GET, $_POST, $wpdb, $table_prefix, $lang, $PHP_SELF;
	$wpca_settings=get_option('wpcareers');
	$linkb=$PHP_SELF."?page=wpcareers_posts";
	if( isset($id) && isset($action) ) {
		switch($action) {
		case "japprove":
			echo "<div class='wpca'><h3>" . $lang['J_MODERAT'] . "</h3>";
			$sql="SELECT * FROM {$table_prefix}wpj_job WHERE l_id=$id";
			$news=$wpdb->get_results($sql);
			for($i=0; $i<count($news); $i++){
				$new=$news[$i];
				?>
				<form method="post" id="wpj_form_post" name="wpj_form_post" action="<?php echo $linkb ?>&admin_action=jinsert&id=<?php echo $new->l_id; ?>">
				<table>
				<?php
				echo "<tr><td valign='top'>" .$lang['J_CATEGORY']."</td><td>";
				$sql="SELECT c_title FROM {$table_prefix}wpj_categories WHERE c_id='".$new->lc_id."'";
				$category=$wpdb->get_var($sql);
				echo $category . "</td></tr>";
				?>
				<tr><th><?php echo $lang['J_COMPANY']; ?></th><td><input type="text" name="wpcareers[company]" value="<?php echo $new->l_company; ?>" size="50"></td></tr>
				<tr><th><?php echo $lang['J_TITLE']; ?></th><td><input type="text" name="wpcareers[title]" value="<?php echo $new->l_title; ?>" size="50"></td></tr>
				<tr><th><?php echo $lang['J_TEL']; ?></th><td><input type="text" name="wpcareers[tel]" value="<?php echo $new->l_tel; ?>" size="50"></td></tr>
				<tr><th><?php echo $lang['J_FAX']; ?></th><td><input type="text" name="wpcareers[fax]" value="<?php echo $new->l_fax; ?>" size="50"></td>
				</tr>
				<tr><th><?php echo $lang['J_EMAIL']; ?></th><td><input type="text" name="wpcareers[email]" value="<?php echo $new->l_email; ?>" size="50"></td></tr>
				<tr><th valign='top'><?php echo $lang['J_DESC']; ?></th><td><textarea rows="5" name="wpcareers[description]" cols="60"><?php echo $new->l_desctext?></textarea></td></tr>
				<tr><th><?php echo $lang['J_APPROVE']; ?>?</th><td><select name="wpcareers[visible]"><option value="Yes">&nbsp;<?php echo $lang['J_YES']; ?>&nbsp;</option><option value="No"><?php echo $lang['J_NO']; ?></option></select></td>
				</tr>
				<tr><th></th><td><input type="submit" value="<?php echo $lang['J_SUBMIT']; ?>">&nbsp;&nbsp;<input type=button value="Cancel" onclick="history.go(-1);"></td></tr>
				</table>
				</form></div>
				<?php
			}
		break;
		case "rapprove":
			echo "<div class='wpca'><h3>" . $lang['R_MODERAT'] . "</h3>";
			$sql="SELECT * FROM {$table_prefix}wpj_resume WHERE r_id=$id";
			$news=$wpdb->get_results($sql);
			for($i=0; $i<count($news); $i++){
				$new=$news[$i];
				?>
				<form method="post" id="wpj_form_post" name="wpj_form_post" action="<?php echo $linkb ?>&admin_action=rinsert&id=<?php echo $new->r_id; ?>">
				<table>
				<?php
				echo "<tr><th valign='top'>" .$lang['J_CATEGORY']."</th><td>";
				$sql="SELECT rc_title FROM {$table_prefix}wpj_res_categories WHERE rc_id='".$new->rc_id."'";
				$category=$wpdb->get_var($sql);
				echo $category . "</td></tr>";
				?>
				<tr><th><?php echo $lang['J_SURNAME']; ?></th><td><input type="text" name="wpcareers[company]" value="<?php echo $new->r_name; ?>" size="50"></td></tr>
				<tr><th><?php echo $lang['J_TITLE']; ?></th><td><input type="text" name="wpcareers[title]" value="<?php echo $new->r_title; ?>" size="50"></td></tr>
				<tr><th><?php echo $lang['J_TEL']; ?></th><td><input type="text" name="wpcareers[tel]" value="<?php echo $new->r_tel; ?>" size="50"></td></tr>
				<tr><th><?php echo $lang['J_FAX']; ?></th><td><input type="text" name="wpcareers[fax]" value="<?php echo $new->r_fax; ?>" size="50"></td></tr>
				<tr><th><?php echo $lang['J_EMAIL']; ?></th><td><input type="text" name="wpcareers[email]" value="<?php echo $new->r_email; ?>" size="50"></td></tr>
				<tr><th valign='top'><?php echo $lang['J_DESC']; ?></td><td><textarea rows="5" name="wpcareers[description]" cols="60"><?php echo $new->r_desctext?></textarea></td></tr>
				<tr><th><?php echo $lang['J_APPROVE'] ?>?</td><td><select name="wpcareers[visible]"><option value="Yes">&nbsp;<?php echo $lang['J_YES']; ?>&nbsp;</option><option value="No"><?php echo $lang['J_NO']; ?></option></select></td></tr>
				<tr><th></th><td><input type="submit" value="<?php echo $lang['J_SUBMIT']; ?>">&nbsp;&nbsp;<input type=button value="Cancel" onclick="history.go(-1);"></td></tr></table></form></div>
				<?php
			}
		break;
		case "jdelete":
			if(isset($_POST['wpcareers']['delete_job'])){
				echo "<div class='wpca'><h3>" . $lang['JH_DEL'] . " ...</h3>";
				$sql="SELECT * FROM {$table_prefix}wpj_job WHERE l_id=$id";
				$results=$wpdb->get_results($sql);
				for($i=0; $i<count($results); $i++){
					$row=$results[$i];
					echo "<table>";
					echo "<tr><th>" . $lang['J_TITLE'] ."</th><td>".$row->l_title."</td></tr>";
					echo "<tr><th>" . $lang['J_DATE'] ."</th><td>".$row->l_date."</td></tr>";
					echo "<tr><th>" . $lang['J_EMAIL'] ."</th><td>".$row->l_email."</td></tr>";
					echo "<tr><td colspan=2><BR /><H3>" . $lang['J_JOBDEL'] . "</td></tr></table>";
				}
				$url_back = admin_url("admin.php?page=wpcareers_posts&admin_action=japprove");
				echo '<p><form method="post" action="'.$url_back.'"><input type="submit" value="&#060;Back"></form></p>';
				$sql="DELETE FROM {$table_prefix}wpj_job WHERE l_id=$id"; 
				$wpdb->query($sql);
			} else {
				echo "<h3>" . $lang['JH_DEL'] ." ...</h3>";
				$sql="SELECT * FROM {$table_prefix}wpj_job WHERE l_id=$id";
				$results=$wpdb->get_results($sql);
				for($i=0; $i<count($results); $i++){
					$row=$results[$i];
					echo "<table>";
					echo "<tr><th>" . $lang['J_TITLE'] ."</th><td>".$row->l_title."</td></tr>";
					echo "<tr><th>" . $lang['J_DATE'] ."</th><td>".$row->l_date."</td></tr>";
					echo "<tr><th>" . $lang['J_EMAIL'] ."</th><td>".$row->l_email."</td></tr>";
					?>
					<form method="post" id="wpj_form_post" name="wpj_form_post" action="<?php echo $linkb ?>&admin_action=jdelete&id=<?php echo $id; ?>" method="POST">
					<tr><td colspan=2><br /><?php echo $lang['J_SURDEL']; ?></td></tr>
					<tr><td></td><td><input type="submit" name="wpcareers[delete_job]" value="<?php echo $lang['JH_DEL']; ?>">&nbsp;&nbsp;<input type=button value="Cancel" onclick="history.go(-1);"></td></tr></table></form></div>
					<?php
				}
			}
		break;
		case "rdelete":
			if(isset($_POST['wpcareers']['delete_job'])){
				echo "<div class='wpca'><h3>" . $lang['JH_DEL'] . " ...</h3>";
				$sql="SELECT * FROM {$table_prefix}wpj_resume WHERE r_id=$id";
				$results=$wpdb->get_results($sql);
				for($i=0; $i<count($results); $i++){
					$row=$results[$i];
					echo "<table>";
					echo "<tr><th>" . $lang['J_TITLE'] ."</th><td>".$row->r_title."</td></tr>";
					echo "<tr><th>" . $lang['J_DATE'] ."</th><td>".$row->r_date."</td></tr>";
					echo "<tr><th>" . $lang['J_EMAIL'] ."</th><td>".$row->r_email."</td></tr>";
					echo "<tr><td colspan=2><BR /><H3>" . $lang['J_RESUMEDEL'] . "</td></tr></table>";
				}
				$url_back = admin_url("admin.php?page=wpcareers_posts&admin_action=rapprove");
				echo '<p><form method="post" action="'.$url_back.'"><input type="submit" value="&#060;Back"></form></p>';
				$sql="DELETE FROM {$table_prefix}wpj_resume WHERE r_id=$id";
				$wpdb->query($sql);
			} else {
				echo "<h3>".$lang['JH_DEL']." ...</h3>";
				$sql="SELECT * FROM {$table_prefix}wpj_resume WHERE r_id=$id";
				$results=$wpdb->get_results($sql);
				for($i=0; $i<count($results); $i++){
					$row=$results[$i];
					echo "<table>";
					echo "<tr><th>" . $lang['J_TITLE'] ."</th><td>".$row->r_title."</td></tr>";
					echo "<tr><th>" . $lang['J_DATE'] ."</th><td>".$row->r_date."</td></tr>";
					echo "<tr><th>" . $lang['J_EMAIL'] ."</th><td>".$row->r_email."</td></tr>";
					?>
					<form method="post" id="wpj_form_post" name="wpj_form_post" action="<?php echo $linkb ?>&admin_action=rdelete&id=<?php echo $id; ?>" method="POST">
					<tr><td colspan=2><br /><?php echo $lang['R_SURDEL']; ?></td></tr>
					<tr><td></td><td><input type="submit" name="wpcareers[delete_job]" value="<?php echo $lang['JH_DEL']; ?>">&nbsp;&nbsp;<input type=button value="Cancel" onclick="history.go(-1);"></td></tr></table></form></div>
					<?php
				}
			}
		break;
		// approve
		case "jinsert":
			$title=$_POST['wpcareers']['title'];
			$description=$_POST['wpcareers']['description'];
			$parent=$_POST['wpcareers']['parent'];
			$email=$_POST['wpcareers']['email'];
			$sql="UPDATE {$table_prefix}wpj_job SET l_valid='Yes' WHERE l_id=$id";
			$wpdb->query($sql);
			echo "<div class='wpca'><h3>" . $lang['J_POSTAPPROVED'] . "</h3>";
			?>
			<table>
				<tr><th><?php echo $lang['J_TITLE'] . "</th><td>" . $title ?></td></tr>
				<tr><th><?php echo $lang['J_EMAIL'] . "</th><td>" . $email ?></td></tr>
				<tr><th><?php echo $lang['J_DESC'] . "</th><td>" . $description?><BR /></td></tr>
			<?php
			$url_back = admin_url("admin.php?page=wpcareers_posts&admin_action=japprove");
			echo '<tr><td colspan=2><BR /><form method="post" action="'.$url_back.'"><input type="submit" value="&#060;Back"></form></td></tr></table></div>';
		break;
		case "rinsert":
			$title=$_POST['wpcareers']['title'];
			$description=$_POST['wpcareers']['description'];
			$parent=$_POST['wpcareers']['parent'];
			$email=$_POST['wpcareers']['email'];
			$sql="UPDATE {$table_prefix}wpj_resume SET r_valid='Yes' WHERE r_id=$id";
			$wpdb->query($sql);
			echo "<div class='wpca'><h3>" . $lang['R_POSTAPPROVED'] . "</h3>";
			?>
			<table>
				<tr><th><?php echo $lang['J_TITLE'] . "</th><td>" . $title ?></td></tr>
				<tr><th><?php echo $lang['J_EMAIL'] . "</th><td>" . $email ?></td></tr>
				<tr><th><?php echo $lang['J_DESC'] . "</th><td>" . $description?><BR /></td></tr>
			<?php
			$url_back = admin_url("admin.php?page=wpcareers_posts&admin_action=rapprove");
			echo '<tr><td colspan=2><BR /><form method="post" action="'.$url_back.'"><input type="submit" value="&#060;Back"></form></td></tr></table></div>';
		break;
		// list
		case "list":
			$sql="SELECT * FROM {$table_prefix}wpj_job WHERE (l_valid='No' or l_valid='') order by l_id";
			$result=$wpdb->get_results($sql);
			$numrows = count($result);
			if (count($result)>0) {
				echo "<div class='wpca'><fieldset><legend>".$lang['J_WAIT']."</legend>"; 
				echo $lang['J_THEREIS']." <b>$numrows</b> ".$lang['J_WAIT']."<br /><br />";
				?>
				<table>
				<tr>
				<th width="80"><?php echo $lang['JH_APPROVE']; ?></th>
				<th><?php echo $lang['JH_TITLE']; ?></th>
				<th width="100"><?php echo $lang['JH_DATE']; ?></th>
				<th width="250"><?php echo $lang['JH_CATEGORY']; ?></th>
				<th width="150"><?php echo $lang['JH_EMAIL']; ?></th>
				<th width="40"><?php echo $lang['JH_DEL']; ?></th></tr>
				<?php
				$rank = 1;
				for($x=0; $x<$numrows; $x++) {
					$row=$result[$x];
					if(is_integer($rank/2)) {
						$color="style='background-color:#fafafa;'";
					} else {
						$color="style='background-color:#fff;'";
					}
					$rank++;
					?>
					<tr <?php echo $color; ?> onMouseOver="this.bgColor='#FFF';" onMouseOut="this.bgColor='#F4F4F4';"><td>
					<a href="<?php echo $linkb; ?>&admin_action=japprove&id=<?php echo $row->l_id;?>">
					<img border="0" src="<?php echo get_bloginfo('wpurl'); ?>/wp-content/plugins/wpcareers/images/ok.gif"></a></td>
					<?php 
					print "<td>".$row->l_title."</td>";
					print "<td>".$row->l_date."</td>";
					$sql="SELECT c_title FROM {$table_prefix}wpj_categories WHERE c_id='".$row->lc_id."'";
					$category=$wpdb->get_var($sql);
					print "<td>".$category."</td>";
					print "<td>".$row->l_email."</td>"; ?><td>
					<a href="<?php echo $linkb ?>&admin_action=jdelete&id=<?php echo $row->l_id?>"><img border="0" src="<?php echo get_bloginfo('wpurl'); ?>/wp-content/plugins/wpcareers/images/delete.png"></a></td></tr>
					<?php
				}
				echo '</table><hr />';
			} else {
				echo "<fieldset><legend>". $lang['J_WAIT'] . "</legend>";
				echo "<br /> ".$lang['J_NOPOSTAVAL']."<br /><br />";
				echo "</fieldset><br />";
			}
			// resume listing
			$sql="SELECT * FROM {$table_prefix}wpj_resume WHERE (r_valid='No' or r_valid='') order by r_id";
			$result=$wpdb->get_results($sql);
			$numrows = count($result);
			if (count($result)>0) {
				echo "<fieldset><legend>".$lang['R_WAIT']."</legend>"; 
				echo $lang['J_THEREIS']." <b>$numrows</b> ".$lang['R_WAIT']."<br /><br />";
				?>
				<table>
				<tr>
				<th width="80"><?php echo $lang['JH_APPROVE']; ?></th>
				<th><?php echo $lang['JH_TITLE']; ?></th>
				<th width="100"><?php echo $lang['JH_DATE']; ?></th>
				<th width="250"><?php echo $lang['JH_CATEGORY']; ?></th>
				<th width="150"><?php echo $lang['JH_EMAIL']; ?></th>
				<th width="40"><?php echo $lang['JH_DEL']; ?></th></tr>
				<?php
				$rank = 1;
				for($x=0; $x<$numrows; $x++) {
					$row=$result[$x];
					if(is_integer($rank/2)) {
						$color="style='background-color:#fafafa;'";
					} else {
						$color="style='background-color:#fff;'";
					}
					$rank++;
					?>
					<tr <?php echo $color; ?> onMouseOver="this.bgColor='#FFF';" onMouseOut="this.bgColor='#F4F4F4';"><td>
					<a href="<?php echo $linkb; ?>&admin_action=rapprove&id=<?php echo $row->r_id;?>">
					<img border="0" src="<?php echo get_bloginfo('wpurl'); ?>/wp-content/plugins/wpcareers/images/ok.gif"></a></td>
					<?php
					print "<td>".$row->r_title."</td>";
					// TODO
					print "<td>".$row->r_date."</td>";
					$sql="SELECT rc_title FROM {$table_prefix}wpj_res_categories WHERE rc_id='".$row->rc_id."'";
					$category=$wpdb->get_var($sql);
					print "<td>".$category."</td>";
					print "<td>".$row->r_email."</td>"; ?><td>
					<a href="<?php echo $linkb ?>&admin_action=rdelete&id=<?php echo $row->r_id?>"><img border="0" src="<?php echo get_bloginfo('wpurl'); ?>/wp-content/plugins/wpcareers/images/delete.png"></a></td></tr><?php
				} 
				echo '</table></div><BR />';
			} else {
				echo "<fieldset><legend>". $lang['R_WAIT'] . "</legend>";
				echo "<br /> ".$lang['R_NOPOSTAVAL']."<br /><br />";
				echo "</fieldset></div>";
			}
			break;
		}
	}
}



function wpcareers_admin_main_job($id, $action){
	global $table_prefix, $wpdb, $_GET, $lang, $PHP_SELF;
	$msg = '';
	$linkb=$PHP_SELF."?page=wpcareers_posts";
	echo '<div class="wpca">';
	if(isset($id) && $action == 'delete'){
		switch($action){ 
			case "jdelete": 
				if(isset($_POST['wpcareers']['delete_job'])){ 
					?><h3><?php echo $lang['J_SURDEL']; ?> ...</h3><?php
					$sql="SELECT * FROM {$table_prefix}wpj_job WHERE l_id=$id";
					$results=$wpdb->get_results($sql); 
					for($i=0; $i<count($results); $i++){
						$row=$results[$i];
						echo "Price: ".$row->l_price."<br>"; 
						echo $lang['J_TITLE']." ".$row->l_title."<br>"; 
						echo $lang['J_DESC']." ".$row->l_description."<br>"; 
						echo $lang['J_EMAIL']." ". $row->l_mail."<br>"; 
					}
					$sql="DELETE FROM {$table_prefix}wpj_job WHERE l_id=$id"; 
					$wpdb->query($sql);
				}else{ 
					?><h3><?php echo $lang['R_SURDEL']; ?> ...</h3><?php
					$sql="SELECT * FROM {$table_prefix}wpj_job WHERE l_id=$id";
					$results=$wpdb->get_results($sql); 
					for($i=0; $i<count($results); $i++){
						$row=$results[$i];
					?>
					<form method="post" id="wpj_form_post" name="wpj_form_post"	action="<?php echo $linkb ?>&admin_action=main&id=<?php echo $id; ?>&action=delete">
					<p>Are you sure you want to delete the website <strong><?php echo $row->l_url?></strong>?</p> 
					<input type="submit" name="wpcareers[delete_job]" value="Delete Link">&nbsp;&nbsp;<input type=button value="Cancel" onclick="history.go(-1);"></form></div>
					<?php
					}
				}
			break; 
		}
	} else{ 
		$sql = "SELECT * FROM {$table_prefix}wpj_job WHERE lc_id='".$id."'";
		$results = $wpdb->get_results($sql);
		if(!empty($results)) {
			for($i=0; $i<count($results); $i++){
				$row=$results[$i];
				?> 
				<form method="post" id="wpj_form_post" name="wpj_form_post" action="<?php echo $linkb ?>&admin_action=editlinks&id=<?php echo $row->l_id; ?>">
				<fieldset>
				<legend>Edit Website</legend>
				<input type="hidden" name="wpcareers[id]" value="<?php echo $row->l_id; ?>">
				<br><label><?php echo $lang['J_TITLE']; ?></label>
				<input type="text" name="wpcareers[title]" value="<?php echo $row->l_title; ?>" size="50">
				<br><label>Email:</label>
				<input type="text" name="wpcareers[email]" value="<?php echo $row->l_email; ?>" size="50">
				<br><label><?php echo $lang['J_DESC']; ?></label>
				<textarea rows="5" name="wpcareers[desctext]" cols="60"><?php echo $row->l_desctext; ?></textarea>
				<br><label>Visible?</label>
				<select name="wpcareers[visible]">
				<?php 
				if($row->l_valid == 'Yes'){
					$yes="SELECTED";
				} else{
					$no="SELECTED";
				}
				?>
				<option <?php $yes?> value="1">Yes</option> 
				<option <?php $no?> value="0">No</option> 
				</select>
				<br><label>Category:</label><select name="wpcareers[parent]"> <?php wpj_list_cats(0,0,0,$row->lc_id); ?> </select>
				<P><label>&nbsp;</label>
				<input	type="submit" value="Save Job!">&nbsp;&nbsp;<input type=button value="Cancel" onclick="history.go(-1);">
				</fieldset>
				</form></div>
				<?php
			}
		} else {
			$sql="SELECT * FROM {$table_prefix}wpj_job WHERE l_valid='Yes' order by l_id";
			$result=$wpdb->get_results($sql); 
			$numrows = count($result);
			if (count($result)>0){
				echo "<fieldset><legend>".$lang[J_ACCESADMIN]."</legend>"; 
				echo $lang[J_THEREIS]." <b>$numrows</b> ".$lang[J_ADVERTISEMENTS]."<br /><br />";
				?> 
				<p>&nbsp;</p>
				<table>
				<tr>
				<th>Edit</th>
				<th><?php echo $lang['JH_TITLE']; ?></th>
				<th>Date</th>
				<th><?php echo $lang['J_DEL']; ?></th></tr> 
				<?php
				$rank = 1;
				for($x=0; $x<$numrows; $x++){
					$row=$result[$x];
					if(is_integer($rank/2)) {
						$color="style='background-color:#fafafa;'";
					} else {
						$color="style='background-color:#fff;'";
					}
					$rank++;
					$modImg = '<img src="' . JP_PLUGIN_URL .'/images/edit.png" border=0 alt="$lang[J_MODIFANN]">';
					$modifyJoblink=wpcareers_create_link("jmodify",array("name"=>$modImg, "id"=>$row->l_id));
					echo "<tr ".$color. " onMouseOver=\"this.bgColor='#FFFFFF';\" onMouseOut=\"this.bgColor='#F4F4F4';\"> ";
					echo "<td>".$modifyJoblink."</td>";
					echo "<td>".$row->l_title."</td>"; print "<td nowrap>".$row->l_date."</td>";
					echo "<td>". "<a href=\"". $linkb ."&admin_action=main&action=jdelete&id=".$row->l_id ."\"><img border=0 src=\"" .get_bloginfo('wpurl'). "/wp-content/plugins/wpcareers/images/delete.png\"</a>"; 
					?> </td></tr>
					<?php
				} //for main links
				?>
				</table>
				</fieldset></div>
				<?php
			} else {
				echo "<fieldset><legend>". $lang['J_WAIT'] . "</legend>"; 
				echo "<br /> ".$lang['J_NOANNVAL']."<br /><br />";
				echo "</fieldset></div>";
			}
		}
	}
	return $msg;
}


?>
