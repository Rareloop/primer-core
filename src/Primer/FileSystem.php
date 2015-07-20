<?php namespace Rareloop\Primer;

use Rareloop\Primer\Templating\ViewData;
use Rareloop\Primer\Events\Event;

/**
 * File system helper class
 */
class FileSystem
{

    /**
     * Retrieve data for a patter
     *
     * @param  String $id The id of the pattern
     * @return object     The decoded JSON data
     */
    public static function getDataForPattern($id)
    {
        $data = array();

        // Load the Patterns default data
        $defaultData = @file_get_contents(Primer::$PATTERN_PATH . '/' . $id . '/data.json');

        if ($defaultData) {
            $json = json_decode($defaultData);

            if ($json) {
                // Merge in the data
                $data += (array)$json;
            }
        }

        $viewData = new ViewData($data);

        Event::fire('data.' . $id, $viewData);

        // TODO: Convert codebase so that ViewData objects are the fundamental data object passed around the system
        return $viewData->toArray();
    }
}
