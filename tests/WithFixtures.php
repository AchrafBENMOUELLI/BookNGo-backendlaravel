<?php

namespace Tests;

trait WithFixtures
{
    private ?array $fixtures = null;
    private function loadFixture(string $path): mixed
    {
        if ($this->fixtures === null) {
            $this->fixtures = json_decode(
                file_get_contents(__DIR__ . '/fixtures/data.json'),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        }
        $keys = explode('.', $path);
        $value = $this->fixtures;
        foreach ($keys as $key) {
            $value = $value[$key] ?? throw new \RuntimeException("Fixture key '$path' not found");
        }
        return $value;
    }
}
