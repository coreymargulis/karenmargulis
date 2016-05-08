<?php get_header(); ?>

			<div id="content">

				<div id="inner-content">

					<div id="main" role="main">

						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>">

			          <header class="article-header">

									<section class="featured-image-wrap">

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
												<?php the_title(); ?>, <?php the_field('width'); ?> x <?php the_field('height'); ?> <a class="price" href="<?php the_permalink(); ?>">$<?php echo get_post_meta( get_the_ID(), '_regular_price', true ); ?></a>
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

                	<p><?php the_field('introduction'); ?></p>

                </section> <?php // end article section ?>

			          <footer class="article-footer wrap">

			            <?php the_tags( '<p class="tags"><span class="tags-title">' . __( 'Tags:', 'bonestheme' ) . '</span> ', ', ', '</p>' ); ?>
										<button type="submit" class="secondary">Comments</button>
										<button type="submit" class="secondary">Share</button>
			            </footer> <?php // end article footer ?>

								<?php // comments go here eventually ?>

			        </article> <?php // end article ?>

			        <section id="read-next" class="wrap">

              	<div id="next-post">
									<h4>Next Post</h4>

				          <?php
										$prev_post = get_previous_post();
										if (!empty( $prev_post )): ?>
										  <h2><a href="<?php echo get_permalink( $prev_post->ID ); ?>"><?php echo $prev_post->post_title; ?></a></h2>
									<?php endif; ?>

								</div>

              	<!-- <div id="prev-post">
              		<h4>Previous Post</h4>
              	</div> -->

			      	</section>

						<?php endwhile; ?>


						<section class="related-posts">

			        <?php
								$related_args = array(
									'numberposts' => 3,
									'orderby' => 'rand',
									'category__in' => wp_get_post_categories($post->ID),
									'post_status' => 'publish',
									'post__not_in' => array($post->ID)
								);
								$related = new WP_Query( $related_args );

								if( $related->have_posts() ) :
							?>

							<h4 class="wrap">Related Posts</h4>

								<?php while( $related->have_posts() ): $related->the_post(); ?>

								<?php //image goes here ?>

									<section id="related-post" class="wrap">

										<p class="byline vcard">
						                    <?php printf( __( '<time class="updated" datetime="%1$s" pubdate>%2$s</time>', 'bonestheme' ), get_the_time('Y-m-j'), get_the_time(get_option('date_format')), get_the_author_link( get_the_author_meta( 'ID' ) )); ?>
						                	<?php printf( '<span class="category">' . __('', 'bonestheme' ) . '%1$s</span>' , get_the_category_list(', ') ); ?>
						                </p>

										<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
										<?php echo custom_field_excerpt(); ?>
 										<a href="<?php the_permalink() ?>" id="excerpt-more">Continue Reading</a>

									</section>


								</section>

							<?php endwhile; ?>

						</section>

						<?php
							endif;
							wp_reset_postdata();
						?>

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
