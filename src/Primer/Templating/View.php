<?php namespace Rareloop\Primer\Templating;

use \Exception;
use Rareloop\Primer\Primer;
use Rareloop\Primer\Events\Event;
use Rareloop\Primer\Templating\ViewData;

/**
 * View helper class for rendering Handlebars templates
 */
class View
{
    /**
     * Load a template and optionally pass in params
     *
     * @param   string $name The name of the template to load (without .handlebars extension)
     * @param   Array|ViewData $params An associative array of variables to export into the view
     * @return  string HTML text
     * @author  Joe Lambert
     **/
    public static function render($name, $params = array())
    {
        if (is_array($params)) {
            $params = new ViewData($params);
        }

        $templateClass = Primer::$TEMPLATE_CLASS;
        $template = new $templateClass(Primer::$VIEW_PATH, $name);

        Event::fire('view.' . $name, $params);

        return $template->render($params);
    }

    public static function composer($name, $callable)
    {
        Event::listen("view.$name", $callable);
    }
}
