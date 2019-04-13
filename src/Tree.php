<?php

namespace Rareloop\Primer;

use Rareloop\Primer\Contracts\Arrayable;
use Rareloop\Primer\Exceptions\TreeNodeNotFoundException;

class Tree implements Arrayable
{
    protected $root;
    protected $nodeLookup = [];
    protected $ids = [];

    public function __construct(array $patternIds)
    {
        $this->root = new TreeNode('root');
        $this->ids = $patternIds;

        foreach ($patternIds as $id) {
            // dump('Handling: ' . $id);
            $parts = explode('/', $id);

            $treeNode = null;
            $treeNodeIndex = null;

            // find the last known node
            for ($i = 0; $i < count($parts); $i++) {
                $thisId = implode('/', array_slice($parts, 0, $i + 1));
                // dump('Looking up: ' . $thisId);

                $thisTreeNode = $this->getNodeWithId($thisId);

                if ($thisTreeNode) {
                    // dump('Found: ' . $thisId);
                    $treeNode = $thisTreeNode;
                    $treeNodeIndex = $i;
                }
            }

            // If no node found, set to root
            if ($treeNodeIndex === null) {
                $treeNode = $this->root;
                $treeNodeIndex = 0;
            } else {
                $treeNodeIndex +=1;
            }

            $partsToCreate = array_slice($parts, $treeNodeIndex);
            $prefix = implode('/', array_slice($parts, 0, $treeNodeIndex));

            // dump($partsToCreate);
            // dump($prefix);

            for ($j = 0; $j < count($partsToCreate); $j++) {
                $thisId = trim($prefix . '/' . implode('/', array_slice($partsToCreate, 0, $j + 1)), '/');
                // dump('Creating: ' . $thisId);
                $node = new TreeNode($thisId);

                $treeNode->addChild($node);
                $treeNode = $node;

                $this->nodeLookup[$thisId] = $node;
            }
        }
    }

    /**
     * Get the array representation of this tree
     *
     * @return array
     */
    public function toArray() : array
    {
        return $this->root->toArray()['children'];
    }

    /**
     * Lookup the node with the provided id
     *
     * @param  string $id A Pattern Id
     * @return TreeNode|null
     */
    protected function getNodeWithId(string $id) : ?TreeNode
    {
        if (isset($this->nodeLookup[$id])) {
            return $this->nodeLookup[$id];
        }

        return null;
    }

    /**
     * Marks the provided id as currently selected
     *
     * @param string $id The Pattern Id
     */
    public function setCurrent(string $id) : Tree
    {
        $node = $this->getNodeWithId(trim($id, '/'));

        if (!$node) {
            throw new TreeNodeNotFoundException;
        }

        $node->setCurrent();

        $parentNode = $node->parent();

        while ($parentNode) {
            $parentNode->setAncestor();
            $parentNode = $parentNode->parent();
        }

        return $this;
    }

    public function count() : int
    {
        return count($this->ids);
    }

    public function getIds() : array
    {
        return $this->ids;
    }
}
