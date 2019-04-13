<?php

namespace Rareloop\Primer;

use Exception;
use Rareloop\Primer\Contracts\Arrayable;
use Rareloop\Primer\Exceptions\SectionNotFoundException;
use Rareloop\Primer\Tree;

class Menu implements Arrayable
{
    protected $sections = [];
    protected $currentSection = null;

    public function addSection(string $name, Tree $tree) : Menu
    {
        $this->sections[$name] = $tree;

        return $this;
    }

    public function hasSection(string $name) : bool
    {
        return isset($this->sections[$name]);
    }

    public function getSection(string $name) : Tree
    {
        if (!$this->hasSection($name)) {
            throw new SectionNotFoundException;
        }
        return $this->sections[$name];
    }

    public function toArray() : array
    {
        return collect($this->sections)->mapWithKeys(function ($item, $key) {
            return [
                $key => [
                    'ancestor' => $key === $this->currentSection,
                    'nodes' => $item->toArray(),
                ],
            ];
        })->toArray();
    }

    public function setCurrent(string $sectionName, string $id)
    {
        $section = $this->sections[$sectionName] ?: null;

        if ($section) {
            try {
                $section->setCurrent($id);
                $this->currentSection = $sectionName;
            } catch (Exception $e) {
                // Gracefully recover
            }
        }

        return $this;
    }
}
