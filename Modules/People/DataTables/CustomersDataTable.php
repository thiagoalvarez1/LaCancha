<?php

namespace Modules\People\DataTables;

use Modules\People\Entities\Customer;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CustomersDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('debt', function ($data) {
                // Obtenemos el monto (dividido 100 por el formato de centavos)
                $debt_amount = $data->sales_sum_due_amount / 100;

                // Formateamos: $ + número con puntos en miles
                // Si quieres decimales, cambia el 0 por un 2
                $formatted_debt = '$ ' . number_format($debt_amount, 0, ',', '.');

                // Si la deuda es mayor a 0, la mostramos en rojo y negrita
                if ($debt_amount > 0) {
                    return '<span class="text-danger font-weight-bold">' . $formatted_debt . '</span>';
                }

                return $formatted_debt;
            })
            ->addColumn('action', function ($data) {
                return view('people::customers.partials.actions', compact('data'));
            })
            // rawColumns permite que el <span> de color rojo funcione
            ->rawColumns(['debt', 'action']);
    }

    public function query(Customer $model)
    {
        // Sumamos la columna due_amount de la tabla sales
        return $model->newQuery()->withSum('sales', 'due_amount');
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('customers-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>> .
                                        'tr' .
                                  <'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
            ->orderBy(0)
            ->buttons(
                Button::make('excel')->text('<i class="bi bi-file-earmark-excel-fill"></i> Excel'),
                Button::make('print')->text('<i class="bi bi-printer-fill"></i> Imprimir'),
                Button::make('reset')->text('<i class="bi bi-x-circle"></i> Reset'),
                Button::make('reload')->text('<i class="bi bi-arrow-repeat"></i> Recargar')
            );
    }

    protected function getColumns()
    {
        return [
            Column::make('customer_name')
                ->title('Nombre')
                ->className('text-center align-middle'),

            Column::make('customer_email')
                ->title('Email')
                ->className('text-center align-middle'),

            Column::make('customer_phone')
                ->title('Teléfono')
                ->className('text-center align-middle'),

            // Esta es la columna que ahora tiene el formato y color
            Column::make('debt')
                ->name('sales_sum_due_amount')
                ->title('Deuda')
                ->className('text-center align-middle'),

            Column::computed('action')
                ->title('Acciones')
                ->exportable(false)
                ->printable(false)
                ->className('text-center align-middle'),
        ];
    }

    protected function filename(): string
    {
        return 'Clientes_' . date('YmdHis');
    }
}
