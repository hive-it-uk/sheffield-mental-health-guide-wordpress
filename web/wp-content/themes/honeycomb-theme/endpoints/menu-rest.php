<?php

declare(strict_types=1);

namespace SMHG;

/**
 * @return \SMHG\MenuItem[]
 */
function getMenuItems($id): array
{
    $items = wp_get_nav_menu_items($id);

    $groups = [];

    foreach ($items as $item) {
        $groups[$item->menu_item_parent][] = $item;
    }

    return getChildMenuItems(0, $groups);
}

/**
 * @return \SMHG\MenuItem[]
 */
function getChildMenuItems(int $parentId, array $menuItemGroups): array
{
    $menuItems = [];

    if (!isset($menuItemGroups[$parentId])) {
        return [];
    }

    foreach ($menuItemGroups[$parentId] as $menuItem) {
        $menuItems[] = new MenuItem(
            $menuItem->url,
            $menuItem->title,
            $menuItem->target,
            getChildMenuItems($menuItem->ID, $menuItemGroups)
        );
    }

    return $menuItems;
}

add_action(
    'wp_update_nav_menu',
    static fn(int $id) => delete_transient("menu-{$id}"),
);

add_action(
    'wp_delete_nav_menu',
    static fn(int $id) => delete_transient("menu-{$id}"),
);

add_action(
    'rest_api_init',
    static function (): void {
        register_rest_route(
            'smhg/v1',
            '/menus/(?P<id>[a-zA-Z0-9_-]+)',
            [
                'methods' => \WP_REST_Server::READABLE,
                'args' => [
                    'id' => [
                        'required' => true,
                        'validate_callback' => static fn($param) => is_nav_menu($param),
                    ],
                ],
                'permission_callback' => '__return_true',
                'callback' => static function (\WP_REST_Request $request): array {
                    $params = $request->get_url_params();

                    $menu = wp_get_nav_menu_object($params['id']);

                    // Cache menu items as these are fetched many times
                    // during the build and can cause 500 errors to be thrown.
                    $transientId = "menu-{$menu->term_id}";
                    $menuItems = get_transient($transientId);

                    if (!$menuItems) {
                        $menuItems = getMenuItems($menu->term_id);
                        set_transient($transientId, $menuItems, 1 * HOUR_IN_SECONDS);
                    }

                    return $menuItems;
                },
            ]
        );
    }
);
