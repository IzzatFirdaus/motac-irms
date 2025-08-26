<?php

namespace App\Livewire\Helpdesk\Admin;

use App\Models\HelpdeskTicket;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * TicketReport.
 *
 * Livewire component for generating helpdesk ticket reports for admin.
 */
class TicketReport extends Component
{
    public string $reportType = 'volume';

    public ?string $startDate = null;

    public ?string $endDate = null;

    public $reportData = [];

    protected array $rules = [
        'startDate' => 'nullable|date',
        'endDate'   => 'nullable|date|after_or_equal:startDate',
    ];

    public function mount(): void
    {
        $this->startDate = now()->subMonths(3)->format('Y-m-d');
        $this->endDate   = now()->format('Y-m-d');
        $this->generateReport();
        // Policy: user must have 'viewAny' permission for HelpdeskTicket
        $this->authorize('viewAny', HelpdeskTicket::class);
    }

    public function updatedReportType(): void
    {
        $this->generateReport();
    }

    /**
     * Generate the report based on selected type and date range.
     */
    public function generateReport(): void
    {
        $this->validate();

        $query = HelpdeskTicket::query();

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        $this->reportData = match ($this->reportType) {
            'volume' => $query->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as total_tickets')
            )->groupBy('month')->orderBy('month')->get(),
            'resolution_time' => $query->where('status', 'closed')
                ->select([
                    'category_id',
                    DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, closed_at)) as avg_hours_to_resolve'),
                ])
                ->with('category')
                ->groupBy('category_id')
                ->get(),
            'status_distribution' => $query->select(
                DB::raw('status, count(*) as count')
            )->groupBy('status')->get(),
            default => [],
        };
    }

    public function render()
    {
        return view('livewire.helpdesk.admin.ticket-report', [
            'categories' => \App\Models\HelpdeskCategory::all(),
        ]);
    }
}
