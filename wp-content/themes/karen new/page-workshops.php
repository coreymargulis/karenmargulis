<?php
/*
 Template Name: Workshops & Classes
*/
?>

<?php get_header(); ?>

			<div id="content">

				<div id="inner-content">

						<main id="main">

							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<section id="learn-intro" <?php post_class( 'wrap' ); ?>>

								<header class="article-header">
									<h1 class="page-title"><?php the_title(); ?></h1>
								</header>

								<section class="entry-content">
									<?php	the_content(); ?>
								</section>

							</section>

							<section id="workshops" class="wrap">

								<h3>Upcoming Workshops</h3>

								<?php
									$related_args = array(
										'numberposts' => 0,
										'post_type' => 'workshops',
										'post_status' => 'publish',
										'post__not_in' => array($post->ID),
										'meta_key'	=> 'start_date',
										'orderby'	=> 'meta_value_num',
										'order'		=> 'ASC'
									);
									$related = new WP_Query( $related_args );

									if( $related->have_posts() ) :
								?>

								<?php while( $related->have_posts() ): $related->the_post(); ?>

								<div class="article-preview">

									<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
									<div><?php the_field('location'); ?></div>

									<?php
									// get raw date
									$end_date = get_field('end_date');

									// make date object
									$end_date = new DateTime($end_date);

									$end_date_ya = get_field('end_date');
									?>

									<div>
										<?php the_field('start_date');?><?php if ( $end_date_ya !== '' ) { echo "–", $end_date->format('j'); } ?>
									</div>

									<a href="<?php the_permalink() ?>" id="excerpt-more">Learn more...</a>

								</div>

								<?php endwhile; ?>

							</section>

							<?php
								endif;
								wp_reset_postdata();
							?>

							<section id="classes" class="wrap">
								<h3>Upcoming Classes</h3>
								<p>No upcoming classes</p>
							</section>

							<?php endwhile; else : ?>

									<article id="post-not-found" class="hentry cf">
											<header class="article-header">
												<h1><?php _e( 'Oops, Post Not Found!', 'bonestheme' ); ?></h1>
										</header>
											<section class="entry-content">
												<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'bonestheme' ); ?></p>
										</section>
										<footer class="article-footer">
												<p><?php _e( 'This is the error message in the page-custom.php template.', 'bonestheme' ); ?></p>
										</footer>
									</article>

							<?php endif; ?>

						</main>

				</div>

			</div>


<?php get_footer(); ?>
