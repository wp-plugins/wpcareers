<?php

/**
 * File Name: jp_post_resume.php
 * Package Name: wpCareers wordpress plugin
 * @author Mohammad Forgani
 * @copyright Copyright 2010, Mohammad Forghanian
 * @version 1.0
 * @link http://www.forgani.com
 * Last modified:  2010-01-17
 * Comments:
 *
*/

function wpcareers_post_resume($message, $mode){
	global $_GET, $_POST, $table_prefix, $wpdb, $lang, $_FILES, $user_ID, $wpcareers;

	$wpca_settings=get_option('wpcareers');
	if (isset($_GET['id'])) $rid=$_GET['id'];
	$displayform=true;
	$error = '';
	$expire=$wpca_settings['expire'];
	$tpl = wpcareers_display_header($message);

	$private = (isset($_POST['wpcareers']['private'])? 1 : 0);
	if (isset($_POST['wpcareers']['title'])) {
		$email = $_POST['wpcareers']['email'];
		$title = strip_tags($_POST['wpcareers']['title']);
		$startDate = strip_tags($_POST['wpcareers']['startDate']);
		$expire = strip_tags($_POST['wpcareers']['expire']);
		$typesalary = $_POST['wpcareers']['typesalary'];
		$name = strip_tags($_POST['wpcareers']['name']);
		$desctext = jp_remove_weblink($_POST['wpcareers']['desctext']);
		$tel = trim(strip_tags($_POST['wpcareers']['tel']));
		$fax = trim(strip_tags($_POST['wpcareers']['fax']));
		$category = $_POST['wpcareers']['category'];
		$salary = strip_tags($_POST['wpcareers']['salary']);
		$information = strip_tags($_POST['contactinfo']);
		//$submitter = $_POST['wpcareers']['submitter'];
		$town = strip_tags($_POST['wpcareers']['town']);
		$oldFileName = $_POST['wpcareers']['oldFileName'];
		$oldUploadName = $_POST['wpcareers']['oldUploadName'];
		$state = $_POST['wpcareers']['state'];
	}
	$date = date("F j, Y");
	$ip = getenv('REMOTE_ADDR');

	$anonymous = 0;
	if ($wpca_settings['must_login']=='y') {
		$permission = jp_check_permission();
	} else {
		$anonymous = 1;
	}
	if ( $permission >= 1 || $anonymous >= 1) {
		if (isset($_POST['wpcareers_post_topic']) && $_POST['wpcareers_post_topic']=='yes') {
			$makepost=true;
			if (!isset($_POST['wpcareers']['agree'])){
				$error .= "- Please accept our policy<br>";
				$makepost=false;
			}
			if (str_replace(" ", "", $typesalary)==''){
				$error .= "- ".$lang['J_VALIDTYPE']."<br>";
				$makepost=false;
			}
			if (str_replace(" ", "", $email)==''){
				$error .= "- ".$lang['J_INVALIDEMAIL']."<br>";
				$makepost=false;
			}

			if (isset($email) && !is_email($email)) {
				$error .= "- " . $lang['J_INVALIDEMAIL'] . "<br>";
				$makepost=false;
			}

			if (strlen($tel) > 0 && !jp_is_valid_phone($tel)) {
				$error .= "- " . $lang['J_INVALIDPHONE'] . "<br>";
				$makepost=false;
			}

			if (strlen($fax) > 0 && !jp_is_valid_phone($fax)) {
				$error .= "- " . $lang['J_INVALIDFAX'] . "<br>";
				$makepost=false;
			}

			if ( isset($expire) && jp_is_valid_number($expire, $wpca_settings['expire'])) {
				$error .= "- " . $lang['J_INVALIDEXPIRE'] . " " .  jp_is_valid_number($expire, $wpca_settings['expire']) . "<br>";
				$makepost=false;
			}

			if (str_replace(" ", "", $name)==''){
				$error .= "- ".$lang['J_VALIDNAME']."<br>";
				$makepost=false;
			} 

			if (str_replace(" ", "", $title)==''){
				$error .= "- ".$lang['J_VALIDTITLE']."<br>";
				$makepost=false;
			}
			if ($category < 1){
				$error .= "- ".$lang['J_VALIDCAT']."<br>";
				$makepost=false;
			}

			if (isset($_POST['remove_photo'])){
				$file = $wpcareers->public_dir . '/images/' . $oldFileName;
				unlink($file);
				$oldFileName = '';
			}

			if (isset($_POST['remove_upload'])){
				$file = $wpcareers->public_dir . '/resume/'. $oldUploadName;
				unlink($file);
				$oldUploadName = '';
			}
			
			if (str_replace(" ", "", $information)==''){
				$error .= "- ".$lang['J_VALIDINFORMATION']."<br>";
				$makepost=false;
			}
			
			if (str_replace(" ", "", $town)==''){
				$error .= "- ". $lang['J_VALIDTOWN']."<br>";
				$makepost=false;
			}

			if($wpca_settings['confirmation_code']=='y'){ 
				if (! _jp_captcha::Validate($_POST['wpcareers']['jp_captcha'])) {
					$error .= "- " . $lang['J_VALIDCOMFIMATION'] . "<br>";
					$makepost=false;
				}
			}
			if ($mode == 0) {
				$sql="SELECT * FROM {$table_prefix}wpj_resume";
				$results=$wpdb->get_results($sql);
				if (!empty($results)) {
					// check double Post by Title and Company
					foreach ($results as $result) {
						if($result->r_title == $title && $result->r_name == $name){
						$error = $lang['R_SUBMITALREADY'];
						$makepost=false;
						}
					}
				}
			}
			if (strlen($_FILES['photo']['name']) > 3) {
				$ok = (substr($_FILES['photo']['type'], 0, 5)=="image")?true:false;
				if ($ok==true){
					// Fetch the image size and mime type
					list($width, $height, $type, $attr)=getimagesize($_FILES['photo']['tmp_name']); 
					// Make sure that the image is readable and valid
					if ($height > $wpca_settings['image_height'] ||  $width > $wpca_settings['image_width']){
						$error .= "<b>" . $lang['J_INVALIDIMG'] . "</b> (".(int)$wpca_settings["image_width"]."x".(int)$wpca_settings["image_height"] . " pixel)"; 
						$makepost=false;
					} else {
						$fp = @fopen($_FILES['photo']['tmp_name'], "r");
						$content = @fread($fp, $_FILES['photo']['size']);
						@fclose($fp);
						$fp = fopen( $wpcareers->public_dir . '/images/' . (int)$user_ID."-".$_FILES['photo']['name'], "w");
						@fwrite($fp, $content);
						@fclose($fp);
						@chmod(fopen( $wpcareers->public_dir . '/images/' . (int)$user_ID."-".$_FILES['photo']['name']), 0777);
						$filename = (int)$user_ID."-".$_FILES['photo']['name'];
					}
				}
			}

			if (strlen($_FILES['upload']['name']) > 3){
				if ($_FILES["upload"]["error"] > 0 ) {
					$error .= "<b>Error: " . $_FILES["upload"]["error"] . "</b><br />";
					$makepost=false;
				} else {
					$size = $_FILES["upload"]["size"] / 1024;
					if ( $size > $wpca_settings['file_max_upl'] ){
						$error .= "<b>" . $lang['J_INVALIDIMG'] . "</b> (".(int)$wpca_settings["file_max_upl"] . " Kbyte)"; 
						$makepost=false;
					} else {
						$fp = @fopen($_FILES['upload']['tmp_name'], "r");
						$content = @fread($fp, $_FILES['upload']['size']);
						@fclose($fp);
						$fp = fopen( $wpcareers->public_dir . "/resume/".(int)$user_ID."-".$_FILES['upload']['name'], "w");
						@fwrite($fp, $content);
						@fclose($fp);
						@chmod(  $wpcareers->public_dir . "/resume/".(int)$user_ID."-".$_FILES['upload']['name'], 0777 );
						$uploadfile = (int)$user_ID."-".$_FILES['upload']['name'];
					}
				}
			}
			if ($makepost==true){
				if (isset($rid)) {
					if (strlen($filename) < 3) {
						if ( isset($oldFileName) && strlen($oldFileName) > 3 ) $filename = $oldFileName;
					}
					if (strlen($uploadfile) < 3) {
						if ( isset($oldUploadName) && strlen($oldUploadName) > 3 ) $uploadfile = $oldUploadName;
					}

					$sql = "UPDATE {$table_prefix}wpj_resume SET 
						r_name='$name',
						r_title='$title',
						r_startDate='$startDate',
						r_desctext='$desctext',
						r_expire='$expire',
						r_tel='$tel',
						r_fax='$fax',
						r_salary='$salary',
						r_typesalary ='$typesalary',
						r_date='$date',
						r_email='$email',
						r_submitter='$submitter',
						r_contactinfo='$information',
						r_town='$town',
						r_state='$state',
						r_photo='$filename',
						r_resume='$uploadfile',
						r_private=$private,
						r_title='$title',
						r_author_ip='$ip',
						rc_id='$category' WHERE r_id=$rid";

					$wpdb->query($sql);
					$message = $lang['R_SUBMITTED'];
				} else {
					if (!isset($expire) || $expire < 3) {
						$expire = $wpca_settings['expire'];
					}
					if ($wpca_settings['approve'] == 'y') {
						$valid = 'No';
					} else $valid = 'Yes';
					$sql= "INSERT INTO {$table_prefix}wpj_resume (
						r_id ,rc_id,r_name,r_title,
						r_status,r_exp,r_expire,r_private,
						r_tel,r_salary,r_typesalary,r_date,
						r_email,r_submitter,r_desctext,r_usid,
						r_town,r_state,r_valid,
						r_photo,r_resume,r_view,
						r_author_ip,r_startDate,r_fax,r_contactinfo
						) VALUES ( NULL , $category, '$name', '$title',
							'1', 1, '$expire', $private,
							'$tel', '$salary', '$typesalary', '$date',
							'$email', '$submitter', '$desctext', '$user_ID',
							'$town', '$state', '$valid',
							'$filename', '$uploadfile', 0,
							'$ip', '$startDate', '$fax', '$information')";
					$wpdb->query($sql);
					$message = $lang['R_SUBMITTED4REVIEW'];
				}
				$out = jp_email_notifications($title, $desctext, $email, $rid);
				$displayform=false;
			} else {
				$displayform=true;
			}
		}

		if ($displayform==true){
			// TODO J_VALIDERORMSG
			if ( strlen($error) > 10 ) $message =  $lang['J_VALIDERORMSG'] . $error ;
			$space="";
			if($wpca_settings['confirmation_code']=='y'){ 
				$oVisualCaptcha=new _jp_captcha();
				$captcha=rand(1, 50).".png";
				$oVisualCaptcha->create($wpcareers->cache_dir ."/".$captcha);
				$confirm='<tr bgcolor="#F4F4F4"><td class="td_left">'.$lang['J_COMFIMATION'].'</td><td><img src="'.get_bloginfo('wpurl').'/wp-content/plugins/wpcareers/cache/' .$captcha.'" alt="ConfirmCode" align="middle"/>';
				$confirm .= '<br><span class ="smallTxt">'.$lang["J_VERIFICATION"].'</span><br><input type="text" name="wpcareers[jp_captcha]" id="wpcareers[jp_captcha]" size="10"></td></tr>';
				$tpl->assign('confirm',$confirm);
			}
			// TODO $lev

			$sql="SELECT * FROM {$table_prefix}wpj_res_categories ORDER BY rc_title ASC";
			$wpj_res_categories = $wpdb->get_results($sql);
			$results=$wpdb->get_results($sql); 
			$categoryId = array();
			$categoryTitle = array();
			if (!empty($results)) {
				foreach ($results as $result) {
					array_push($categoryId, $result->rc_id);
					array_push($categoryTitle, $result->rc_title);
				}
			}

			$photomax= "maximum $wpca_settings[image_width] x $wpca_settings[image_height] pixel.";
			$tpl->assign('photomax', $photomax);
			$uploadmax= "maximum " . $wpca_settings['file_max_upl'] . " Kbyte.";
			$tpl->assign('uploadmax', $uploadmax);

			$expiredefault= "max. " .	$wpca_settings['expire'];
			$tpl->assign('expiredefault', $expiredefault);
			$tpl->assign('categoryId', $categoryId);
			$tpl->assign('categoryTitle', $categoryTitle);
			$results = $wpdb->get_results("SELECT * FROM {$table_prefix}wpj_type ORDER BY t_nom ASC");
			$typeId = array();
			$typeTitle = array();
			if (!empty($results)) {
				foreach ($results as $result) {
					array_push($typeId, $result->t_id);
					array_push($typeTitle, $result->t_nom);
				}
			}

			$tpl->assign('typeId', $typeId);
			$tpl->assign('typeTitle', $typeTitle);
			$results = $wpdb->get_results("SELECT * FROM {$table_prefix}wpj_price ORDER BY p_nom ASC");
			$salaryId = array();
			$salaryTitle = array();
			if (!empty($results)) {
				foreach ($results as $result) {
					array_push($salaryId, $result->p_id);
					array_push($salaryTitle, $result->p_nom);
				}
			}
			$tpl->assign('salaryId', $salaryId);
			$tpl->assign('salaryTitle', $salaryTitle);
			// modify	
			if (isset($rid) && $rid > 0) {
				$sql="SELECT * FROM {$table_prefix}wpj_resume WHERE r_id='".$rid."'";
				$results=$wpdb->get_results($sql);
				$result=$results[0];
				$title=$result->r_title;
				$tpl->assign('title',$title);
				if (strlen($result->r_photo) > 3)
					$_photo = '&nbsp;&nbsp;<div class="logo"><img src="' . $wpcareers->public_url . '/images/' . $result->r_photo . '" style="width:40px;" /></div>';

				if (strlen($result->r_resume) > 3)
					$_upload = '&nbsp;&nbsp;<a target="_blank" href="' . $wpcareers->public_url . '/resume/' . $result->r_resume . '" return false;"><div class="logo"><img src="' . get_bloginfo('wpurl') . '/wp-content/plugins/wpcareers/images/doc.jpg" style="width:40px;" /></div></a></div>';

				if ($result->r_resume > 0) $private = 1;
				$startDate=$result->r_startDate;
				$tpl->assign('startDate',$startDate);
				$tpl->assign('rid',$result->r_id);
				$tpl->assign('categorySelected', $result->rc_id);
				//$tpl->assign('typeSelected', $result->r_type);
				$tpl->assign('town',$result->r_town);
				$tpl->assign('state',$result->r_state);
				$tpl->assign('salary',$result->r_salary);
				$tpl->assign('expire',$result->r_expire);
				$tpl->assign('salarySelected', $result->r_typesalary);
				$tpl->assign('desctext',$result->r_desctext);
				$tpl->assign('tel',$result->r_tel);
				$tpl->assign('date',$result->r_date);
				$tpl->assign('email',$result->r_email);
				$tpl->assign('photo',$result->r_photo);
				$tpl->assign('_photo',$_photo);
				$tpl->assign('oldFileName',$result->r_photo);
				$tpl->assign('resume',$result->r_resume);
				$tpl->assign('_upload',$_upload);
				$tpl->assign('oldUploadName',$result->r_resume);
				$tpl->assign('name',$result->r_name);
				$tpl->assign('fax',$result->r_fax);
				$tpl->assign('information',$result->r_contactinfo);
				$tpl->assign('private',$private);
			}

			$tpl->assign('message',$message);
			wpcareers_footer($tpl);
			$tpl->display('post_resume.tpl');
		} else {
			if ($message) $message = '<span class="green">' .$message. "</span>";
			$_GET=array();
			wpcareers_display_index($message);
		}
	} else {
		wpcareers_display_index($lang['J_MUSTLOGIN']);
	}
} //wpcareers_post_resume




function wpcareers_send_resume($message){
	global $_GET, $_POST, $table_prefix, $wpdb, $lang, $_FILES, $wpcareers;

	$wpca_settings=get_option('wpcareers');
	$id=$_GET['id'];
	$displayform=true;

	$sql="SELECT * FROM {$table_prefix}wpj_resume WHERE r_id=".$id;
	$results=$wpdb->get_results($sql); 
	if (!empty($results)) {
		foreach ($results as $result) {
			$desctext=$result->r_desctext;
			$submitter=$result->r_submitter;
			$mailfrom=$result->r_email;
			$title=$result->r_title;
		}
	}

	$displayform=true;
	if ($_POST['jp_send_resume']=='yes'){
		$sendAd=true;
		$yourname=$_POST['wpcareers'][yourname];
		$mailfrom=$_POST['wpcareers'][mailfrom];
		$mailto=$_POST['wpcareers'][mailto];
		$maildesc=$_POST['wpcareers'][maildesc];



		if (str_replace(" ", "", $mailto)=='' || str_replace(" ", "", $mailfrom)==''){
			$message .= "- " . $lang['J_VALIDEMAIL'] . "<br>";
			$sendAd=false;
		}
		If (!is_email($mailto) && !is_email($mailfrom)) {
			$message .= "- " . $lang['J_INVALIDEMAIL'] . "<br>";
			$sendAd=false;
		}

		if (str_replace(" ", "", $fname)==''  || str_replace(" ", "", $yourname)==''){
			$message .= "- ".$lang['J_VALIDNAME']."<br>";
			$sendAd=false;
		}

		if (str_replace(" ", "", $maildesc)==''){
			$message .= "- ".$lang['J_VALIDDESCRIPTION']."<br>";
			$sendAd=false;
		}
	
		if($wpca_settings['confirmation_code']=='y'){ 
			if (! _jp_captcha::Validate($_POST['wpcareers']['jp_captcha'])) {
				$message .= "- " . $lang['J_VALIDCOMFIMATION'] . "<br>";
				$sendAd=false;
			}
		}

		if (!eregi("^[a-z0-9]+([-_\.]?[a-z0-9])+@[a-z0-9]+([-_\.]?[a-z0-9])+\.[a-z]{2,4}$", $_POST['wpcareers'][mailto])){
			$message= $lang['J_INVALIDEMAIL'];
			$sendAd=false;
		}
		if($wpca_settings['confirmation_code']=='y'){ 
			if (! _jp_captcha::Validate($_POST['wpcareers']['jp_captcha'])) {
				$message = "The confirmation code didn't matched.";
				$sendAd=false;
			}
		}
		if ($sendAd == true) {
			$displayform=false;
			$subject = "".$lang['J_SUBJET']." ".get_bloginfo('wpurl')."";
			$message .= "".$lang['J_HELLO']." $fname,\n\n$yourname ".$lang[J_MESSAGE]."\n\n";
			$message .= "$title :	$typesalary\n$desctext\n\n";
			if ($salary == 1) {
				$message .= "".$lang['J_PRICE']." ". $salary . $typesalary . "\n";
			}
			if ($tel) {
				$message .= "".$lang['J_TEL']." $tel\n";
			}
			if ($fax) {
				$message .= "".$lang['J_FAX']." $fax\n";
			}
			if ($town) {
				$message .= "".$lang['J_TOWN']." $town\n";
			}
			$message = $message . '<P>'. $maildesc;
			$email_status=jp_send_email($mailto, $subject, $message, $mailfrom); 
			if ($email_status[0] == false) {
				$message=$email_status[1];
				$sendAd=false;
			} else {
				wpcareers_display_index($email_status[1]);
			}
		}
	} else {
	 $displayform=true;
	}
	if ($displayform==true){
		$tpl = wpcareers_display_header($message);
		if($wpca_settings['confirmation_code']=='y'){ 
			$oVisualCaptcha=new _jp_captcha();
			$captcha=rand(1, 50).".png";
			$oVisualCaptcha->create($wpcareers->cache_dir ."/".$captcha);
			$confirm='<tr><td class="td_left">'.$lang['J_COMFIMATION'].'</td><td><img src="'.get_bloginfo('wpurl').'/wp-content/plugins/wpcareers/cache/' .$captcha.'" alt="ConfirmCode" align="middle"/>';
			$confirm .= '<br><span class ="smallTxt">'.$lang["J_VERIFICATION"].'</span><br><input type="text" name="wpcareers[jp_captcha]" id="wpcareers[jp_captcha]" size="10"></td></tr>';
			$tpl->assign('confirm',$confirm);
		}
		$tpl->assign('desctext',$desctext);
		$tpl->assign('yourname',$submitter);
		$tpl->assign('title',$title);
		$tpl->assign('mailfrom',$mailfrom);
		$tpl->assign('mailto',$mailto);
		$tpl->assign('fname',$fname);

		if ( strlen($message) > 10 ) $message = $lang['J_VALIDERORMSG'] . $message;
		$tpl->assign('message',$message);
		wpcareers_footer($tpl);
		$tpl->display('send.tpl');
	} else {
		if ($message) $message = '<font color="green">' .$message. "</font>";
		$_GET=array();
		wpcareers_display_index($message);
	}
}


function wpcareers_delete_resume() {
	global $_GET, $_POST, $table_prefix, $wpdb, $lang, $_FILES;

	$wpca_settings=get_option('wpcareers');
	$id=(int)$_GET['id'];
	$displayform=true;

	$sql="SELECT * FROM {$table_prefix}wpj_resume WHERE r_id=".$id;
	$results=$wpdb->get_results($sql);
	$links=array();

	$permission = jp_check_permission();
	if ($displayform==true){
		$tpl = wpcareers_display_header($message);
		if ($permission < 5) {
			$tpl->assign('job_mustlogin',$lang['J_PERMISSION']);
		}
		if (!$_GET['id']) $_GET['id']=$_POST['YesOrNo'];
		if ($_POST['YesOrNo']>0){
			$sql = "DELETE FROM {$table_prefix}wpj_resume WHERE r_id = '".((int)$_GET['id'])."'";
			$wpdb->query($sql);
			$message = $lang['J_JOBDEL'];
			$_GET=array();
			wpcareers_list_resumes($message);
			return true;
		} else {
			$deleteResumeLinkForm=wpcareers_create_link("jdeleteform", array("id"=>$id));
			$message .= '<h3>'.$lang['J_CONFDEL'].'</h3>';
			$message .= '<form method="post" id="delete_conform" name="delete_conform" action="'.$deleteResumeLinkForm.'"><strong><br />';
			$message .= '<input type="hidden" name="YesOrNo" value="'.$_GET['id'].'">';
			$message .= $lang['J_SURDELANN'] . '<br />';
			$message .= '<input type=submit value="'.$lang['J_YES'].'"> <input type=button value="'.$lang['J_NO'].'" onclick="history.go(-1);">';
			$message .= '</strong></form>';
			wpcareers_list_resumes($message);
			return false;
		}
	}
}

?>
