<?php

declare(strict_types=1);

namespace Crm\Core;

use RuntimeException;

final class View
{
    public function __construct(private readonly string $basePath)
    {
    }

    public function render(string $template, array $data = []): string
    {
        $file = str_starts_with($template, '/')
            ? $template
            : $this->basePath . '/views/' . $template . '.php';
        if (!is_file($file)) {
            throw new RuntimeException('View not found: ' . $template);
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        $content = (string) ob_get_clean();

        ob_start();
        require $this->basePath . '/views/layout.php';

        return (string) ob_get_clean();
    }
}
