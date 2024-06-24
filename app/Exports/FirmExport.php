<?php
namespace App\Exports;

use App\Models\Firm;
use App\Models\Branch;
use App\Models\User;
use App\Models\Move;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        $sheets[] = new SingleSheetExport(
            Move::whereIn('sales_representative', User::where('firm', $this->firmId)
                ->orWhereIn('branch', Branch::where('firm', $this->firmId)->pluck('id'))
                ->pluck('id'))
                ->with('salesRepresentative', 'branch')
                ->get(),
            'Moves'
        );

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

//    public function headings(): array
//    {
//        // Assuming all items in the collection have the same structure
//        if ($this->data->isEmpty()) {
//            return [];
//        }
//
//        return array_keys($this->data->first()->toArray());
//    }
    public function headings(): array
    {
        // Define main headings
        $headings = [
            'Column 1',
            'Column 2',
            // Add more general headings as needed
        ];

        // Fetch first names of sales representatives
        $salesRepIds = $this->data->pluck('sales_representative')->unique();
        $salesRepNames = User::whereIn('id', $salesRepIds)->pluck('first_name', 'id')->toArray();

// Fetch names of branches
        $branchIds = $this->data->pluck('branch')->unique();
        $branchNames = Branch::whereIn('id', $branchIds)->pluck('name', 'id')->toArray();

// Add additional headings dynamically based on fetched names
        $additionalHeadings = [];
        foreach ($this->data as $item) {
            $salesRepId = $item->sales_representative;
            if (!isset($salesRepNames[$salesRepId])) {
//                $salesRepNames[$salesRepId] = User::findOrFail($salesRepId)->first_name;
                try {
                    $salesRepNames[$salesRepId] = User::findOrFail($salesRepId)->first_name;
                } catch (ModelNotFoundException $e) {
                    // Handle the case where the user with $salesRepId is not found
                    $salesRepNames[$salesRepId] = 'Unknown Sales Rep';
                }
            }

            $branchId = $item->branch;
            if (!isset($branchNames[$branchId])) {
//                $branchNames[$branchId] = Branch::findOrFail($branchId)->name;
                try {
//                    $salesRepNames[$salesRepId] = User::findOrFail($salesRepId)->first_name;
                $branchNames[$branchId] = Branch::findOrFail($branchId)->name;

                } catch (ModelNotFoundException $e) {
                    // Handle the case where the user with $salesRepId is not found
                    $branchNames[$branchId] = 'Unknown Branch';
                }
            }

            // Prepare additional headings dynamically
            $additionalHeadings[] = 'Sales Rep: ' . $salesRepNames[$salesRepId];
            $additionalHeadings[] = 'Branch: ' . $branchNames[$branchId];
        }

// Merge with existing headings
        $headings = array_merge($this->headings(), $additionalHeadings);

        return $headings;
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
