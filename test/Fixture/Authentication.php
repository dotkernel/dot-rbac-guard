<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Fixture;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\AuthenticationResult;
use Dot\Authentication\Identity\IdentityInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * An authentication service with or without an identity.
 */
final class Authentication implements AuthenticationInterface
{
    public function __construct(private readonly bool $hasIdentity)
    {
    }

    public function challenge(ServerRequestInterface $request): ResponseInterface
    {
        return new EmptyResponse(401);
    }

    public function authenticate(ServerRequestInterface $request): AuthenticationResult
    {
        throw new LogicException('Not used in the tests');
    }

    public function hasIdentity(): bool
    {
        return $this->hasIdentity;
    }

    public function getIdentity(): ?IdentityInterface
    {
        return null;
    }

    public function setIdentity(IdentityInterface $identity): void
    {
    }

    public function clearIdentity(): void
    {
    }
}
