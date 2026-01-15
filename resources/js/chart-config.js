$(window).on('load', function() {
    console.log("🕵️ Sistema de Gráficos: Iniciando carga de datos...");

    const urlParams = new URLSearchParams(window.location.search);
    const sucursalEmail = urlParams.get('sucursal_email') || '';

    // Función auxiliar para limpiar lienzos antes de redibujar
    const destroy = (id) => {
        const instance = Chart.getChart(id);
        if (instance) instance.destroy();
    };

    // --- 1. GRÁFICO DE BARRAS: VENTAS Y COMPRAS (7 DÍAS) ---
    const salesCanvas = document.getElementById('salesPurchasesChart');
    if (salesCanvas) {
        console.log("📡 Pidiendo datos de Ventas/Compras...");
        $.get('/sales-purchases/chart-data', { sucursal_email: sucursalEmail })
            .done(function(res) {
                console.log("✅ Datos de Ventas recibidos:", res);
                destroy('salesPurchasesChart');

                let salesData = res.sales.original || res.sales;
                let purchaseData = res.purchases.original || res.purchases;

                new Chart(salesCanvas, {
                    type: 'bar',
                    data: {
                        labels: salesData.days,
                        datasets: [
                            { label: 'Ventas', data: salesData.data, backgroundColor: '#6366F1' },
                            { label: 'Compras', data: purchaseData.data, backgroundColor: '#A5B4FC' }
                        ]
                    },
                    options: { maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
                });
            })
            .fail(function(err) { console.error("❌ Error en Ventas:", err.status); });
    }

    // --- 2. GRÁFICO DE DONA: RESUMEN MENSUAL ---
    const monthCanvas = document.getElementById('currentMonthChart');
    if (monthCanvas) {
        console.log("📡 Pidiendo datos del Mes...");
        $.get('/current-month/chart-data', { sucursal_email: sucursalEmail })
            .done(function(res) {
                console.log("✅ Datos del Mes recibidos:", res);
                destroy('currentMonthChart');

                new Chart(monthCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: ['Ventas', 'Compras', 'Gastos'],
                        datasets: [{
                            data: [res.sales, res.purchases, res.expenses],
                            backgroundColor: ['#F59E0B', '#0284C7', '#EF4444']
                        }]
                    },
                    options: { maintainAspectRatio: false }
                });
            })
            .fail(function(err) { console.error("❌ Error en Resumen Mensual:", err.status); });
    }

    // --- 3. GRÁFICO DE LÍNEAS: FLUJO DE PAGOS (PAGOS RECIBIDOS VS ENVIADOS) ---
    const paymentCanvas = document.getElementById('paymentChart');
    if (paymentCanvas) {
        console.log("📡 Pidiendo datos de Flujo de Pagos...");
        $.get('/payment-flow/chart-data', { sucursal_email: sucursalEmail })
            .done(function (res) {
                console.log("✅ Datos de Pagos recibidos:", res);
                destroy('paymentChart');

                new Chart(paymentCanvas, {
                    type: 'line',
                    data: {
                        labels: res.months,
                        datasets: [
                            {
                                label: 'Pagos Recibidos',
                                data: res.payment_received,
                                borderColor: '#2563EB',
                                backgroundColor: '#2563EB',
                                fill: false,
                                tension: 0.3
                            },
                            {
                                label: 'Pagos Enviados',
                                data: res.payment_sent,
                                borderColor: '#EA580C',
                                backgroundColor: '#EA580C',
                                fill: false,
                                tension: 0.3
                            }
                        ]
                    },
                    options: {
                        maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true } }
                    }
                });
            })
            .fail(function(err) { console.error("❌ Error en Flujo de Pagos:", err.status); });
    }
});
