<?php namespace Rareloop\Primer\Renderable;

// use Rareloop\Primer\Templating\Handlebars;
use Rareloop\Primer\Exceptions\NotFoundException;
use Rareloop\Primer\Templating\View;
use Rareloop\Primer\Templating\ViewData;
use Rareloop\Primer\Primer;
use Rareloop\Primer\FileSystem;
use Rareloop\Primer\Events\Event;

/**
 * Class to represent a pattern
 */
class Pattern implements Renderable
{

    /**
     * Path to this pattern
     *
     * @var String
     */
    protected $path;

    /**
     * The id of this pattern
     *
     * @var String
     */
    protected $id;

    /**
     * The title of the pattern
     *
     * @var String
     */
    protected $title;

    /**
     * The Handlebars template rendered
     *
     * @var String
     */
    protected $html;

    /**
     * Data to pass into the template
     *
     * @var array
     */
    protected $data = array();

    /**
     * Content/annotations to add to the pattern
     *
     * @var String
     */
    protected $copy;

    /**
     * Constructor
     *
     * @param String $id The Id of the pattern
     * @param  Array $customData Optional data to load at runtime
     */
    public function __construct($id, $customData = array())
    {
        $this->id = Primer::cleanId($id);

        $this->path = Primer::$PATTERN_PATH . '/' . $this->id;


        // Check the path is valid
        if (!is_dir($this->path)) {
            throw new NotFoundException('Pattern not found: ' . $this->id);
        }

        $pathToPattern = $this->path;

        // If this is an alias we need to load the template of the parent pattern
        if (strpos($this->id, "~") !== false) {
            $parts = explode("~", $this->id);

            if (count($parts) > 1) {
                $pathToPattern = Primer::$PATTERN_PATH . '/' . $parts[0];
            }
        }

        $templateClass = Primer::$TEMPLATE_CLASS;
        $template = new $templateClass($pathToPattern, 'template');

        // Get the title
        $idComponents = explode('/', $this->id);
        $this->title = ucwords(preg_replace('/(\-|~)/', ' ', strtolower(end($idComponents))));

        // Attempt to load the init script to bootstrap any listeners
        @include_once($this->path . '/init.php');

        // Load the copy
        $this->copy = $this->loadCopy();

        // Load the data
        $this->data = $this->loadData($customData);


        // Render the template
        Event::fire('pattern.' . $this->id, $this->data);

        $parser = new \Gajus\Dindent\Parser();

        // $this->html = $engine->render($this->id, $this->data);
        $this->html = $template->render($this->data);

        // Tidy the HTML
        $this->html = $parser->indent($this->html);
    }

    /**
     * Load the data for this template
     *
     * @param Array $customData Custom data to load at runtime
     * @return array The data as an associative array
     */
    protected function loadData($customData = array())
    {
        // Get the data for the pattern and resolve any aliases
        $defaultData = FileSystem::getDataForPattern($this->id, true);

        return new ViewData(array_merge($defaultData, $customData));
    }

    /**
     * Renders the pattern
     *
     * @param  boolean $showChrome Whether or not the item should render descriptive chrome
     * @return String              HTML text
     */
    public function render($showChrome = true)
    {
        if ($showChrome) {
            return View::render('pattern', array(
                'title' => $this->title,
                'id' => $this->id,
                'html' => $this->html,
                'copy' => $this->copy
            ));
        } else {
            return $this->html;
        }
    }

    /**
     * Helper function to load all patterns in a folder
     *
     * @param  String $path The path of the directory to load from
     * @return Array       Array of all patterns loaded
     */
    public static function loadPatternsInPath($path)
    {
        $patterns = array();

        if ($handle = opendir($path)) {

            while (false !== ($entry = readdir($handle))) {

                $fullPath = $path . '/' . $entry;

                if ($entry != '..' && $entry != '.' && is_dir($fullPath)) {



                    $id = trim(str_replace(Primer::$PATTERN_PATH, '', $fullPath), '/');

                    // Load the pattern
                    $patterns[] = new Pattern($id);
                }
            }

            closedir($handle);
        }

        return $patterns;
    }

    /**
     * Load the copy/descriptive text for this pattern
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

    /**
     * Get the patterns data
     *
     * @return Array
     */
    public function getData()
    {
        return (array)$this->data;
    }

    public static function composer($ids, $callable)
    {
        if (!is_array(($ids))) {
            $ids = array($ids);
        }

        foreach ($ids as $id) {
            Event::listen("pattern.$id", $callable);
        }
    }
}
