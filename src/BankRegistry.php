<?php

declare(strict_types=1);

namespace NigerianBankResolver;

use JsonException;
use RuntimeException;

final class BankRegistry
{
    /** @var list<Bank> */
    private array $banks = [];

    /**
     * @param list<array<string, mixed>> $banksData
     */
    public function __construct(array $banksData = [])
    {
        if ($banksData === []) {
            $banksData = $this->loadDefaultData();
        }

        $phoneCapabilities = $this->loadPhoneCapabilities();
        foreach ($banksData as $data) {
            $slug = (string) ($data['slug'] ?? '');
            $this->banks[] = new Bank(
                (string) $data['name'],
                isset($data['code']) ? (string) $data['code'] : null,
                isset($data['prefix']) ? (string) $data['prefix'] : null,
                $slug,
                isset($data['logo_path']) ? (string) $data['logo_path'] : null,
                isset($phoneCapabilities[$slug]),
            );
        }
    }

    /**
     * @return list<Bank>
     */
    public function all(): array
    {
        // Reasoning: return the registry's ordered list so resolution remains deterministic for callers and tests.
        return $this->banks;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function loadDefaultData(): array
    {
        // Reasoning: keep the resolver offline-first by loading its committed dataset instead of querying a remote service.
        $jsonPath = __DIR__ . '/../data/banks.json';
        return $this->decodeList($jsonPath, 'bank dataset');
    }

    /**
     * @return array<string, true>
     */
    private function loadPhoneCapabilities(): array
    {
        // Reasoning: isolate the small, evidence-backed capability registry so unsupported fintech assumptions cannot enter the resolver implicitly.
        $jsonPath = __DIR__ . '/../data/phone-resolution.json';
        if (!is_file($jsonPath) || !is_readable($jsonPath)) {
            throw new RuntimeException("Phone capability dataset is not readable: {$jsonPath}");
        }

        $contents = file_get_contents($jsonPath);
        if ($contents === false) {
            throw new RuntimeException("Phone capability dataset could not be read: {$jsonPath}");
        }

        try {
            $document = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Phone capability dataset is invalid JSON.', 0, $exception);
        }

        $records = $document['records'] ?? null;
        if (!is_array($records)) {
            throw new RuntimeException('Phone capability dataset must contain records.');
        }

        $capabilities = [];
        foreach ($records as $record) {
            if (is_array($record) && ($record['phone_account_capability'] ?? null) === 'documented' && is_string($record['slug'] ?? null)) {
                $capabilities[$record['slug']] = true;
            }
        }

        return $capabilities;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function decodeList(string $jsonPath, string $description): array
    {
        // Reasoning: fail closed on missing or malformed local data so callers never receive a partially hydrated registry.
        if (!is_file($jsonPath) || !is_readable($jsonPath)) {
            throw new RuntimeException("{$description} is not readable: {$jsonPath}");
        }

        $contents = file_get_contents($jsonPath);
        if ($contents === false) {
            throw new RuntimeException("{$description} could not be read: {$jsonPath}");
        }

        try {
            $document = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException("{$description} is invalid JSON.", 0, $exception);
        }

        if (!is_array($document)) {
            throw new RuntimeException("{$description} must contain a JSON array.");
        }

        return array_values($document);
    }
}
