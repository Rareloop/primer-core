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

        $engine = new \Handlebars\Handlebars(array(
            'loader' => new \Rareloop\Primer\Templating\HandlebarsLoader(Primer::$BASE_PATH.'/views/')
        ));

        Event::fire('handlebars.new', $engine);

        Event::fire('view.' . $name, $params);

        return $engine->render($name, $params);
    }
}
