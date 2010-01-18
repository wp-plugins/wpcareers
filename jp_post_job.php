<?php

/**
 * jp_post_job.php
 * wpCareers wordpress plugin
 * @author Mohammad Forgani
 * @copyright Copyright 2010, Mohammad Forgani
 * @version 1.0
 * @link http://www.forgani.com
 */



function wpcareers_post_job($message, $mode){
	global $_GET, $_POST, $table_prefix, $wpdb, $lang, $_FILES, $user_ID, $wpcareers;
	
	$wpca_settings=get_option('wpcareers');
	if (isset($_GET['id'])) $lid=$_GET['id'];
	$displayform=true;
	$error = '';
	$tpl = wpcareers_display_header($message);

	
	$email= trim($_POST['wpcareers']['email']);
	$title = trim(strip_tags($_POST['wpcareers']['title']));
	$expire = strip_tags($_POST['wpcareers']['expire']);
	$type = $_POST['wpcareers']['type'];
	$company = trim(strip_tags($_POST['wpcareers']['company']));
	$desctext = jp_remove_weblink($_POST['wpcareers']['desctext']);
	$requirements = trim( strip_tags($_POST['wpcareers']['requirements']) );
	$tel = trim(strip_tags($_POST['wpcareers']['tel']));
	$fax = trim(strip_tags($_POST['wpcareers']['fax']));
	$category = strip_tags($_POST['wpcareers']['category']);
	$price = trim(strip_tags($_POST['wpcareers']['price']));
	$pricetype = $_POST['wpcareers']['pricetype'];
	$contactinfo = strip_tags($_POST['contactinfo']);
	$submitter = trim(strip_tags($_POST['wpcareers']['submitter']));
	$town = trim(strip_tags($_POST['wpcareers']['town']));
	$state = trim(strip_tags($_POST['wpcareers']['state']));
	$oldFileName = $_POST['wpcareers']['oldFileName'];
	

	$date = date("F j, Y");
	$ip = getenv('REMOTE_ADDR');
	if (isset($wpca_settings['must_login']) && $wpca_settings['must_login']=='y') {
		$permission = jp_check_permission();
	} else {
		$anonymous = 1;
	}
	if ( $permission >= 1 || $anonymous >= 1) {
		if (isset($_POST['wpcareers_post_topic']) && $_POST['wpcareers_post_topic']=='yes' ) {
			$makepost=true;
			if (!isset($_POST['wpcareers']['agree'])){
				$error .= "- Please accept our policy<br>";
				$makepost=false;
			}
			if (str_replace(" ", "", $type)==''){
				$error .= $lang['J_VALIDTYPE']."<br>";
				$makepost=false;
			}
			if (str_replace(" ", "", $email)==''){
				$error .= "- " . $lang['J_VALIDEMAIL'] . "<br>";
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

			if (str_replace(" ", "", $company)==''){
				$error .= "- " . $lang['J_VALIDCOMPANY'] . "<br>";
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
			if (str_replace(" ", "", $submitter)==''){
				$error .= "- ".$lang['J_VALIDSUBMITTER']."<br>";
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

			if (isset($_POST['remove_photo'])){
				$file = $wpcareers->public_dir . "/images/" . $oldFileName;
				unlink($file);
				$oldFileName = '';
			}

			if ($mode == 0) {
				$sql="SELECT * FROM {$table_prefix}wpj_job";
				$results=$wpdb->get_results($sql); 
				if (!empty($results)) {
					// check double Post by Title and Company
					foreach ($results as $result) {
						if($result->l_title == $title && $result->l_company == $company){
							$error = $lang['J_SUBMITALREADY'];
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
						$fp = fopen( $wpcareers->public_dir . "/images/" . (int)$user_ID."-".$_FILES['photo']['name'], "w" );
						@fwrite($fp, $content);
						@fclose($fp);
						@chmod(fopen(  $wpcareers->public_dir . "/images/" . (int)$user_ID."-".$_FILES['photo']['name'] ), 0777);
						$filename = (int)$user_ID."-".$_FILES['photo']['name'];
					}
				}
			}
			if ($makepost==true){
				if (isset($lid)) {
					if (strlen($filename) < 3) {
						if (isset($oldFileName) && strlen($oldFileName) > 3) $filename = $oldFileName;
					}
					$sql = "UPDATE {$table_prefix}wpj_job SET 
						l_price='$pricetype',
						l_contactinfo='$contactinfo',
						l_email='$email',
						l_date='$date',
						l_town='$town',
						l_company='$company',
						l_type='$type',
						l_desctext='$desctext',
						l_requirements='$requirements',
						l_tel='$tel',
						l_price='$price',
						l_expire='$expire',
						l_submitter='$submitter',
						l_state='$state',
						l_photo='$filename',
						l_author_ip='$ip',
						l_title='$title',
						l_fax='$fax',
						lc_id='$category' WHERE l_id=$lid";
					$wpdb->query($sql);
					$message = $lang['J_SUBMITTED'];
				} else {
					// FIXME what did you here !!!!!!!!!!!!!!
					if (!isset($expire) || $expire < 1) {
						$expire = $wpca_settings['expire'];
					} 
					if ($wpca_settings['approve'] == 'y') {
						$valid = 'No';
					} else $valid = 'Yes';
					$sql = "INSERT INTO {$table_prefix}wpj_job VALUES ('', $category, '$title',1,'$expire', '$type', '$company', '$desctext', '$requirements', '$tel','$price', '$pricetype','$contactinfo','$date', '$email','$submitter','$user_ID', '$town','$state','$valid','$filename', 0,'$ip', '$fax')";
					$wpdb->query($sql);
					$message = $lang['J_SUBMITTED4REVIEW'];
				}
				$out = jp_email_notifications($title, $desctext, $email, $lid);
				$displayform=false;
			} else {
				$displayform=true;
			}
		}
		if ($displayform==true) {
			if ( strlen($error) > 10 ) $message = $lang['J_VALIDERORMSG'] . $error;
			$space="";
			if($wpca_settings['confirmation_code']=='y') {
				$oVisualCaptcha=new _jp_captcha();
				$captcha=rand(1, 50).".png";
				$oVisualCaptcha->create($wpcareers->cache_dir ."/".$captcha);
				$confirm='<tr bgcolor="#F4F4F4"><td class="td_left">'.$lang['J_COMFIMATION'].'</td><td><img src="'.get_bloginfo('wpurl').'/wp-content/plugins/wpcareers/cache/' .$captcha.'" alt="ConfirmCode" align="middle"/>';
				$confirm .= '<br><span class ="smallTxt">'.$lang["J_VERIFICATION"].'</span><br><input type="text" name="wpcareers[jp_captcha]" id="wpcareers[jp_captcha]" size="10"></td></tr>';
				$tpl->assign('confirm',$confirm);
			}
			$sql="SELECT * FROM {$table_prefix}wpj_categories ORDER BY c_title ASC";
			$wpj_categories = $wpdb->get_results($sql);
			$results=$wpdb->get_results($sql);
			$categoryId = array();
			$categoryTitle = array();
			if ( !empty($results) ) {
				foreach ($results as $result) {
					array_push($categoryId, $result->c_id);
					array_push($categoryTitle, $result->c_title);
				}
			}
			$photomax= "maximum $wpca_settings[image_width] x $wpca_settings[image_height] pixel.";
			$tpl->assign('photomax', $photomax);
			$expiredefault= "max.".$wpca_settings['expire'];
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
			$priceId = array();
			$priceTitle = array();
			if (!empty($results)) {
				foreach ($results as $result) {
					array_push($priceId, $result->p_id);
					array_push($priceTitle, $result->p_nom);
				}
			}
			$tpl->assign('priceId', $priceId);
			$tpl->assign('priceTitle', $priceTitle);
			// someone try to modify the post
			if (isset($lid) && $lid > 0) { 
				$sql="SELECT * FROM {$table_prefix}wpj_job WHERE l_id='".$lid."'";
				$results=$wpdb->get_results($sql);
				$result=$results[0];
				$title=$result->l_title;
				$tpl->assign('title',$title);
				if (strlen($result->l_photo) > 3)
					$photo = '<div class="logo"><img src="' . $wpcareers->public_url . '/images/' . $result->l_photo . '" style="width:40px;"></a></div>';
				$tpl->assign('lid',$result->l_id);
				$tpl->assign('categorySelected', $result->lc_id);
				$tpl->assign('typeSelected', $result->l_type);
				$tpl->assign('town',$result->l_town);
				$tpl->assign('state',$result->l_state);
				$tpl->assign('contactinfo',$result->l_contactinfo);
				$tpl->assign('price',$result->l_price);
				$tpl->assign('expire',$result->l_expire);
				$tpl->assign('priceSelected', $result->l_type);
				$tpl->assign('desctext',$result->l_desctext);
				$tpl->assign('requirements',$result->l_requirements);
				$tpl->assign('price',$result->l_price);
				$tpl->assign('tel',$result->l_tel);
				$tpl->assign('date',$result->l_date);
				$tpl->assign('submitter',$result->l_submitter);
				$tpl->assign('email',$result->l_email);
				$tpl->assign('photo',$result->l_photo);
				$tpl->assign('oldFileName',$result->l_photo);
				$tpl->assign('_photo',$photo);
				$tpl->assign('company',$result->l_company);
				$tpl->assign('fax',$result->l_fax);
			} else {
				$tpl->assign('town',$town);
				$tpl->assign('title',$title);
				$tpl->assign('state',$state);
				$tpl->assign('contactinfo',$contactinfo);
				$tpl->assign('submitter',$submitter);
				$tpl->assign('price',$price);
				$tpl->assign('expire',$expire);
				$tpl->assign('priceSelected', $type);
				$tpl->assign('desctext',$desctext);
				$tpl->assign('requirements',$requirements);
				$tpl->assign('price',$price);
				$tpl->assign('tel',$tel);
				$tpl->assign('date',$date);
				$tpl->assign('email',$email);
				$tpl->assign('_photo',$photo);
				$tpl->assign('company',$company);
				$tpl->assign('fax',$fax);
			}
			$tpl->assign('message',$message);
			wpcareers_footer($tpl);
			$tpl->display('post_job.tpl');
		} else {
			if ($message) $message = '<span class="green">' .$message. "</span>";
			$_GET=array();
			wpcareers_display_index($message);
		}
	} else {
		wpcareers_display_index($lang['J_MUSTLOGIN']);
	}
}
//wpcareers_post_job


/**
 *  wpcareers_send_job() sends a job.
 */
function wpcareers_send_job($message){
	global $_GET, $_POST, $table_prefix, $wpdb, $lang, $_FILES, $wpcareers;
	$wpca_settings=get_option('wpcareers');
	$id=$_GET['id'];
	$displayform=true;
	$sql="SELECT * FROM {$table_prefix}wpj_job WHERE l_id=".$id;
	$results=$wpdb->get_results($sql); 
	if (!empty($results)) {
		foreach ($results as $result) {
			$desctext=$result->l_desctext;
			$submitter=$result->l_submitter;
			$mailfrom=$result->l_email;
			$title=$result->l_title;
		}
	}
	$displayform=true;
	if ($_POST['jp_send_job']=='yes'){
		$sendAd=true;
		$yourname=$_POST['wpcareers'][yourname];
		$mailfrom=$_POST['wpcareers'][mailfrom];
		$mailto=$_POST['wpcareers'][mailto];
		$maildesc=$_POST['wpcareers'][maildesc];
		$fname=$_POST['wpcareers'][fname];

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
		if ($sendAd == true) {
			$displayform=false;
			$subject = "".$lang[J_SUBJET]." ".get_bloginfo('wpurl')."";
			$message .= "".$lang[J_HELLO]." $fname,\n\n$yourname ".$lang[J_MESSAGE]."\n\n";
			$message .= "$title : $type\n$desctext\n\n";
			if ($price == 1) {
				$message .= "".$lang[J_PRICE]." ". $price . $typeprice . "\n";
			}
			if ($tel) {
				$message .= "".$lang[J_TEL]." $tel\n";
			}
			if ($town) {
				$message .= "".$lang[J_TOWN]." $town\n";
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
	if ($displayform==true) {
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
		$tpl->assign('message', $message);
		wpcareers_footer($tpl);
		$tpl->display('send.tpl');
	} else {
		if ($message) $message = '<font color="green">' .$message. "</font>";
		$_GET=array();
		wpcareers_display_index($message);
	}
}

function wpcareers_delete_job() {
	global $_GET, $_POST, $table_prefix, $wpdb, $lang, $_FILES;

	$wpca_settings=get_option('wpcareers');
	$id=(int)$_GET['id'];
	$sql="SELECT lc_id, l_title FROM {$table_prefix}wpj_job WHERE l_id=" . $id;
	$results=$wpdb->get_results($sql);
	foreach ($results as $result) {
		$lc_id = $result->lc_id;
		$title = $result->l_title;
	}

	$permission = jp_check_permission();
		$tpl = wpcareers_display_header();
		if ($permission < 5) {
			$tpl->assign('job_mustlogin',$lang['J_PERMISSION']);
		}
		if ($_POST['YesOrNo'] > 0){
			$sql = "DELETE FROM {$table_prefix}wpj_job WHERE l_id =" . $id;
			$wpdb->query($sql);
			$message = '<h3><span class="green">' . $lang['J_JOBDEL'] . '</span></h3>';
			$_GET=array();
			wpcareers_list_jobs($message, $lc_id);
		} else {
			$deleteJobLinkForm=wpcareers_create_link("jdeleteform", array("id"=>$id));
			$message = '<h3><span class="red">'.$lang['J_CONFDEL'].'</span></h3>';
			$message .= '<b>' . $lang['J_TITLE'] . '</b>&nbsp;&nbsp;'. $title ;
			$message .= '<p><form method="post" id="delete_conform" name="delete_conform" action="'.$deleteJobLinkForm.'">';
			$message .= '<input type="hidden" name="YesOrNo" value="'.$id.'"><b>' . $lang['J_SURDELANN'] . '</b><br />';
			$message .= '<input type=submit value="'.$lang['J_YES'].'"> <input type=button value="'.$lang['J_NO'].'" onclick="history.go(-1);">';
			$message .= '</form></p>';
			wpcareers_list_jobs($message, $lc_id);
		}
}

?>
