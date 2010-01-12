<?php

/**
 * jp_list_resume.php
 * Package Name: Wordpress plugin wpCareers 
 * @author Mohammad Forgani
 * @copyright Copyright 2010, Oh Jung-Su
 * @version 1.0
 * @link http://www.forgani.com
*/

function wpcareers_list_resumes($message=''){
   global $_GET, $_POST, $table_prefix, $wpdb, $lang, $_FILES, $user_ID, $job_mustlogin;;

   $tpl = wpcareers_display_header($message);
	$permission = jp_check_permission();
   $wpca_settings=get_option('wpcareers');
   $rcid=$_GET['id'];

   $sql="SELECT * FROM {$table_prefix}wpj_resume WHERE rc_id=".$rcid." AND r_valid='Yes'";
   $results=$wpdb->get_results($sql); 
   for ($i=0; $i<count($results); $i++){
      $result=$results[$i];
      $sql="SELECT rc_title FROM {$table_prefix}wpj_res_categories WHERE rc_id=".$rcid;
      $category=$wpdb->get_var($sql);
      $tpl->assign('category', $category);
      $title = $result->r_title;
      //$sendResumeLink=wpcareers_create_link("rsend", array("name"=>"<img src='".JP_PLUGIN_URL."/images/post/refer.gif' border=0 /> Refer it to a Friend", "id"=>$result->r_id));
      
      $photo = false;
      if (strlen($result->r_photo) > 3)
         $photo = '<div class="logo"><img src="' .get_bloginfo('wpurl').'/wp-content/plugins/wpcareers/public/' . $result->r_photo . '" style="width:40px;" /></a></div>';

		$viewResume=wpcareers_create_link("rview", array("name"=>$title, "id"=>$result->r_id));
		$viewResumeDetail=wpcareers_create_link("rview", array("name"=> $lang['J_VIEW_ICON'] . ' View', "id"=>$result->r_id));
		$sendResumeLink=wpcareers_create_link("rsend", array("name"=> $lang['J_REFER_ICON'] . " Refer it to a Friend", "id"=>$result->r_id));
      if ($permission >= 5) {
         $modifyResumeLink=wpcareers_create_link("rmodify", array("name"=> $lang['J_MODIFY_ICON'] . " Modify", "id"=>$result->r_id));
         $deletResumeLink=wpcareers_create_link("rdelete", array("name"=> $lang['J_DELETE_ICON'] . " Delete", "id"=>$result->r_id));
      }

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
		   'sendResumeLink'=> $sendResumeLink, 
			'deletResumeLink' => $deletResumeLink,
         'modifyResumeLink' => $modifyResumeLink,
			'viewResumeDetail'=>$viewResumeDetail,
         'viewResume'=>$viewResume);
   }

    $tpl->assign('resumes', $resumes);
   wpcareers_footer($tpl);
   return $tpl->display('list_resume.tpl'); 
} //wpcareers_list_resumes



?>
