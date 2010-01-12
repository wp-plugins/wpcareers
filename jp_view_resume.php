<?php

/**
 * File Name: jp_view_resume.php
 * Package Name: wpCareers wordpress plugin
 * @author Mohammad Forgani
 * @version 1.0
 * @link http://www.forgani.com
 * Last modified:  2010-01-17
 * Comments:
 *
*/

//$mydir = basename( dirname( __FILE__ ) ) ;

function wpcareers_view_resume(){
   global $_GET, $_POST, $table_prefix, $lang, $wpdb, $_FILES, $postinfo, $user_ID, $job_mustlogin;

   $tpl = wpcareers_display_header();
   $wpca_settings=get_option('wpcareers');
   $rid=$_GET['id'];


   $sql="SELECT * FROM {$table_prefix}wpj_resume WHERE r_id='".$rid."'";
   $results=$wpdb->get_results($sql);
   $links=array();

   $permission = jp_check_permission();
   for ($i=0; $i<count($results); $i++){
      $result=$results[$i];
      $sql="SELECT rc_title FROM {$table_prefix}wpj_res_categories WHERE rc_id='".$result->rc_id."'";
      $category=$wpdb->get_var($sql);
      $linkMain=wpcareers_create_link("rlist", array("name"=>$category, "id"=>$result->rc_id));
      $tpl->assign('category', $linkMain);
      $title=$result->r_title;

      $main_link=wpcareers_create_link("index", array("name"=>$lang['J_MAIN']));
      $category_link=wpcareers_create_link("rlist", array("name"=>$category, "id"=>$result->rc_id));
      $tpl->assign('top', $category_link); 
      $sendResumeLink=wpcareers_create_link("rsend", array("name"=> $lang['J_REFER_ICON'] . " Refer it to a Friend", "id"=>$result->r_id));
      if ($permission >= 5) {
         $modifyResumeLink=wpcareers_create_link("rmodify", array("name"=>$lang['J_MODIFY_ICON'] . " Modify", "id"=>$result->r_id));
         $deleteResumeLink=wpcareers_create_link("rdelete", array("name"=>$lang['J_DELETE_ICON'] . " Delete", "id"=>$result->r_id));
      }

      //$ttype = $wpdb->get_var("SELECT t_nom FROM {$table_prefix}wpj_type WHERE t_id=".$result->r_type);
      $ptype = $wpdb->get_var("SELECT p_nom FROM {$table_prefix}wpj_price WHERE p_id=".$result->r_typesalary);
      $photo = false;
      if (strlen($result->r_photo) > 3)
         $photo = '<div class="logo"><img src="' . get_bloginfo('wpurl') . '/wp-content/plugins/wpcareers/public/' . $result->r_photo . '"></a></div>';

      if (strlen($result->r_resume) > 3)
         $_upload = '&nbsp;&nbsp;<a target="_blank" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/wpcareers/resume/' . $result->r_resume . '" return false;"><div class="logo"><img src="' . get_bloginfo('wpurl') . '/wp-content/plugins/wpcareers/images/post/doc.jpg"></div></a><br />&nbsp;&nbsp;<b>Included File: </b>' . $result->r_resume;
      $resume[]=array (
         'title'=>$title,
         'rid'=>$result->r_id,
         //'type'=>$ttype,
         'town'=>$result->r_town,
         'state'=>$result->r_state,
         'information'=>$result->r_contactinfo,
         'salary'=>$result->r_salary,
         'typesalary'=>$ptype,
         'desctext'=>$result->r_desctext,
         'price'=>$result->r_price,
         'tel'=>$result->r_tel,
         'date'=>$result->r_date,
         'submitter'=>$result->r_submitter,
         'email'=>$result->r_email,
         'photo'=>$photo,
         '_upload'=>$_upload,
         'name'=>$result->r_name,
         'view'=>$result->r_view . '&nbsp;' . $lang['J_VIEW'],
         'startDate'=>$result->r_startDate,
         'fax'=>$result->r_fax,
         'sendResumeLink'=> $sendResumeLink,
         'modifyResumeLink' => $modifyResumeLink,
         'deleteResumeLink' => $deleteResumeLink,
         'job_mustlogin'=>$job_mustlogin);
   }

   $view=$result->r_view+1;
   $wpdb->query("UPDATE {$table_prefix}wpj_resume SET r_view='".$view."' WHERE r_id='".$rid."'");

   $tpl->assign('resume', $resume);

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
   return $tpl->display('view_resume.tpl'); 
} //wpcareers_view_resume


?>
