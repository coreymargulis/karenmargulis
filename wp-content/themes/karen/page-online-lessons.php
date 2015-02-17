<?php
/*
 Template Name: Online Lessons
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

				<div id="inner-content" class="wrap cf">

						<div id="main" class="m-all t-2of3 d-5of7 cf" role="main">

							<h1 class="page-title" itemprop="headline"><?php the_title(); ?></h1>
							<ul class="lessons">
							    <?php
							        $args = array( 'post_type' => 'product', 'posts_per_page' => 50, 'product_cat' => 'demo', 'orderby' => 'date' );
							        $loop = new WP_Query( $args );
							        while ( $loop->have_posts() ) : $loop->the_post(); global $product; 
							    ?>

				                <li class="lesson">    
				
				                    <a href="<?php echo get_permalink( $loop->post->ID ) ?>" title="<?php echo esc_attr($loop->post->post_title ? $loop->post->post_title : $loop->post->ID); ?>">
				
					                    <?php woocommerce_show_product_sale_flash( $post, $product ); ?>
										
										<?php if (has_post_thumbnail( $loop->post->ID )) echo get_the_post_thumbnail($loop->post->ID, 'shop_catalog'); else echo '<img src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" width="300px" height="300px" />'; ?>
					
					                    <h3><?php the_title(); ?></h3>
					
										<span class="price"><?php echo $product->get_price_html(); ?></span>                    
				
				                    </a>
				
				                    <?php woocommerce_template_loop_add_to_cart( $loop->post, $product ); ?>
				
				                </li>
		
								<?php endwhile; ?>
								<?php wp_reset_query(); ?>
								
							</ul><!--/demos-->
						</div>

				</div>

			</div>


<?php get_footer(); ?>
