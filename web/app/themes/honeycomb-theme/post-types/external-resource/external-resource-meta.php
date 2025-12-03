<?php

declare(strict_types=1);

namespace SMHG;

add_action(
    'init',
    static function (): void {
        register_rest_field(
            'external_resource',
            'acfMeta',
            [
                'get_callback' => static function (array $post) {
                    $postId = $post['id'];
                    return [
                        'url' => get_post_meta($postId, ExternalResourceMeta::URL, true),
                        'file' => get_field(ExternalResourceMeta::FILE),
                        'type' => get_post_meta($postId, ExternalResourceMeta::TYPE, true),
                        'openInNewTab' => get_field(ExternalResourceMeta::NEWTAB) === 'true',
                    ];
                },
            ]
        );
    }
);
