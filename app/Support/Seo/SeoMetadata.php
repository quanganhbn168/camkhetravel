<?php

namespace App\Support\Seo;

class SeoMetadata
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $canonicalUrl,
        public readonly array $robots,
        public readonly string $ogTitle,
        public readonly ?string $ogDescription,
        public readonly ?string $ogImageUrl,
        public readonly string $twitterTitle,
        public readonly ?string $twitterDescription,
        public readonly ?string $twitterImageUrl,
        public readonly array $structuredData,
    ) {}

    public function withRobots(array $robots): self
    {
        return new self(
            title: $this->title,
            description: $this->description,
            canonicalUrl: $this->canonicalUrl,
            robots: $robots,
            ogTitle: $this->ogTitle,
            ogDescription: $this->ogDescription,
            ogImageUrl: $this->ogImageUrl,
            twitterTitle: $this->twitterTitle,
            twitterDescription: $this->twitterDescription,
            twitterImageUrl: $this->twitterImageUrl,
            structuredData: $this->structuredData,
        );
    }

    public function withoutStructuredData(): self
    {
        return new self(
            title: $this->title,
            description: $this->description,
            canonicalUrl: $this->canonicalUrl,
            robots: $this->robots,
            ogTitle: $this->ogTitle,
            ogDescription: $this->ogDescription,
            ogImageUrl: $this->ogImageUrl,
            twitterTitle: $this->twitterTitle,
            twitterDescription: $this->twitterDescription,
            twitterImageUrl: $this->twitterImageUrl,
            structuredData: [],
        );
    }
}
