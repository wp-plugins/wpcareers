<?php

/**
 * File Name: jp_list_job.php
 * Package Name: wpCareers wordpress plugin
 * @author Mohammad Forgani
 * @copyright Copyright 2010, Oh Jung-Su
 * @version 1.0
 * @link http://www.forgani.com
 * Last modified:  2010-01-17
 * Comments:
*/

function wpcareers_list_jobs($message='', $lcid){
   global $_GET, $_POST, $table_prefix, $wpdb, $lang, $_FILES, $user_ID, 
		$job_mustlogin, $wpcareers;

	$permission = jp_check_permission();
   $tpl = wpcareers_display_header($message);
   $wpca_settings=get_option('wpcareers');
   if (!isset($lcid)) 
      if (isset($_GET['id'])) $lcid = $_GET['id'];

   $sql="SELECT * FROM {$table_prefix}wpj_job WHERE lc_id=".$lcid." AND l_valid='Yes'";

   $results=$wpdb->get_results($sql); 
   for ($i=0; $i<count($results); $i++){
      $result=$results[$i];
      $sql="SELECT c_title FROM {$table_prefix}wpj_categories WHERE c_id=".$lcid;
      $category=$wpdb->get_var($sql);
      $tpl->assign('category', $category);
      $title = $result->l_title;

      $photo = false;
      if (strlen($result->l_photo) > 3)
      $photo = '<div class="logo"><img src="' . $wpcareers->public_url . '/images/' . $result->l_photo . '" style="width:40px;"></a></div>';

		$viewJob=wpcareers_create_link("jview", array("name"=>$title, "id"=>$result->l_id));
		$viewJobDetail=wpcareers_create_link("jview", array("name"=> $lang['J_VIEW_ICON'] . ' View', "id"=>$result->l_id));
		$sendJobLink=wpcareers_create_link("jsend", array("name"=> $lang['J_REFER_ICON'] . " Refer it to a Friend", "id"=>$result->l_id));
      if ($permission >= 5) {
         $modifyJobLink=wpcareers_create_link("jmodify", array("name"=> $lang['J_MODIFY_ICON'] . " Modify", "id"=>$result->l_id));
         $deleteJobLink=wpcareers_create_link("jdelete", array("name"=> $lang['J_DELETE_ICON'] . " Delete", "id"=>$result->l_id));
      }

      $jobs[]=array (
         'title'=>$title,
         'lid'=>$result->l_id,
         'town'=>$result->l_town,
         'contactinfo'=>$result->l_contactinfo,
         'date'=>$result->l_date,
         'desctext'=>$result->l_desctext,
         'email'=>$result->l_email,
         'photo'=>$photo,
         'company'=>$result->l_company,
         'sendJobLink'=> $sendJobLink, 
         'modifyJobLink' => $modifyJobLink,
         'deleteJobLink' => $deleteJobLink,
         'job_mustlogin'=>$job_mustlogin,
         'viewjob'=>$viewJob,
			'viewJobDetail'=>$viewJobDetail);
   }

   if (isset($jobs)) $tpl->assign('jobs', $jobs);
   wpcareers_footer($tpl);
   return $tpl->display('list_job.tpl'); 
} //wpcareers_list_jobs



?>
