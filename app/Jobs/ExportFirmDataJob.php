<?php
namespace App\Jobs;

use App\Exports\FirmExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ExportFirmDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $firmId;

    /**
     * Create a new job instance.
     *
     * @param int $firmId
     * @return void
     */
    public function __construct($firmId)
    {
        $this->firmId = $firmId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $fileName = 'firm_data_' . $this->firmId . '.xlsx';
        Excel::store(new FirmExport($this->firmId), $fileName, 'local');

        // Optionally, you can send a notification or perform other actions here
    }
}
