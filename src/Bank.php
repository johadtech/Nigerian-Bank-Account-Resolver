<?php

namespace NigerianBankResolver;

/**
 * Class Bank
 * 
 * Represents a Nigerian financial institution entity with code, prefix, and local logo path.
 */
class Bank
{
    public string $name;
    public string $code;
    public string $nubanPrefix;
    public string $slug;
    public ?string $logoPath;

    /**
     * Bank constructor.
     *
     * @param string      $name
     * @param string      $code
     * @param string      $nubanPrefix
     * @param string      $slug
     * @param string|null $logoPath Local path to the logo file
     */
    public function __construct(
        string $name,
        string $code,
        string $nubanPrefix,
        string $slug,
        ?string $logoPath = null
    ) {
        $this->name = $name;
        $this->code = $code;
        $this->nubanPrefix = $nubanPrefix;
        $this->slug = $slug;
        $this->logoPath = $logoPath;
    }

    /**
     * Convert bank entity to array representation.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'code' => $this->code,
            'nuban_prefix' => $this->nubanPrefix,
            'slug' => $this->slug,
            'logo_path' => $this->logoPath,
        ];
    }
}
