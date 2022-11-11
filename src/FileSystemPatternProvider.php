<?php

namespace Rareloop\Primer;

use Rareloop\Primer\Pattern;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;
use Rareloop\Primer\Contracts\DataParser;
use Rareloop\Primer\Contracts\PatternProvider;
use Rareloop\Primer\Contracts\TemplateProvider;
use Rareloop\Primer\Exceptions\PatternNotFoundException;

class FileSystemPatternProvider implements PatternProvider, TemplateProvider
{
    protected $paths;
    protected $fileExtension;

    protected $patternPaths = [];

    public function __construct(array $paths, $fileExtension, DataParser $dataParser = null)
    {
        $this->paths = $paths;
        $this->fileExtension = $fileExtension;
        $this->dataParser = $dataParser;
    }

    /**
     * Get a list of all the known pattern id's
     *
     * @return array
     */
    public function allPatternIds(): array
    {
        if (empty($this->paths)) {
            return [];
        }

        $finder = new Finder();
        $finder->files()->in($this->paths)->name('template.' . $this->fileExtension);

        return (new Collection($finder))->map(function ($file) {
            return $file->getRelativePath();
        })->sort()->values()->toArray();
    }

    /**
     * Get a list of all known states for a given pattern
     *
     * The array must *always* contain `default` whether or not data exists
     *
     * @param  string $id The pattern ID
     * @return array
     */
    protected function allPatternStates(string $id): array
    {
        $path = $this->getPathForPattern($id);

        $supportedFormatsRegex = implode('|', $this->supportedFormats());

        $finder = new Finder();
        $finder->files()->in($path)->name('/data~.*\.(' . $supportedFormatsRegex . ')/')->sortByName();

        $states = ['default'];

        foreach ($finder as $file) {
            preg_match('/data~(.*)\.(' . $supportedFormatsRegex . ')/', $file->getFilename(), $matches);
            $states[] = $matches[1];
        }

        return $states;
    }

    /**
     * Retrieve a Pattern
     *
     * @param  string $id    The pattern ID
     * @param  string $state The state name
     * @return Rareloop\Primer\Pattern
     */
    public function getPattern(string $id, string $state = 'default'): Pattern
    {
        if (empty($this->paths)) {
            throw new PatternNotFoundException;
        }

        if (!$this->patternHasState($id, $state)) {
            $state = 'default';
        }

        return new Pattern(
            $id,
            $this->getPatternStateData($id, $state),
            $this->getPatternTemplate($id),
            $state,
            $this->allPatternStates($id)
        );
    }

    /**
     * Get the contents of the template for a given pattern
     *
     * @param  string $id The pattern ID
     * @return string
     */
    public function getPatternTemplate(string $id): string
    {
        $path = $this->getPathForPattern($id);

        return file_get_contents($path . '/template.' . $this->fileExtension);
    }

    protected function convertIdToPathRegex(string $id): string
    {
        $parts = array_map(function ($part) {
            return str_replace('-', '\-', $part);
        }, explode('/', $id));

        $id = '/^' . implode('\/', $parts) . '\//';

        return $id;
    }

    protected function getPathForPattern($id)
    {
        if (isset($this->patternPaths[$id])) {
            return $this->patternPaths[$id];
        }

        foreach ($this->paths as $path) {
            $filePath = $path . '/' . $id . '/template.' . $this->fileExtension;

            if (file_exists($filePath)) {
                $this->patternPaths[$id] = $path . '/' . $id . '/';
                return $path . '/' . $id . '/';
            }
        }

        throw new PatternNotFoundException();
    }

    /**
     * Does a given pattern exist?
     *
     * @param  string $id The pattern ID
     * @return bool
     */
    public function patternExists(string $id): bool
    {
        if (empty($this->paths)) {
            return false;
        }

        try {
            $this->getPathForPattern($id);

            return true;
        } catch (PatternNotFoundException $e) {
            return false;
        }
    }

    /**
     * Does a given state exists for a given pattern?
     *
     * All valid pattern's will return true for the `default` state
     *
     * @param  string      $id    The pattern ID
     * @param  string      $state The state name
     * @return bool
     */
    public function patternHasState(string $id, string $state = 'default'): bool
    {
        if (empty($this->paths)) {
            return false;
        }

        $path = $this->getPathForPattern($id);

        if ($state === 'default') {
            return true;
        }

        $supportedFormatsRegex = implode('|', $this->supportedFormats());

        $finder = new Finder;
        $finder->files()->in($path)->name('/data\~' . $state . '\.(' . $supportedFormatsRegex . ')/');

        $files = array_values(iterator_to_array($finder));

        return count($files) > 0;
    }

    protected function supportedFormats(): array
    {
        return array_merge(['php'], $this->dataParser ? $this->dataParser->supportedFormats() : []);
    }

    /**
     * Get the data for the given pattern and state
     *
     * @param  string $id    [description]
     * @param  string $state [description]
     * @return [type]        [description]
     */
    public function getPatternStateData(string $id, string $state = 'default'): array
    {
        if (!$this->patternHasState($id, $state)) {
            return [];
        }

        $path = $this->getPathForPattern($id);

        $finder = new Finder;
        $finder->in($path)->files();

        $glob = $state !== 'default' ? 'data~' . $state . '.*' : 'data.*';

        $finder->name($glob);

        $files = array_values(iterator_to_array($finder));
        $file = array_shift($files);

        if (!$file) {
            return [];
        }

        if ($file->getExtension() === 'php') {
            return include $file->getPath() . '/' . $file->getFilename();
        }

        return $this->dataParser->parse($file->getContents(), $file->getExtension());
    }

    protected function createFinderForPattern($id)
    {
        $finder = new Finder();

        $finder
            ->files()
            ->in($this->paths)
            ->path($this->convertIdToPathRegex($id));

        return $finder;
    }

    /**
     * Get when a pattern template was last modified
     *
     * @param  string $id The pattern ID
     * @return int        Unix timestamp of when last modified
     */
    public function getPatternTemplateLastModified(string $id): int
    {
        $path = $this->getPathForPattern($id);

        return filemtime($path . '/template.' . $this->fileExtension);
    }
}
