<?php
/*
 Template Name: Home
 *
 * This is your custom page template. You can create as many of these as you need.
 * Simply name is "page-whatever.php" and in add the "Template Name" title at the
 * top, the same way it is here.
 *
 * When you create your page, you can just select the template and viola, you have
 * a custom page template to call your very own. Your mother would be so proud.
 *
 * For more info: http://codex.wordpress.org/Page_Templates
*/
?>

<?php get_header(); ?>

			<div id="content">
				<div id="featured-image-home-container">
				<?php
					$args = array(
				    'post_type' => 'product',
				    'meta_key' => '_featured',
				    'meta_value' => 'yes',
				    'posts_per_page' => 1,
				    'orderby' => 'rand'
				);

				$featured_query = new WP_Query( $args );

				if ($featured_query->have_posts()) :

				    while ($featured_query->have_posts()) :

				        $featured_query->the_post();

				        $product = get_product( $featured_query->post->ID );

				        // Output product information here

							// do_action( 'woocommerce_before_single_product_summary' );
			        		$image = get_field('painting');

							$url = $image['url'];
							$alt = $image['alt'];
							$caption = $image['caption'];

							// thumbnail
							$size = 'large';
							$thumb = $image['sizes'][ $size ];

							if( !empty($image) ):
							echo '<div id="featured-image-home" style="background-image: url(' . $thumb . ')">';
								//echo '<img src="' . $thumb . '" alt="' . $alt . '" />';

								endif;

								?>

								<div class="featured-image-caption">
									<?php the_title(); ?>, <?php the_field('width'); ?> x <?php the_field('height'); ?> <a class="price" href="<?php the_permalink(); ?>">$<?php echo get_post_meta( get_the_ID(), '_regular_price', true ); ?></a>
						    	</div>
						    </div>


				    <?php endwhile;

				endif;

				wp_reset_query(); // Remember to reset
				?>
				</div>

				<div id="inner-content" class="wrap cf">

					<div id="main" class="m-all t-all d-all cf" role="main">

						<section class="about-home">
							<h1>Welcome</h1>
							<p>This is where I introduce myself.</p>
						</section>

					</div>

				</div>

			</div>


<?php get_footer(); ?>
