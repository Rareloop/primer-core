<?php

namespace Rareloop\Primer;

class TreeNode
{
    protected $id;
    protected $name;
    protected $children = [];
    protected $parent;
    protected $ancestor = false;
    protected $current = false;

    public function __construct(string $id)
    {
        $this->id = $id;

        $this->name = IdHelpers::title($id);
    }

    public function addChild(TreeNode $node)
    {
        $this->children[] = $node;
        $node->setParent($this);
    }

    public function setParent(TreeNode $node)
    {
        $this->parent = $node;
    }

    public function parent() : ?TreeNode
    {
        return $this->parent;
    }

    public function children() : array
    {
        return $this->children;
    }

    public function name() : string
    {
        return $this->name;
    }

    public function id() : string
    {
        return $this->id;
    }

    public function toArray() : array
    {
        return [
            'id' => $this->id(),
            'name' => $this->name(),
            'ancestor' => $this->isAncestor(),
            'current' => $this->isCurrent(),
            'children' => array_map(function ($child) {
                return $child->toArray();
            }, $this->children()),
        ];
    }

    public function setCurrent()
    {
        $this->current = true;
    }

    public function setAncestor()
    {
        $this->ancestor = true;
    }

    public function isCurrent()
    {
        return $this->current;
    }

    public function isAncestor()
    {
        return $this->ancestor;
    }
}
