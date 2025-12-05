<?php

declare(strict_types=1);

namespace SMHG;

add_action(
    'init',
    static function (): void {
        register_rest_field(
            'team_member',
            'acfMeta',
            [
                'get_callback' => static function (array $post) {
                    $postId = $post['id'];

                    return [
                        'job_title' => get_post_meta($postId, TeamMemberMeta::JOB_TITLE, true),
                    ];
                },
            ]
        );
    }
);
