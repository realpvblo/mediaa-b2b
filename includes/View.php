<?php

namespace MediaaB2B;

class View
{
    public static function render(string $template, array $data = []): string
    {
        $templatePath = dirname(__DIR__) . '/templates/' . $template . '.php';

        if (! file_exists($templatePath)) {
            return sprintf('Template "%s" not found.', $template);
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $templatePath;

        return ob_get_clean();
    }
}
