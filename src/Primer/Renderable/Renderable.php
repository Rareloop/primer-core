<?php namespace Rareloop\Primer\Renderable;

/**
 * Interface defining a common render function
 */
interface Renderable
{
    /**
     * Render the current object
     *
     * @param  boolean $showChrome Whether or not the item should render descriptive chrome
     * @return String              HTML text
     */
    public function render($showChrome = true);
}
