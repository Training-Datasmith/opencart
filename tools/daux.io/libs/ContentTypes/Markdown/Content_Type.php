<?php

declare(strict_types=1);

namespace Todaymade\Daux\ContentTypes\Markdown;

use Symfony\Component\Console\Output\OutputInterface;
use Todaymade\Daux\Cache;
use Todaymade\Daux\Config;
use Todaymade\Daux\Daux;
use Todaymade\Daux\Tree\Content;

class ContentType implements \Todaymade\Daux\ContentTypes\ContentType
{
    private ?CommonMarkConverter $converter = null;

    public function __construct(protected Config $config)
    {
    }

    protected function createConverter(): \Todaymade\Daux\ContentTypes\Markdown\CommonMarkConverter
    {
        return new CommonMarkConverter(['daux' => $this->config]);
    }

    protected function getConverter(): ?\Todaymade\Daux\ContentTypes\Markdown\CommonMarkConverter
    {
        if (!isset($this->converter)) {
            $this->converter = $this->createConverter();
        }

        return $this->converter;
    }

    /**
     * @return string[]
     */
    public function getExtensions(): array
    {
        return ['md', 'markdown'];
    }

    protected function doConversion($raw)
    {
        Daux::writeln('Running conversion', OutputInterface::VERBOSITY_VERBOSE);

        return $this->getConverter()->convert($raw)->getContent();
    }

    public function convert($raw, Content $node)
    {
        $this->config->setCurrentPage($node);

        $canCache = $this->config->canCache();

        // TODO :: add daux version to cache key
        $cacheKey = $this->config->getCacheKey() . sha1($raw);

        $payload = Cache::get($cacheKey);

        if ($canCache && $payload) {
            Daux::writeln('Using cached version', OutputInterface::VERBOSITY_VERBOSE);
        }

        if (!$canCache || !$payload) {
            $message = $canCache ? 'Not found in the cache, generating...' : 'Cache disabled, generating...';
            Daux::writeln($message, OutputInterface::VERBOSITY_VERBOSE);
            $payload = $this->doConversion($raw);
        }

        if ($canCache) {
            Cache::put($cacheKey, $payload);
        }

        return $payload;
    }
}
