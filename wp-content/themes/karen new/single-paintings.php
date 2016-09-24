<?php get_header(); ?>

			<div id="content">

				<div id="inner-content" class="wrap cf">

						<main id="main" >

							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>" <?php post_class('cf'); ?> role="article">

								<?php
								$image = get_field('painting');
								$available = get_field('available');
								?>

								<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />

								<div class="featured-image-caption">
									<div id="caption">
										<i><?php the_title(); ?></i><br><?php the_field('width'); ?> x <?php the_field('height'); ?>" pastel
									</div>
											<?php if( !empty($available) ):
												?>
												<div class="price">
													<a href="<?php the_field('etsy_link'); ?>">
														$<?php the_field('price'); ?>
													</a>
												</div>
											<?php else: ?>
												<div class="price">
													<span id="sold">Sold</span>
												</div>
											<?php endif; ?>
										</a>
									</div>

							</article>

							<?php endwhile; ?>

							<?php else : ?>

									<article id="post-not-found" class="hentry cf">
										<header class="article-header">
											<h1><?php _e( 'Oops, Post Not Found!', 'bonestheme' ); ?></h1>
										</header>
										<section class="entry-content">
											<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'bonestheme' ); ?></p>
										</section>
										<footer class="article-footer">
											<p><?php _e( 'This is the error message in the single-custom_type.php template.', 'bonestheme' ); ?></p>
										</footer>
									</article>

							<?php endif; ?>

						</main>

				</div>

			</div>

<?php get_footer(); ?>
