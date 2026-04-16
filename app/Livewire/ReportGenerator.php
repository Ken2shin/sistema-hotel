<?php

namespace App\Livewire;

use App\Models\Report;
use App\Services\ReportGeneratorService;
use Livewire\Component;
use Livewire\WithPagination;

class ReportGenerator extends Component
{
    use WithPagination;

    public $reportName = '';
    public $reportType = 'revenue';
    public $frequency = 'monthly';
    public $startDate = '';
    public $endDate = '';
    public $exportFormat = 'csv';
    public $showForm = false;
    public $isGenerating = false;
    public $selectedReport = null;
    public $showExportModal = false;

    protected ReportGeneratorService $reportGenerator;

    public function mount()
    {
        $this->reportGenerator = app(ReportGeneratorService::class);
        $this->setDefaultDates();
    }

    private function setDefaultDates()
    {
        $this->endDate = now()->format('Y-m-d');
        $this->startDate = now()->subMonth()->format('Y-m-d');
    }

    public function render()
    {
        $reports = Report::where('created_by', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.report-generator', [
            'reports' => $reports,
            'showForm' => $this->showForm,
            'reportName' => $this->reportName,
            'reportType' => $this->reportType,
            'frequency' => $this->frequency,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'isGenerating' => $this->isGenerating,
            'showExportModal' => $this->showExportModal,
        ]);
    }

    public function generateReport()
    {
        $this->validate([
            'reportName' => 'required|string|max:255',
            'reportType' => 'required|in:revenue,occupancy,clients,rooms,payment_methods',
            'frequency' => 'required|in:daily,weekly,monthly,custom',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:startDate',
        ]);

        $this->isGenerating = true;

        try {
            $report = Report::create([
                'name' => $this->reportName,
                'slug' => \Illuminate\Support\Str::slug($this->reportName) . '-' . uniqid(),
                'type' => $this->reportType,
                'frequency' => $this->frequency,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'created_by' => auth()->id(),
                'status' => 'pending',
            ]);

            match ($report->type) {
                'revenue' => $this->reportGenerator->generateRevenueReport($report),
                'occupancy' => $this->reportGenerator->generateOccupancyReport($report),
                'clients' => $this->reportGenerator->generateClientReport($report),
                default => throw new \Exception('Report type not supported'),
            };

            session()->flash('message', 'Reporte generado exitosamente');
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        } finally {
            $this->isGenerating = false;
        }
    }

    public function exportReport(Report $report)
    {
        $this->selectedReport = $report;
        $this->showExportModal = true;
    }

    public function doExport()
    {
        $this->validate([
            'exportFormat' => 'required|in:csv,pdf,xlsx,json',
        ]);

        try {
            $format = $this->exportFormat;
            $content = match ($format) {
                'json' => json_encode($this->selectedReport->data, JSON_PRETTY_PRINT),
                'csv' => $this->generateCsv($this->selectedReport),
                default => json_encode($this->selectedReport->data),
            };

            $filename = "report-{$this->selectedReport->slug}-" . now()->timestamp . ".{$format}";
            $path = "exports/{$filename}";

            \Illuminate\Support\Facades\Storage::disk('public')->put($path, $content);

            $this->selectedReport->markAsExported($format, $path);

            session()->flash('message', 'Reporte exportado exitosamente');
            $this->showExportModal = false;
        } catch (\Exception $e) {
            session()->flash('error', 'Error al exportar');
        }
    }

    private function generateCsv(Report $report)
    {
        $csv = "Reporte: {$report->name}\n";
        $csv .= "Tipo: {$report->type}\n";
        $csv .= "Período: {$report->start_date} a {$report->end_date}\n\n";

        if ($report->data) {
            foreach ($report->data as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        $csv .= "{$key} - {$k},{$v}\n";
                    }
                } else {
                    $csv .= "{$key},{$value}\n";
                }
            }
        }

        return $csv;
    }

    public function deleteReport(Report $report)
    {
        try {
            $report->delete();
            session()->flash('message', 'Reporte eliminado');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar');
        }
    }

    private function resetForm()
    {
        $this->reportName = '';
        $this->reportType = 'revenue';
        $this->frequency = 'monthly';
        $this->showForm = false;
        $this->setDefaultDates();
    }
}
