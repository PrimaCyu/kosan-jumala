<?php

namespace App\Models\Concerns;

trait HasVideoMedia
{
    /**
     * Cek apakah model memiliki video (upload atau link).
     */
    public function getHasVideoAttribute(): bool
    {
        return !empty($this->video_path) || !empty($this->video_url);
    }

    /**
     * Resolusi metadata player video siap pakai untuk tampilan frontend/backend.
     */
    public function getVideoEmbedAttribute(): ?array
    {
        return self::parseVideoMedia($this->video_path ?? null, $this->video_url ?? null);
    }

    /**
     * Parser cerdas untuk file video unggahan maupun tautan video (YouTube, Shorts, Vimeo, Google Drive, TikTok, Instagram, MP4).
     */
    public static function parseVideoMedia(?string $videoPath, ?string $videoUrl): ?array
    {
        $path = trim((string) $videoPath);
        if ($path !== '') {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $mime = match ($ext) {
                'webm' => 'video/webm',
                'ogg', 'ogv' => 'video/ogg',
                'mov' => 'video/mp4',
                default => 'video/mp4',
            };
            $src = asset('storage/' . ltrim($path, '/'));

            return [
                'type'         => 'file',
                'provider'     => 'upload',
                'src'          => $src,
                'mime'         => $mime,
                'is_vertical'  => false,
                'label'        => 'Video Unggahan',
                'original_url' => $src,
            ];
        }

        $url = trim((string) $videoUrl);
        if ($url === '') {
            return null;
        }

        // Keamanan: hanya izinkan skema http / https
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        if (!in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        // 1. YouTube (watch, youtu.be, shorts, embed)
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|v/)|youtu\.be/)([a-zA-Z0-9_-]{11})%i', $url, $m)) {
            $isShort = str_contains(strtolower($url), '/shorts/');
            return [
                'type'         => 'iframe',
                'provider'     => 'youtube',
                'src'          => 'https://www.youtube.com/embed/' . $m[1] . '?rel=0',
                'mime'         => null,
                'is_vertical'  => $isShort,
                'label'        => $isShort ? 'YouTube Shorts' : 'YouTube',
                'original_url' => $url,
            ];
        }

        // 2. Vimeo
        if (preg_match('%vimeo\.com/(?:video/)?(\d+)%i', $url, $m)) {
            return [
                'type'         => 'iframe',
                'provider'     => 'vimeo',
                'src'          => 'https://player.vimeo.com/video/' . $m[1],
                'mime'         => null,
                'is_vertical'  => false,
                'label'        => 'Vimeo',
                'original_url' => $url,
            ];
        }

        // 3. Google Drive
        if (preg_match('%drive\.google\.com/(?:file/d/|open\?id=)([a-zA-Z0-9_-]+)%i', $url, $m)) {
            return [
                'type'         => 'iframe',
                'provider'     => 'gdrive',
                'src'          => 'https://drive.google.com/file/d/' . $m[1] . '/preview',
                'mime'         => null,
                'is_vertical'  => false,
                'label'        => 'Google Drive',
                'original_url' => $url,
            ];
        }

        // 4. TikTok
        if (preg_match('%tiktok\.com/(?:@[^/]+/video/|embed(?:/v2)?/)(\d+)%i', $url, $m)) {
            return [
                'type'         => 'iframe',
                'provider'     => 'tiktok',
                'src'          => 'https://www.tiktok.com/embed/v2/' . $m[1],
                'mime'         => null,
                'is_vertical'  => true,
                'label'        => 'TikTok',
                'original_url' => $url,
            ];
        }

        // 5. Instagram Reels / Post
        if (preg_match('%instagram\.com/(?:reel|reels|p)/([a-zA-Z0-9_-]+)%i', $url, $m)) {
            return [
                'type'         => 'iframe',
                'provider'     => 'instagram',
                'src'          => 'https://www.instagram.com/reel/' . $m[1] . '/embed',
                'mime'         => null,
                'is_vertical'  => true,
                'label'        => 'Instagram',
                'original_url' => $url,
            ];
        }

        // 6. Direct Video File URL (.mp4, .webm, .ogg, .mov)
        if (preg_match('/\.(mp4|webm|ogg|ogv|mov)(\?.*)?$/i', $url, $m)) {
            $ext = strtolower($m[1]);
            $mime = match ($ext) {
                'webm' => 'video/webm',
                'ogg', 'ogv' => 'video/ogg',
                default => 'video/mp4',
            };
            return [
                'type'         => 'file',
                'provider'     => 'direct',
                'src'          => $url,
                'mime'         => $mime,
                'is_vertical'  => false,
                'label'        => 'Video Langsung',
                'original_url' => $url,
            ];
        }

        // 7. Fallback tautan eksternal aman
        return [
            'type'         => 'link',
            'provider'     => 'external',
            'src'          => $url,
            'mime'         => null,
            'is_vertical'  => false,
            'label'        => 'Tautan Video',
            'original_url' => $url,
        ];
    }
}
