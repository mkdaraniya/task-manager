<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class ReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        // Flatten grouped data for Excel
        $flattened = new Collection();
        foreach ($this->data as $group => $items) {
            foreach ($items as $item) {
                $flattened->push((object) array_merge(['group' => $group], (array) $item));
            }
        }
        return $flattened->isEmpty() ? collect([['No data available']]) : $flattened;
    }

    public function headings(): array
    {
        if ($this->data->isEmpty()) {
            return ['Message'];
        }
        $firstItem = $this->data->first()->first();
        return ['Group', ...array_keys((array) $firstItem)];
    }

    public function map($item): array
    {
        if (is_array($item) && isset($item[0]) && $item[0] === 'No data available') {
            return ['No data available'];
        }
        return [
            $item->group,
            ...array_values((array) $item),
        ];
    }
}
