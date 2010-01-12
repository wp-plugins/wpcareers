<?php

/**
 * File Name: jp_GADlink.php
 * Description: This file is part of wpCareers wordpress plugin
 * @author Mohammad Forgani
 * @copyright Copyright 2010, Oh Jung-Su
 * @version 1.0
 * @link http://www.forgani.com
*/

function get_jp_GADlink() {
	global $_GET, $_POST, $table_prefix, $wpdb, $PHP_SELF, $wpca_version;
	$wpca_settings=get_option('wpcareers');

	$gtop=false;
	$gbtn=false;
	if ($wpca_settings['GADposition'] == 'bth') {
		$gtop=true;
		$gbtn=true;
	} else {
		if ($wpca_settings['GADposition'] == 'top') {
			$gtop=true;
		} elseif ($wpca_settings['GADposition'] == 'btn') {
			$gbtn=true;
		}
	}

	if ($gtop || $gbtn){
		$format=$wpca_settings['GADLformat'] . '_0ads_al'; // _0ads_al_s  5 Ads Per Unit
		list($width,$height)=preg_split('/[x]/',$wpca_settings['GADLformat']);
		$code="\n" . '<script type="text/javascript"><!--' . "\n";
		$code.= 'google_ad_client="' . $wpca_settings['googleID'] . '"; ' . "\n";
		$code.= 'google_ad_width="' . $width . '"; ' . "\n";
		$code.= 'google_ad_height="' . $height . '"; ' . "\n";
		$code.= 'google_ad_format="' . $format . '"; ' . "\n";
		if(isset($settings['alternate_url']) && $settings['alternate_url']!=''){ 
			$code.= 'google_alternate_ad_url="' . $settings['alternate_url'] . '"; ' . "\n";
		} else {
			if(isset($settings['alternate_color']) && $settings['alternate_color']!='') { 
				$code.= 'google_alternate_color="' . $settings['alternate_color'] . '"; ' . "\n";
			}
		}				
		//Default to Ads
		$code.= 'google_color_border="' . $wpca_settings['GADcolor_border'] . '"' . ";\n";
		$code.= 'google_color_bg="' . $wpca_settings['GADcolor_bg'] . '"' . ";\n";
		$code.= 'google_color_border="' . $wpca_settings['GADcolor_border'] . '"' . ";\n";
		$code.= 'google_color_text="' . $wpca_settings['GADcolor_text'] . '"' . ";\n";
		$code.= 'google_color_url="' . $wpca_settings['GADcolor_url'] . '"' . ";\n";
		$code.= '//--></script>' . "\n";
		$code.= '<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>' . "\n";
		
		return array($code, $gtop, $gbtn);
	}
	return false;
}



?>
