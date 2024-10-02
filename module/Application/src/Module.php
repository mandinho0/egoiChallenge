<?php

declare(strict_types=1);

namespace Application;

use Laminas\Mvc\MvcEvent;
use Laminas\View\Model\JsonModel;

class Module
{
    public function getConfig(): array
    {
        /** @var array $config */
        $config = include __DIR__ . '/../config/module.config.php';
        return $config;
    }

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();

        $sharedEventManager->attach(
            __CLASS__,
            MvcEvent::EVENT_RENDER,
            function (MvcEvent $e) {
                if ($e->getResult() instanceof JsonModel) {
                    $e->getViewModel()->setTerminal(true);
                }
            },
            100
        );
    }
}
