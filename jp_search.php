<?php

/**
 * jp_search.php
 * wpCareers wordpress plugin
 * @author Mohammad Forgani
 * @copyright Copyright 2010, Mohammad Forgani
 * @version 1.0
 * @link http://www.forgani.com
 */


function wpcareers_display_search(){
   global $_GET, $_POST, $table_prefix, $wpdb, $PHP_SELF;

   $wpca_settings = get_option('wpcareers');
   $tpl = wpcareers_display_header();
   if ($message) $tpl->assign('message', $message);
   $results_limit = 10;
   $tpl->assign('results_limit', $results_limit); 
   $type = $_POST['type'];
   $search_terms = $_POST['search_terms'];
   $tpl->assign('search_terms',$search_terms);
   if(isset($search_terms)){
      $search_terms = stripslashes($_POST['search_terms']);
      $searchwords = addslashes(htmlspecialchars($search_terms));
   }
   if(!$searchwords){
      $tpl->assign('message', "You didn't search for anything!");
   } else {
      if($type == "jobs"){
        $list = wpcareers_search_by_job($searchwords);
      } elseif($type == "resume"){
        $list = wpcareers_search_by_resume($searchwords);
      } elseif($type == "all"){
        $jobList = wpcareers_search_by_job($searchwords);
        $resList = wpcareers_search_by_resume($searchwords);
        $list = array_merge((array)$jobList, (array)$resList);
      }
      $tpl->assign('results', $list);
      //$jp_advanced = wpcareers_create_link("searchlink", array("name"=>'Advanced'));
      //$tpl->assign('jp_advanced', $jp_advanced);
      $tpl->display('search.tpl');
   }
}


function wpcareers_search_by_job($searchwords){
   global $_GET, $_POST, $table_prefix, $wpdb, $PHP_SELF;
    $wpca_settings = get_option('wpcareers');
    $sql = "SELECT * FROM {$table_prefix}wpj_job WHERE l_title LIKE '%$searchwords%' OR l_desctext LIKE '%$searchwords%'";
    $results = $wpdb->get_results($sql);
    for ($i=0; $i<count($results); $i++){
      $result=$results[$i];
      $sql="SELECT c_title, c_id FROM {$table_prefix}wpj_categories WHERE c_id=".$result->lc_id;
      $jobCategories=$wpdb->get_results($sql);
      for ($x=0; $x<count($jobCategories); $x++){
        $cat=$jobCategories[$x];
        $viewcategory=wpcareers_create_link("jlist", array("name"=>$cat->c_title, "id"=>$cat->c_id));
      }
      $title = $result->l_title;
      $sendJobLink=wpcareers_create_link("jsend", array("name"=>"Refer it to a Friend", "id"=>$result->l_id));
      $viewJoblink=wpcareers_create_link("jview", array("name"=>$title, "id"=>$result->l_id));
      $modifyJobLink=wpcareers_create_link("jmodify", array("name"=>"<img src='".JP_PLUGIN_URL."/images/modify.gif' border=0 />", "id"=>$result->l_id));
      $list[]=array (
       'title'=>$title,
       'lid'=>$result->l_id,
       'town'=>$result->l_town,
       'contactinfo'=>$result->l_contactinfo,
       'date'=> $result->l_date,
       'desctext'=>$result->l_desctext,
       'email'=>$result->l_email,
       'photo'=>$result->l_photo,
       'company'=>$result->l_company,
       'sendjob'=> $sendJobLink,
       'viewcategory' => $viewcategory,
       'modifyJobLink' => $modifyJobLink,
       'viewjob'=>$viewJoblink);
    }
    return $list;
}

function wpcareers_search_by_resume($searchwords){
   global $_GET, $_POST, $table_prefix, $wpdb, $PHP_SELF;
   $wpca_settings = get_option('wpcareers');
   $sql = "SELECT * FROM {$table_prefix}wpj_resume WHERE r_title LIKE '%$searchwords%' OR r_desctext LIKE '%$searchwords%'";
   $results = $wpdb->get_results($sql);
   for ($i=0; $i<count($results); $i++){
      $result=$results[$i];
      $sql="SELECT rc_title, rc_id FROM {$table_prefix}wpj_res_categories WHERE rc_id=".$result->rc_id;
      $resCategories=$wpdb->get_results($sql);
      for ($x=0; $x<count($resCategories); $x++){
         $cat=$resCategories[$x];
         $viewcategory=wpcareers_create_link("rlist", array("name"=>$cat->rc_title, "id"=>$cat->rc_id));
      }
      $title = $result->r_title;
      $sendResumeLink=wpcareers_create_link("rsend", array("name"=>"Refer it to a Friend", "id"=>$result->r_id));
      $viewResumelink=wpcareers_create_link("rview", array("name"=>$title, "id"=>$result->r_id));
      $list[]=array (
         'title'=>$title,
         'rid'=>$result->r_id,
         'town'=>$result->r_town,
         'date'=>$result->r_date,
         'desctext'=>$result->r_desctext,
         'email'=>$result->r_email,
         'photo'=>$result->r_photo,
         'upload'=>$result->r_upload,
         'name'=>$result->r_name,
         'sendResume'=> $sendResumeLink,
         'viewcategory' => $viewcategory,
         'viewResume'=>$viewResumelink);
   }
  return $list;
}

?>