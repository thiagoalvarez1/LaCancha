@extends('layouts.app')

@section('title', 'Inicio')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item active">Inicio</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        {{-- Sección de Estadísticas Totales --}}
        @can('show_total_stats')
            <div class="row">
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0 d-flex align-items-center">
                            <div class="bg-gradient-primary p-4 mfe-3 rounded-left">
                                <i class="bi bi-bar-chart font-2xl"></i>
                            </div>
                            <div>
                                <div class="text-value text-primary">${{ number_format($revenue, 0, ',', '.') }}</div>
                                <div class="text-muted text-uppercase font-weight-bold small">Ingresos</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0 d-flex align-items-center">
                            <div class="bg-gradient-warning p-4 mfe-3 rounded-left">
                                <i class="bi bi-arrow-return-left font-2xl"></i>
                            </div>
                            <div>
                                <div class="text-value text-warning">${{ number_format($sale_returns, 0, ',', '.') }}</div>
                                <div class="text-muted text-uppercase font-weight-bold small">Devoluciones Venta</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0 d-flex align-items-center">
                            <div class="bg-gradient-success p-4 mfe-3 rounded-left">
                                <i class="bi bi-arrow-return-right font-2xl"></i>
                            </div>
                            <div>
                                <div class="text-value text-success">${{ number_format($purchase_returns, 0, ',', '.') }}</div>
                                <div class="text-muted text-uppercase font-weight-bold small">Devoluciones Compra</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0 d-flex align-items-center">
                            <div class="bg-gradient-info p-4 mfe-3 rounded-left">
                                <i class="bi bi-trophy font-2xl"></i>
                            </div>
                            <div>
                                <div class="text-value text-info">${{ number_format($profit, 0, ',', '.') }}</div>
                                <div class="text-muted text-uppercase font-weight-bold small">Ganancia</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan

        {{-- Sección de Gráficos --}}
        <div class="row mb-4">
            {{-- Gráfico de Barras --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom font-weight-bold">
                        Ventas y Compras (7 Días)
                    </div>
                    <div class="card-body">
                        <div style="height: 300px; position: relative;">
                            <canvas id="salesPurchasesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Gráfico de Dona --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom font-weight-bold">
                        Resumen de {{ now()->locale('es')->format('F, Y') }}
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <div style="width: 100%; max-width: 280px; height: 280px; position: relative;">
                            <canvas id="currentMonthChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom font-weight-bold">
                Flujo de Pagos (Enviados vs Recibidos)
            </div>
            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="paymentChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

        {{-- Clientes y Productos --}}
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom font-weight-bold">
                        <i class="bi bi-person-x text-danger"></i> Clientes con Deuda
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0 text-center">
                            <thead class="thead-light">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($top_deudores as $deudor)
                                    <tr>
                                        <td>{{ $deudor->customer_name }}</td>
                                        <td class="text-danger font-weight-bold">${{ number_format($deudor->due_amount, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="py-3">No hay deudas</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom font-weight-bold">
                        <i class="bi bi-star text-primary"></i> Más Vendidos
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0 text-center">
                            <thead class="thead-light">
                                <tr>
                                    <th>Producto</th>
                                    <th>Cant.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($top_productos as $prod)
                                    <tr>
                                        <td>{{ $prod->product_name }}</td>
                                        <td><span class="badge badge-info">{{ (int) $prod->total_qty }} u.</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="py-3">Sin ventas</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actividad Reciente --}}
        <div class="row mt-2">
            <div class="col-lg-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom font-weight-bold">
                        <i class="bi bi-clock-history text-primary"></i> Actividad Reciente (Últimas Ventas)
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 text-center">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Estado</th>
                                        <th>Total</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recent_activities as $activity)
                                        <tr>
                                            <td class="align-middle">{{ \Carbon\Carbon::parse($activity->date)->format('d/m/Y') }}</td>
                                            <td class="align-middle font-weight-bold text-left px-4">{{ $activity->customer_name }}</td>
                                            <td class="align-middle">
                                                <span class="badge badge-{{ $activity->status == 'Completed' ? 'success' : 'warning' }}">
                                                    {{ $activity->status == 'Completed' ? 'Completado' : 'Pendiente' }}
                                                </span>
                                            </td>
                                            <td class="align-middle font-weight-bold text-dark">${{ number_format($activity->total_amount, 0, ',', '.') }}</td>
                                            <td class="align-middle">
                                                <a href="{{ route('sales.show', $activity->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="py-4">No se registró actividad</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('third_party_scripts')
    {{-- Solo una carga de Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
@endsection

@push('page_scripts')
    @vite('resources/js/chart-config.js')
@endpush
