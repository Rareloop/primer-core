<?php

namespace Rareloop\Primer\Contracts;

interface TemplateProvider
{
    /**
     * Get the contents of the template for a given pattern
     *
     * @param  string $id The pattern ID
     * @return string
     */
    public function getPatternTemplate(string $id) : string;

    /**
     * Get when a pattern template was last modified
     *
     * @param  string $id The pattern ID
     * @return int        Unix timestamp of when last modified
     */
    public function getPatternTemplateLastModified(string $id) : int;

    /**
     * Does a given pattern exist?
     *
     * @param  string $id The pattern ID
     * @return bool
     */
    public function patternExists(string $id) : bool;
}
