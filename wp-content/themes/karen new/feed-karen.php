<?php
/**
 * Custom WordImpress RSS2 Feed
 * Integrates Featured Image as "Enclosure"
 * See http://www.rssboard.org/rss-2-0-1#ltenclosuregtSubelementOfLtitemgt
 * for RSS 2.0 specs
 * @package WordPress
 */

header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
$more = 1;




echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';

/**
 * Fires between the <xml> and <rss> tags in a feed.
 * @since 4.0.0
 * @param string $context Type of feed. Possible values include
 * 'rss2', 'rss2-comments', 'rdf', 'atom', and 'atom-comments'.
 */

do_action( 'rss_tag_pre', 'rss2' );
?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	<?php
	/**
	 * Fires at the end of the RSS root to add namespaces.
	 * @since 2.0.0
	 */
	do_action( 'rss2_ns' );
	?>
>

<channel>
	<title><?php bloginfo_rss('Karen Margulis'); wp_title_rss(); ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss("Daily Painter and Pastel Teacher") ?></description>
	<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
	<language><?php bloginfo_rss( 'language' ); ?></language>
	<?php
	$duration = 'hourly';
	/**
	 * Filter how often to update the RSS feed.
	 * @since 2.1.0
	 * @param string $duration The update period.
	 * Default 'hourly'.
	 * Accepts 'hourly', 'daily', 'weekly', 'monthly', 'yearly'.
	 */
	?>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', $duration ); ?></sy:updatePeriod>
	<?php
	$frequency = '1';
	/**
	 * Filter the RSS update frequency.
	 * @since 2.1.0
	 * @param string $frequency An integer passed as a string
	 * representing the frequency of RSS updates within the update period.
	 * Default '1'.
	 */
	?>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', $frequency ); ?></sy:updateFrequency>
	<?php
	/**
	 * Fires at the end of the RSS2 Feed Header.
	 * @since 2.0.0
	 */
	do_action( 'rss2_head');

	while( have_posts()) : the_post();
	?>
	<item>
		<title><?php the_title_rss() ?></title>
		<link><?php the_permalink_rss() ?></link>

		<?php
			$post_object = get_field('featured_painting');
			if( $post_object ):
				// override $post
				$post = $post_object;
				setup_postdata( $post );
		?>
			<?php
				$image = get_field('painting');

				if( !empty($image) ): ?>


					<enclosure url="<?php echo $image['url']; ?>" type="image/jpeg" length="0" />
					<content:encoded><![CDATA[<i><?php the_title(); ?></i>, <?php the_field('width'); ?> x <?php the_field('height'); ?>"]]></content:encoded>

			<?php endif; ?>

		    	<!-- <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		    	<span>Post Object Custom Field: <?php the_field('height'); ?></span> -->

		    <?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
		<?php endif; ?>

		<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>

		<?php the_category_rss('rss2') ?>

		<guid isPermaLink="false"><?php the_guid(); ?></guid>

		<?php $content = get_the_content_feed('rss2'); ?>
		<?php if ( strlen( $content ) > 0 ) : ?>
			<description><![CDATA[<?php the_field('introduction'); ?>]]></description>
		<?php else : ?>
			<description><![CDATA[<?php the_field('introduction'); ?>]]></description>
		<?php endif; ?>

		<?php rss_enclosure(); ?>
	<?php
	/**
	 * Fires at the end of each RSS2 feed item.
	 *
	 * @since 2.0.0
	 */
	do_action( 'rss2_item' );
	?>
	</item>
<?php endwhile; ?>
</channel>
</rss>
