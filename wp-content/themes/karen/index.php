<?php get_header(); ?>

	<div id="content">

		<div id="inner-content" class="wrap cf">

				<div id="main" class="m-all t-all d-all cf" role="main">

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

					<article id="post-<?php the_ID(); ?> list" <?php post_class( 'cf' ); ?> role="article">

						<section class="featured-image-wrap">
								
							<?php

								$post_object = get_field('featured_painting');

								if( $post_object ): 
								 
									// override $post
									$post = $post_object;
									setup_postdata( $post ); 
								 
							?>

						    <div class="featured-image">

						    	<!-- <a href="<?php the_permalink(); ?>"> -->
						    		<?php 
							    		$image = get_field('painting');

											if( !empty($image) ): ?>

											<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />

							    	<?php endif; ?>
						    	<!-- </a> -->
						    	
						    	<div class="featured-image-caption">
									<?php the_title(); ?>, <?php the_field('width'); ?> x <?php the_field('height'); ?> <a class="price" href="<?php the_permalink(); ?>">$<?php echo get_post_meta( get_the_ID(), '_regular_price', true ); ?></a>
						    	</div>

						    </div>
						    
						    <?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
							
							<?php endif; ?>
											
						</section>

						<section class="entry-content cf">

							<p class="byline vcard">
                                <?php printf( __( '<time class="updated" datetime="%1$s" pubdate>%2$s</time>', 'bonestheme' ), get_the_time('Y-m-j'), get_the_time(get_option('date_format')), get_the_author_link( get_the_author_meta( 'ID' ) )); ?>
								<?php printf( '<span class="category">' . __('', 'bonestheme' ) . '%1$s</span>' , get_the_category_list(', ') ); ?>
							</p>
							<h1><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
							
							<p><?php the_field('introduction') ?></p>

						</section>

						<footer class="article-footer cf">
							<!-- <p class="footer-comment-count">
								<?php comments_number( __( '<span>No</span> Comments', 'bonestheme' ), __( '<span>One</span> Comment', 'bonestheme' ), __( '<span>%</span> Comments', 'bonestheme' ) );?>
							</p> -->


         	

          <?php the_tags( '<p class="footer-tags tags"><span class="tags-title">' . __( 'Tags:', 'bonestheme' ) . '</span> ', ', ', '</p>' ); ?>


						</footer>

					</article>

					<?php endwhile; ?>

							<?php bones_page_navi(); ?>

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


				</div>

<!-- 					<?php get_sidebar(); ?> -->

		</div>

	</div>


<?php get_footer(); ?>
