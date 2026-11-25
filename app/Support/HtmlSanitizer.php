<?php

namespace App\Support;

class HtmlSanitizer
{
    /**
     * Sanitize HTML content to prevent XSS while allowing safe rich text formatting.
     */
    public static function clean(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // If content has no HTML tags, treat as plain text and preserve newlines
        if (strip_tags($html) === $html) {
            return nl2br(e($html));
        }

        // Remove dangerous script/style/iframe/applet/object/form tags
        $dangerousTags = ['script', 'style', 'iframe', 'object', 'embed', 'applet', 'form'];
        foreach ($dangerousTags as $tag) {
            $html = preg_replace('#<' . $tag . '[^>]*>.*?</' . $tag . '>#is', '', $html);
            $html = preg_replace('#<' . $tag . '[^>]*>#is', '', $html);
        }

        // Remove inline event handlers (onload, onerror, onclick, etc.)
        $html = preg_replace('/(\s+)on[a-z]+\s*=\s*(["\'])(.*?)\2/i', '', $html);
        $html = preg_replace('/(\s+)on[a-z]+\s*=\s*([^\s>]+)/i', '', $html);

        // Remove javascript: and vbscript: URIs in links
        $html = preg_replace('/href\s*=\s*(["\'])\s*(javascript|vbscript):.*?\1/i', 'href="#"', $html);

        // Clean up proprietary/scraped attributes
        $html = preg_replace('/\s*(_ngcontent[^\s=>]*|data-index-in-node|data-path-to-node)(="[^"]*")?/i', '', $html);

        // Clean aggressive inline styles copied from external editors
        $html = preg_replace_callback('/style\s*=\s*(["\'])(.*?)\1/is', function ($matches) {
            $style = $matches[2];
            $style = preg_replace('/font-family\s*:\s*[^;"]+;?/i', '', $style);
            $style = preg_replace('/line-height\s*:\s*[^;"]+;?/i', '', $style);
            $style = preg_replace('/--[a-zA-Z0-9\-]+\s*:\s*[^;"]+;?/i', '', $style);
            $style = trim(trim($style), ';');
            return !empty($style) ? 'style="' . $style . '"' : '';
        }, $html);

        return trim($html);
    }
}
