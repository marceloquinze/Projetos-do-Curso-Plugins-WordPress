<?php get_header(); ?>
<div class="mv-testimonials-single">
    <header class="entry-header">
        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
    </header>

        <?php
            while( have_posts() ):
                the_post();

                $url_meta = get_post_meta( get_the_ID(), 'mv_testimonials_user_url', true );
                $occupation_meta = get_post_meta( get_the_ID(), 'mv_testimonials_occupation', true );
                $company_meta = get_post_meta( get_the_ID(), 'mv_testimonials_company', true );
    ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="testimonial-item">
                <div class="content">
                        <div class="thumb">
                            <?php 
                                if( has_post_thumbnail()){
                                    the_post_thumbnail( array( 200, 200 ), array( 'class' => 'img-fluid' ) );
                                } 
                            ?>
                        </div>
                    <?php the_content(); ?>
                </div>
                <div class="meta">
                        <span class="occupation"><?php echo esc_html( $occupation_meta ); ?></span>
                        <span class="company"><a href="<?php echo esc_attr( $url_meta ) ?>"><?php echo esc_html( $company_meta ); ?></a></span>          
                </div>
            </div>
        </article>
    <?php 
            endwhile; 
    ?>

</div>
<?php get_footer(); ?>