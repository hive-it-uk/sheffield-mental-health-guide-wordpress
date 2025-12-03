<?php

declare(strict_types=1);

namespace SMHG;

/**
 * Register a taxonomy.
 * Also registers a REST field for the taxonomy. Which hides the taxonomy from the REST API when the taxonomy is empty.
 */
function registerTaxonomy(string $taxonomyType, array $config): void
{
    if (!isset($config['postTypes']) || !isset($config['title'])) {
        return;
    }

    $title = $config['title'];
    $titlePlural = $config['titlePlural'] ?? $config['title'] . 's';
    $namePlural = strtolower($titlePlural);
    register_taxonomy(
        $taxonomyType,
        $config['postTypes'],
        [
            'labels' => [
                'name' => $titlePlural,
                'singular_name' => $title,
                'search_items' => 'Search ' . $titlePlural,
                'popular_items' => 'Popular ' . $titlePlural,
                'all_items' => 'All ' . $titlePlural,
                'parent_item' => null,
                'parent_item_colon' => null,
                'edit_item' => 'Edit ' . $title,
                'update_item' => 'Update ' . $title,
                'add_new_item' => 'Add a new ' . $title,
                'new_item_name' => 'New ' . $title . ' Name',
                'separate_items_with_commas' => 'Separate ' . $namePlural . ' with commas',
                'add_or_remove_items' => 'Add or remove ' . $namePlural,
                'choose_from_most_used' => 'Choose from the most used ' . $namePlural,
                'menu_name' => $titlePlural,
            ],
            'hierarchical' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var' => true,
            'rewrite' => ['slug' => $taxonomyType],
        ]
    );

    add_filter(
        'rest_' . $taxonomyType . '_query',
        static function (array $args) {
            if (is_user_logged_in()) {
                return $args;
            }

            $args['hide_empty'] = true;
            return $args;
        },
        10,
        2
    );
}

add_action(
    'init',
    static function (): void {
        // Age Taxonomy.
        registerTaxonomy(
            Taxonomies::AUDIENCE,
            [
                'postTypes' => ['service', 'activity'],
                'title' => 'Age Range',
            ]
        );

        // Activity Category Taxonomy.
        registerTaxonomy(
            Taxonomies::ACTIVITY_CATEGORY,
            [
                'postTypes' => ['activity'],
                'title' => 'Activity Category',
                'titlePlural' => 'Activity Categories',
            ]
        );

        // Activity Type Taxonomy.
        registerTaxonomy(
            Taxonomies::ACTIVITY_TYPE,
            [
                'postTypes' => ['activity'],
                'title' => 'Activity Type',
            ]
        );

        // Service Category Taxonomy.
        registerTaxonomy(
            Taxonomies::SERVICE_CATEGORY,
            [
                'postTypes' => ['service', 'resource', 'external_resource'],
                'title' => 'Service Category',
                'titlePlural' => 'Service Categories',
            ]
        );

        // Service Type Taxonomy.
        registerTaxonomy(
            Taxonomies::SERVICE_TYPE,
            [
                'postTypes' => ['service'],
                'title' => 'Service Type',
            ]
        );

        // Team Member Category Taxonomy.
        registerTaxonomy(
            Taxonomies::TEAM_CATEGORY,
            [
                'postTypes' => ['team_member'],
                'title' => 'Member Category',
                'titlePlural' => 'Member Categories',
            ]
        );
    }
);
