<?php

namespace Rareloop\Primer\Events;

interface EventInstanceInterface
{
    /**
     * Unbind the event(s) associated with this object
     */
    public function stop();
}
