<?php

declare(strict_types=1);

// Remove canonical redirects to stop WP "intelligently" trying to find closest-matching URLs.
remove_filter('template_redirect', 'redirect_canonical');

// Let WordPress manage the document title.
// By adding theme support, we declare that this theme does not use a
// hard-coded <title> tag in the document head, and expect WordPress to
// provide it for us.
add_theme_support('title-tag');
add_theme_support('menus');
add_theme_support('post-thumbnails');

// Remove out-of-the-box WordPress image sizes.
remove_image_size('medium_large');
remove_image_size('large');

// Unregister unused out-of-the-box image sizes.
add_filter(
    'intermediate_image_sizes_advanced',
    static function ($sizes) {
        unset($sizes['medium_large']); // 768px
        unset($sizes['large']); // 1024px
        return $sizes;
    }
);
