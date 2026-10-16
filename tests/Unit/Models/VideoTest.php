<?php

use App\Models\Video;

it('extracts the video id from a YouTube link', function (string $value): void {
    expect(Video::extractYoutubeId($value))->toBe('dQw4w9WgXcQ');
})->with([
    'bare id' => 'dQw4w9WgXcQ',
    'padded id' => '  dQw4w9WgXcQ  ',
    'watch link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    'watch link with extra parameters' => 'https://www.youtube.com/watch?feature=shared&v=dQw4w9WgXcQ&t=42s',
    'mobile link' => 'https://m.youtube.com/watch?v=dQw4w9WgXcQ',
    'short link' => 'https://youtu.be/dQw4w9WgXcQ?si=abc123',
    'shorts link' => 'https://www.youtube.com/shorts/dQw4w9WgXcQ',
    'embed link' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
    'live link' => 'https://youtube.com/live/dQw4w9WgXcQ?feature=share',
    'link without scheme' => 'youtube.com/watch?v=dQw4w9WgXcQ',
]);

it('does not extract an id from anything else', function (string $value): void {
    expect(Video::extractYoutubeId($value))->toBeNull();
})->with([
    'empty' => '',
    'plain text' => 'meu vídeo',
    'too short id' => 'dQw4w9WgXc',
    'other site' => 'https://vimeo.com/watch?v=dQw4w9WgXcQ',
    'lookalike host' => 'https://youtube.com.evil.test/watch?v=dQw4w9WgXcQ',
    'channel link' => 'https://www.youtube.com/@stn_soul_true_noise',
    'markup' => '"><script>alert(1)</script>',
]);
