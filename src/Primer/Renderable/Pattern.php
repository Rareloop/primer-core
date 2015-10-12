<?php namespace Rareloop\Primer\Renderable;

// use Rareloop\Primer\Templating\Handlebars;
use Rareloop\Primer\Exceptions\NotFoundException;
use Rareloop\Primer\Templating\View;
use Rareloop\Primer\Templating\ViewData;
use Rareloop\Primer\Primer;
use Rareloop\Primer\FileSystem;
use Rareloop\Primer\Events\Event;
use Gajus\Dindent\Parser;
use Michelf\Markdown;

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

        // Load the correct template with the correct template engine
        $templateClass = Primer::$TEMPLATE_CLASS;
        $this->template = new $templateClass($pathToPattern, 'template');

        // Save the raw template string too
        $this->templateRaw = $this->template->raw();

        // Get the title
        $idComponents = explode('/', $this->id);
        $this->title = ucwords(preg_replace('/(\-|~)/', ' ', strtolower(end($idComponents))));

        // Load the copy
        $this->loadCopy();

        // Load the data
        $this->loadData($customData);
    }

    /**
     * Load the copy/descriptive text for this pattern
     */
    protected function loadCopy()
    {
        $copy = @file_get_contents($this->path . '/README.md');

        if ($copy) {
            $this->copy = Markdown::defaultTransform($copy);
        } else {
            $this->copy = false;
        }
    }

    /**
     * Load the data for this pattern
     */
    protected function loadData()
    {
        $this->data = FileSystem::getDataForPattern($this->id);
    }

    public function setData($data)
    {
        $this->data->merge($data);
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * Renders the pattern
     *
     * @param  boolean $showChrome Whether or not the item should render descriptive chrome
     * @return String              HTML text
     */
    public function render($showChrome = true)
    {
        $html = $this->template->render($this->data);

        // Tidy the HTML
        $parser = new Parser();
        $html = $parser->indent($html);

        if ($showChrome) {
            return View::render('pattern', [
                'title' => $this->title,
                'id' => $this->id,
                'html' => $html,
                'template' => $this->templateRaw,
                'copy' => $this->copy,
                'data' => json_encode($this->data, JSON_PRETTY_PRINT),
            ]);
        } else {
            return $html;
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
     * Composer function to bind events to Pattern events
     *
     * @param  [type] $ids      [description]
     * @param  [type] $callable [description]
     * @return [type]           [description]
     */
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
