<?php

namespace Ubermanu\Email\Console\Command;

use Magento\Framework\Phrase;
use Magento\Store\Model\Store;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DumpCommand extends Command
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $_state;

    /**
     * @var \Magento\Framework\Phrase\RendererInterface
     */
    protected $_phraseRenderer;

    /**
     * @var \Magento\Framework\Mail\Template\FactoryInterface
     */
    protected $_templateFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Email\Model\Template\Config
     */
    protected $_emailConfig;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Framework\Phrase\RendererInterface $phraseRenderer,
        \Magento\Framework\Mail\Template\FactoryInterface $templateFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Email\Model\Template\Config $emailConfig,
        $name = null
    ) {
        parent::__construct($name);
        $this->_state = $state;
        $this->_phraseRenderer = $phraseRenderer;
        $this->_templateFactory = $templateFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_emailConfig = $emailConfig;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('email:dump');
        $this->setDescription('Dump a transactional email template.');

        $this->addOption(
            'template',
            't',
            InputOption::VALUE_REQUIRED,
            'Template name to generate'
        );

        $this->addOption(
            'store',
            's',
            InputOption::VALUE_OPTIONAL,
            'Store ID',
            Store::DEFAULT_STORE_ID
        );

        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);

        if ($this->_scopeConfig->getValue('dev/front_end_development_workflow/type') === 'client_side_compilation') {
            return $output->writeln('<error>Please disable the frontend compilation before sending an email.</error>');
        }

        $templateId = $input->getOption('template');
        $storeId = $input->getOption('store');

        // TODO: Load variables from external json file?

        Phrase::setRenderer($this->_phraseRenderer);

        $template = $this->generateTemplate($templateId, [], $storeId);
        echo $template->processTemplate();
    }

    /**
     * Generate template.
     *
     * @param string $templateId
     * @param array $templateVariables
     * @param int $storeId
     *
     * @return \Magento\Framework\Mail\TemplateInterface
     */
    public function generateTemplate($templateId, $templateVariables, $storeId)
    {
        return $this->_templateFactory
            ->get($templateId)
            ->setOptions(
                [
                    'area' => $this->_emailConfig->getTemplateArea($templateId),
                    'store' => $storeId,
                ]
            )
            ->setVars($templateVariables);
    }
}
