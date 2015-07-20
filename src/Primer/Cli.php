<?php namespace Rareloop\Primer;

use Symfony\Component\Console\Application;
use Rareloop\Primer\Events\Event;

class Cli
{
    /**
     * The singleton instance
     *
     * @var Cli
     */
    protected static $instance;

    protected $app;

    public function __construct()
    {
        $this->app = new Application;
    }

    public static function instance()
    {
        if (!isset(Cli::$instance)) {
            Cli::$instance = new Cli;

            // Add the inbuilt commands
            Cli::$instance->add(new \Rareloop\Primer\Commands\PatternMake);
            Cli::$instance->add(new \Rareloop\Primer\Commands\Serve);

            Event::fire('cli.init', Cli::$instance);
        }

        return Cli::$instance;
    }

    public static function run()
    {
        Cli::instance()->app->run();
    }

    public static function add($command)
    {
        return Cli::instance()->app->add($command);
    }
}
