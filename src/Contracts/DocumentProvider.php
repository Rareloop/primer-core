<?php

namespace Rareloop\Primer\Contracts;

use Rareloop\Primer\Document;
use Rareloop\Primer\Pattern;

interface DocumentProvider
{
    /**
     * Get a list of all the known document id's
     *
     * @return array
     */
    public function allDocumentIds() : array;

    /**
     * Retrieve a Document
     *
     * @param  string $id    The pattern ID
     * @param  string $state The state name
     * @return Rareloop\Primer\Document
     */
    public function getDocument(string $id) : Document;
}
