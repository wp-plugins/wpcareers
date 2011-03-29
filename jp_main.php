<?php

/**
 * jp_main.php
 * @author Mohammad Forgani
 * wpCareers wordpress plugin
 * @copyright Copyright 2010, Mohammad Forgani
 * @version 1.0
 * @link http://www.forgani.com
 */

function wpcareers_display_header($message=''){
	global $_GET, $_POST, $table_prefix, $wpdb, $lang, $PHP_SELF, 
		$wpcareers, $user_ID, $user_identity, $user_level, $user_login;

   $wpca_settings= get_option('wpcareers');
   $tpl=new wpca_smarty_compiler_class();

	$wpcareers->ip_cleanUp();

   $g120_600 ='<script type="text/javascript"><!--
google_ad_client = "pub-xxxxxx";
/* 120x600, created 5/19/08 */
google_ad_slot = "2965935555";
google_ad_width = 120;
google_ad_height = 600;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>';

   $ug = esc_html(stripslashes($user_login), 1);
	$tpl->assign('user_login', $ug);
   
	if (isset($user_ID)) {
		$sql="SELECT * FROM {$table_prefix}wpj_job WHERE l_usid='".$user_ID."' LIMIT 2";
		$results=$wpdb->get_results($sql); 
		for ($i=0; $i<count($results); $i++){
			$result=$results[$i];
			$title = substr($result->l_title, 0, 32) . '..';
			$viewJob=wpcareers_create_link("jview", array("name"=>$title, "id"=>$result->l_id));
			
			$ljobs[]=array (
				'title'=>$title,
				'l_view'=>$result->l_view,
				'viewjob'=>$viewJob);
		}
		$tpl->assign('ljobs', $ljobs);
		
		$sql="SELECT * FROM {$table_prefix}wpj_resume WHERE r_usid='".$user_ID."' LIMIT 2";
		$results=$wpdb->get_results($sql); 
		for ($i=0; $i<count($results); $i++){
			$result=$results[$i];
			$title = substr($result->r_title, 0, 32) . '..';
			$viewResume=wpcareers_create_link("rview", array("name"=>$title, "id"=>$result->r_id));
			$lresume[]=array (
				'title'=>$title,
				'r_view'=>$result->r_view,
				'viewResume'=>$viewResume);
		}
		$tpl->assign('lresume', $lresume);
	}

   $tpl->assign('plugin_url', JP_PLUGIN_URL);
   $tpl->assign('REQUEST_URI', $_SERVER['REQUEST_URI']);
   $tpl->assign('lang', $lang);
   $tpl->assign('user_ID', $user_ID);
   $tpl->assign('siteurl', get_bloginfo('wpurl'));
   $tpl->assign('user_identity', $user_identity);
   $tpl->assign('user_level', $user_level);
   $permission = jp_check_permission();
   $tpl->assign('permission', $permission);
   $tpl->assign('wpca_settings',$wpca_settings);
   if ($message) $tpl->assign('message', $message);
   $searchform=wpcareers_create_link("searchform", array());
   $tpl->assign('search_link', $searchform);

   $main_link=wpcareers_create_link("index", array("name"=>$lang['J_MAIN']));
   $mainLink=wpcareers_create_link("indexLink", 'undef');

   $tpl->assign('main_link', $main_link);
   $tpl->assign('mainLink', $mainLink);

   $headpic=wpcareers_create_link("index", array("name"=>'<img src="'.JP_PLUGIN_URL.'/images/'. $wpca_settings['page_image'] . '" align="left" vspace=10 hspace=20 border=0 />'));
   $tpl->assign('headpic', $headpic);
   $tpl->assign('headtxt', $wpca_settings['description']);

   $job_link=wpcareers_create_link("jpostform", '');
   $resume_link=wpcareers_create_link("rpostform", '');
   $tpl->assign('job_link', $job_link);
   $tpl->assign('resume_link', $resume_link);
   $tpl->assign('g120_600', $g120_600);
   return $tpl;
}


function wpcareers_display_index($message){
   global $_GET, $_POST, $table_prefix, $wpdb, $PHP_SELF;
   $tpl = wpcareers_display_header($message);

   if(!isset($_GET['id'])) $_GET['id']=0;
   $id=$_GET['id'];
   $tpl->assign('cid',$id); 
   ?>
   <script type="text/javascript">
	   
	imgout=new Image(9,9);
	imgin=new Image(9,9);
	imgout.src="<?php echo JP_PLUGIN_URL ?>/images/expand.gif";
	imgin.src="<?php echo JP_PLUGIN_URL ?>/images/collapse.gif";
	//this switches expand collapse icons
	function filter(imagename,objectsrc){
		if (document.images){
			document.images[imagename].src=eval(objectsrc+".src");
		}
	}
	//show OR hide funtion depends on if element is shown or hidden
	function hide(id) {
		if (document.getElementById) { // DOM3 = IE5, NS6
			if (document.getElementById(id).style.display == "none"){
				document.getElementById(id).style.display = 'block';
				filter(("img"+id),'imgin');			
			} else {
				filter(("img"+id),'imgout');
				document.getElementById(id).style.display = 'none';			
			}	
		} else { 
			if (document.layers) {	
				if (document.id.display == "none"){
					document.id.display = 'block';
					filter(("img"+id),'imgin');
				} else {
					filter(("img"+id),'imgout');	
					document.id.display = 'none';
				}
			} else {
				if (document.all.id.style.visibility == "none"){
					document.all.id.style.display = 'block';
				} else {
					filter(("img"+id),'imgout');
					document.all.id.style.display = 'none';
				}
			}
		}
	}
   </script>
   <?php
   wpcareers_list_job_categories($tpl, $id);
   wpcareers_list_res_categories($tpl, $id);

   wpcareers_footer($tpl);
   return $tpl->display('body.tpl'); 
}


function wpcareers_footer($tpl){
   global $lang, $wpdb, $table_prefix, $wpcareers;
   $wpca_settings = get_option('wpcareers');
   include_once ( $wpcareers->plugin_dir . '/include/jp_rss.php');

   if (isset($wpca_settings['wpcareers_show_credits']) &&
      $wpca_settings['wpcareers_show_credits']=='y'){
      $tpl->assign('wpcareers_show_credits', str_replace("[VERSION]", $wpca_settings['wpcareers_version'], stripslashes($wpca_settings['wpcareers_credit_line'])));
   }

   list($gAd, $gtop, $gbtn)=get_jp_GADlink();
   if ($gAd) {
      $code='<div class="jp_googleAd">' . $gAd . '</div>';
      $tpl->assign('googletop',$gtop); 
      $tpl->assign('googlebtn',$gbtn); 
      $tpl->assign('googleAd',$code); 
   }

  function array_sort_by_fields(&$data, $sortby){
    if(is_array($sortby)) {$sortby = join(',', $sortby);}
      uasort( $data,
      create_function('$a, $b', '
        $skeys=split(\',\',\''.$sortby.'\');
        foreach($skeys as $key){
          if(($c=strcasecmp($a[$key],$b[$key])) != 0 ){return($c);}
        }
	     return($c);'));
  }
  function smarty_modifier_sortby($arrData, $sortfields) {
    array_sort_by_fields($arrData, $sortfields);
    return $arrData;
  }
  $tpl->register_modifier( "sortby", "smarty_modifier_sortby" );


   if (!isset($wpca_settings['new_links'])) $wpca_settings['new_links']=4;
   $start=0;
   $tpl->assign("jobsNum", $wpca_settings['new_links']); 
   $sql = "SELECT * FROM {$table_prefix}wpj_job l, {$table_prefix}wpj_categories c WHERE l.lc_id = c.c_id ORDER BY l.l_date DESC LIMIT ".($start).", ".($wpca_settings['new_links']);
   $lastAds=$wpdb->get_results($sql);
   $new_jobs=array();
   for ($l=0; $l<count($lastAds); $l++){
      $result=$lastAds[$l];
      $previewlink=wpcareers_create_link("jview", array("name"=>$result->l_title, "id"=>$result->l_id));
      $new_jobs[]=array ('date'=>$result->l_date, 'title'=>$result->l_title, 'category'=>$result->c_title, 'previewlink'=>$previewlink); 
   }
   $tpl->assign('new_jobs', $new_jobs);

   $sql = "SELECT * FROM {$table_prefix}wpj_resume l, {$table_prefix}wpj_res_categories c WHERE l.rc_id = c.rc_id ORDER BY l.r_date DESC LIMIT ".($start).", ".($wpca_settings['new_links']);
   $lastAds=$wpdb->get_results($sql);
   $new_resumes=array();
   for ($l=0; $l<count($lastAds); $l++){
      $result=$lastAds[$l];
      $previewlink=wpcareers_create_link("rview", array("name"=>$result->r_title, "id"=>$result->r_id));
      $new_resumes[]=array ('date'=>$result->r_date, 'title'=>$result->r_title, 'category'=>$result->rc_title, 'previewlink'=>$previewlink); 
   }
   $tpl->assign('new_resumes', $new_resumes);

   $categories_total=$wpdb->get_var("SELECT count(*) FROM {$table_prefix}wpj_categories"); 
   $tpl->assign('categories_total',number_format($categories_total)); 
   $jobs_total=$wpdb->get_var("SELECT count(*) FROM {$table_prefix}wpj_job"); 
   $tpl->assign('jobs_total',number_format($jobs_total)); 
   $resume_total=$wpdb->get_var("SELECT COUNT(*) as count FROM {$table_prefix}wpj_resume");
   $tpl->assign('resume_total',number_format($resume_total)); 

   // TODO

   $filename = $wpcareers->cache_url .'wpcareers.xml';
   ?>
   <script type="text/javascript">
      function pop (file,name){
      rsswindow = window.open (file,name,"location=1,status=1,scrollbars=1,width=680,height=800");
      rsswindow.moveTo(0,0);
      rsswindow.focus();
      return false;
      }
   </script>
   <?php
   $rssurl = '<b><a href="'. $filename . '" target="_blank" onclick="return pop('.$filename.',' .  $wpca_settings['slug'] . ');"><img src="' . $wpcareers->plugin_url . '/images/rss.png"/>';
   $rssurl .= '&nbsp;RSS </a></b>';
   $tpl->assign('rssurl', $rssurl);
   if ($wpca_settings['show_credits'] == 'y') {
      $credit='Powered by <a href="http://www.forgani.com/" target="_blank">4gani</a> version '. VERSION;
      $tpl->assign('credit', $credit);
   }
}

function jpRssFilter($text){echo convert_chars(ent2ncr($text));} 

function jpRssLink($vars) {
	global $wpdb, $table_prefix, $wp_rewrite, $wpcareers;
	$wpca_settings = get_option('wpcareers');
	$pageinfo = $wpcareers->get_pageinfo();
	if($wp_rewrite->using_permalinks()) $delim = "?";
	else $delim = "&amp;";
	$page_id = $pageinfo['ID']; 
	$perm = get_permalink($page_id);

	$main_link = $perm.$delim;
	return $main_link."op=jview&amp;id=".$vars["id"];
}

?>