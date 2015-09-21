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
     * @param  Boolean $resolveAlias Whether or not to resolve data from aliased patterns (e.g. button~outline -> button)
     * @return object     The decoded JSON data
     */
    public static function getDataForPattern($id, $resolveAlias = false)
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

        if ($resolveAlias) {
            // Parent data - e.g. elements/button is the parent of elements/button~primary
            $parentData = array();

            // Load parent data if this is inherit
            if (preg_match('/(.*?)~.*?/', $id, $matches)) {

                $parentData = FileSystem::getDataForPattern($matches[1]);
            }

            // Merge the parent and pattern data together, giving preference to the pattern data
            $data = array_replace_recursive((array)$parentData, (array)$data);
        }

        $viewData = new ViewData($data);

        Event::fire('data.' . $id, $viewData);
        echo 'data.' . $id . "</br>";

        // TODO: Convert codebase so that ViewData objects are the fundamental data object passed around the system
        return $viewData->toArray();
    }
}
