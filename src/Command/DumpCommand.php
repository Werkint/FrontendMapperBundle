<?php
namespace Werkint\Bundle\FrontendMapperBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Werkint\Bundle\FrontendMapperBundle\Service\PathsStorage;

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
        $data = $this->getPathsStorage()->getRegisteredPaths(PathsStorage::NS_FRONTEND);

        $data = array_map(function (array $data) {
            return [
                'path' => $data['path'],
                'name' => $data['metadata']['name'],
            ];
        }, $data);

        $output->write(json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * @return PathsStorage
     */
    protected function getPathsStorage()
    {
        return $this->getContainer()->get('werkint_frontend_mapper.pathsstorage');
    }
}