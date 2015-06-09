<?php
namespace Tommy\Bundle\JsTemplatingBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tommy\Bundle\JsTemplatingBundle\Service\DumpProcessor;

/**
 * @author Tomfun <tomfun1990@gmail.com>
 */
class DumpCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tommy:js:dump')
            ->setDescription('Dump bundles\' metadata')
            ->addArgument(
                'bundle-name',
                InputArgument::OPTIONAL,
                'Who do you want to greet?'
            )
            ->addArgument(
                'type',
                InputArgument::OPTIONAL,
                'Who do you want to greet?'
            )
            ->addOption(
                'screen',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will yell in uppercase letters'
            )
            ->addOption(
                'dump',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will yell in uppercase letters'
            )
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('screen')) {
            $style = new OutputFormatterStyle('green', 'black', ['bold', 'blink']);
            $output->getFormatter()->setStyle('fire', $style);

            $output->writeln('<info>begin dumping</info>');
        }
        /** @var DumpProcessor $processor */
        $processor = $this->getContainer()->get('tommy_js_templating.dump_processor');
//        $name = $input->getArgument('bundle-name');
//        if ($name) {
//            $text = 'Hello '.$name;
//        } else {
//            $text = 'Hello';
//        }

        if ($input->getOption('dump')) {
            if ($processor->isUseSymLinks()) {
                $output->writeln('<comment>use symlinks</comment>');
            }
            $output->writeln('<info>use destination dirs:</info>');
            foreach ($processor->getBasePaths() as $type => $path) {
                $output->writeln($type . ': <comment>' . $path . '</comment>');
            }
            $processor->dump();
        } elseif ($input->getOption('screen')) {
            $output->write($processor->buildJson($input->getArgument('bundle-name')));
        } else {
            $processor->exportJsonFile($input->getArgument('bundle-name'));
            $output->writeln('file placed: <comment>' . $processor->getExportJsonFile() . '</comment>');
        }
        if (!$input->getOption('screen')) {
            $output->writeln('<fire>OK</fire>');
        }
    }
}