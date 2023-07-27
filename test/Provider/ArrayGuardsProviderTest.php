<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Provider;

use Dot\Rbac\Guard\Guard\Factory;
use Dot\Rbac\Guard\Guard\GuardInterface;
use Dot\Rbac\Guard\Provider\ArrayGuardsProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class ArrayGuardsProviderTest extends TestCase
{
    protected ArrayGuardsProvider $subject;

    protected Factory|MockObject $mockFactory;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->mockFactory = $this->createMock(Factory::class);

        $this->subject = new ArrayGuardsProvider(['guard_factory' => $this->mockFactory]);
    }

    public function testGetGuardsEmptyGuards(): void
    {
        $result = $this->subject->getGuards();

        $this->assertEmpty($result);
    }

    public function testGetGuards(): void
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

        $this->assertCount(1, $this->subject->getGuards());
    }

    public function testGetGuardsConfig(): void
    {
        $this->subject->setGuardsConfig(['test' => 'config']);

        $this->assertCount(1, $this->subject->getGuardsConfig());
    }
}
