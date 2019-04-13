<?php

namespace Rareloop\Primer;

use Rareloop\Primer\Contracts\Arrayable;

class Pattern implements Arrayable
{
    protected $id;
    protected $stateData = [];
    protected $stateName = 'default';
    protected $stateOptions = ['default'];
    protected $template;

    public function __construct(
        string $id,
        array $stateData,
        string $template,
        string $stateName = 'default',
        array $stateOptions = ['default']
    ) {
        $this->id = $id;
        $this->stateData = $stateData;
        $this->stateName = $stateName;
        $this->stateOptions = $stateOptions;
        $this->template = $template;
    }

    public function id() : string
    {
        return $this->id;
    }

    public function data() : array
    {
        return $this->stateData;
    }

    public function state() : string
    {
        return $this->stateName;
    }

    public function states() : array
    {
        return $this->stateOptions;
    }

    public function title() : string
    {
        return IdHelpers::title($this->id);
    }

    public function template() : string
    {
        return $this->template;
    }

    public function toArray() : array
    {
        return [
            'id' => $this->id(),
            'title' => $this->title(),
            'data' => $this->data(),
            'state' => $this->state(),
            'states' => $this->states(),
            'template' => $this->template(),
        ];
    }
}
