<?php namespace Rareloop\Primer;

use Rareloop\Primer\Templating\ViewData;
use Rareloop\Primer\Events\Event;

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

use Symfony\Component\Finder\Finder;

use Rareloop\Primer\Renderable\RenderList;
use Rareloop\Primer\Renderable\Pattern;
use Rareloop\Primer\Renderable\Group;
use Rareloop\Primer\Renderable\Section;
use Rareloop\Primer\Templating\View;

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
     * Path to the cache directory
     *
     * @var String
     */
    public static $CACHE_PATH;

    /**
     * Path to the patterns directory
     *
     * @var String
     */
    public static $PATTERN_PATH;

    /**
     * Path to the patterns directory
     *
     * @var String
     */
    public static $VIEW_PATH;

    /**
     * Template class including namespace
     *
     * @var String
     */
    public static $TEMPLATE_CLASS;

    /**
     * Should templates be wrapped with a View
     *
     * @var Bool
     */
    public static $WRAP_TEMPLATES;

    /**
     * Singleton accessor
     *
     * @return Primer The singleton instance
     */
    public static function instance()
    {
        if (!isset(Primer::$instance)) {
            Primer::$instance = new Primer;

            Event::fire('primer.init', Primer::$instance);
        }

        return Primer::$instance;
    }

    /**
     * Bootstrap the Primer singleton
     *
     * For backwards compatibility the first param is either a string to the BASE_PATH
     * or it can be an assoc array
     *
     * array(
     *     'basePath' => '',
     *     'patternPath' => '',     // Defaults to PRIMER::$BASE_PATH . '/pattern'
     *     'cachePath' => '',       // Defaults to PRIMER::$BASE_PATH . '/cache'
     *     'templateEngine' => ''   // Defaults to 'Rareloop\Primer\TemplateEngine\Handlebars\Template'
     * )
     *
     * @param  String|Array $options
     * @return Rareloop\Primer\Primer
     */
    public static function start($options)
    {
        ErrorHandler::register();
        ExceptionHandler::register();

        // Default params
        $defaultTemplateClass = 'Rareloop\Primer\TemplateEngine\Handlebars\Template';

        // Work out what we were passed as an argument
        if (is_string($options)) {
            // Backwards compatibility
            Primer::$BASE_PATH = realpath($options);
            Primer::$PATTERN_PATH = Primer::$BASE_PATH . '/patterns';
            Primer::$PATTERN_PATH = Primer::$BASE_PATH . '/views';
            Primer::$CACHE_PATH = Primer::$BASE_PATH . '/cache';
            Primer::$TEMPLATE_CLASS = $defaultTemplateClass;
            Primer::$WRAP_TEMPLATES = true;
        } else {
            // New more expressive function params
            if (!isset($options['basePath'])) {
                throw new Exception('No `basePath` param passed to Primer::start()');
            }

            Primer::$BASE_PATH = realpath($options['basePath']);

            Primer::$PATTERN_PATH = isset($options['patternPath']) ? realpath($options['patternPath']) : Primer::$BASE_PATH . '/patterns';
            Primer::$VIEW_PATH = isset($options['viewPath']) ? realpath($options['viewPath']) : Primer::$BASE_PATH . '/views';
            Primer::$CACHE_PATH = isset($options['cachePath']) ? realpath($options['cachePath']) : Primer::$BASE_PATH . '/cache';
            Primer::$TEMPLATE_CLASS = isset($options['templateClass']) ? $options['templateClass'] : $defaultTemplateClass;

            Primer::$WRAP_TEMPLATES = isset($options['wrapTemplate']) ? $options['wrapTemplate'] : true;
        }

        // Attempt to load all `init.php` files. We shouldn't really have to do this here but currently
        // include functions don't actually load patterns so its hard to track when things are loaded
        // TODO: Move everything through Pattern constructors
        $finder = new Finder();
        $initFiles = $finder->in(Primer::$PATTERN_PATH)->files()->name('init.php');

        foreach ($initFiles as $file) {
            include_once($file);
        }

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
        // Default system wide template wrapping config
        $wrapTemplate = Primer::$WRAP_TEMPLATES;

        $id = Primer::cleanId($id);

        // Create the template
        $template = new Pattern('templates/' . $id);

        $templateData = new ViewData([
            "primer" => [
                'bodyClass' => 'is-template',
                'template' => $id,
            ],
        ]);

        $data = $template->getData();

        // Template level wrapping config
        if (isset($data->primer->wrapTemplate)) {
            $wrapTemplate = $data->primer->wrapTemplate;
        }

        if ($wrapTemplate) {
            $view = 'template';

            // Check the data to see if there is a custom view
            if (isset($data->primer) && isset($data->primer->view)) {
                $view = $data->primer->view;
            }

            $templateData->primer->items = $template->render(false);

            Event::fire('render', $templateData);

            return View::render($view, $templateData);
        } else {
            // Merge the data we would have passed into the view into the template
            $template->setData($templateData);

            // Get a reference to the template data so that we can pass it to anyone who's listening
            $viewData = $template->getData();
            Event::fire('render', $viewData);

            return $template->render(false);
        }
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

    protected function prepareViewForPatterns(RenderList $renderList, $showChrome = false)
    {
        $bodyClasses = array('not-template');

        // If we're in minimal mode then add a new class to the body
        if (!$showChrome) {
            $bodyClasses[] = 'minimal';
        }

        $viewData = new ViewData([
            'primer' => [
                'items' => $renderList->render($showChrome),
                'bodyClass' => implode(' ', $bodyClasses),
                'components' => $this->getComponents(),
                'elements' => $this->getElements(),
                'templates' => $this->getTemplates()
            ]
        ]);

        Event::fire('render', $viewData);

        return View::render('template', $viewData);
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
        $renderList = new RenderList();

        foreach ($ids as $id) {
            $id = Primer::cleanId($id);

            // Check if the Id is for a pattern or a group
            $parts = explode('/', $id);
            if (count($parts) > 2) {
                // It's a pattern
                $renderList->add(new Pattern($id));
            } elseif (count($parts) > 1) {
                // It's a group
                $renderList->add(new Group($id));
            } else {
                // It's a section (e.g. all elements or all components)
                $renderList->add(new Section($id));
            }
        }

        return $this->prepareViewForPatterns($renderList, $showChrome);
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
        $renderList = new RenderList();

        // Show all patterns
        $renderList->add(new Section('elements'));
        $renderList->add(new Section('components'));

        return $this->prepareViewForPatterns($renderList, $showChrome);
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
     * Get the list of templates available. Returns an array of associative arrays
     *
     * e.g.
     *
     * array(
     *     array(
     *         'id' => 'home',
     *         'title' => 'Home',
     *     ),
     *     array(
     *         'id' => 'about-us',
     *         'title' => 'About Us'
     *     )
     * );
     *
     * @return array
     */
    public function getTemplates()
    {
        $templates = array();

        if (!file_exists(Primer::$PATTERN_PATH . '/templates')) {
            return $templates;
        }

        if ($handle = opendir(Primer::$PATTERN_PATH . '/templates')) {
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

        return $templates;
    }

    public function getComponents()
    {
        $components = array();

        if (!file_exists(Primer::$PATTERN_PATH . '/components')) {
            return $components;
        }

        if ($handle = opendir(Primer::$PATTERN_PATH . '/components')) {
            while (false !== ($entry = readdir($handle))) {
                if (substr($entry, 0, 1) !== '.') {
                    $components[] = array(
                        'id' => $entry,
                        'title' => preg_replace('/[^A-Za-z0-9]/', ' ', $entry),
                    );
                }
            }

            closedir($handle);
        }

        return $components;
    }

    public function getElements()
    {
        $elements = array();

        if (!file_exists(Primer::$PATTERN_PATH . '/elements')) {
            return $elements;
        }

        if ($handle = opendir(Primer::$PATTERN_PATH . '/elements')) {
            while (false !== ($entry = readdir($handle))) {
                if (substr($entry, 0, 1) !== '.') {
                    $elements[] = array(
                        'id' => $entry,
                        'title' => preg_replace('/[^A-Za-z0-9]/', ' ', $entry),
                    );
                }
            }

            closedir($handle);
        }

        return $elements;
    }

    /**
     * Get the menu listing all the page templates in the site
     *
     * @return String
     */
    public function getMenu()
    {
        $viewData = new ViewData(array(
            'components' => $this->getComponents(),
            'elements' => $this->getElements(),
            'templates' => $this->getTemplates()
        ));

        return View::render('menu', $viewData);
    }

    /**
     * Render a menu listing all the page templates in the site
     */
    public function showMenu()
    {
        echo $this->getMenu();
    }
}
