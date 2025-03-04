<?php

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

class ForceLogin extends Module
{
    /**
     * @var array
     */
    private $allowedControllers = ['authentication', 'password'];

    public function __construct()
    {
        $this->name = 'forcelogin';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Mateusz Serwach';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Force Login');
        $this->description = $this->l('B2B module for forcing login (hiding the content for B2C)');
    }

    public function install(): bool
    {
        return parent::install()
            && $this->registerHook('actionDispatcher')
            && $this->registerHook('displayHeader');
    }

    /**
     * @param array $params
     */
    public function hookActionDispatcher(array $params): void
    {
        if ($this->context->customer->isLogged()) {
            return;
        }

        $currentController = Dispatcher::getInstance()->getController();

        if (!$this->isAllowedController($currentController)) {
            Tools::redirect($this->context->link->getPageLink('authentication', true));
        }
    }

    public function hookDisplayHeader(): void
    {
        if (Dispatcher::getInstance()->getController() === 'authentication') {
            $this->context->controller->registerStylesheet(
                'forcelogin-css',
                $this->_path . 'views/css/forcelogin.css',
                ['media' => 'all', 'priority' => 1]
            );
        }
    }

    private function isAllowedController(string $controller): bool
    {
        return in_array(strtolower($controller), $this->allowedControllers, true);
    }
}
