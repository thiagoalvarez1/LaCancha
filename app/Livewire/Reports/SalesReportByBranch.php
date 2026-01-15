<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Sale\Entities\Sale;

class SalesReportByBranch extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $customers;
    public $start_date;
    public $end_date;
    public $customer_id;
    public $sale_status;
    public $payment_status;
    public $sede; // <- NUEVA PROPIEDAD

    protected $rules = [
        'start_date' => 'required|date|before:end_date',
        'end_date' => 'required|date|after:start_date',
    ];

    public function mount($customers)
    {
        $this->customers = $customers;
        $this->start_date = today()->subDays(30)->format('Y-m-d');
        $this->end_date = today()->format('Y-m-d');
        $this->customer_id = '';
        $this->sale_status = '';
        $this->payment_status = '';
        $this->sede = 'todas'; // <- Valor por defecto
    }

    // En lugar de dividir por 100, formatear correctamente
    public function render()
    {
        $sales = Sale::whereDate('date', '>=', $this->start_date)
            ->whereDate('date', '<=', $this->end_date)
            ->when($this->customer_id, function ($query) {
                return $query->where('customer_id', $this->customer_id);
            })
            ->when($this->sale_status, function ($query) {
                return $query->where('status', $this->sale_status);
            })
            ->when($this->payment_status, function ($query) {
                return $query->where('payment_status', $this->payment_status);
            })
            ->when($this->sede && $this->sede !== 'todas', function ($query) {
                if ($this->sede == 'lc') {
                    return $query->where('reference', 'LIKE', 'LC-%');
                } elseif ($this->sede == 'bl') {
                    return $query->where('reference', 'LIKE', 'BL-%');
                }
                return $query;
            })
            ->orderBy('date', 'desc')->paginate(10);

        return view('livewire.reports.sales-report-by-branch', [
            'sales' => $sales
        ]);
    }

    public function generateReport()
    {
        $this->validate();
        $this->render();
    }
}
