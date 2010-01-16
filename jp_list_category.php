<?php

/**
 * File Name: jp_list_category.php
 * @author Mohammad Forgani
 * Package Name: wpCareers wordpress plugin
 * @copyright Copyright 2010, Mohammad Forgani
 * @version 1.0
 * @link http://www.forgani.com
 * Last modified:  2010-01-17
 * Comments:
*/

function wpcareers_list_job_categories($tpl, $id){
   global $_GET, $_POST, $table_prefix, $wpdb, $PHP_SELF, $wpca_version;

   $categorys=$wpdb->get_results("SELECT * FROM {$table_prefix}wpj_categories WHERE cp_id=".$id." ORDER BY c_id");
   if (!empty($categorys)) {
		foreach ($categorys as $category) { 
			$jcounTotal = 0;
			if($category->cp_id == $id){
				$title=trim($category->c_title);
				$category_link=wpcareers_create_link("jlist", array("name"=>$title, "id"=>$category->c_id));
				$sql="SELECT count(*) FROM {$table_prefix}wpj_job WHERE lc_id=".$category->c_id;
				$jcount=$wpdb->get_var($sql);
				$sql="SELECT * FROM {$table_prefix}wpj_categories WHERE cp_id=".$category->c_id;
				$subCategories=$wpdb->get_results($sql);
				if (!empty($subCategories))
				foreach ($subCategories as $subCategory) { 
					$title=trim($subCategory->c_title);
					$subCategory_link=wpcareers_create_link("jlist", array("name"=>$title, "id"=>$subCategory->c_id));
					$sql="SELECT count(*) FROM {$table_prefix}wpj_job WHERE lc_id=".$subCategory->c_id;
					$jsubCount=$wpdb->get_var($sql);
					$catSubImg='<div class="icon"><img src="'.JP_PLUGIN_URL.'/images/'.$category->c_img.'"></div>';
					$jobSubCategories[]=array (
						'c_id'=>$subCategory->c_id,
						'cp_id'=>$subCategory->cp_id,
						'c_title'=>$title,
						'jcount'=>$jsubCount,
						'catImg'=>$catSubImg,
						'subCategory_link'=>$subCategory_link);
					$jcounTotal = $jcounTotal + $jsubCount;
				}
				$catImg='<div class="icon"><img src="'.JP_PLUGIN_URL.'/images/'.$category->c_img.'" name="imgANOTHER" border="0" alt="arrow icon"></div>';
				$catImgSrc= JP_PLUGIN_URL.'/images/'.$category->c_img;
				$jcounTotal = $jcounTotal + $jcount;
				$jobCategories[]=array (
					'c_id'=>$category->c_id,
					'cp_id'=>$category->cp_id,
					'c_title'=>$title,
					'jcount'=>$jcount,
					'catImg'=>$catImg,
					'jcounTotal'=>$jcounTotal,
					'category_link'=>$category_link);
			}
		}
   }
	$tpl->assign('catImgSrc', JP_PLUGIN_URL . "/images/expand.gif"); 
   $tpl->assign('jobCategories',$jobCategories); 
   $tpl->assign('jobSubCategories',$jobSubCategories); 
}


function wpcareers_list_res_categories($tpl, $id){
   global $_GET, $_POST, $table_prefix, $wpdb, $PHP_SELF, $wpca_version;
   $sql = "SELECT * FROM {$table_prefix}wpj_res_categories WHERE rcp_id=".$id." ORDER BY rc_id";
   $categorys=$wpdb->get_results($sql);
   if (!empty($categorys)) {
		foreach ($categorys as $category) { 
			$rcounTotal = 0;
			if($category->rcp_id == $id){
			$title=trim($category->rc_title);
			$resume_link=wpcareers_create_link("rlist", array("name"=>$title, "id"=>$category->rc_id));
			$sql="SELECT count(*) FROM {$table_prefix}wpj_resume WHERE rc_id=".$category->rc_id;
			$rcount=$wpdb->get_var($sql);
			$sql="SELECT * FROM {$table_prefix}wpj_res_categories WHERE rcp_id=".$category->rc_id;
			$subCategories=$wpdb->get_results($sql);
			if (!empty($subCategories))
			foreach ($subCategories as $subCategory) {
				$title=trim($subCategory->rc_title);
				$subResume_link=wpcareers_create_link("rlist", array("name"=>$title, "id"=>$subCategory->rc_id));
				$sql="SELECT count(*) FROM {$table_prefix}wpj_resume WHERE rc_id=".$subCategory->rc_id;
				$rsubCount=$wpdb->get_var($sql);
				$catSubImg='<div class="icon"><img src="'.JP_PLUGIN_URL.'/images/'.$category->rc_img.'"></div>';
				$resSubCategories[]=array (
					'rc_id'=>$subCategory->rc_id,
					'rcp_id'=>$subCategory->rcp_id,
					'rc_title'=>$title,
					'rcount'=>$rsubCount,
					'catImg'=>$catSubImg,
					'subResume_link'=>$subResume_link);
				$rcounTotal = $rcounTotal + $rsubCount;
			}
			$catImg='<div class="icon"><img src="'.JP_PLUGIN_URL.'/images/'.$category->rc_img.'"></div>';
			$rcounTotal = $rcounTotal + $rcount;
			$resCategories[]=array (
				'rc_id'=>$category->rc_id,
				'rcp_id'=>$category->rcp_id,
				'rc_title'=>$title,
				'rcount'=>$rcount,
				'rcounTotal'=>$rcounTotal,
				'catImg'=>$catImg,
				'resume_link'=>$resume_link);
			}
		}
   }

   $tpl->assign('resCategories',$resCategories); 
   $tpl->assign('resSubCategories',$resSubCategories);
}

?>