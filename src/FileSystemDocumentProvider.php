<?php

namespace Rareloop\Primer;

use Rareloop\Primer\Contracts\DocumentParser;
use Rareloop\Primer\Contracts\DocumentProvider;
use Rareloop\Primer\Document;
use Rareloop\Primer\Exceptions\DocumentNotFoundException;
use Symfony\Component\Finder\Finder;

class FileSystemDocumentProvider implements DocumentProvider
{
    protected $paths;
    protected $fileExtension;
    protected $documentParser;

    public function __construct(array $paths, string $fileExtension, DocumentParser $parser = null)
    {
        $this->paths = $paths;
        $this->fileExtension = $fileExtension;
        $this->documentParser = $parser;
    }

    public function allDocumentIds() : array
    {
        if (empty($this->paths)) {
            return [];
        }

        $finder = new Finder();
        $finder->files()->in($this->paths)->name('*.' . $this->fileExtension);

        return collect($finder)->map(function ($file, $test) {
            return str_replace('.' . $this->fileExtension, '', $file->getRelativePathname());
        })->sort()->map(function ($id) {
            // Remove any numeric prefix from sub section
            $id = preg_replace('/\/[0-9]+\-/', '/', $id);

            // Remove any numeric prefix from top seciton
            $id = preg_replace('/^[0-9]+\-/', '', $id);

            return $id;
        })->values()->toArray();
    }

    public function getDocument(string $id) : Document
    {
        if (empty($this->paths)) {
            throw new DocumentNotFoundException;
        }

        $finder = new Finder;
        $finder->in($this->paths)->path($this->getFolderPathFromId($id));

        $parts = explode('/', $id);
        $filename = array_pop($parts);

        $finder->name('/([0-9]+\-)?' . $filename . '\.' . $this->fileExtension . '/');
        $files = array_values(iterator_to_array($finder));

        if (count($files) === 0) {
            throw new DocumentNotFoundException;
        }

        $doc = new Document($id, $files[0]->getContents());

        return $this->documentParser ? $this->documentParser->parse($doc) : $doc;
    }

    protected function getFolderPathFromId(string $id) : string
    {
        $parts = explode('/', $id);
        array_pop($parts);

        return $this->convertIdToPathRegex(implode('/', $parts));
    }

    protected function convertIdToPathRegex(string $id) : string
    {
        $parts = array_map(function ($part) {
            return '([0-9]+\-)?' . str_replace('-', '\-', $part);
        }, explode('/', $id));

        $id = '/^' . implode('\/', $parts) . '/';

        return $id;
    }
}
