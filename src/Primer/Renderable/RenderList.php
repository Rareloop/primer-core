<?php namespace Rareloop\Primer\Renderable;

/**
 * Maintain a list of patterns/groups to render into a template
 */
class RenderList implements Renderable
{
    /**
     * Array of the items to render
     *
     * @var array
     */
    protected $items = array();

    /**
     * Constructor
     *
     * @param array $patterns
     */
    public function __construct(array $patterns = array())
    {
        $this->items = $patterns;
    }

    /**
     * Add items to the list of items to render
     *
     * @param [Pattern|Group] $pattern
     */
    public function add($pattern)
    {
        if (is_array($pattern)) {
            $this->items += $pattern;
        } else {
            $this->items[] = $pattern;
        }
    }

    /**
     * Render the list of items
     *
     * @param  boolean $showChrome [description]
     * @return [type]              [description]
     */
    public function render($showChrome = true)
    {
        $html = "";

        foreach ($this->items as $item) {
            $html .= $item->render($showChrome);
        }

        return $html;
    }
}
