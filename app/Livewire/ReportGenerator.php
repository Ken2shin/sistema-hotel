<?php

namespace App\Livewire;

use App\Models\Report;
use App\Services\ReportGeneratorService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

#[Layout('layouts.app')] 
class ReportGenerator extends Component
{
    use WithPagination;

    public $reportName = '';
    public $reportType = 'revenue';
    public $frequency = 'monthly';
    public $startDate = '';
    public $endDate = '';
    public $exportFormat = 'pdf';
    
    public $showForm = false;
    public $isGenerating = false;
    
    public $selectedReport = null;
    public $showExportModal = false;
    public $viewingReport = null;
    public $showViewModal = false;

    public function mount()
    {
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
        ]);
    }

    public function generateReport(ReportGeneratorService $reportGenerator)
    {
        $this->validate([
            'reportName' => 'required|string|max:255',
            'reportType' => 'required|in:revenue,occupancy,clients,rooms',
            'frequency' => 'required|in:daily,weekly,monthly,custom',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:startDate',
        ]);

        $this->isGenerating = true;

        $report = Report::create([
            'name' => $this->reportName,
            'slug' => Str::slug($this->reportName) . '-' . uniqid(),
            'type' => $this->reportType,
            'frequency' => $this->frequency,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'created_by' => auth()->id(),
            'status' => 'pending',
        ]);

        $this->processReportData($report, $reportGenerator);
        
        $this->isGenerating = false;
        $this->resetForm();
    }

    public function retryReport(Report $report, ReportGeneratorService $reportGenerator)
    {
        $this->processReportData($report, $reportGenerator);
    }

    private function processReportData(Report $report, ReportGeneratorService $reportGenerator)
    {
        try {
            $data = match ($report->type) {
                'revenue' => $reportGenerator->generateRevenueReport($report),
                'occupancy' => $reportGenerator->generateOccupancyReport($report),
                'clients' => $reportGenerator->generateClientReport($report),
                default => throw new \Exception('Tipo de reporte no soportado'),
            };

            $report->update([
                'data' => $data,
                'status' => 'generated'
            ]);
            
            session()->flash('message', 'Reporte procesado exitosamente.');
        } catch (\Exception $e) {
            $report->update(['status' => 'failed']);
            session()->flash('error', 'Fallo al procesar: (' . $e->getMessage() . ')');
        }
    }

    public function viewReport(Report $report)
    {
        $this->viewingReport = $report;
        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->viewingReport = null;
        $this->showViewModal = false;
    }

    public function exportReport(Report $report)
    {
        $this->selectedReport = $report;
        $this->showExportModal = true;
    }

    public function closeExportModal()
    {
        $this->selectedReport = null;
        $this->showExportModal = false;
    }

    public function doExport()
    {
        $this->validate(['exportFormat' => 'required|in:csv,pdf,json']);

        try {
            $format = $this->exportFormat;
            $report = $this->selectedReport;
            $filename = "{$report->slug}.{$format}";
            
            $this->closeExportModal();
            $report->markAsExported($format, '');

            if ($format === 'pdf') {
                $pdf = Pdf::loadView('reports.pdf-template', ['report' => $report]);
                return response()->streamDownload(fn () => print($pdf->output()), $filename);
            }

            if ($format === 'csv') {
                $csvContent = $this->generateCsv($report);
                return response()->streamDownload(fn () => print($csvContent), $filename, ['Content-Type' => 'text/csv']);
            }

            return response()->streamDownload(fn () => print(json_encode($report->data, JSON_PRETTY_PRINT)), $filename, ['Content-Type' => 'application/json']);

        } catch (\Exception $e) {
            session()->flash('error', 'Error al exportar: ' . $e->getMessage());
        }
    }

    // CORRECCIÓN: Soporte para múltiples niveles de profundidad en Excel
    private function generateCsv(Report $report)
    {
        $csv = "Reporte;{$report->name}\nTipo;{$report->type}\nPeríodo;{$report->start_date?->format('d/m/Y')} a {$report->end_date?->format('d/m/Y')}\n\n";

        if (is_array($report->data)) {
            foreach ($report->data as $section => $values) {
                if (is_array($values)) {
                    $csv .= "\n" . strtoupper(str_replace('_', ' ', $section)) . "\n";
                    foreach ($values as $k => $v) {
                        if (is_array($v)) {
                            // Tercer nivel (Ej: Métodos de pago -> Tarjeta -> Cantidad)
                            $csv .= strtoupper(str_replace('_', ' ', $k)) . "\n";
                            foreach ($v as $subK => $subV) {
                                $csv .= str_replace('_', ' ', ucfirst($subK)) . ";{$subV}\n";
                            }
                        } else {
                            $csv .= str_replace('_', ' ', ucfirst($k)) . ";{$v}\n";
                        }
                    }
                }
            }
        }
        return "\xEF\xBB\xBF" . $csv; 
    }

    public function deleteReport(Report $report)
    {
        $report->delete();
        session()->flash('message', 'Reporte eliminado');
    }

    private function resetForm()
    {
        $this->reportName = '';
        $this->showForm = false;
        $this->setDefaultDates();
    }
}