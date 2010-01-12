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
         $sql = "SELECT * FROM {$table_prefix}wpj_job WHERE l_title LIKE '%$searchwords%' OR l_desctext LIKE '%$searchwords%'";
         $results = $wpdb->get_results($sql);
         for ($i=0; $i<count($results); $i++){
         $result=$results[$i];
         $sql="SELECT c_title, c_id FROM {$table_prefix}wpj_categories WHERE c_id=".$result->lc_id;
         $category=$wpdb->get_results($sql);
         for ($x=0; $x<count($category); $x++){
            $cat=$category[$x];
            $viewcategory=wpcareers_create_link("jlist", array("name"=>$cat->c_title, "id"=>$cat->c_id));
         }
         $title = $result->l_title;
         $sendJobLink=wpcareers_create_link("jsend", array("name"=>"Refer it to a Friend", "id"=>$result->l_id));
         $viewJoblink=wpcareers_create_link("jview", array("name"=>$title, "id"=>$result->l_id));
         $modifyJobLink=wpcareers_create_link("jmodify", array("name"=>"<img src='".JP_PLUGIN_URL."/images/post/modify.gif' border=0 />", "id"=>$result->l_id));
         $list[]=array (
            'title'=>$title,
            'lid'=>$result->l_id,
            'town'=>$result->l_town,
            'contactinfo'=>$result->l_contactinfo,
            'date'=>date('Y-m-d',$result->l_date),
            'desctext'=>$result->l_desctext,
            'email'=>$result->l_email,
            'photo'=>$result->l_photo,
            'company'=>$result->l_company,
            'sendjob'=> $sendJobLink,
            'viewcategory' => $viewcategory,
            'modifyJobLink' => $modifyJobLink,
            'viewjob'=>$viewJoblink);
         }
      } elseif($type == "resume"){
         $sql = "SELECT * FROM {$table_prefix}wpj_resume WHERE r_title LIKE '%$searchwords%' OR r_desctext LIKE '%$searchwords%'";
         $results = $wpdb->get_results($sql);
         for ($i=0; $i<count($results); $i++){
            $result=$results[$i];
            $sql="SELECT rc_title, rc_id FROM {$table_prefix}wpj_categories WHERE rc_id=".$result->rc_id;
            $category=$wpdb->get_var($sql);
            for ($x=0; $x<count($category); $x++){
               $cat=$category[$x];
               $viewcategory=wpcareers_create_link("rlist", array("name"=>$cat->rc_title, "id"=>$cat->rc_id));
            }
            $title = $result->r_title;
            $sendResumeLink=wpcareers_create_link("rsend", array("name"=>"Refer it to a Friend", "id"=>$result->r_id));
            $viewResumelink=wpcareers_create_link("rview", array("name"=>$title, "id"=>$result->r_id));
            $list[]=array (
               'title'=>$title,
               'rid'=>$result->r_id,
               'town'=>$result->r_town,
               'date'=>date('Y-m-d',$result->r_date),
               'desctext'=>$result->r_desctext,
               'email'=>$result->r_email,
               'photo'=>$result->r_photo,
               'upload'=>$result->r_upload,
               'name'=>$result->r_name,
               'sendResume'=> $sendResumeLink,
               'viewcategory' => $viewcategory,
               'viewResume'=>$viewResumelink);
         }
      }
   }

   //print_r($list);
   $tpl->assign('results', $list);
   //$jp_advanced = wpcareers_create_link("searchlink", array("name"=>'Advanced'));
   //$tpl->assign('jp_advanced', $jp_advanced);
   $tpl->display('search.tpl');
}





?>
