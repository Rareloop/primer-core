<?php namespace Rareloop\Primer\Templating;

use \Handlebars\Loader\FilesystemLoader;

class HandlebarsLoader extends FilesystemLoader
{
    private $_extension = '.hbs';
    private $_prefix = '';
    private $_templates = array();

    /**
     * Helper function for getting a Handlebars template file name.
     * Attempts to find variants of .hbs, .handlebars etc
     *
     * @param string $name template name
     *
     * @return string Template file name
     */
    protected function getFileName($name)
    {
        foreach ($this->baseDir as $baseDir) {
            $fileName = $baseDir . '/';
            $fileParts = explode('/', $name);
            $file = array_pop($fileParts);
            if (substr($file, strlen($this->_prefix)) !== $this->_prefix) {
                $file = $this->_prefix . $file;
            }
            $fileParts[] = $file;
            $fileName .= implode('/', $fileParts);
            $lastCharacters = substr($fileName, 0 - strlen($this->_extension));
            if ($lastCharacters !== $this->_extension) {
                $fileName .= $this->_extension;
            }
            if (file_exists($fileName)) {
                return $fileName;
            }
            else {
                // Attempt to find .handlebars rather than .hbs
                $fileName = str_replace($this->_extension, '.handlebars', $fileName);

                if(file_exists($fileName)) {
                    return $fileName;
                }
            }
        }
        return false;
    }
}