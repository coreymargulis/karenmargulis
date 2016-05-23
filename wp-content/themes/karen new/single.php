<?php get_header(); ?>

			<div id="content">

				<div id="inner-content">

					<div id="main" role="main">

						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>">

			          <header class="article-header">

									<section class="featured-image-container">

										<?php
										$relationships = get_field('featured_painting');
										if( $relationships ): ?>

										<div class="featured-image">
									    <?php foreach( $relationships as $post): // variable must be called $post (IMPORTANT) ?>
								        <?php setup_postdata($post);
													$image = get_field('painting');
													$available = get_field('available');

													if( !empty($image) ): ?>
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
														</div>
													<?php endif; ?>
									    <?php endforeach; ?>
									    <?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
										</div>
										<?php endif; ?>


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
										<button type="submit" id="share" class="secondary">Share</button>
										<button type="submit" id="comments" class="secondary"><?php comments_number( __( 'Comment', 'bonestheme' ), __( '<span>1</span> Comment', 'bonestheme' ), __( '<span>%</span> Comments', 'bonestheme' ) );?></button>
										<div class="share-modal">
											<div id="twitter"><a href="https://twitter.com/intent/tweet?text=&ldquo;<?php the_title();?>&rdquo;&via=karenmargulis&url=<?php the_permalink();?>">Share on Twitter</a></div>
											<div id="facebook"><div data-href="<?php the_permalink(); ?>" data-layout="link" data-mobile-iframe="true"></div></div>

											<a href="https://www.facebook.com/dialog/share?app_id=0&href=<?php the_permalink(); ?>&display=popup" target="_blank">
  Share on Facebook
</a>

								</footer> <?php // end article footer ?>

								<footer class="comments">
									<div class="wrap">
										<?php comments_template(); ?>
									</div>
								</footer>

			        </article> <?php // end article ?>

							<footer class="more-reading">

			          <?php
									$prev_post = get_next_post();
									if (!empty( $prev_post )): ?>

									<a href="<?php echo get_permalink( $prev_post->ID ); ?>">

										<section id="read-next">

											<div id="next-post" class="wrap">

												<h3>Next Post</h3>
												<h2><?php echo $prev_post->post_title; ?></h2>

											</div>

										</section>

									</a>

								<?php else: ?>

								<section id="related-posts">

									<?php
										$related_args = array(
											'posts_per_page' => 3,
											'orderby' => 'rand',
											'category__in' => wp_get_post_categories($post->ID),
											'post_status' => 'publish',
											'post__not_in' => array($post->ID)
										);
										$related = new WP_Query( $related_args );

										if( $related->have_posts() ) :
									?>

									<div class="wrap"><h3>Related Posts</h3></div>

										<?php while( $related->have_posts() ): $related->the_post(); ?>

										<?php //image goes here ?>

										<section id="related-post" class="wrap">

											<p class="byline">
													<!-- <?php printf( __( '<time class="updated" datetime="%1$s" pubdate>%2$s</time>', 'bonestheme' ), get_the_time('Y-m-j'), get_the_time(get_option('date_format')), get_the_author_link( get_the_author_meta( 'ID' ) )); ?> -->
												<!-- <?php printf( '<span class="category">' . __('', 'bonestheme' ) . '%1$s</span>' , get_the_category_list(', ') ); ?> -->
											</p>

											<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
											<!-- <?php echo custom_field_excerpt(); ?> -->
											<!-- <a href="<?php the_permalink() ?>" id="excerpt-more">Continue Reading</a> -->

										</section>

									<?php endwhile; ?>

								</section> <?php //end related posts ?>

									<?php
										endif;
										wp_reset_postdata();
									?>

								<?php endif; ?>

							<?php endwhile; ?>

						</footer> <?php //end read more footer ?>

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
