<?php

declare(strict_types=1);

namespace SMHG;

/**
 * @return string|int|int[]|null
 */
function getQueryParam(\WP_REST_Request $request, string $paramKey, $default = null)
{
    $params = $request->get_query_params();
    return (isset($params[$paramKey])) ? $params[$paramKey] : $default;
}

/**
 * @return array<string|mixed>
 */
function addTaxonomyQueryTermIds(
    array $queryArgs,
    \WP_REST_Request $request,
    string $termParamName,
    string $taxonomyName
): array {
    $termIds = getQueryParam($request, $termParamName);
    if (!$termIds) {
        return $queryArgs;
    }

    $queryArgs['tax_query'][] = [
        'taxonomy' => $taxonomyName,
        'field' => 'term_id',
        'terms' => $termIds,
        'operator' => 'OR',
    ];

    return $queryArgs;
}
