<?php

declare(strict_types=1);

namespace SMHG;

/**
 * class ExternalResourceMeta houses external resource meta keys.
 */

abstract class ExternalResourceMeta
{
    public const URL = 'resource_url';
    public const FILE = 'resource_file';
    public const NEWTAB = 'resource_new_tab';
    public const TYPE = 'resource_type';
}
