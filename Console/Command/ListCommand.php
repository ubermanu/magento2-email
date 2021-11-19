<?php

namespace Ubermanu\Email\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends Command
{
    /**
     * @var \Magento\Email\Model\Template\Config
     */
    protected $_emailConfig;

    public function __construct(
        \Magento\Email\Model\Template\Config $emailConfig,
        $name = null
    ) {
        parent::__construct($name);
        $this->_emailConfig = $emailConfig;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('email:list');
        $this->setDescription('List all the available email templates.');

        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->_emailConfig->getAvailableTemplates() as $template) {
            $output->writeln(sprintf('<info>%s</info>: %s', $template['value'], $template['label']));
        }
    }
}
