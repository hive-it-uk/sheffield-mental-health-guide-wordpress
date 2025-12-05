<?php

/**
 * Specifiy the allowed block types that can be used in the editor
 */

declare(strict_types=1);

add_filter(
    'allowed_block_types',
    static function (): array {
        return [
            'core/buttons',
            'core/column',
            'core/columns',
            'core/embed',
            'core/file',
            'core/heading',
            'core/image',
            'core/list',
            'core/paragraph',
            'core/quote',
            'core/separator',
            'core/table',
            'pb/accordion-item',
        ];
    }
);

add_action(
    'after_setup_theme',
    static function (): void {
        add_theme_support('disable-custom-colors');
        add_theme_support(
            'editor-color-palette',
            [
                [
                    'name' => __('Green', 'themeLangDomain'),
                    'slug' => 'green',
                    'color' => '#1FA641',
                ],
                [
                    'name' => __('Dark green', 'themeLangDomain'),
                    'slug' => 'dark-green',
                    'color' => '#1f8740',
                ],
                [
                    'name' => __('Pink', 'themeLangDomain'),
                    'slug' => 'pink',
                    'color' => '#F272B8',
                ],
                [
                    'name' => __('Dark Blue', 'themeLangDomain'),
                    'slug' => 'dark-blue',
                    'color' => '#233567',
                ],
                [
                    'name' => __('Light blue', 'themeLangDomain'),
                    'slug' => 'light-blue',
                    'color' => '#4271df',
                ],
                [
                    'name' => __('Yellow', 'themeLangDomain'),
                    'slug' => 'yellow',
                    'color' => '#F2B705',
                ],
                [
                    'name' => __('Red', 'themeLangDomain'),
                    'slug' => 'red',
                    'color' => '#F23827',
                ],
                [
                    'name' => __('Black', 'themeLangDomain'),
                    'slug' => 'black',
                    'color' => '#000000',
                ],
                [
                    'name' => __('Light grey', 'themeLangDomain'),
                    'slug' => 'light-grey',
                    'color' => '#DEDEDE',
                ],
                [
                    'name' => __('White', 'themeLangDomain'),
                    'slug' => 'white',
                    'color' => '#FFFFFF',
                ],
            ]
        );
        add_theme_support('editor-gradient-presets', []);
        add_theme_support('disable-custom-gradients');
    }
);
