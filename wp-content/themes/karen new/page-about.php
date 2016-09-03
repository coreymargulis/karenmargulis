<?php
/*
 Template Name: About
*/
?>

<?php get_header(); ?>

			<div id="content">

				<div id="inner-content">

						<main id="main">

							<section id="learn-intro" class="wrap">

								<!-- <header class="article-header">
									<h1 class="page-title"><?php the_title(); ?></h1>
								</header> -->

								<section class="entry-content">
									<?php	the_field('intro'); ?>
								</section>

							</section>

							<section id="faq" class="wrap">

								<h3>Frequently Asked Questions</h3>

								<?php

									// check if the repeater field has rows of data
									if( have_rows('faqs') ):

									 	// loop through the rows of data
									    while ( have_rows('faqs') ) : the_row();

									        // display a sub field value
													?><div id="question"><?php the_sub_field('question'); ?></div>
													<div id="answer"><?php the_sub_field('answer'); ?></div><?php

									    endwhile;

									else :

									    // no rows found

									endif;

									?>

							</section>

						</main>

				</div>

			</div>


<?php get_footer(); ?>
