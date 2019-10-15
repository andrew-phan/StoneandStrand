<?php

namespace WDC\Criteo\Console\Feed;

use Symfony\Component\Console\Command\Command;
use Magento\Framework\App\State;
use WDC\Criteo\Api\Feed\GeneratorInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand
    extends Command
{
    protected $_generatorInterface;
    protected $_state;
    
    public function __construct(
        GeneratorInterface $generatorInterface,
        State $state,
        $name = null
    )
    {
        $this->_generatorInterface = $generatorInterface;
        $this->_state = $state;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('wdc:criteo:feed:generate')
            ->setDescription("Exports a criteo feed")
            ->addArgument(
                'filename',
                InputArgument::REQUIRED,
                'What is the filename to export?'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_state->setAreaCode('adminhtml');
        $filename = $input->getArgument('filename');
        $this->_generatorInterface->generate($filename);
        echo "Imported feed $filename.\n";
    }
}