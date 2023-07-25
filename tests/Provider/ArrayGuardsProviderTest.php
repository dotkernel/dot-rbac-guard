<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Guard\Factory;
use Dot\Rbac\Guard\Guard\GuardInterface;
use Dot\Rbac\Guard\Provider\ArrayGuardsProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class ArrayGuardsProviderTest extends TestCase
{
    protected ArrayGuardsProvider $subject;

    protected Factory $mockFactory;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->mockFactory = $this->createMock(Factory::class);

        $this->subject = new ArrayGuardsProvider(['guard_factory' => $this->mockFactory]);
    }

    public function testGetGuardsEmptyGuards()
    {
        $result = $this->subject->getGuards();

        $this->assertIsArray($result);
    }

    public function testGetGuards()
    {
        $this->mockFactory->expects($this->once())
        ->method('create')
        ->with(['test'])
        ->willReturn(new class implements GuardInterface {
            public function isGranted(ServerRequestInterface $request): bool
            {
                return true;
            }

            public function getPriority(): int
            {
                return 1;
            }
        });
        $this->subject->setGuardsConfig([['test']]);
        $result = $this->subject->getGuards();

        $this->assertIsArray($result);
    }

    public function testGetGuardsConfig()
    {
        $this->subject->setGuardsConfig(['test' => 'config']);
        $result = $this->subject->getGuardsConfig();

        $this->assertIsArray($result);
    }
}
