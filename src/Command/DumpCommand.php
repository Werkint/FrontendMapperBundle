<?php
namespace Werkint\Bundle\FrontendMapperBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Werkint\Bundle\FrontendMapperBundle\Configuration\NamespaceMapping;

/**
 * Дампит данные бандлов
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class DumpCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('werkint:frontendmapper:dump');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write(json_encode(
            $this->getNamespaceMapping()->getRegisteredPaths()
        ));
    }

    /**
     * @return NamespaceMapping
     */
    protected function getNamespaceMapping()
    {
        return $this->getContainer()->get('werkint_frontend_mapper.namespace_mapping');
    }
}