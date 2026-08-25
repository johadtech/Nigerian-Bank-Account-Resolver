<?php

declare(strict_types=1);

namespace NigerianBankResolver;

use RuntimeException;

final class PaystackAccountVerificationProvider implements AccountVerificationProvider
{
    public function __construct(
        private readonly string $secretKey,
        private readonly string $endpoint = 'https://api.paystack.co/bank/resolve',
        private readonly int $timeoutSeconds = 10,
    ) {
        if ($this->secretKey === '') {
            throw new RuntimeException('Paystack secret key is required.');
        }
    }

    public function verify(string $accountNumber, string $bankCode): AccountVerificationResult
    {
        // Reasoning: call only Paystack's documented account-resolution endpoint and map its response into the provider-neutral result.
        $query = http_build_query([
            'account_number' => $accountNumber,
            'bank_code' => $bankCode,
        ]);
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => implode("\r\n", [
                    'Authorization: Bearer ' . $this->secretKey,
                    'Accept: application/json',
                ]),
                'timeout' => $this->timeoutSeconds,
                'ignore_errors' => true,
            ],
        ]);
        $body = @file_get_contents($this->endpoint . '?' . $query, false, $context);
        $httpStatus = $this->extractHttpStatus($http_response_header ?? []);

        if ($body === false) {
            throw new RuntimeException('Paystack account resolution failed while making the HTTPS request.');
        }

        $response = json_decode($body, true);
        if (!is_array($response)) {
            throw new RuntimeException('Paystack account resolution returned invalid JSON.');
        }

        if ($httpStatus < 200 || $httpStatus >= 300) {
            throw new RuntimeException('Paystack account resolution returned HTTP ' . $httpStatus . '.');
        }

        if (($response['status'] ?? false) !== true) {
            return AccountVerificationResult::failure((string) ($response['message'] ?? 'Account was not resolved.'));
        }

        $data = $response['data'] ?? null;
        if (!is_array($data) || !is_string($data['account_number'] ?? null)) {
            throw new RuntimeException('Paystack account resolution returned an unexpected success payload.');
        }

        return AccountVerificationResult::success(
            $data['account_number'],
            is_string($data['account_name'] ?? null) ? $data['account_name'] : null,
            is_string($response['message'] ?? null) ? $response['message'] : null,
        );
    }

    /**
     * @param list<string> $headers
     */
    private function extractHttpStatus(array $headers): int
    {
        // Reasoning: derive the HTTP status from PHP's built-in stream metadata without requiring an optional cURL extension.
        foreach (array_reverse($headers) as $header) {
            if (preg_match('/^HTTP\\/\\d(?:\\.\\d)?\\s+(\\d{3})/', $header, $matches) === 1) {
                return (int) $matches[1];
            }
        }

        return 0;
    }
}
