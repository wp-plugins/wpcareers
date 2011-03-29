<?php

/**
 * odl_main.php
 * @author Mohammad Forgani
 * wordpress plugin website directory project
 * @copyright Copyright 2008, Oh Jung-Su
 * @version 0.1.0
 * @link http://www.forgani.com
 */



function wpcareers_admin_page(){
   global $_GET, $_POST, $PHP_SELF, $user_level, $pagelabel,
      $wpdb, $wpca_suser_level, $wpca_sversion, $_REQUEST, $wpcareers;

   get_currentuserinfo();

   $wpca_ssettings = get_option('wpcareers');
   if (!isset($_REQUEST['admin_page_arg'])) $_REQUEST['admin_page_arg']='wpcareers_settings';
   ?>
   <div class="wrap">
   <h2><?php echo $pagelabel;?></h2>
   <?php
   switch ($_REQUEST['admin_page_arg']){
      case "wpcareers_settings":
         default:
         $wpcareers->process_option_settings();
      break;
      case "wpcareers_structure":
         process_structure();
      break;
      case "wpcareers_posts":
         process_posts();
      break;
      case "wpcareers_utilities":
         process_utilities();
      break;
   }
   ?>
   </div>
   <?php

}


function wpcareers_admin_menu(){
  global $wpcareers, $PHP_SELF;
  $head = '<div class="wrap"><h2>Wordpress Careers</h2><p>';
  $head .= '<div style="text-align: right;"><a href="http://www.forgani.com/">Support this software</a><br>Read my opinion</div>';
  $menu = '<a href=' . $PHP_SELF . '?page=' . 'wpcareers_settings' . '>Settings & Options</a> | ';
  for ($i=0; $i<count($wpcareers->admin_menu); $i++){
    $tlink = $wpcareers->admin_menu[$i];
    if (!isset($_GET['adm_arg'])) $_GET['adm_arg']='';
    
    $sel = "";
    // TOTO
    /*
    if ($tlink['arg']==$_GET['adm_arg'] || ($_GET['adm_arg'] == '' && $i==0)){
      $sel = " class=\"current\"";
      $pagelabel = $tlink['name'];
    } else {
      $sel = "";
    }
    */
    $menu .= '<a href=' . $PHP_SELF . '?page=' .$tlink['arg']. ' ' . $sel .'>'.$tlink['name']. '</a> | ';
  }
  return $head . $menu . '<p><hr style="display: block; border:1px solid #e18a00;"></p>';
}

?>
