<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class SimplePayClient
{
    private $http;
    private $base;
    private $key;

    public function __construct()
    {
        $this->base = rtrim(config('services.simplepay.base'), '/');
        $this->key = config('services.simplepay.key');
        $this->http = new Client([
            'base_uri' => $this->base . '/',
            'headers' => [
                'Authorization' => $this->key,   // SimplePay expects just the key here
                'Accept' => 'application/json',
            ],
            'timeout' => 20,
        ]);
    }

    private function request(string $method, string $path, array $opts = [])
    {
        try {
            $res = $this->http->request($method, $path, $opts);
            $ct = $res->getHeaderLine('Content-Type');
            $body = (string) $res->getBody();
            return strpos($ct, 'application/json') !== false
                ? json_decode($body, true)
                : $body;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $msg = $e->getResponse()
                ? (string) $e->getResponse()->getBody()
                : $e->getMessage();
            throw new \RuntimeException("SimplePay {$method} {$path} failed: {$msg}", $e->getCode(), $e);
        }
    }

    private function get(string $path, array $opts = [])
    {
        return $this->request('GET', $path, $opts);
    }
    private function post(string $path, array $opts = [])
    {
        return $this->request('POST', $path, $opts);
    }
    private function patch(string $path, array $opts = [])
    {
        return $this->request('PATCH', $path, $opts);
    }

    public function listClients(): array
    {
        return $this->get('clients');
    }

    public function listEmployees(int $clientId): array
    {
        return $this->get("clients/{$clientId}/employees");
    }

    public function listPayRuns(int $clientId): array
    {
        return $this->get("clients/{$clientId}/payment_runs");
    }

    public function listPayslipsInRun(int $paymentRunId): array
    {
        return $this->get("payment_runs/{$paymentRunId}/payslips");
    }

    public function getPayslip(int $payslipId): array
    {
        return $this->get("payslips/{$payslipId}");
    }

    public function createEmployee(int $clientId, array $payload): array
    {
        return $this->post("clients/{$clientId}/employees", ['json' => ['employee' => $payload]]);
    }
    public function updateEmployee(int $employeeId, array $payload): array
    {
        return $this->patch("employees/{$employeeId}", ['json' => ['employee' => $payload]]);
    }
    public function getPayslipPdf(int $payslipId): string
    {
        return $this->request('GET', "payslips/{$payslipId}.pdf", ['headers' => ['Accept' => 'application/pdf']]);
    }

    public function getLatestRunId(int $clientId): ?int
    {
        $runs = $this->listPayRuns($clientId);
        return (int) (data_get($runs, '0.payment_run.id') ?? 0) ?: null;
    }

    public function findPayslipIdForEmployee(int $paymentRunId, int $employeeId): ?int
    {
        $payslips = $this->listPayslipsInRun($paymentRunId);
        foreach ($payslips as $row) {
            if ((int) data_get($row, 'payslip.employee_id') === $employeeId) {
                return (int) data_get($row, 'payslip.id');
            }
        }
        return null;
    }

    /** Shortcut: get full payslip JSON for an employee in the latest run */
    public function getLatestPayslipForEmployee(int $clientId, int $employeeId): ?array
    {
        $runId = $this->getLatestRunId($clientId);
        if (!$runId)
            return null;
        $psId = $this->findPayslipIdForEmployee($runId, $employeeId);
        return $psId ? $this->getPayslip($psId) : null;
    }

    public function getPrimaryClientId(): int
    {
        return Cache::remember('simplepay:primary_client_id', 300, function () {
            $clients = $this->listClients();
            return (int) data_get($clients, '0.client.id');
        });
    }

    public function ping(): bool
    {
        try {
            $this->listClients();
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

}
