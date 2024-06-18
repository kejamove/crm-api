<?php


namespace App\Exports;

use App\Models\Firm;
use App\Models\Branch;
use App\Models\User;
use App\Models\Move;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class FirmExport implements WithMultipleSheets
{
    protected $firmId;

    public function __construct($firmId)
    {
        $this->firmId = $firmId;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Add a sheet for the firm
        $sheets[] = new SingleSheetExport(Firm::where('id', $this->firmId)->get(), 'Firm Data');

        // Add a sheet for branches
        $sheets[] = new SingleSheetExport(Branch::where('firm', $this->firmId)->get(), 'Branches');

        // Add a sheet for users
        $sheets[] = new SingleSheetExport(User::where('firm', $this->firmId)
            ->orWhereIn('branch', Branch::where('firm', $this->firmId)->pluck('id'))
            ->get(), 'Users');

        // Add a sheet for moves
        $sheets[] = new SingleSheetExport(Move::whereIn('sales_representative', User::where('firm', $this->firmId)
            ->orWhereIn('branch', Branch::where('firm', $this->firmId)->pluck('id'))
            ->pluck('id'))->get(), 'Moves');

        return $sheets;
    }
}

class SingleSheetExport implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    protected $data;
    protected $title;

    public function __construct($data, $title)
    {
        $this->data = $data;
        $this->title = $title;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        // Assuming all items in the collection have the same structure
        if ($this->data->isEmpty()) {
            return [];
        }

        return array_keys($this->data->first()->toArray());
    }

    public function title(): string
    {
        return $this->title;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $worksheet = $event->sheet->getDelegate();
                foreach (range('A', $worksheet->getHighestColumn()) as $column) {
                    $worksheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
