<?php

namespace App\Imports;

use App\Models\MarketData;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MarketDataImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure
{
    private $errors = [];
    private $rowCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $this->rowCount++;
            
            try {
                // Validate the row data
                $validator = Validator::make($row->toArray(), [
                    'date' => 'required|date',
                    'open' => 'required|numeric|min:0',
                    'high' => 'required|numeric|min:0',
                    'low' => 'required|numeric|min:0',
                    'close' => 'required|numeric|min:0',
                    'volume' => 'required|integer|min:0',
                    'symbol' => 'required|string|max:10',
                ]);

                if ($validator->fails()) {
                    $this->errors[] = [
                        'row' => $this->rowCount,
                        'errors' => $validator->errors()->all()
                    ];
                    continue;
                }

                // Create or update market data
                MarketData::updateOrCreate(
                    [
                        'date' => $row['date'],
                        'symbol' => $row['symbol']
                    ],
                    [
                        'open' => $row['open'],
                        'high' => $row['high'],
                        'low' => $row['low'],
                        'close' => $row['close'],
                        'volume' => $row['volume'],
                        'symbol' => $row['symbol']
                    ]
                );
            } catch (\Exception $e) {
                $this->errors[] = [
                    'row' => $this->rowCount,
                    'errors' => [$e->getMessage()]
                ];
            }
        }
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'open' => 'required|numeric|min:0',
            'high' => 'required|numeric|min:0',
            'low' => 'required|numeric|min:0',
            'close' => 'required|numeric|min:0',
            'volume' => 'required|integer|min:0',
            'symbol' => 'required|string|max:10',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'date.required' => 'The date field is required.',
            'date.date' => 'The date must be a valid date.',
            'open.required' => 'The open price is required.',
            'open.numeric' => 'The open price must be a number.',
            'high.required' => 'The high price is required.',
            'high.numeric' => 'The high price must be a number.',
            'low.required' => 'The low price is required.',
            'low.numeric' => 'The low price must be a number.',
            'close.required' => 'The close price is required.',
            'close.numeric' => 'The close price must be a number.',
            'volume.required' => 'The volume is required.',
            'volume.integer' => 'The volume must be an integer.',
            'symbol.required' => 'The symbol is required.',
            'symbol.max' => 'The symbol may not be greater than 10 characters.',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = [
                'row' => $failure->row(),
                'errors' => $failure->errors()
            ];
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }
} 