<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Exception;

use Psr\Container\ContainerExceptionInterface;

use function get_debug_type;
use function sprintf;

class InvalidPluginException extends RuntimeException implements ContainerExceptionInterface
{
    public static function fromInstance(string $label, string $name, string $expected, mixed $instance): self
    {
        return new self(sprintf(
            'The %s "%s" must implement %s, %s given.',
            $label,
            $name,
            $expected,
            get_debug_type($instance)
        ));
    }

    public static function fromFactory(string $label, string $name, mixed $factory): self
    {
        return new self(sprintf(
            'The factory of the %s "%s" must be callable or the name of an invokable class, %s given.',
            $label,
            $name,
            get_debug_type($factory)
        ));
    }
}
