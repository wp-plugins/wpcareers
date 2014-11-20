<?php
/**
 * File Name: jp_rss.php
 * Description: Component for wpCareers wordpress plugin
 * @author Mohammad Forgani
 * @copyright Copyright 2010, Mohamad Forghanian
 * @version 1.0
 * @link http://www.forgani.com
 *
 * Last modified:  2010-05-27
 * Comments:
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('You are not allowed to call this page directly.'); 
}

ob_start();
global $wpcareers;
$pageinfo = $wpcareers->get_pageinfo();

# Get Data
$start=0;
$sql = "SELECT * FROM {$table_prefix}wpj_job l, {$table_prefix}wpj_categories c WHERE l.lc_id = c.c_id ORDER BY l.l_date DESC, l.l_title DESC LIMIT ".($start).", ".($wpca_settings['new_links']);

$posts = $wpdb->get_results($sql);

# Define Channel Elements
$rssTitle=get_bloginfo('name').' - '.__("wpCareers");
$rssLink = get_bloginfo('wpurl'). "/?page_id=". $pageinfo["ID"]. "&_action=odlfeed";
$atomLink= $rssLink;
$rssDescription=get_bloginfo('description');
$rssGenerator=__('wpCareers ') . 'v1.0';
$rssItem=array();

 // $new_jobs[]=array ('date'=>$result->l_date, 'title'=>$result->l_title, 'category'=>$result->c_title, 'previewlink'=>$previewlink); 
if($posts) {
	foreach($posts as $post){
		# Define Item Elements
		$item = new stdClass;
		$item->title=$post->l_title;
		$item->pubDate=$post->l_date; 
		$item->category=$post->c_title;
		$item->post=$post->l_desctext;
		$item->guid=jpRssLink(array("name"=>$post->l_title, "id"=>$post->l_id, "parent"=>$post->lc_id));
		$rssItem[]=$item;
	}
}

if (empty($wp)) {
    require_once('wp-config.php');
    wp('feed=rss2');
}

$contents = '<?xml version="1.0" encoding="' .  get_option('blog_charset') . '"?>';
$contents .= '<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" ' .  do_action('rss2_ns') . ">\n";
$contents .= "<channel>\n<title>$rssTitle</title>\n";
$contents .= "<link>". $wpcareers->cache_url . "wpcareers.xml</link>\n";
$contents .= "<description>$rssDescription</description>\n";
$contents .= "<generator>$rssGenerator</generator>\n";
$contents .= "<language>" .  get_option('rss_language') . "</language>\n";
$contents .= "<pubDate>" .  date("r") . "</pubDate>\n";

$filename = $wpcareers->cache_dir .'/wpcareers.xml';
$fp = fopen($filename, 'w');

fwrite($fp, $contents);
?>
<?php do_action('rss2_head'); ?>
<?php foreach($rssItem as $item): ?>
<?php ob_start(); start_wp();?>
	<item>
		<title><?php echo jpRssFilter($item->title); ?></title>
		<link><?php echo $item->guid ?></link>
		<category><?php echo jpRssFilter($item->category) ?></category>
		<guid isPermaLink="true"><?php echo jpRssFilter($item->guid) ?></guid>
		<!-- dc:creator><?php //the_author() ?></dc:creator -->
		<description>[CDATA[<?php echo jpRssFilter($item->post) ?>]]</description>
		<pubDate><?php echo $item->pubDate; ?></pubDate>
	</item>
<?php
	$contents = ob_get_clean();
   fwrite($fp, $contents);
?>
<?php endforeach; ?>
<?php ob_start(); ?>
</channel>
</rss>
<?php
	$contents = ob_get_clean();
	fwrite($fp, $contents);
?>