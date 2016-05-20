<?php get_header(); ?>

			<div id="content">

				<div id="inner-content">

					<div id="main" role="main">

						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>">

			          <header class="article-header">

									<section class="featured-image-container">

										<?php
											$post_object = get_field('featured_painting');
											if( $post_object ):
												// override $post
												$post = $post_object;
												setup_postdata( $post );
										?>

									  <div class="featured-image">

							    		<?php
								    		$image = get_field('painting');

												if( !empty($image) ): ?>
													<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
								    		<?php endif; ?>

									    	<div class="featured-image-caption">
													<div id="caption">
														<i><?php the_title(); ?></i><br><?php the_field('width'); ?> x <?php the_field('height'); ?>" pastel
													</div>
													<div id="price">
														<a href="<?php the_field('etsy_link'); ?>">$<?php the_field('price'); ?></a>
										    	</div>
												</div>

									  </div>

									  <?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
									<?php endif; // end featured image ?>

									</section>

			          </header> <?php // end article header ?>

                <section class="entry-content wrap">

									<p class="byline">
	                  <?php printf( __( '<time class="updated" datetime="%1$s" pubdate>%2$s</time>', 'bonestheme' ), get_the_time('Y-m-j'), get_the_time(get_option('date_format')), get_the_author_link( get_the_author_meta( 'ID' ) )); ?>
	                </p>

				          <h1 class="entry-title single-title"><?php the_title(); ?></h1>

                	<?php the_field('introduction'); ?>

									<?php

										// check if the flexible content field has rows of data
										if( have_rows('additional_content') ):

										     // loop through the rows of data
										    while ( have_rows('additional_content') ) : the_row();

										        if( get_row_layout() == 'text' ):

										        	the_sub_field('text');

										        elseif( get_row_layout() == 'image' ):

															$image = get_sub_field('image');

															if( !empty($image) ): ?>
															<div class="inset-center">
																<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
																<div id="caption"><?php the_sub_field('caption'); ?></div>
															</div>
															<?php endif;

										        endif;

										    endwhile;

										else :

										    // no layouts found

										endif;

										?>

                </section> <?php // end article section ?>

			          <footer class="article-footer wrap">

			            <?php the_tags( '<p class="tags"><span class="tags-title">' . __( 'Tags:', 'bonestheme' ) . '</span> ', ', ', '</p>' ); ?>
										<button type="submit" class="secondary">Share</button>
										<button type="submit" class="secondary">Comment</button>
			            </footer> <?php // end article footer ?>

								<?php // comments go here eventually ?>

			        </article> <?php // end article ?>

		          <?php
								$prev_post = get_previous_post();
								if (!empty( $prev_post )): ?>

								<a href="<?php echo get_permalink( $prev_post->ID ); ?>">

									<section id="read-next">

										<div id="next-post" class="wrap">

											<h3>Next Post</h3>
											<h2><?php echo $prev_post->post_title; ?></h2>

										<?php endif; ?>

										</div>

									</section>

								</a>



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
											<p><?php _e( 'This is the error message in the single.php template.', 'bonestheme' ); ?></p>
									</footer>
							</article>

						<?php endif; ?>

					</div>

				</div>

			</div>

<?php get_footer(); ?>
