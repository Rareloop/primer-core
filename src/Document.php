<?php

namespace Rareloop\Primer;

use Rareloop\Primer\Contracts\Arrayable;
use Rareloop\Primer\IdHelpers;

class Document implements Arrayable
{
    protected $id;
    protected $content;
    protected $title = '';
    protected $description = '';
    protected $meta = [];

    public function __construct(string $id, string $content)
    {
        $this->id = $id;
        $this->content = $content;

        $this->setTitle(IdHelpers::title($id));
    }

    public function id() : string
    {
        return $this->id;
    }

    public function content() : string
    {
        return $this->content;
    }

    public function setContent(string $content)
    {
        $this->content = $content;
    }

    public function setMeta(array $meta)
    {
        $this->meta = $meta;
    }

    public function meta() : array
    {
        return $this->meta;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function title() : string
    {
        return $this->title;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    public function description() : string
    {
        return $this->description;
    }

    public function toArray() : array
    {
        return [
            'id' => $this->id(),
            'content' => $this->content(),
            'title' => $this->title(),
            'description' => $this->description(),
            'meta' => $this->meta(),
        ];
    }
}
