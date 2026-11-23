<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Fixture;

use Dot\Rbac\Guard\Guard\GuardInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * A guard with a fixed result and priority. Counts its calls.
 */
final class Guard implements GuardInterface
{
    public int $calls = 0;

    public function __construct(private readonly bool $result, private readonly int $priority = 1)
    {
    }

    public function isGranted(ServerRequestInterface $request): bool
    {
        $this->calls++;

        return $this->result;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}
