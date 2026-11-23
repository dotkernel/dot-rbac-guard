<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Exception\InvalidArgumentException;
use Dot\Rbac\Guard\Exception\InvalidPluginException;
use Dot\Rbac\Guard\Exception\PluginNotFoundException;
use Dot\Rbac\Guard\Factory\GuardsProviderFactory;
use Dot\Rbac\Guard\PluginManagerTrait;
use Psr\Container\ContainerInterface;

/**
 * A PSR-11 container for guards providers.
 *
 * It supports the "factories" and "aliases" keys of the "dot_authorization.guards_provider_manager" config.
 */
class GuardsProviderPluginManager implements ContainerInterface
{
    use PluginManagerTrait;

    private const FACTORIES = [
        ArrayGuardsProvider::class => GuardsProviderFactory::class,
    ];

    private const ALIASES = [
        'arrayguardsprovider' => ArrayGuardsProvider::class,
        'arrayGuardsProvider' => ArrayGuardsProvider::class,
        'ArrayGuardsProvider' => ArrayGuardsProvider::class,
        'arrayguards'         => ArrayGuardsProvider::class,
        'arrayGuards'         => ArrayGuardsProvider::class,
        'ArrayGuards'         => ArrayGuardsProvider::class,
    ];

    /**
     * @param array<array-key, mixed> $config
     * @throws InvalidArgumentException
     * @throws InvalidPluginException
     */
    public function __construct(ContainerInterface $container, array $config = [])
    {
        $this->initialize($container, self::FACTORIES, self::ALIASES, $config);
    }

    /**
     * Return the shared instance of a guards provider. Create it on the first call.
     *
     * @throws PluginNotFoundException
     * @throws InvalidPluginException
     */
    public function get(string $id): GuardsProviderInterface
    {
        $provider = $this->getPlugin($id);
        if (! $provider instanceof GuardsProviderInterface) {
            throw InvalidPluginException::fromInstance(
                $this->getPluginLabel(),
                $id,
                GuardsProviderInterface::class,
                $provider
            );
        }

        return $provider;
    }

    /**
     * Create a new instance of a guards provider on each call.
     *
     * @param array<array-key, mixed>|null $options
     * @throws PluginNotFoundException
     * @throws InvalidPluginException
     */
    public function build(string $name, ?array $options = null): GuardsProviderInterface
    {
        $provider = $this->buildPlugin($name, $options);
        if (! $provider instanceof GuardsProviderInterface) {
            throw InvalidPluginException::fromInstance(
                $this->getPluginLabel(),
                $name,
                GuardsProviderInterface::class,
                $provider
            );
        }

        return $provider;
    }

    protected function getPluginLabel(): string
    {
        return 'guards provider';
    }

    protected function getPluginInterface(): string
    {
        return GuardsProviderInterface::class;
    }
}
