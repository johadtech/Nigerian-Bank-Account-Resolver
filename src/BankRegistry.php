<?php

namespace NigerianBankResolver;

/**
 * Class BankRegistry
 * 
 * Manages the collection and hydration of all active Nigerian financial institutions.
 */
class BankRegistry
{
    /** @var Bank[] */
    private array $banks = [];

    /**
     * BankRegistry constructor.
     *
     * @param array $banksData
     */
    public function __construct(array $banksData = [])
    {
        if (empty($banksData)) {
            $banksData = $this->loadDefaultData();
        }

        foreach ($banksData as $data) {
            $this->banks[] = new Bank(
                $data['name'],
                $data['code'],
                $data['prefix'],
                $data['slug'],
                $data['logo_url'] ?? $data['logo_path'] ?? null
            );
        }
    }

    /**
     * Retrieve all registered banks.
     *
     * @return Bank[]
     */
    public function all(): array
    {
        return $this->banks;
    }

    /**
     * Load default bank dataset from JSON file.
     *
     * @return array
     */
    private function loadDefaultData(): array
    {
        $jsonPath = __DIR__ . '/../data/banks.json';
        if (file_exists($jsonPath)) {
            $content = file_get_contents($jsonPath);
            return json_decode($content, true) ?? [];
        }

        return [];
    }
}
