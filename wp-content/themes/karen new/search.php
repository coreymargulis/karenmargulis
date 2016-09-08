<?php get_header(); ?>

			<div id="content">

				<div id="inner-content" class="wrap cf">

					<main id="main" role="main">
						<h1 class="archive-title"><span><?php _e( 'Search Results for', 'bonestheme' ); ?></span> <span id="search-query"><?php echo esc_attr(get_search_query()); ?></span></h1>

						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>" <?php post_class('cf'); ?> role="article">

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

								<!-- <footer class="article-footer">

									<?php if(get_the_category_list(', ') != ''): ?>
                  					<?php printf( __( 'Filed under: %1$s', 'bonestheme' ), get_the_category_list(', ') ); ?>
                  					<?php endif; ?>

                 					<?php the_tags( '<p class="tags"><span class="tags-title">' . __( 'Tags:', 'bonestheme' ) . '</span> ', ', ', '</p>' ); ?>

								</footer> -->

							</article>

						<?php endwhile; ?>

								<?php bones_page_navi(); ?>

							<?php else : ?>

									<article id="post-not-found" class="hentry cf">
										<header class="article-header">
											<h2><?php _e( 'Sorry, no results found.', 'bonestheme' ); ?></h2>
										</header>
										<section class="entry-content">
											<p><?php _e( 'Try your search again.', 'bonestheme' ); ?></p>
											<form role="search" method="get" class="search-form" action="<?php echo home_url( '/' ); ?>">
												<label>
													<input type="search" class="search-field" id="search-field" placeholder="<?php echo esc_attr_x( 'Search', 'placeholder' ) ?>" value="<?php echo get_search_query() ?>" name="s" title="<?php echo esc_attr_x( 'Press Enter to Search', 'label' ) ?>" autofocus/>
													<i class="ion-search"></i>
												</label>
											</form>
										</section>
										<footer class="article-footer">

										</footer>
									</article>

							<?php endif; ?>

						</main>


					</div>

			</div>

<?php get_footer(); ?>
