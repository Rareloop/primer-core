<?php namespace Rareloop\Primer\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Serve extends Command
{
    protected function configure()
    {
        $this
            ->setName('serve')
            ->setDescription('Create a standalone server')
            ->addArgument(
                'port',
                InputArgument::OPTIONAL,
                'Port to create the server on (default: 8080)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $port = $input->getArgument('port');

        if(!isset($port)) {
            $port = 8080;
        }

        $output->writeln('<info>Server created http://localhost:' . $port . '</info>');
        system('php -S 0.0.0.0:'.$port.' '.__DIR__.'/../../../../../../server.php');
    }
}
