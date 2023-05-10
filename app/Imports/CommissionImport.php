<?php

namespace App\Imports;

use App\Services\CalculateCommissionFeeService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class CommissionImport implements ToCollection, WithChunkReading
{
    protected $service;
    private $data = [];

    public function __construct(CalculateCommissionFeeService $calculateCommissionFeeService)
    {
        $this->service = $calculateCommissionFeeService;
    }

    /*
     * Class handle Excel import
     */
    public function collection(Collection $rows)
    {
        $this->data = $this->service->commissionFee($rows);
    }

    // Return the $data array
    public function getData(): array
    {
        return $this->data;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
