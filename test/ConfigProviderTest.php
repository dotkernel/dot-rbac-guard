<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard;

use Dot\Rbac\Guard\ConfigProvider;
use Dot\Rbac\Guard\Guard\GuardPluginManager;
use Dot\Rbac\Guard\Middleware\ForbiddenHandler;
use Dot\Rbac\Guard\Middleware\RbacGuardMiddleware;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Dot\Rbac\Guard\Provider\GuardsProviderPluginManager;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    protected array $config;

    protected function setup(): void
    {
        $this->config = (new ConfigProvider())();
    }

    public function testHasDependencies(): void
    {
        $this->assertArrayHasKey('dependencies', $this->config);
    }

    public function testHasDotAuthorization(): void
    {
        $this->assertArrayHasKey('dot_authorization', $this->config);
    }

    public function testDependenciesHasFactories(): void
    {
        $factories = $this->config['dependencies']['factories'];
        $this->assertArrayHasKey('factories', $this->config['dependencies']);
        $this->assertArrayHasKey(GuardPluginManager::class, $factories);
        $this->assertArrayHasKey(GuardsProviderPluginManager::class, $factories);
        $this->assertArrayHasKey(RbacGuardOptions::class, $factories);
        $this->assertArrayHasKey(RbacGuardMiddleware::class, $factories);
        $this->assertArrayHasKey(ForbiddenHandler::class, $factories);
    }

    public function testDotAuthorizationHasConfig(): void
    {
        $config = $this->config['dot_authorization'];
        $this->assertArrayHasKey('protection_policy', $config);
        $this->assertArrayHasKey('guards_provider_manager', $config);
        $this->assertArrayHasKey('guard_manager', $config);
        $this->assertArrayHasKey('guards_provider', $config);
        $this->assertArrayHasKey('messages_options', $config);
    }
}
