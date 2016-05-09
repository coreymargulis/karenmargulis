<?php
/*
 Template Name: Workshops & Classes
*/
?>

<?php get_header(); ?>

			<div id="content">

				<div id="inner-content" class="wrap cf">

						<main id="main">

							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

								<header class="article-header">

									<h1 class="page-title"><?php the_title(); ?></h1>

								</header>

								<section class="entry-content cf">
									<?php
										// the content (pretty self explanatory huh)
										the_content();
									?>
								</section>

							</article>

							<section class="workshops">
								<h3>Upcoming Workshops</h3>

								<?php
									$related_args = array(
										'numberposts' => 0,
										'post_type' => 'workshops',
										'post_status' => 'publish',
										'post__not_in' => array($post->ID)
									);
									$related = new WP_Query( $related_args );

									if( $related->have_posts() ) :
								?>

								<?php while( $related->have_posts() ): $related->the_post(); ?>

									<div class="article-preview">

										<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
										<?php echo custom_field_excerpt(); ?>
										<a href="<?php the_permalink() ?>" id="excerpt-more">Read more...</a>

									</div>

								<?php endwhile; ?>

							</section>

							<?php
								endif;
								wp_reset_postdata();
							?>

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
