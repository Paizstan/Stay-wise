<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Reservaciones</title>
    <style>
        @page { margin: 40px 50px; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 10px;
        }
        .header { text-align: center; margin-top: 20px; }
        .header img { width: 80px; }
        .header h2 { margin: 5px 0; font-size: 18px; }
        .header p { margin: 2px 0; font-size: 14px; }
        .tabla { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .tabla th, .tabla td { border: 1px solid black; padding: 8px; text-align: left; }
        .tabla th { background-color: black; color: white; }
        .footer { font-weight: bold; color: blue; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/stay.png') }}" alt="logo">
        <h2>Hotel StayWise</h2>
        <p><strong>Reporte de Reservaciones</strong></p>
        <p>Desde: {{ date('d/m/Y', strtotime($fecha1)) }} - Hasta: {{ date('d/m/Y', strtotime($fecha2)) }}</p>
        <p>Estado: {{ $estado }}</p>
    </div>

    @foreach ($data as $reserva)
    <h3>Reservación #: {{ $reserva['id'] }} - Cliente: {{ $reserva['cliente'] }}</h3>
    <table class="tabla">
        <thead>
            <tr>
                <th>Habitación</th>
                <th>Tipo</th>
                <th>Entrada</th>
                <th>Salida</th>
                <th>Subtotal</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reserva['detalle'] as $detalle)
            <tr>
                <td>{{ $detalle['habitacion'] }}</td>
                <td>{{ $detalle['tipo'] }}</td>
                <td>{{ date('d/m/Y', strtotime($detalle['fecha_entrada'])) }}</td>
                <td>{{ date('d/m/Y', strtotime($detalle['fecha_salida'])) }}</td>
                <td style="text-align: right;">${{ number_format($detalle['precio'], 2) }}</td>
                <td style="text-align: right; color: blue;"><strong>${{ number_format($detalle['subtotal'], 2) }}</strong></td>
            </tr>
            @endforeach
            <tr>
                <td colspan="5"><strong>Total de la Reservación</strong></td>
                <td style="text-align: right; color: blue;"><strong>${{ number_format($reserva['total'], 2) }}</strong></td>
            </tr>
        </tbody>
    </table>
    @endforeach 
    
    <hr style="margin-top:16px;">
    <p class="footer">Total de reservaciones en el periodo: {{ count($data ?? []) }} | Monto Total: ${{ number_format(collect($data ?? [])->sum('total'), 2) }}</p>

    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(500, 820, "Página $PAGE_NUM de $PAGE_COUNT", $font, 10);
            ');
        }
    </script>
</body>
</html>