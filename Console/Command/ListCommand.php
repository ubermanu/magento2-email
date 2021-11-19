<?php

namespace Ubermanu\Email\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
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
        $this->setDescription('List all the available email templates');

        $this->addArgument(
            'filter',
            InputArgument::OPTIONAL,
            'Filter the list by template identifier'
        );

        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filter = $input->getArgument('filter');

        $table = new Table($output);
        $table->setHeaders(['Identifier', 'Title', 'Module']);
        $count = 0;

        foreach ($this->_emailConfig->getAvailableTemplates() as $template) {
            // Filter by template identifier (if provided)
            if ($filter && substr($template['value'], 0, strlen($filter)) !== $filter) {
                continue;
            }
            $table->addRow([$template['value'], $template['label'], $template['group']]);
            $count++;
        }

        if ($count) {
            $table->render();
        } else {
            $output->writeln('No email templates found.');
        }
    }
}
