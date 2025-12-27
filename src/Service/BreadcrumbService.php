<?php

namespace App\Service;

class BreadcrumbService
{
    private array $items = [];

    public function add(string $label, ?string $url = null): void
    {
        $this->items[] = compact('label', 'url');
    }

    public function all(): array
    {
        return $this->items;
    }
}
