<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('posts')
            ->select(['id', 'body'])
            ->where(function ($query): void {
                $query
                    ->where('body', 'like', '%thtmedia.com.vn%')
                    ->orWhere('body', 'like', '%wordpress-%');
            })
            ->orderBy('id')
            ->chunkById(100, function ($posts): void {
                foreach ($posts as $post) {
                    $body = $this->normalize((string) $post->body);

                    if ($body !== (string) $post->body) {
                        DB::table('posts')
                            ->where('id', $post->id)
                            ->update([
                                'body' => $body,
                                'updated_at' => now(),
                            ]);
                    }
                }
            });
    }

    public function down(): void
    {
        throw new RuntimeException('This data cleanup is irreversible; restore the database backup to roll it back.');
    }

    private function normalize(string $body): string
    {
        $body = preg_replace(
            '~https?://l\.facebook\.com/l\.php\?u=http%3A%2F%2F(?:www%3F)?thtmedia\.com\.vn[^"\'<>\s]*~i',
            '/',
            $body,
        ) ?? $body;
        $body = preg_replace(
            '~https?://(?:www\.)?thtmedia\.com\.vn(?:/[^"\'<>\s]*)?~i',
            '/',
            $body,
        ) ?? $body;
        $body = preg_replace(
            '~href="/tin-tuc/wordpress-[^"]*"~i',
            'href="/"',
            $body,
        ) ?? $body;
        $body = preg_replace(
            "~href='/tin-tuc/wordpress-[^']*'~i",
            "href='/'",
            $body,
        ) ?? $body;

        return str_ireplace(
            ['www.thtmedia.com.vn', 'thtmedia.com.vn'],
            'THT Media',
            $body,
        );
    }
};
