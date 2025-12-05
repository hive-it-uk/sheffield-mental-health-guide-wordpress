<?php

declare(strict_types=1);

namespace SMHG;

/**
 * Class MenuItem
 *
 * @package SMHG
 */
class MenuItem
{
    public string $url;

    public string $title;

    /** @var \SMHG\MenuItem[] */
    public array $children = [];

    public string $target;

    public function __construct(string $url, string $title, string $target, array $children = [])
    {
        $this->url = $url;
        $this->title = $title;
        $this->children = $children;
        $this->target = $target;
    }
}
