<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MarketDataTemplateExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize
{
    public function array(): array
    {
        return [
            // Example row
            [
                '2024-01-01',  // date
                '100.50',      // open
                '102.75',      // high
                '99.25',       // low
                '101.00',      // close
                '1000000',     // volume
                'AAPL',        // symbol
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'date',
            'open',
            'high',
            'low',
            'close',
            'volume',
            'symbol'
        ];
    }

    public function title(): string
    {
        return 'Market Data Template';
    }
} 