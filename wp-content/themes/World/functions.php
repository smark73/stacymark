<?php
function mytheme_add_admin() {
global $themename, $shortname, $options;
if ( $_GET['page'] == basename(__FILE__) ) {
	if ( 'save' == $_REQUEST['action'] ) {
		foreach ($options as $value) {
		update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
 
foreach ($options as $value) {
	if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }
	header("Location: admin.php?page=functions.php&saved=true");
die;
 
} 
else if( 'reset' == $_REQUEST['action'] ) {
	foreach ($options as $value) {
		delete_option( $value['id'] ); }
		header("Location: admin.php?page=functions.php&reset=true");
die;
 }
}
 
add_theme_page($themename.' Settings', '"World" Settings', 'administrator', basename(__FILE__), 'mytheme_admin',get_template_directory_uri() . '/images/icon.png', 61);}



$themename = "World";
$shortname = "sg";

$categories = get_categories('hide_empty=0&orderby=name');
$wp_cats = array();
foreach ($categories as $category_list ) {
       $wp_cats[$category_list->cat_ID] = $category_list->cat_name;
}
array_unshift($wp_cats, "Choose a category"); 

$options = array (
 
array( "name" => $themename." Options",
	"type" => "title"),
 

array( "name" => "General",
	"type" => "section"),
array( "type" => "open"),
 
array( "name" => "Colour Scheme",
	"desc" => "Select the colour scheme for the theme",
	"id" => $shortname."_color_scheme",
	"type" => "select",
	"options" => array("Orange", "Blue", "Red", "Green","Purple"),
	"std" => ""),
	
array( "name" => "Enable Breadcrumbs",
	"desc" => "Choose whether to display breadcrumbs under your main menu",
	"id" => $shortname."_enable_brd",
	"type" => "select",
	"options" => array("No", "Yes"),
	"std" => "Choose Yes or No"),
	
array( "name" => "Enable Fancybox",
	"desc" => "Choose whether to enable prettyPhoto for all images",
	"id" => $shortname."_enable_fancy",
	"type" => "select",
	"options" => array("Yes", "No"),
	"std" => "Choose Yes or No"),
	
array( "name" => "Enable Back to Top",
	"desc" => "Enable jQuery powered link to the top of your page",
	"id" => $shortname."_enable_back",
	"type" => "select",
	"options" => array("Yes", "No"),
	"std" => "Choose Yes or No"),
	
array( "name" => "Custom Logo URL",
	"desc" => "Enter the link to your custom logo image",
	"id" => $shortname."_logo",
	"type" => "text",
	"std" => ""),
	
array( "type" => "close"),



array( "name" => "Social Networking",
	"type" => "section"),
array( "type" => "open"),

array( "name" => "Enable Social Media Icons",
	"desc" => "Choose if you want our social icons displayed on your site.",
	"id" => $shortname."_enable_soc",
	"type" => "select",
	"options" => array("Yes", "No"),
	"std" => "Choose Yes or No"),
	
array( "name" => "Social widget title",
	"desc" => "Enter the title above your social icons.",
	"id" => $shortname."_social_title",
	"type" => "text",
	"std" => ""),	

array( "name" => "Facebook page/profile URL",
	"desc" => "Enter the link to your Facebook profile or page.",
	"id" => $shortname."_facebook_url",
	"type" => "text",
	"std" => ""),
	
array( "name" => "Twitter Username",
	"desc" => "Enter your Twitter URL.",
	"id" => $shortname."_twitter_url",
	"type" => "text",
	"std" => ""),
		
array( "name" => "Google+ Account",
	"desc" => "Enter the link to your Google+ account.",
	"id" => $shortname."_google_url",
	"type" => "text",
	"std" => ""),		
	
array( "name" => "RSS Feed URL",
	"desc" => "Enter the link to your RSS feed.",
	"id" => $shortname."_rss_url",
	"type" => "text",
	"std" => ""),		

array( "type" => "close"),
array( "name" => "Footer",
	"type" => "section"),
array( "type" => "open"),
	
array( "name" => "Footer copyright text",
	"desc" => "Enter text used in the right side of the footer.",
	"id" => $shortname."_copyright",
	"type" => "text",
	"std" => ""),	

array( "type" => "close")
 
);


function mytheme_add_init() {
$file_dir=get_bloginfo('template_directory');
wp_enqueue_style("functions", $file_dir."/functions/functions.css", false, "1.0", "all");


}
function mytheme_admin() {
 
global $themename, $shortname, $options;
$i=0;
 
?>




<div class="wrap rm_wrap">
<h2>"<strong>World</strong>" Theme Settings</h2>
 
<div class="rm_opts">
<form method="post">
<?php foreach ($options as $value) {
switch ( $value['type'] ) {
 
case "open":
?>
 
<?php break;
 
case "close":
?>
 
</div>
</div>
<br />

 
<?php break;
 
case "title":
?>
 
<?php break;
 
case 'text':
?>

<div class="rm_input rm_text">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
 	<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id'])  ); } else { echo $value['std']; } ?>" />
 <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
 
 </div>
<?php
break;
 
case 'textarea':
?>

<div class="rm_input rm_textarea">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
 	<textarea name="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" cols="" rows=""><?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id']) ); } else { echo $value['std']; } ?></textarea>
 <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
 
 </div>
  
<?php
break;
 
case 'select':
?>

<div class="rm_input rm_select">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
	
<select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
<?php foreach ($value['options'] as $option) { ?>
		<option <?php if (get_option( $value['id'] ) == $option) { echo 'selected="selected"'; } ?>><?php echo $option; ?></option><?php } ?>
</select>

	<small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
</div>
<?php
break;
 
case "checkbox":
?>

<div class="rm_input rm_checkbox">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
	
<?php if(get_option($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
<input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />


	<small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
 </div>
<?php break; 
case "section":

$i++;

?>

<div class="rm_section">
<div class="rm_title"><h3><?php echo $value['name']; ?></h3><span class="submit"><input name="save<?php echo $i; ?>" type="submit" value="Save changes" />
</span><div class="clearfix"></div></div>
<div class="rm_options">

 
<?php break;
 
}
}
?>
 
<input type="hidden" name="action" value="save" />
</form>
<a href="http://www.siteground.com/tracking/WPadmin" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/images/sgbanner.gif" width="740" height="90" class="inactive" alt="WordPress Hosting by SiteGround"></a>
<form method="post">
<p class="submit">
<input name="reset" type="submit" value="Reset" />
<input type="hidden" name="action" value="reset" />
</p>
</form>
 </div> 
 
<?php } ?>
<?php
add_action('admin_init', 'mytheme_add_init');
add_action('admin_menu', 'mytheme_add_admin');

remove_filter('the_content', 'wptexturize');
remove_filter('the_title', 'wptexturize');
remove_filter('comment_text', 'wptexturize');



# WIDGET: Sidebar
if ( function_exists('register_sidebar') )
    register_sidebar(array(
		'name' => 'Right Sidebar',
        'before_title' => '<h2>',
        'after_title' => '</h2>',
		'before_widget' => '',
        'after_widget' => '',
    ));
    
 # WIDGET: Content Bottom
 if ( function_exists('register_sidebar') )
     register_sidebar(array(
 		'name' => 'Footer',
         'before_title' => '<h2>',
         'after_title' => '</h2>',
 		'before_widget' => '<div class="box">',
         'after_widget' => '</div>',
     ));
     
   

# Displays a list of categories
function dp_list_categories($exclude='', $limit=0) {
	if (strlen($exclude)>0) $exclude = '&exclude=' . $exclude;
	$categories = get_categories('hide_empty=1'.$exclude);
	$first = true; $count = 0;
	foreach ($categories as $category) {
		$count++; if ($count>$limit && $limit>0) break;
		if ($category->parent<1) {
			if ($first) { $first = false; $f = ' class="f"'; } else { $f = ''; }
			?><li<?php echo $f; ?>>
			<a href="<?php echo get_category_link($category->cat_ID); ?>"><?php echo $category->name ?><?php echo $raquo; ?></a></li>
			<?php
		}
	}
}

# Displays a list of popular posts
function dp_popular_posts($num, $pre='<li>', $suf='</li>', $excerpt=false) {
	global $wpdb;
	$querystr = "SELECT $wpdb->posts.post_title, $wpdb->posts.ID, $wpdb->posts.post_content FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'post' ORDER BY $wpdb->posts.comment_count DESC LIMIT $num";
	$myposts = $wpdb->get_results($querystr, OBJECT);
	foreach($myposts as $post) {
		echo $pre;
		?><a class="title" href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title ?></a><?php
		if ($excerpt) {
			dp_attachment_image($post->ID, 'medium', 'alt="'.$post->post_title.'"');
			?><p><?php echo dp_clean($post->post_content, 85); ?>... <a href="<?php echo get_permalink($post->ID); ?>">Read More</a></p><?php
		}
		echo $suf;
	}
}

# Displays a list of recent categories
function dp_recent_comments($num, $pre='<li>', $suf='</li>') {
	global $wpdb, $post;
	$querystr = "SELECT $wpdb->comments.comment_ID, $wpdb->comments.comment_post_ID, $wpdb->comments.comment_author, $wpdb->comments.comment_content, $wpdb->comments.comment_author_email FROM $wpdb->comments WHERE $wpdb->comments.comment_approved=1 ORDER BY $wpdb->comments.comment_date DESC LIMIT $num";
	$recentcomments = $wpdb->get_results($querystr, OBJECT);
	foreach ($recentcomments as $rc) {
		$post = get_post($rc->comment_post_ID);
		echo $pre;
		?><a href="<?php the_permalink() ?>#comment-<?php echo $rc->comment_ID ?>"><?php echo $rc->comment_author ?></a>: <?php echo dp_clean($rc->comment_content, 20); ?><?php
		echo $suf;
	}
}



# Removes tags and trailing dots from excerpt
function dp_clean($excerpt, $substr=0) {
	$string = strip_tags(str_replace('[...]', '...', $excerpt));
	if ($substr>0) {
		$string = substr($string, 0, $substr);
	}
	return $string;
}

# Displays the comment authors gravatar if available
function dp_gravatar($size=50, $attributes='', $author_email='') {
	global $comment, $settings;
	if (dp_settings('gravatar')=='enabled') {
		if (empty($author_email)) {
			ob_start();
			comment_author_email();
			$author_email = ob_get_clean();
		}
		$gravatar_url = 'http://www.gravatar.com/avatar/' . md5(strtolower($author_email)) . '?s=' . $size . '&amp;d=' . dp_settings('gravatar_fallback');
		?><img src="<?php echo $gravatar_url; ?>" <?php echo $attributes ?>/><?php
	}
}

# Retrieves the setting's value depending on 'key'.
function dp_settings($key) {
	global $settings;
	return $settings[$key];
}

# Removes the inline padding:0 for the recent comments widget
function remove_wp_widget_recent_comments_style() {  
   if ( has_filter('wp_head', 'wp_widget_recent_comments_style') ) {  
      remove_filter('wp_head', 'wp_widget_recent_comments_style' );  
   }  
}  

add_filter( 'post_thumbnail_html', 'remove_thumbnail_dimensions', 10, 3 );

function remove_thumbnail_dimensions( $html, $post_id, $post_image_id ) {
    $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
    return $html;
}

add_filter('the_content', 'addlightboxrel_replace');
function addlightboxrel_replace ($content)
{	global $post;
	$pattern = "/<a(.*?)href=('|\")(.*?).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>/i";
  	$replacement = '<a$1href=$2$3.$4$5 class="fancybox"$6>';
    $content = preg_replace($pattern, $replacement, $content);
	$content = str_replace("%LIGHTID%", $post->ID, $content);
    return $content;
}

function insert_jquery(){
	wp_enqueue_script('jquery');
}
add_action('init', 'insert_jquery');
if ( ! isset( $content_width ) ) $content_width = 650;
add_theme_support( 'automatic-feed-links' );
$headerdefaults = array(
	'default-image'          => '%s/images/logo.png',
	'random-default'         => false,
	'width'                  => 200,
	'height'                 => 150,
	'flex-height'            => false,
	'flex-width'             => false,
	'default-text-color'     => '',
	'header-text'            => true,
	'uploads'                => true,
	'wp-head-callback'       => '',
	'admin-head-callback'    => '',
	'admin-preview-callback' => '',
);
add_theme_support( 'custom-header', $headerdefaults );
?>