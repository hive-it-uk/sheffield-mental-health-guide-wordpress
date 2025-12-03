<?php

declare(strict_types=1);

namespace SMHG;

// Replace all WordPress URLs with frontend URLs.
// We need to do this this to ensure that SEO metadata
// links correctly to the frontend and not to the
// blank pages on the backend.

add_filter('wpseo_canonical', 'SMHG\replaceFrontendUrl');
add_filter('wpseo_opengraph_url', 'SMHG\replaceFrontendUrl');

add_filter(
    'wpseo_schema_organization',
    static function (array $data) {
        replaceFrontendUrlByReference($data['@id']);
        replaceFrontendUrlByReference($data['url']);
        replaceFrontendUrlByReference($data['logo']['@id']);
        replaceFrontendUrlByReference($data['image']['@id']);

        return $data;
    }
);

add_filter(
    'wpseo_schema_website',
    static function (array $data) {
        replaceFrontendUrlByReference($data['@id']);
        replaceFrontendUrlByReference($data['url']);
        replaceFrontendUrlByReference($data['publisher']['@id']);

        foreach ($data['potentialAction'] as $key => $action) {
            switch ($action['@type']) {
                case 'SearchAction':
                    $data['potentialAction'][$key]['target'] = str_replace(
                        WP_HOME . '/?s=',
                        APP_FRONTEND_URL . '/search?search=',
                        $action['target']
                    );
                    break;
            }
        }

        return $data;
    }
);

add_filter(
    'wpseo_schema_webpage',
    static function (array $data) {
        replaceFrontendUrlByReference($data['@id']);
        replaceFrontendUrlByReference($data['url']);
        replaceFrontendUrlByReference($data['isPartOf']['@id']);
        replaceFrontendUrlByReference($data['primaryImageOfPage']['@id']);

        foreach ($data['potentialAction'] as $actionKey => $action) {
            // phpcs:ignore SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable
            foreach ($action['target'] as $targetKey => $target) {
                replaceFrontendUrlByReference(
                    $data['potentialAction'][$actionKey]['target'][$targetKey]
                );
            }
        }

        return $data;
    }
);

add_filter(
    'wpseo_schema_imageobject',
    static function (array $data) {
        replaceFrontendUrlByReference($data['@id']);

        return $data;
    }
);
