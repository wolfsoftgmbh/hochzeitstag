<?php
/**
 * Template Name: Hochzeitstag Full-Width
 * Template Post Type: page
 * Description: Displays the Hochzeitstag countdown in a full-width, blank page without theme header/footer.
 */

// We don't want any WordPress theme elements.
// So, we don't call get_header() or get_footer().
// We also don't call wp_head() or wp_footer() in the body,
// as the plugin's enqueue script will handle loading necessary CSS/JS.
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); // Only essential WordPress head elements, mostly for meta and enqueued scripts/styles ?>
    <style>
        /* Minimal reset to ensure full-width and no body padding/margin from theme */
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden; /* Prevent horizontal scroll on some layouts */
            background: none; /* Ensure no background from theme */
        }
        /* Hide common theme elements that might still load */
        #wpadminbar,
        .site-header,
        .site-footer,
        .site-info,
        .entry-header,
        .page-header,
        .comments-area,
        .sidebar,
        #secondary,
        .navigation,
        .main-navigation,
        .primary-menu,
        .footer-widgets,
        #footer {
            display: none !important;
        }
        /* Ensure the content takes full available space */
        .site-content, .content-area, .hentry, .entry-content, .page-content, #primary, #content {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
    </style>
</head>
<body>
    <?php 
    // This is where the shortcode content will be rendered.
    // The plugin's shortcode output should fill this space.
    if ( have_posts() ) {
        while ( have_posts() ) {
            the_post();
            the_content();
        }
    }
    ?>
    <?php wp_footer(); // Essential for enqueued scripts at the end of body ?>
</body>
</html>
