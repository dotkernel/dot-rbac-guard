<?php

declare(strict_types=1);

namespace DotTest\Rbac\Guard\Fixture;

use Mezzio\Template\TemplateRendererInterface;

use function is_array;
use function is_scalar;

/**
 * Renders "template: message" and records the last template data.
 *
 * It works with the interface of mezzio-template 2.x and 3.x: "$params" has no native type,
 * and addPath() and getPaths() exist only for 2.x.
 */
final class TemplateRenderer implements TemplateRendererInterface
{
    public string $lastTemplate = '';

    /** @var array<array-key, mixed> */
    public array $lastParams = [];

    /**
     * @param array<array-key, mixed>|object $params
     */
    public function render(string $name, $params = []): string
    {
        $this->lastTemplate = $name;
        $this->lastParams   = is_array($params) ? $params : [];
        $message            = $this->lastParams['message'] ?? '';

        return $name . ': ' . (is_scalar($message) ? (string) $message : '');
    }

    public function addPath(string $path, ?string $namespace = null): void
    {
    }

    /**
     * @return array<never>
     */
    public function getPaths(): array
    {
        return [];
    }

    public function addDefaultParam(string $templateName, string $param, mixed $value): void
    {
    }
}
