<?php

namespace Ubermanu\Email\Console\Command;

use Magento\Developer\Model\Config\Source\WorkflowType;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Store\Model\Store;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SendCommand extends Command
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
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

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
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Email\Model\Template\Config $emailConfig,
        $name = null
    ) {
        parent::__construct($name);
        $this->_state = $state;
        $this->_phraseRenderer = $phraseRenderer;
        $this->_transportBuilder = $transportBuilder;
        $this->_scopeConfig = $scopeConfig;
        $this->_emailConfig = $emailConfig;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('email:send');
        $this->setDescription('Send a transactional email template');

        $this->addOption(
            'template',
            't',
            InputOption::VALUE_REQUIRED,
            'Email template identifier'
        );

        $this->addOption(
            'store',
            's',
            InputOption::VALUE_OPTIONAL,
            'Store ID',
            Store::DEFAULT_STORE_ID
        );

        $this->addOption(
            'vars',
            'i',
            InputOption::VALUE_OPTIONAL,
            'File that contains the variables to inject into the template (YAML)'
        );

        $this->addArgument(
            'email',
            InputArgument::REQUIRED,
            'The email address of the receiver'
        );
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);

        if ($this->_scopeConfig->getValue(WorkflowType::CONFIG_NAME_PATH) === WorkflowType::CLIENT_SIDE_COMPILATION) {
            throw new LocalizedException(__('Client side compilation is not supported for this command.'));
        }

        $templateId = $input->getOption('template');
        $storeId = $input->getOption('store');
        $receiverEmail = $input->getArgument('email');

        $variables = [];

        if ($input->getOption('vars')) {
            try {
                $variables = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($input->getOption('vars')));
            } catch (\Symfony\Component\Yaml\Exception\ParseException $e) {
                throw new LocalizedException(__('Error parsing variables file: %1', [$e->getMessage()]));
            }
        }

        Phrase::setRenderer($this->_phraseRenderer);

        $template = $this->_transportBuilder
            ->setTemplateIdentifier($templateId)
            ->setTemplateOptions(
                [
                    'area' => $this->_emailConfig->getTemplateArea($templateId),
                    'store' => $storeId,
                ]
            )
            ->setFromByScope('general', $storeId)
            ->setTemplateVars($variables)
            ->addTo($receiverEmail);

        $template->getTransport()->sendMessage();
    }
}
