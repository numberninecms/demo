<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ComposerExtension extends AbstractExtension
{
    private array $packages = [];

    public function getFunctions(): array
    {
        return [
            new TwigFunction('package_version', [$this, 'packageVersion']),
        ];
    }

    public function packageVersion(string $packageName): string
    {
        return $this->getPackage($packageName)['version'] ?? '';
    }

    private function getPackage(string $name): array
    {
        return current(array_filter(
            $this->getPackages(),
            static fn (array $package) => $package['name'] === $name,
        )) ?: [];
    }

    private function getPackages(): array
    {
        if (!$this->packages) {
            /** @noinspection JsonEncodingApiUsageInspection */
            $this->packages = json_decode(
                file_get_contents(__DIR__ . '/../../../vendor/composer/installed.json'),
                true,
                512,
            )['packages'];
        }

        return $this->packages;
    }
}
