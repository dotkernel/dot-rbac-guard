<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard;

use Dot\Rbac\Guard\Exception\InvalidArgumentException;
use Dot\Rbac\Guard\Exception\InvalidPluginException;
use Dot\Rbac\Guard\Exception\PluginNotFoundException;
use Psr\Container\ContainerInterface;

use function array_diff;
use function array_keys;
use function class_exists;
use function implode;
use function is_array;
use function is_callable;
use function is_string;
use function sprintf;

/**
 * The shared logic of the guard and guards provider plugin managers.
 *
 * A plugin manager supports the "factories" and "aliases" configuration keys.
 * A factory receives the application container, the plugin name and the build options.
 */
trait PluginManagerTrait
{
    private ContainerInterface $container;

    /** @var array<string, callable|string> */
    private array $pluginFactories = [];

    /** @var array<string, string> */
    private array $pluginAliases = [];

    /** @var array<string, object> */
    private array $pluginInstances = [];

    /**
     * The plugin type, used in the error messages.
     */
    abstract protected function getPluginLabel(): string;

    /**
     * The interface that each plugin must implement.
     *
     * @return class-string
     */
    abstract protected function getPluginInterface(): string;

    public function setFactory(string $name, callable|string $factory): void
    {
        $this->pluginFactories[$name] = $factory;
        unset($this->pluginInstances[$name]);
    }

    public function setAlias(string $alias, string $target): void
    {
        $this->pluginAliases[$alias] = $target;
    }

    public function has(string $id): bool
    {
        return isset($this->pluginFactories[$this->resolveName($id)]);
    }

    /**
     * @param array<string, callable|string> $factories
     * @param array<string, string> $aliases
     * @param array<array-key, mixed> $config
     * @throws InvalidArgumentException
     * @throws InvalidPluginException
     */
    private function initialize(ContainerInterface $container, array $factories, array $aliases, array $config): void
    {
        $this->container = $container;

        $unsupported = array_diff(array_keys($config), ['factories', 'aliases']);
        if ($unsupported !== []) {
            throw new InvalidArgumentException(sprintf(
                'The %s manager supports only the keys "factories" and "aliases". Remove the keys "%s".',
                $this->getPluginLabel(),
                implode('", "', $unsupported)
            ));
        }

        foreach ($this->readMap($config, 'factories') + $factories as $name => $factory) {
            if (! is_callable($factory) && ! is_string($factory)) {
                throw InvalidPluginException::fromFactory($this->getPluginLabel(), $name, $factory);
            }
            $this->setFactory($name, $factory);
        }

        foreach ($this->readMap($config, 'aliases') + $aliases as $alias => $target) {
            if (! is_string($target)) {
                throw new InvalidArgumentException(sprintf(
                    'The alias "%s" of the %s manager must point to a %s name.',
                    $alias,
                    $this->getPluginLabel(),
                    $this->getPluginLabel()
                ));
            }
            $this->setAlias($alias, $target);
        }
    }

    /**
     * Return the shared instance of a plugin. Create it on the first call.
     *
     * @throws PluginNotFoundException
     * @throws InvalidPluginException
     */
    private function getPlugin(string $id): object
    {
        $name = $this->resolveName($id);

        return $this->pluginInstances[$name] ??= $this->buildPlugin($name, null);
    }

    /**
     * Create a new instance of a plugin on each call.
     *
     * @param array<array-key, mixed>|null $options
     * @throws PluginNotFoundException
     * @throws InvalidPluginException
     */
    private function buildPlugin(string $name, ?array $options): object
    {
        $name    = $this->resolveName($name);
        $factory = $this->pluginFactories[$name]
            ?? throw PluginNotFoundException::fromName($this->getPluginLabel(), $name);

        if (is_string($factory) && ! is_callable($factory)) {
            if (! class_exists($factory)) {
                throw InvalidPluginException::fromFactory($this->getPluginLabel(), $name, $factory);
            }
            $factory = new $factory();
        }

        if (! is_callable($factory)) {
            throw InvalidPluginException::fromFactory($this->getPluginLabel(), $name, $factory);
        }

        $plugin    = $factory($this->container, $name, $options);
        $interface = $this->getPluginInterface();
        if (! $plugin instanceof $interface) {
            throw InvalidPluginException::fromInstance($this->getPluginLabel(), $name, $interface, $plugin);
        }

        return $plugin;
    }

    /**
     * Follow the aliases until a name without an alias is found.
     *
     * @throws InvalidArgumentException
     */
    private function resolveName(string $name): string
    {
        $visited = [];
        while (isset($this->pluginAliases[$name])) {
            if (isset($visited[$name])) {
                throw new InvalidArgumentException(sprintf(
                    'The alias "%s" of the %s manager is part of a cycle.',
                    $name,
                    $this->getPluginLabel()
                ));
            }
            $visited[$name] = true;
            $name           = $this->pluginAliases[$name];
        }

        return $name;
    }

    /**
     * @param array<array-key, mixed> $config
     * @return array<string, mixed>
     * @throws InvalidArgumentException
     */
    private function readMap(array $config, string $key): array
    {
        $map = $config[$key] ?? [];
        if (! is_array($map)) {
            throw new InvalidArgumentException(sprintf(
                'The "%s" key of the %s manager must be an array.',
                $key,
                $this->getPluginLabel()
            ));
        }

        $result = [];
        foreach ($map as $name => $value) {
            $result[(string) $name] = $value;
        }

        return $result;
    }
}
