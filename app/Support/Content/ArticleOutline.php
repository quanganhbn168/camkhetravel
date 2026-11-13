<?php

namespace App\Support\Content;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Str;

/**
 * Gắn id ổn định cho các heading H2/H3 trong nội dung soạn thảo, trả về mục lục,
 * và bọc bảng để cuộn ngang trên điện thoại. Dùng chung cho bài viết và dịch vụ.
 */
final class ArticleOutline
{
    /** @return array{html: string, tableOfContents: list<array{id: string, title: string, level: int}>} */
    public static function prepare(string $html): array
    {
        if (blank($html)) {
            return ['html' => $html, 'tableOfContents' => []];
        }

        $previousErrors = libxml_use_internal_errors(true);

        try {
            $document = new DOMDocument('1.0', 'UTF-8');
            $document->loadHTML(
                '<?xml encoding="UTF-8"><!DOCTYPE html><html><body><div id="article-body-root">'.$html.'</div></body></html>',
                LIBXML_HTML_NODEFDTD | LIBXML_NONET,
            );

            $root = (new DOMXPath($document))->query('//*[@id="article-body-root"]')->item(0);

            if (! $root instanceof DOMElement) {
                return ['html' => $html, 'tableOfContents' => []];
            }

            $usedIds = [];
            $tableOfContents = [];
            $headings = (new DOMXPath($document))->query('.//*[self::h2 or self::h3]', $root);

            foreach ($headings ?: [] as $heading) {
                if (! $heading instanceof DOMElement) {
                    continue;
                }

                $title = trim((string) preg_replace('/\s+/u', ' ', $heading->textContent));

                if ($title === '') {
                    continue;
                }

                $id = $heading->getAttribute('id');

                if (! preg_match('/^[A-Za-z][A-Za-z0-9_:.\\-]*$/', $id)) {
                    $id = Str::slug($title) ?: 'muc-noi-dung';
                }

                $baseId = $id;
                $suffix = 2;

                while (isset($usedIds[$id])) {
                    $id = $baseId.'-'.$suffix++;
                }

                $usedIds[$id] = true;
                $heading->setAttribute('id', $id);
                $tableOfContents[] = [
                    'id' => $id,
                    'title' => $title,
                    'level' => (int) substr($heading->tagName, 1),
                ];
            }

            foreach (self::tables($document, $root) as $table) {
                $wrapper = $document->createElement('div');
                $wrapper->setAttribute('class', 'article-table-wrap');
                $table->parentNode?->replaceChild($wrapper, $table);
                $wrapper->appendChild($table);
            }

            $bodyHtml = '';

            foreach ($root->childNodes as $child) {
                $bodyHtml .= $document->saveHTML($child);
            }

            return ['html' => $bodyHtml, 'tableOfContents' => $tableOfContents];
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousErrors);
        }
    }

    /** @return list<DOMElement> */
    private static function tables(DOMDocument $document, DOMElement $root): array
    {
        $tables = (new DOMXPath($document))->query('.//table', $root);

        if ($tables === false) {
            return [];
        }

        return array_values(array_filter(
            iterator_to_array($tables),
            fn ($table): bool => $table instanceof DOMElement
                && ! ($table->parentNode instanceof DOMElement
                    && str_contains(' '.$table->parentNode->getAttribute('class').' ', ' article-table-wrap ')),
        ));
    }
}
