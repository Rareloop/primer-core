<?php namespace Rareloop\Primer;

use Rareloop\Primer\Templating\ViewData;
use Rareloop\Primer\Events\Event;

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

class Primer
{
    /**
     * The singleton instance
     *
     * @var Primer
     */
    protected static $instance;

    /**
     * Base path for the App
     *
     * @var String
     */
    public static $BASE_PATH;

    /**
     * Base path for the App
     *
     * @var String
     */
    public static $PATTERN_PATH;

    /**
     * Singleton accessor
     *
     * @return Primer The singleton instance
     */
    public static function instance()
    {
        if (!isset(Primer::$instance)) {
            Primer::$instance = new Primer;

            Event::fire('patternlab.init', Primer::$instance);
        }

        return Primer::$instance;
    }

    /**
     * Bootstrap the Primer singleton
     *
     * @param  String $basePath The base path of the project
     * @return Rareloop\Primer\Primer
     */
    public static function start($basePath)
    {
        ErrorHandler::register();
        ExceptionHandler::register();

        Primer::$BASE_PATH = realpath($basePath);
        Primer::$PATTERN_PATH = Primer::$BASE_PATH . '/patterns';

        return Primer::instance();
    }

    /**
     * Remove unsafe/unsupported characters from the id
     *
     * @param  String $id The unclean id
     * @return String     The cleaned id
     */
    public static function cleanId($id)
    {
        // Remove non allowed characters
        $id = preg_replace('/[^A-Za-z0-9\-\_\/~]/', '', $id);

        // Replace multiple slashes
        $id = preg_replace('/\/+/', '/', $id);

        return trim($id, '/');
    }

    /**
     * Get a specific page template
     *
     * @param  String $id The template id
     * @return String
     */
    public function getTemplate($id)
    {
        $id = \Rareloop\Primer\Primer::cleanId($id);

        // Create the template
        $template = new \Rareloop\Primer\Renderable\Pattern('templates/' . $id);

        $data = $template->getData();
        $view = 'template';

        // Check the data to see if there is a custom view
        if (isset($data['view'])) {
            $view = $data['view'];
        }

        $renderList = new \Rareloop\Primer\Renderable\RenderList(array($template));

        $viewData = new ViewData(array(
            'items' => $renderList->render(false),
            'bodyClass' => 'is-template',
            'template' => $id,
        ));

        Event::fire('render', $viewData);

        return \Rareloop\Primer\Templating\View::render($view, $viewData);
    }

    /**
     * Show a specific page template
     *
     * @param  String $id The template id
     */
    public function showTemplate($id)
    {
        echo $this->getTemplate($id);
    }

    /**
     * Get a selection of patterns
     *
     * @param  Array  $ids        An array of pattern/group/section ids
     * @param  boolean $showChrome Should we show the chrome
     * @return String
     */
    public function getPatterns($ids, $showChrome = true)
    {
        /**
         * A list of groups/patterns to render
         *
         * @var array
         */
        $renderList = new \Rareloop\Primer\Renderable\RenderList();

        foreach ($ids as $id) {

            $id = \Rareloop\Primer\Primer::cleanId($id);

            // Check if the Id is for a pattern or a group
            $parts = explode('/', $id);
            if (count($parts) > 2) {
                // It's a pattern
                $renderList->add(new \Rareloop\Primer\Renderable\Pattern($id));
            } elseif (count($parts) > 1) {
                // It's a group
                $renderList->add(new \Rareloop\Primer\Renderable\Group($id));
            } else {
                // It's a section (e.g. all elements or all components)
                $renderList->add(new \Rareloop\Primer\Renderable\Section($id));
            }
        }

        $bodyClasses = array('not-template');

        // If we're in minimal mode then add a new class to the body
        if(!$showChrome) {
            $bodyClasses[] = 'minimal';
        }

        $viewData = new ViewData(array(
            'items' => $renderList->render($showChrome),
            'bodyClass' => implode(' ', $bodyClasses),
        ));

        Event::fire('render', $viewData);

        return \Rareloop\Primer\Templating\View::render('template', $viewData);
    }

    /**
     * Show a selection of patterns
     *
     * @param  Array  $ids        An array of pattern/group/section ids
     * @param  boolean $showChrome Should we show the chrome
     */
    public function showPatterns($ids, $showChrome = true)
    {
        echo $this->getPatterns($ids, $showChrome);
    }

    /**
     * Get all the patterns/groups/sections
     *
     * @param  boolean $showChrome Should we show the chrome
     * @return String
     */
    public function getAllPatterns($showChrome = true)
    {
        /**
         * A list of groups/patterns to render
         *
         * @var array
         */
        $renderList = new \Rareloop\Primer\Renderable\RenderList();

        // Show all patterns
        $renderList->add(new \Rareloop\Primer\Renderable\Section('elements'));
        $renderList->add(new \Rareloop\Primer\Renderable\Section('components'));

        $bodyClasses = array('not-template');

        // If we're in minimal mode then add a new class to the body
        if(!$showChrome) {
            $bodyClasses[] = 'minimal';
        }

        $viewData = new ViewData(array(
            'items' => $renderList->render($showChrome),
            'bodyClass' => implode(' ', $bodyClasses),
        ));

        Event::fire('render', $viewData);

        return \Rareloop\Primer\Templating\View::render('template', $viewData);
    }

    /**
     * Show all the patterns/groups/sections
     *
     * @param  boolean $showChrome Should we show the chrome
     */
    public function showAllPatterns($showChrome = true)
    {
        echo $this->getAllPatterns($showChrome);
    }

    /**
     * Get the menu listing all the page templates in the site
     * 
     * @return String
     */
    public function getMenu()
    {
        $templates = array();
        $path = Primer::$BASE_PATH . '/patterns/templates';

        if ($handle = opendir($path)) {

            while (false !== ($entry = readdir($handle))) {
                
                if (substr($entry, 0, 1) !== '.') {
                    $templates[] = array(
                        'id' => $entry,
                        'title' => preg_replace('/[^A-Za-z0-9]/', ' ', $entry),
                    );
                }
            }

            closedir($handle);
        }

        $viewData = new ViewData(array(
            'templates' => $templates
        ));

        return \Rareloop\Primer\Templating\View::render('menu', $viewData);
    }

    /**
     * Render a menu listing all the page templates in the site
     */
    public function showMenu()
    {
        echo $this->getMenu();
    }
}
