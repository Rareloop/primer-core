<?php namespace Rareloop\Primer\Renderable;

use Rareloop\Primer\Exceptions\NotFoundException;
use Rareloop\Primer\Primer;
use Rareloop\Primer\Templating\View;
use Rareloop\Primer\Renderable\Pattern;

class Group implements Renderable
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
            throw new NotFoundException('Group not found: ' . $this->id);
        }

        // Get the title
        $idComponents = explode('/', $this->id);
        $this->title = ucwords(preg_replace('/\-/', ' ', strtolower(end($idComponents))));

        $this->copy = $this->loadCopy();

        // Load the patterns
        $this->patterns = new RenderList(Pattern::loadPatternsInPath($this->path));
    }

    /**
     * Renders the group
     *
     * @param  boolean $showChrome Whether or not the item should render descriptive chrome
     * @return String              HTML text
     */
    public function render($showChrome = true)
    {
        if ($showChrome) {
            return View::render('group', array(
                'title' => $this->title,
                'id' => $this->id,
                'copy' => $this->copy,
                'patterns' => $this->patterns->render()
            ));
        } else {
            return $this->patterns->render($showChrome);
        }
        
    }

    /**
     * Helper function to load all groups in a folder
     *
     * @param  String $path The path of the directory to load from
     * @return Array       Array of all groups loaded
     */
    public static function loadPatternsInPath($path)
    {
        $groups = array();

        if ($handle = opendir($path)) {

            while (false !== ($entry = readdir($handle))) {
                
                $fullPath = $path . '/' . $entry;

                if (substr($entry, 0, 1) !== '.') {

                    $id = trim(str_replace(Primer::$PATTERN_PATH, '', $fullPath), '/');

                    // Load the pattern
                    $groups[] = new Group($id);
                }
            }

            closedir($handle);
        }

        return $groups;
    }

    /**
     * Load the copy/descriptive text for this group
     *
     * @return String HTML text
     */
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
