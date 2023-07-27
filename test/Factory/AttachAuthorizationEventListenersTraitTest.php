<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Factory;

use Dot\Rbac\Guard\Factory\AttachAuthorizationEventListenersTrait;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Laminas\EventManager\EventManager;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class AttachAuthorizationEventListenersTraitTest extends TestCase
{
    use AttachAuthorizationEventListenersTrait;

    protected array $optionsConfig = [
        'dot_authorization' => [
            'guest_role'            => 'guest',
            'role_provider_manager' => [],
            'role_provider'         => [
                'type'    => 'InMemory',
                'options' => [
                    'roles' => [
                        'superuser' => [
                            'permissions' => [
                                'authenticated',
                                'premium',
                            ],
                        ],
                        'admin'     => [
                            'permissions' => [
                                'authenticated',
                                'premium',
                            ],
                        ],
                        'user'      => [
                            'permissions' => [
                                'authenticated',
                                'premium',
                            ],
                        ],
                        'guest'     => [
                            'permissions' => [
                                'unauthenticated',
                            ],
                        ],
                    ],
                ],
            ],
            'assertion_manager'     => [],
            'assertions'            => [],
        ],
    ];

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testAttachListeners(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with(RbacGuardOptions::class)
            ->willReturn(
                new RbacGuardOptions($this->optionsConfig)
            );

        $eventManager = new EventManager();

        $this->attachListeners($container, $eventManager);
    }
}
