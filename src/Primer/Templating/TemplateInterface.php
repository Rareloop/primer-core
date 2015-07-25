<?php namespace Rareloop\Primer\Templating;

/**
 * Interface defining common templating functions
 */
interface TemplateInterface
{
    /**
     * Loads a template from the filesystem
     *
     * @param String $directory The full path to the templates parent folder
     * @param String $filename The name of the file (without extension)
     * @return Template Chainable interface
     */
    public function load($directory, $filename);

    /**
     * Render the current object
     *
     * @param  Object $data An associative array to pass to the template
     * @return String              HTML text
     */
    public function render($data);

    /**
     * Returns a list of acceptable file extensions
     *
     * @return Array A list of strings
     */
    public function extensions();
}
