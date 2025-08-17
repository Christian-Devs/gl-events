<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class SimplePayClient {
    private Client $http;
    private string $base;
    private string $key;

    public function __construct() {
        $this->base = rtrim(config('services.simplepay.base'), '/');
        $this->key  = config('services.simplepay.key');
        $this->http = new Client([
            'base_uri' => $this->base . '/',
            'headers'  => [
                'Authorization' => $this->key,   // SimplePay expects just the key here
                'Accept'        => 'application/json',
            ],
            'timeout' => 20,
        ]);
    }

    private function get(string $path, array $opts = []) {
        $res = $this->http->get($path, $opts);
        return json_decode((string) $res->getBody(), true);
    }

    public function listClients(): array {
        return $this->get('clients');
    }

    public function listEmployees(int $clientId): array {
        return $this->get("clients/{$clientId}/employees");
    }

    public function listPayRuns(int $clientId): array {
        return $this->get("clients/{$clientId}/payment_runs");
    }

    public function listPayslipsInRun(int $paymentRunId): array {
        return $this->get("payment_runs/{$paymentRunId}/payslips");
    }

    public function getPayslip(int $payslipId): array {
        return $this->get("payslips/{$payslipId}");
    }

    public function getPayslipPdf(int $payslipId): string {
        $res = $this->http->get("payslips/{$payslipId}.pdf", [
            'headers' => ['Accept' => 'application/pdf']
        ]);
        return $res->getBody()->getContents();
    }

    // Optional helper to cache a default client_id
    public function getPrimaryClientId(): int {
        return Cache::remember('simplepay:primary_client_id', 300, function () {
            $clients = $this->listClients();
            return (int) data_get($clients, '0.client.id');
        });
    }
}
