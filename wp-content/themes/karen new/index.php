<?php get_header(); ?>

			<div id="content">

				<div id="inner-content" class="wrap">

						<main id="main">

							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>">

								<a href="<?php the_permalink() ?>">

									<section class="featured-image-wrap">

										<?php
										$relationships = get_field('featured_painting');
										if( $relationships ): ?>
										<div class="featured-image">
									    <?php foreach( $relationships as $post): // variable must be called $post (IMPORTANT) ?>
								        <?php setup_postdata($post);
													$image = get_field('painting');

													if( !empty($image) ): ?>
														<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
													<?php endif; ?>
									    <?php endforeach; ?>
									    <?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
										<?php endif; ?>
										</div>

									</section>

								</a>

								<section class="article-preview">

									<p class="byline">
										<?php printf( __( '', 'bonestheme' ).' %1$s',
											/* the time the post was published */
											'<time class="updated entry-time" datetime="' . get_the_time('Y-m-d') . '" itemprop="datePublished">' . get_the_time(get_option('date_format')) . '</time>'
										); ?>
									</p>

									<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
									<?php echo custom_field_excerpt(); ?>
									<a href="<?php the_permalink() ?>" id="excerpt-more">Read more...</a>

								</section>

							</article>

							<?php endwhile; ?>

									<?php bones_page_navi(); ?>
									<div id="archive-message">
										Looking for my older posts? I am moving over some of my favorites, but a complete archive of all of my posts can be found at my old site <a href="http://www.kemstudios.blogspot.com" rel="Archive" target="_blank">here</a>.
									</div>
							<?php else : ?>

									<article id="post-not-found" class="hentry cf">
											<header class="article-header">
												<h1><?php _e( 'Oops, Post Not Found!', 'bonestheme' ); ?></h1>
										</header>
											<section class="entry-content">
												<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'bonestheme' ); ?></p>
										</section>
										<footer class="article-footer">
												<p><?php _e( 'This is the error message in the index.php template.', 'bonestheme' ); ?></p>
										</footer>
									</article>

							<?php endif; ?>

						</main>

				</div>

			</div>


<?php get_footer(); ?>
