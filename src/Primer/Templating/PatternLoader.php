<?php namespace Rareloop\Primer\Templating;

use Rareloop\Primer\Primer;
use Handlebars\Loader;
use Handlebars\StringWrapper;

class PatternLoader implements Loader
{
    /**
     * Cached templates
     *
     * @var array
     */
    protected $templates = array();

    public function load($name)
    {
        return new StringWrapper($this->loadPattern($name));
    }

    protected function loadPattern($name)
    {
        // If this is an alias, remove the specialty so that the original template is loaded
        if (strpos($name, "~") !== false) {
            $parts = explode("~", $name);

            if (count($parts) > 1) {
                $name = $parts[0];
            }
        }

        $handlebarsTemplate = null;
        $path = Primer::$BASE_PATH . '/patterns/' . $name;

        if (file_exists($path . '/template.hbs')) {
            $handlebarsTemplate = $path . '/template.hbs';
        } else if (file_exists($path . '/template.handlebars')) {
            $handlebarsTemplate = $path . '/template.handlebars';
        }

        $this->templates[$name] = file_get_contents($handlebarsTemplate);

        return $this->templates[$name];
    }
}
