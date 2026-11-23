<?php

declare(strict_types=1);

namespace Dot\Rbac\Guard\Exception;

use Psr\Container\NotFoundExceptionInterface;

use function sprintf;

class PluginNotFoundException extends RuntimeException implements NotFoundExceptionInterface
{
    public static function fromName(string $label, string $name): self
    {
        return new self(sprintf('The %s "%s" is not registered.', $label, $name));
    }
}
