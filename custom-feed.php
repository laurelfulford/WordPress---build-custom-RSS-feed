<?php
/*
Template Name: Custom Feed
*/
header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
$more = 1;

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; 

//while we're up here, nab the query strings
$customAut = $_GET['byline'];
$customCat = $_GET['category']; 

if($customCat) $categoryList = join(",", $customCat);

//create filter
//makes it possible to filter query with OR statement
//so that it can be a mix of categories OR authors
//not categories AND authors

function categoryORauthor( $where = '') {
	
	//set initial value for query
	$authorQuery = '';
	
	//get 'byline' value from query string
	$customAut = $_GET['byline'];
	
	//if there is a byline, for each value append an OR statement to the filter
	if($customAut) {
		foreach($customAut as $author) $authorQuery .= " OR post_author = ".$author;
	}
	
	//apply completed filter to $where variable
	//append more specific query filter to weed out drafts, etc
	$where .= $authorQuery; 
	
	
	
	return $where;
}

function killDupes( $where = '') {
	
	//apply completed filter to $where variable
	//append more specific query filter to weed out drafts, etc
	$where .= " AND post_status = 'publish' AND post_type='post'"; 
	
	return $where;
}

//apply filters
add_filter( 'posts_where', 'categoryORauthor' );
add_filter( 'posts_join', 'killDupes');	


?>



<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	<?php do_action('rss2_ns'); ?>
>

<channel>
	<title><?php bloginfo_rss('name'); wp_title_rss(); ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
	<?php the_generator( 'rss2' ); ?>
	<language><?php echo get_option('rss_language'); ?></language>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
	<?php do_action('rss2_head'); ?>
	
	<?php 
	
	$args = array(
		//create query with categories from query string
	   'category__in'=>array($categoryList),
	
		//get all the posts - more for testing than anything
		'posts_per_page'=>-1,
	
		//turn off filter supression for query so the above is applied
	   'suppress_filters' => false
	);
	
	//call the new query with the above arguments applied
	$customizeRSS = new WP_Query($args);
	
	//The Loop, as per usual
	if ( $customizeRSS->have_posts() ) : while ( $customizeRSS->have_posts() ) : $customizeRSS->the_post();

 	?>
	
	<item>
		<title><?php the_title_rss() ?> - by <?php the_author() ?></title>
		<link><?php the_permalink_rss() ?></link>
		<comments><?php comments_link(); ?></comments>
		<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
		<dc:creator><?php the_author() ?></dc:creator>
		<?php the_category_rss() ?>

		<guid isPermaLink="false"><?php the_guid(); ?></guid>
		<?php if (get_option('rss_use_excerpt')) : ?>
			<description><![CDATA[<?php the_excerpt_rss() ?>]]></description>
		<?php else : ?>
			<description><![CDATA[<?php the_excerpt_rss() ?>]]></description>
			
			<?php if ( strlen( $post->post_content ) > 0 ) : ?>
				<content:encoded><![CDATA[<?php the_content_feed('rss2') ?>]]></content:encoded>
			<?php else : ?>
				<content:encoded><![CDATA[<?php the_excerpt_rss() ?>]]></content:encoded>
			<?php endif; ?>	
		<?php endif; ?>
		
		<wfw:commentRss><?php echo get_post_comments_feed_link(null, 'rss2'); ?></wfw:commentRss>
		<slash:comments><?php echo get_comments_number(); ?></slash:comments>
<?php rss_enclosure(); ?>
	<?php do_action('rss2_item'); ?>
	</item>
	
	<?php endwhile; endif;?>
</channel>
</rss>












