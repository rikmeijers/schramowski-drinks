<?php

if (! function_exists('vasset')) {
    /**
     * Build a versioned asset URL so iOS/Safari PWA caches pick up CSS/JS updates.
     *
     * Uses APP_VERSION + filemtime so:
     * - editing a file busts that file automatically
     * - bumping APP_VERSION in .env busts everything on deploy
     */
    function vasset(string $path): string
    {
        $path = '/'.ltrim($path, '/');

        // Web root lives next to the Laravel app (../public from /secure).
        $webRoot = dirname(base_path()).'/public';
        $absolute = $webRoot.$path;

        $parts = [(string) config('app.version', '1')];

        if (is_file($absolute)) {
            $parts[] = (string) filemtime($absolute);
        }

        return url($path).'?v='.rawurlencode(implode('.', $parts));
    }
}
