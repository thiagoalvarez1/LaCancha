<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Expense\Entities\Expense;
use Modules\Purchase\Entities\Purchase;
use Modules\Purchase\Entities\PurchasePayment;
use Modules\PurchasesReturn\Entities\PurchaseReturn;
use Modules\PurchasesReturn\Entities\PurchaseReturnPayment;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SalePayment;
use Modules\SalesReturn\Entities\SaleReturn;
use Modules\SalesReturn\Entities\SaleReturnPayment;
use Illuminate\Http\Request; // Importante

class HomeController extends Controller
{
    public function index()
    {
        // Capturamos el filtro si existe
        $emailFiltro = request('sucursal_email');

        // Consultas base con filtro condicional
        $salesQuery = Sale::completed();
        $saleReturnsQuery = SaleReturn::completed();
        $purchaseReturnsQuery = PurchaseReturn::completed();

        if ($emailFiltro) {
            $salesQuery->where('reference', 'LIKE', '%' . $emailFiltro . '%');
            $saleReturnsQuery->where('reference', 'LIKE', '%' . $emailFiltro . '%');
            // Nota: Si las compras no tienen referencia por sucursal, no se filtrarán
        }

        $sales = $salesQuery->sum('total_amount');
        $sale_returns = $saleReturnsQuery->sum('total_amount');
        $purchase_returns = $purchaseReturnsQuery->sum('total_amount');

        $product_costs = 0;
        foreach ($salesQuery->with('saleDetails.product')->get() as $sale) {
            foreach ($sale->saleDetails as $saleDetail) {
                if (!is_null($saleDetail->product)) {
                    $product_costs += ($saleDetail->product->product_cost * $saleDetail->quantity);
                }
            }
        }

        $revenue = ($sales - $sale_returns) / 100;
        $profit = $revenue - ($product_costs / 100);

        // 1. Clientes con deuda (filtrado)
        $top_deudores_query = Sale::where('due_amount', '>', 0);
        if ($emailFiltro) {
            $top_deudores_query->where('reference', 'LIKE', '%' . $emailFiltro . '%');
        }
        $top_deudores = $top_deudores_query->select('customer_name', DB::raw('SUM(due_amount) as due_amount'))
            ->groupBy('customer_name')->orderByDesc('due_amount')->limit(5)->get();

        // 2. Productos más vendidos (filtrado)
        $top_productos_query = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->where('sales.status', 'Completed');
        if ($emailFiltro) {
            $top_productos_query->where('sales.reference', 'LIKE', '%' . $emailFiltro . '%');
        }
        $top_productos = $top_productos_query->select('product_name', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_name')->orderByDesc('total_qty')->limit(5)->get();

        // 3. Actividad Reciente (filtrado)
        $recent_query = Sale::latest();
        if ($emailFiltro) {
            $recent_query->where('reference', 'LIKE', '%' . $emailFiltro . '%');
        }
        $recent_activities = $recent_query->limit(5)->get();

        return view('home', [
            'revenue' => $revenue,
            'sale_returns' => $sale_returns / 100,
            'purchase_returns' => $purchase_returns / 100,
            'profit' => $profit,
            'top_deudores' => $top_deudores,
            'top_productos' => $top_productos,
            'recent_activities' => $recent_activities
        ]);
    }

    public function currentMonthChart()
    {
        abort_if(!request()->ajax(), 404);
        $emailFiltro = request('sucursal_email');

        $salesQuery = Sale::where('status', 'Completed')->whereMonth('date', date('m'))->whereYear('date', date('Y'));
        $purchasesQuery = Purchase::where('status', 'Completed')->whereMonth('date', date('m'))->whereYear('date', date('Y'));

        if ($emailFiltro) {
            $salesQuery->where('reference', 'LIKE', '%' . $emailFiltro . '%');
            // Si las compras también llevan referencia: $purchasesQuery->where('reference', 'LIKE', '%' . $emailFiltro . '%');
        }

        return response()->json([
            'sales' => $salesQuery->sum('total_amount') / 100,
            'purchases' => $purchasesQuery->sum('total_amount') / 100,
            'expenses' => Expense::whereMonth('date', date('m'))->whereYear('date', date('Y'))->sum('amount') / 100
        ]);
    }




    public function salesPurchasesChart()
    {
        abort_if(!request()->ajax(), 404);

        $sales = $this->salesChartData();
        $purchases = $this->purchasesChartData();

        return response()->json(['sales' => $sales, 'purchases' => $purchases]);
    }


    public function paymentChart()
    {
        abort_if(!request()->ajax(), 404);

        $dates = collect();
        foreach (range(-11, 0) as $i) {
            $date = Carbon::now()->addMonths($i)->format('m-Y');
            $dates->put($date, 0);
        }

        $date_range = Carbon::today()->subYear()->format('Y-m-d');

        $sale_payments = SalePayment::where('date', '>=', $date_range)
            ->select([
                DB::raw("DATE_FORMAT(date, '%m-%Y') as month"),
                DB::raw("SUM(amount) as amount")
            ])
            ->groupBy('month')->orderBy('month')
            ->get()->pluck('amount', 'month');

        $sale_return_payments = SaleReturnPayment::where('date', '>=', $date_range)
            ->select([
                DB::raw("DATE_FORMAT(date, '%m-%Y') as month"),
                DB::raw("SUM(amount) as amount")
            ])
            ->groupBy('month')->orderBy('month')
            ->get()->pluck('amount', 'month');

        $purchase_payments = PurchasePayment::where('date', '>=', $date_range)
            ->select([
                DB::raw("DATE_FORMAT(date, '%m-%Y') as month"),
                DB::raw("SUM(amount) as amount")
            ])
            ->groupBy('month')->orderBy('month')
            ->get()->pluck('amount', 'month');

        $purchase_return_payments = PurchaseReturnPayment::where('date', '>=', $date_range)
            ->select([
                DB::raw("DATE_FORMAT(date, '%m-%Y') as month"),
                DB::raw("SUM(amount) as amount")
            ])
            ->groupBy('month')->orderBy('month')
            ->get()->pluck('amount', 'month');

        $expenses = Expense::where('date', '>=', $date_range)
            ->select([
                DB::raw("DATE_FORMAT(date, '%m-%Y') as month"),
                DB::raw("SUM(amount) as amount")
            ])
            ->groupBy('month')->orderBy('month')
            ->get()->pluck('amount', 'month');

        $payment_received = array_merge_numeric_values($sale_payments, $purchase_return_payments);
        $payment_sent = array_merge_numeric_values($purchase_payments, $sale_return_payments, $expenses);

        $dates_received = $dates->merge($payment_received);
        $dates_sent = $dates->merge($payment_sent);

        $received_payments = [];
        $sent_payments = [];
        $months = [];

        foreach ($dates_received as $key => $value) {
            $received_payments[] = $value;
            $months[] = $key;
        }

        foreach ($dates_sent as $key => $value) {
            $sent_payments[] = $value;
        }

        return response()->json([
            'payment_sent' => $sent_payments,
            'payment_received' => $received_payments,
            'months' => $months,
        ]);
    }

    public function salesChartData()
    {
        $dates = collect();
        foreach (range(-6, 0) as $i) {
            $date = Carbon::now()->addDays($i)->format('d-m-y');
            $dates->put($date, 0);
        }

        $date_range = Carbon::today()->subDays(6);

        $sales = Sale::where('status', 'Completed')
            ->where('date', '>=', $date_range)
            ->groupBy(DB::raw("DATE_FORMAT(date,'%d-%m-%y')"))
            ->orderBy('date')
            ->get([
                DB::raw(DB::raw("DATE_FORMAT(date,'%d-%m-%y') as date")),
                DB::raw('SUM(total_amount) AS count'),
            ])
            ->pluck('count', 'date');

        $dates = $dates->merge($sales);

        $data = [];
        $days = [];
        foreach ($dates as $key => $value) {
            $data[] = $value / 100;
            $days[] = $key;
        }

        return response()->json(['data' => $data, 'days' => $days]);
    }


    public function purchasesChartData()
    {
        $dates = collect();
        foreach (range(-6, 0) as $i) {
            $date = Carbon::now()->addDays($i)->format('d-m-y');
            $dates->put($date, 0);
        }

        $date_range = Carbon::today()->subDays(6);

        $purchases = Purchase::where('status', 'Completed')
            ->where('date', '>=', $date_range)
            ->groupBy(DB::raw("DATE_FORMAT(date,'%d-%m-%y')"))
            ->orderBy('date')
            ->get([
                DB::raw(DB::raw("DATE_FORMAT(date,'%d-%m-%y') as date")),
                DB::raw('SUM(total_amount) AS count'),
            ])
            ->pluck('count', 'date');

        $dates = $dates->merge($purchases);

        $data = [];
        $days = [];
        foreach ($dates as $key => $value) {
            $data[] = $value / 100;
            $days[] = $key;
        }

        return response()->json(['data' => $data, 'days' => $days]);

    }
}
