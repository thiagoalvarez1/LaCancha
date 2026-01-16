<div>
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-store"></i> Reporte de Ventas por Sede</h4>
        </div>
        <div class="card-body">
            <!-- Filtros -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <label>Fecha Inicio *</label>
                    <input type="date" wire:model="start_date" class="form-control">
                    @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-lg-3 col-md-6">
                    <label>Fecha Fin *</label>
                    <input type="date" wire:model="end_date" class="form-control">
                    @error('end_date') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-lg-2 col-md-4">
                    <label>Cliente</label>
                    <select wire:model="customer_id" class="form-control">
                        <option value="">Todos</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label>Sede</label>
                    <select wire:model="sede" class="form-control">
                        <option value="todas">Todas</option>
                        <option value="lc">La Cancha (LC-)</option>
                        <option value="bl">Blindex (BL-)</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 align-self-end">
                    <div class="d-flex gap-2">
                        <button wire:click="generateReport" class="btn btn-primary">
                            <i class="bi bi-filter"></i> Filtrar
                        </button>
                        <button wire:click="clearFilters" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>


            <!-- Resumen -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h6>Total Ventas</h6>
                            <h4>{{ $sales->total() }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h6>Monto Total</h6>
                            <h4>{{ format_currency($sales->sum('total_amount')) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h6>Pagado</h6>
                            <h4>{{ format_currency($sales->sum('paid_amount')) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h6>Pendiente</h6>
                            <h4>{{ format_currency($sales->sum('due_amount')) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Ventas -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Referencia</th>
                            <th>Cliente</th>
                            <th>Estado</th>
                            <th>Total</th>
                            <th>Pagado</th>
                            <th>Pendiente</th>
                            <th>Estado Pago</th>
                            <th>Método Pago</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td>{{ $sale->date }}</td>
                                <td>
                                    <span
                                        class="badge {{ str_starts_with($sale->reference, 'LC-') ? 'bg-info' : 'bg-warning' }}">
                                        {{ $sale->reference }}
                                    </span>
                                </td>
                                <td>{{ $sale->customer_name }}</td>
                                <td><span class="badge bg-success">{{ $sale->status }}</span></td>
                                <td>{{ format_currency($sale->total_amount) }}</td>
                                <td>{{ format_currency($sale->paid_amount) }}</td>
                                <td>
                                    @if($sale->due_amount > 0)
                                        <span class="badge bg-danger">{{ format_currency($sale->due_amount) }}</span>
                                    @else
                                        <span class="badge bg-success">{{ format_currency(0) }}</span>
                                    @endif
                                </td>
                                <td><span
                                        class="badge bg-{{ $sale->payment_status == 'Paid' ? 'success' : 'warning' }}">{{ $sale->payment_status }}</span>
                                </td>
                                <td>{{ $sale->payment_method }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No hay ventas para los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-3">
                {{ $sales->links() }}
            </div>
        </div>
    </div>
</div>