<?php

namespace Bolt\Utils;

use Bolt\Configuration\Config;
use Symfony\Component\Finder\Finder;
use Tightenco\Collect\Support\Collection;
use Webmozart\PathUtil\Path;

class FilesIndex
{
    /** @var Config */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function get(string $path, string $type, string $basePath): Collection
    {
        if ($type === 'images') {
            $glob = sprintf('*.{%s}', $this->config->getMediaTypes()->implode(','));
        } else {
            $glob = null;
        }

        $files = [];

        foreach (self::findFiles($path, $glob) as $file) {
            $files[] = [
                'group' => basename($basePath),
                'value' => Path::makeRelative($file->getRealPath(), $basePath),
                'text' => $file->getFilename(),
            ];
        }

        return new Collection($files);
    }

    private function findFiles(string $path, string $glob = null): Finder
    {
        $finder = new Finder();
        $finder->in($path)->depth('< 5')->sortByType()->files();

        if ($glob) {
            $finder->name($glob);
        }

        return $finder;
    }
}