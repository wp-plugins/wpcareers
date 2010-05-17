<?php

/**
 * File Name: jp_view_job.php
 * Package Name: wpCareers wordpress plugin
 * @author Mohammad Forgani
 * @version 1.0
 * @link http://www.forgani.com
 * Last modified:  2010-01-17
 * Comments:
 *
 */

//$mydir = basename( dirname( __FILE__ ) ) ;
function wpcareers_view_job() {
   global $_GET, $_POST, $table_prefix, $lang, $wpdb, $_FILES, $postinfo, $user_ID,
		$job_mustlogin, $wpcareers;

   $tpl = wpcareers_display_header();
   $wpca_settings=get_option('wpcareers');
   $lid=$_GET['id'];

   
   $sql="SELECT * FROM {$table_prefix}wpj_job WHERE l_id='".$lid."'";
   $results=$wpdb->get_results($sql);
   $links=array();

   $permission = jp_check_permission();
   for ($i=0; $i<count($results); $i++){
      $result=$results[$i];
      $sql="SELECT c_title FROM {$table_prefix}wpj_categories WHERE c_id='".$result->lc_id."'";
      $category=$wpdb->get_var($sql);
      $linkMain=wpcareers_create_link("jlist", array("name"=>$category, "id"=>$result->lc_id));
      $tpl->assign('category', $linkMain);
      $title=$result->l_title;

      $main_link=wpcareers_create_link("index", array("name"=>$lang['J_MAIN']));
      $category_link=wpcareers_create_link("jlist", array("name"=>$category, "id"=>$result->lc_id));
      $tpl->assign('top', $category_link); 

      $sendJobLink=wpcareers_create_link("jsend", array("name"=> $lang['J_REFER_ICON'] . " Refer it to a Friend", "id"=>$result->l_id));
      if ($permission >= 5) {
         $modifyJobLink=wpcareers_create_link("jmodify", array("name"=> $lang['J_MODIFY_ICON'] . " Modify", "id"=>$result->l_id));
         $deleteJobLink=wpcareers_create_link("jdelete", array("name"=> $lang['J_DELETE_ICON'] . " Delete", "id"=>$result->l_id));
      }

      $ttype = $wpdb->get_var("SELECT t_nom FROM {$table_prefix}wpj_type WHERE t_id=".$result->l_type);
      $ptype = $wpdb->get_var("SELECT p_nom FROM {$table_prefix}wpj_price WHERE p_id=".$result->l_typeprice);
      $photo = false;
      if (strlen($result->l_photo) > 3)
         $photo = '<div class="logo"><img src="' . $wpcareers->public_url . 'images/' . $result->l_photo . '"></a></div>';
      $job[]=array (
         'title'=>$title,
         'lid'=>$result->l_id,
         'type'=>$ttype,
         'town'=>$result->l_town,
         'state'=>$result->l_state,
         'contactinfo'=>$result->l_contactinfo,
         'price'=>$result->l_price,
         'typeprice'=>$ptype,
         'description'=>$result->l_desctext,
         'requirements'=>$result->l_requirements,
         'price'=>$result->l_price,
         'tel'=>$result->l_tel,
         'date'=>$result->l_date,
         'submitter'=>$result->l_submitter,
         'email'=>$result->l_email,
         'photo'=>$photo,
		   'fax'=>$result->l_fax,
         'company'=>$result->l_company,
         'view'=>$result->l_view . '&nbsp;' . $lang['J_VIEW'],
         'sendJobLink'=> $sendJobLink, 
         'modifyJobLink' => $modifyJobLink,
         'deleteJobLink' => $deleteJobLink,
         'job_mustlogin'=>$job_mustlogin,
         'viewcategory'=>$linkMain);
   }

   $view=$result->l_view+1;
 
   $wpdb->query("UPDATE {$table_prefix}wpj_job SET l_view='".$view."' WHERE l_id='".$lid."'");

    $tpl->assign('job', $job);

   /*
   if (isset($wpca_settings['wpcareers_show_credits']) &&
      $wpca_settings['wpcareers_show_credits'] == 'y') {
      $credit='Open Directory Links Powered By <a href="http://www.forgani.com/" target="_blank">4gani</a> Version ' . $wpca_version;
      $tpl->assign('jp_credit_line', $credit);
      $tpl->assign('jp_slug_url', plugins_url('wpcareers'));
   }

   list($gAd, $gtop, $gbtn)=get_jp_GADlink();

   if ($gAd) {
      $code='<div class="jp_googleAd">' . $gAd . '</div>';
      $tpl->assign('googletop',$gtop); 
      $tpl->assign('googlebtn',$gbtn); 
      $tpl->assign('googleAd',$code); 
   }
   */

   wpcareers_footer($tpl);
   return $tpl->display('view_job.tpl'); 
} //wpcareers_view_job



?>
