<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateReportRequest;
use App\Models\Report;
use App\Services\ReportGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function __construct(private ReportGeneratorService $reportGenerator) {}

    public function index(): JsonResponse
    {
        try {
            $reports = Report::where('created_by', auth()->id())
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return response()->json([
                'data' => $reports->items(),
                'pagination' => [
                    'current_page' => $reports->currentPage(),
                    'total' => $reports->total(),
                    'per_page' => $reports->perPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching reports'], 500);
        }
    }

    public function store(GenerateReportRequest $request): JsonResponse
    {
        try {
            $slug = Str::slug($request->input('name')) . '-' . uniqid();

            $report = Report::create([
                'name' => $request->input('name'),
                'slug' => $slug,
                'type' => $request->input('type'),
                'frequency' => $request->input('frequency'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'created_by' => auth()->id(),
                'filters' => $request->input('filters'),
                'status' => 'pending',
            ]);

            return response()->json([
                'data' => $report,
                'message' => 'Report queued for generation',
            ], 201);
        } catch (\Exception $e) {
            Log::error('Report creation failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function show(Report $report): JsonResponse
    {
        try {
            $this->authorize('view', $report);

            return response()->json(['data' => $report]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    }

    public function generate(Report $report): JsonResponse
    {
        try {
            $this->authorize('update', $report);

            match ($report->type) {
                'revenue' => $this->reportGenerator->generateRevenueReport($report),
                'occupancy' => $this->reportGenerator->generateOccupancyReport($report),
                'clients' => $this->reportGenerator->generateClientReport($report),
                default => throw new \Exception('Tipo de reporte no soportado'),
            };

            Log::info('Report generated', ['report_id' => $report->id]);

            return response()->json(['data' => $report->refresh(), 'message' => 'Reporte generado']);
        } catch (\Exception $e) {
            Log::error('Report generation failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function export(Report $report): JsonResponse
    {
        try {
            $this->authorize('view', $report);

            if ($report->status !== 'generated') {
                return response()->json(['error' => 'Report not generated yet'], 400);
            }

            $format = request()->input('format', 'csv');

            if (!in_array($format, ['csv', 'pdf', 'xlsx', 'json'])) {
                return response()->json(['error' => 'Invalid format'], 400);
            }

            $exportPath = $this->exportReport($report, $format);

            $report->markAsExported($format, $exportPath);

            return response()->json([
                'url' => $report->getDownloadUrl(),
                'format' => $format,
            ]);
        } catch (\Exception $e) {
            Log::error('Report export failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Export failed'], 500);
        }
    }

    public function delete(Report $report): JsonResponse
    {
        try {
            $this->authorize('delete', $report);

            $report->delete();

            return response()->json(['message' => 'Report deleted']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    }

    private function exportReport(Report $report, string $format): string
    {
        $filename = "report-{$report->slug}-" . now()->timestamp . ".{$format}";
        $path = "exports/{$filename}";

        match ($format) {
            'json' => $this->exportJson($report, $path),
            'csv' => $this->exportCsv($report, $path),
            default => throw new \Exception('Format not implemented'),
        };

        return $path;
    }

    private function exportJson(Report $report, string $path): void
    {
        $content = json_encode([
            'report_name' => $report->name,
            'type' => $report->type,
            'period' => [
                'start' => $report->start_date,
                'end' => $report->end_date,
            ],
            'generated_at' => $report->updated_at,
            'data' => $report->data,
            'summary' => $report->summary,
        ], JSON_PRETTY_PRINT);

        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $content);
    }

    private function exportCsv(Report $report, string $path): void
    {
        $csv = "Report: {$report->name}\n";
        $csv .= "Type: {$report->type}\n";
        $csv .= "Period: {$report->start_date} to {$report->end_date}\n\n";

        if ($report->data) {
            $csv .= "Data\n";
            foreach ($report->data as $key => $value) {
                if (is_array($value)) {
                    $csv .= "{$key}:\n";
                    foreach ($value as $k => $v) {
                        $csv .= "  {$k},{$v}\n";
                    }
                } else {
                    $csv .= "{$key},{$value}\n";
                }
            }
        }

        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $csv);
    }
}
