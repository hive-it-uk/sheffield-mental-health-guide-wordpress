<?php

declare(strict_types=1);

use function SMHG\generateAccessToken;
use function SMHG\replaceFrontendUrl;

/** @var \WP_Post */
global $post;

// We render an iframe with the post that is being
// previewed and pass in any required query parameters
// to make the preview work, including the access token
// for authentication with the REST API.

$postTypeObject = get_post_type_object($post->post_type);

if (!is_user_logged_in() || !is_preview()) {
    $url = replaceFrontendUrl(get_permalink($post));
    header('Location:' . $url, true, 303);

    exit;
}

?>

<style>
body {
    margin: 0;
}
</style>

<?php

$slugBase = $postTypeObject->rewrite ? '/' . $postTypeObject->rewrite['slug'] : '';

$url = add_query_arg(
    [
        'id' => $post->ID,
        'post_type' => $post->post_type,
        'slug' => $slugBase . '/' . $post->ID,
        'token' => generateAccessToken(wp_get_current_user(), AUTH_PREVIEW_TOKEN_DURATION),
    ],
    APP_FRONTEND_URL . '/api/preview'
);
?>

<?php wp_head(); ?>

<iframe
  src="<?php echo $url; ?>"
  style="width:100%; height: 100%;"
></iframe>

<?php wp_footer(); ?>

