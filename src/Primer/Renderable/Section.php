<?php namespace Rareloop\Primer\Renderable;

use Rareloop\Primer\Exceptions\NotFoundException;
use Rareloop\Primer\Primer;
use Rareloop\Primer\Templating\View;
use Rareloop\Primer\Renderable\Group;

class Section implements Renderable
{

    /**
     * The groups Id
     *
     * @var String
     */
    protected $id;

    /**
     * The path to the group
     *
     * @var String
     */
    protected $path;

    /**
     * The groups title
     *
     * @var String
     */
    protected $title;

    /**
     * Descriptive text for this Group
     *
     * @var String
     */
    protected $copy;

    /**
     * The patterns in this group
     *
     * @var Array
     */
    protected $patterns;

    public function __construct($id)
    {
        $this->id = Primer::cleanId($id);
        $this->path = Primer::$PATTERN_PATH . '/' . $this->id;

        // Check the path is valid
        if (!is_dir($this->path)) {
            throw new NotFoundException('Section not found: ' . $this->id);
        }

        // Get the title
        $idComponents = explode('/', $this->id);
        $this->title = ucwords(preg_replace('/\-/', ' ', strtolower(end($idComponents))));

        $this->copy = $this->loadCopy();

        // Load the patterns
        $this->patterns = new RenderList(Group::loadPatternsInPath($this->path));
    }

    public function render($showChrome = true)
    {
        if ($showChrome) {
            return View::render('section', array(
                'title' => $this->title,
                'id' => $this->id,
                'copy' => $this->copy,
                'patterns' => $this->patterns->render()
            ));
        } else {
            return $this->patterns->render($showChrome);
        }
        
    }

    protected function loadCopy()
    {
        $copy = @file_get_contents($this->path . '/README.md');

        if ($copy) {
            return \Michelf\Markdown::defaultTransform($copy);
        } else {
            return false;
        }
    }
}
