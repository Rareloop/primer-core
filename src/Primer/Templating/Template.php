<?php namespace Rareloop\Primer\Templating;

use Rareloop\Primer\Templating\TemplateInterface;

abstract class Template implements TemplateInterface
{
    /**
     * Array of file extensions
     *
     * @var array
     */
    protected $extensions = array();

    protected $directory;

    protected $id;


    /**
     * Creates a new template object
     *
     * @param String $directory The full path to the templates parent folder
     * @param String $id The id of the file (could just be the name of a view or a pattern e.g. elements/forms/input)
     */
    public function __construct($directory, $filename)
    {
        $this->load($directory, $filename);
    }

    public function load($directory, $filename)
    {
        $this->directory = $directory;
        $this->filename = $filename;
    }

    /**
     * Default implementation of the extensions function
     *
     * @return Array A list of strings
     */
    public function extensions()
    {
        return $this->extensions;
    }
}
