<?php namespace Rareloop\Primer\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Rareloop\Primer\Primer;

class PatternMake extends Command
{
    protected function configure()
    {
        $this
            ->setName('pattern:make')
            ->setDescription('Create a pattern')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Full ID of the pattern (e.g. components/group/name'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('id');

        $cleanId = Primer::cleanId($id);

        // Does the pattern already exist?
        $patternPath = Primer::$PATTERN_PATH . '/' . $cleanId;

        if (file_exists($patternPath)) {
            $output->writeln('');
            $output->writeln('<error>`'.$cleanId.'` already exists</error>');
            $output->writeln('');
            return;
        }

        $success = @mkdir($patternPath, 0777, true);

        if (!$success) {
            $error = error_get_last();
            $output->writeln('');
            $output->writeln('<error>`'.$error['message'].'` already exists</error>');
            $output->writeln('');
            return;
        }

        $templateClass = Primer::$TEMPLATE_CLASS;
        $templateExtension = $templateClass::$extension;

        @touch($patternPath . '/template.' . $templateExtension);
        @touch($patternPath . '/data.json');

        $output->writeln('<info>Pattern `' . $cleanId . '` created</info>');
    }
}
