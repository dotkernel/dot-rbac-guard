<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Fixture;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

use function sprintf;

/**
 * NotFoundExceptionInterface extends ContainerExceptionInterface, so this class implements both.
 */
final class ServiceNotFoundException extends RuntimeException implements NotFoundExceptionInterface
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Service "%s" is not registered.', $id));
    }
}
