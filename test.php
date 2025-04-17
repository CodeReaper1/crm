<?php
/**
 * Template Name: Homepage
 * 
 * @package Testo
 */

get_header(); ?>

<!-- PAGE CONTENT -->
<div id="page" class="page">

    <!-- HERO SECTION -->
    <section id="hero-2" class="bg-fixed hero-section division">
        <div class="bg-fixed bg-inner division">
            <!-- HERO TEXT -->
            <div class="container">							
                <div class="row">
                    <div class="col-lg-10 offset-lg-1">
                        <div class="hero-2-txt text-center">
                            <h2 class="dark-color shadow-txt-white" style="color: black"><?php echo get_theme_mod('hero_title', 'Изживей всяка хапка!'); ?></h2>
                            <div class="hero-2-img">
                                <?php 
                                $hero_image = get_theme_mod('hero_image', get_template_directory_uri() . '/images/hero-2-img.png');
                                ?>
                                <img class="img-fluid" src="<?php echo esc_url($hero_image); ?>" alt="hero-image">
                                
                                <!-- Price Badge -->
                                <div class="price-badge-sm bg-fixed white-color1">
                                    <div class="badge-txt">

                                    <img class="img-fluid" src="https://food-hub.bg/wp-content/themes/foodhub/images/ikonka.png" alt="hero-image">

                                        <!--                                        <h5 class="h5-md">From</h5>
                                        <h4 class="h4-lg"><?php //echo get_theme_mod('hero_price', '$6.99'); ?></h4>
 -->
                                    </div>
                                </div>
                            </div>
                        </div>  
                    </div>	 
                </div>
            </div>

            <!-- SECTION OVERLAY -->	
            <div class="bg-fixed white-overlay-wave"></div>
        </div>
    </section>
    <?php get_menu_section(); ?>

    <!-- FEATURED PRODUCTS -->
    <?php 
    // Get featured products from custom post type
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 6,
        'meta_key' => '_featured',
        'meta_value' => 'yes'
    );
    $featured_products = new WP_Query($args);
    
    if ($featured_products->have_posts()) : ?>
    <section id="promo-14" class="wide-100 promo-section division">
        <div class="container">
            <div class="row d-flex align-items-center">
                <?php while ($featured_products->have_posts()) : $featured_products->the_post(); ?>
                    <div class="col-lg-4">
                        <div class="pbox-14-item">
                            <div class="pbox-14-img rel">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('full', array('class' => 'img-fluid')); ?>
                                <?php endif; ?>
                                
                                <!-- Price -->
                                <?php $price = get_post_meta(get_the_ID(), '_price', true); ?>
                                <div class="pbox-14-price bg-coffee">
                                    <h5 class="h5-xs yellow-color">$<?php echo esc_html(number_format($price, 2)); ?></h5>
                                </div>
                            </div>

                            <div class="pbox-14-txt rel">
                                <h5 class="h5-md"><?php the_title(); ?></h5>
                                <p class="grey-color"><?php echo wp_trim_words(get_the_content(), 20); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ABOUT SECTION 3 -->
    <section style="background-color: #040404" id="about-3" class="wide-60 about-section division">
        <div class="container">
            <div class="row d-flex align-items-center">
                <div class="col-md-5 col-lg-6">
                    <div class="about-3-img text-center mb-40">
                        <?php 
                        $about_image = get_theme_mod('about_section_image', get_template_directory_uri() . '/images/about-03-img.png');
                        ?>
                        <img class="img-fluid" src="https://food-hub.bg/wp-content/uploads/2025/01/glovo.jpg" alt="about-image">
                    </div>
                </div>

                <div class="col-md-7 col-lg-6">
                    <div class="about-3-txt mb-40">
                        <?php
                        $about3_title = get_theme_mod('about_section_title', 'Довери се на Бургери номер 1 в град Плевен!');
                        $about3_text = get_theme_mod('about_section_text', 'През 2024 нашият ресторант беше избран за ‘Най-доброто място за бургери’ в платформата Glovo! Тази награда е признание за качеството, вкуса и любовта, които влагаме във всяка една поръчка. Благодарим на нашите клиенти за доверието и подкрепата!');
                        ?>
                        <h2 class="h2-sm"><?php echo esc_html($about3_title); ?></h2>
                        <p class="p-md"><?php echo esc_html($about3_text); ?></p>
<a href="https://glovoapp.com/bg/en/pleven/food-hub-pvn/" target="_blank">
    <button style="color: black; background-color: #FA9722; font-weight: bold; font-size: 1.3rem; border: 0px;">
        Поръчай от Glovo
    </button>
</a>

                        <?php
                        // Get feature list from WordPress options
                        $features = get_option('about_features', array());
                        if (!empty($features)) : ?>
                        <ul class="txt-list">
                            <?php foreach ($features as $feature) : ?>
                            <li class="list-item">
                                <p class="p-md"><?php echo esc_html($feature); ?></p>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- GALLERY SECTION -->
	
    <?php get_gallery_section(); ?>

    <!-- DELIVERY BANNER -->
    <section style = "
    min-height: 320px;
    display: flex;
    align-items: center;
    justify-content: center;
" id="banner-4" class="bg-fixed wide-100 banner-section division">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 col-xl-8 offset-lg-1 offset-xl-2">
                    <div class="banner-4-txt text-center">
                        <?php
                       /* $delivery_title = get_theme_mod('delivery_title', ' доставка до 60 минути.');
                        $delivery_text = get_theme_mod('delivery_text', ' Поръчай сега!
'); */
                        $delivery_phone = get_theme_mod('delivery_phone', '0876 76 35 35');
                        ?>
 
                        <a href="tel:<?php echo esc_attr($delivery_phone); ?>" class="btn btn-lg tra-red-hover" style="background-color: black;">
                            <?php echo esc_html__('Поръчай Сега!', 'testo'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div><!-- END PAGE CONTENT -->

<?php get_footer(); ?>