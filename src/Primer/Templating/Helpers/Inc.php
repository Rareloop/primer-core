<?php namespace Rareloop\Primer\Templating\Helpers;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use \InvalidArgumentException;
use Rareloop\Primer\Templating\Handlebars;
use Rareloop\Primer\Templating\ViewData;
use Rareloop\Primer\FileSystem;
use Rareloop\Primer\Events\Event;

class Inc implements Helper
{
    /**
     * Execute the helper
     *
     * @param \Handlebars\Template $template The template instance
     * @param \Handlebars\Context  $context  The current context
     * @param array                $args     The arguments passed the the helper
     * @param string               $source   The source
     *
     * @return mixed
     */
    public function execute(Template $template, Context $context, $args, $source)
    {
        $args = explode(' ', $args);
                    
        if (count($args) > 0) {
            $partialId = $args[0];

            // Check if we're asking for a variable in the current context first
            try {
                $partialId = $context->get($partialId, true);
            } catch (InvalidArgumentException $e) {

            }

            // Is there an alias within the default data that we should use as the primary data source?
            // This is useful for customising patterns when building a template
            $dataAlias = false;

            if (count($args) > 1) {
                $dataAlias = $args[1];

                @list($name, $value) = explode("=", $dataAlias);

                if ($name === "data" && isset($value)) {
                    $dataAlias = trim($value, '"');
                } else {
                    $dataAlias = false;
                }
            }

            $partial = Handlebars::instance()->loadPartial($partialId);

            // Get the default data for this pattern
            $defaultData = FileSystem::getDataForPattern($partialId);

            // Parent data - e.g. elements/button is the parent of elements/button~primary
            $parentData = array();

            // Load parent data if this is inherit
            if (preg_match('/(.*?)~.*?/', $partialId, $matches)) {

                $parentData = FileSystem::getDataForPattern($matches[1]);
            }

            // Merge the parent and pattern data together, giving preference to the pattern data
            $defaultData = array_replace_recursive((array)$parentData, (array)$defaultData);

            // Alias data
            $aliasData = array();

            // Get the filename from the partial id
            $parts = explode('/', $partialId);
            $filename = end($parts);

            // We need to do this by hand as the Handlebars.php implementation blacklists the ~ char
            $contextFrame = $context->last();

            if($contextFrame instanceof ViewData) {
                $contextFrame = $contextFrame->toArray();
            }

            // Do we have specialist data for this instance of this pattern?
            if ($dataAlias) {
                $aliasKey = $filename . ":" . $dataAlias;

                $aliasData = isset($contextFrame[$aliasKey]) ? (array)$contextFrame[$aliasKey] : array();
            }

            // Get the data passed in by the parent template
            $passedInData = isset($contextFrame[$filename]) ? $contextFrame[$filename] : array();
            
            if($passedInData instanceof ViewData) {
                $passedInData = $passedInData->toArray();
            }

            // Merge the passed in and default data
            $mergedData = new ViewData(array_replace_recursive((array)$defaultData, (array)$passedInData, $aliasData));

            Event::fire('pattern.' . $partialId, $mergedData);

            return $partial->render($mergedData);
        } else {
            return "";
        }
    }
}