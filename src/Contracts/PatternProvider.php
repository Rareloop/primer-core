<?php

namespace Rareloop\Primer\Contracts;

use Rareloop\Primer\Pattern;

interface PatternProvider
{
    /**
     * Get a list of all the known pattern id's
     *
     * @return array
     */
    public function allPatternIds() : array;

    /**
     * Does a given pattern exist?
     *
     * @param  string $id The pattern ID
     * @return bool
     */
    public function patternExists(string $id) : bool;

    /**
     * Does a given state exists for a given pattern?
     *
     * All valid pattern's will return true for the `default` state
     *
     * @param  string      $id    The pattern ID
     * @param  string      $state The state name
     * @return bool
     */
    public function patternHasState(string $id, string $state = 'default') : bool;

    /**
     * Retrieve a Pattern
     *
     * @param  string $id    The pattern ID
     * @param  string $state The state name
     * @return Rareloop\Primer\Pattern
     */
    public function getPattern(string $id, string $state = 'default') : Pattern;

    /**
     * Get the data for the given pattern and state
     *
     * @param  string $id    [description]
     * @param  string $state [description]
     * @return [type]        [description]
     */
    public function getPatternStateData(string $id, string $state = 'default') : array;
}
