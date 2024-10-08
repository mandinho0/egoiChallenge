<?php

declare(strict_types=1);

namespace ApplicationTest;

use Application\ConfigProvider;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Application\ConfigProvider;
 */
class ModuleTest extends TestCase
{
    public function testProvidesConfig(): void
    {
        $module = new ConfigProvider();
        $config = $module->getConfig();

        self::assertArrayHasKey('router', $config);
        self::assertArrayHasKey('controllers', $config);
    }
}
