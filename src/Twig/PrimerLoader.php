<?php

namespace Rareloop\Primer\Twig;

use Rareloop\Primer\Contracts\TemplateProvider;
use Twig\Loader\LoaderInterface;
use Twig_Source;

class PrimerLoader implements LoaderInterface
{
    protected $templateProvider;

    public function __construct(TemplateProvider $templateProvider)
    {
        $this->templateProvider = $templateProvider;
    }

    /**
     * {Returns the source context for a given template logical name.
     *
     * @param string $name The template logical name
     *
     * @return \Twig\Source
     *
     * @throws \Twig\Error\LoaderError When $name is not found
     */
    public function getSourceContext($name)
    {
        return new Twig_Source($this->templateProvider->getPatternTemplate($name), $name);
    }

    /**
     * Gets the cache key to use for the cache for a given template name.
     *
     * @param string $name The name of the template to load
     *
     * @return string The cache key
     *
     * @throws \Twig\Error\LoaderError When $name is not found
     */
    public function getCacheKey($name)
    {
        return md5($name);
    }

    /**
     * Returns true if the template is still fresh.
     *
     * @param string    $name The template name
     * @param timestamp $time The last modification time of the cached template
     *
     * @return bool    true if the template is fresh, false otherwise
     *
     * @throws \Twig\Error\LoaderError When $name is not found
     */
    public function isFresh($name, $time)
    {
        return $time < $this->templateProvider->getPatternTemplateLastModified($name);
    }

    /**
     * Check if we have the source code of a template, given its name.
     *
     * @param string $name The name of the template to check if we can load
     *
     * @return bool    If the template source code is handled by this loader or not
     */
    public function exists($name)
    {
        return $this->templateProvider->patternExists($name);
    }
}
