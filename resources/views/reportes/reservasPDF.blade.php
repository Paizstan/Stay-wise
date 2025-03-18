<!DOCTYPE html>
<html>

<head>
    <title>StayWise</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .reserva {
            margin-bottom: 30px;
            border: 1px solid #ddd;
            padding: 10px;
        }

        .detalle {
            margin-left: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .total {
            font-weight: bold;
            text-align: right;
            margin-top: 10px;
        }

        .subtotal {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1> Hotel StayWise</h1>
        <h1>Reportes de Reserva</h1>
        <p>Período: {{ date('d/m/Y', strtotime($fecha1)) }} - {{ date('d/m/Y', strtotime($fecha2)) }}</p>
        <p>Estado: {{ $estado }}</p>
    </div>

    @foreach($data as $reserva)
        <div class="reserva">
            <h3>Reserva #{{ $reserva['id'] }}</h3>
            <p>Cliente: {{ $reserva['cliente'] }}</p>
            <p>Fecha: {{ date('d/m/Y', strtotime($reserva['fecha_creacion'])) }}</p>
            <p>Estado: {{ $reserva['estado'] }}</p>

            <table>
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
                    @foreach($reserva['detalle'] as $detalle)
                        <tr>
                            <td>{{ $detalle['habitacion'] }}</td>
                            <td>{{ $detalle['tipo'] }}</td>
                            <td>{{ date('d/m/Y', strtotime($detalle['fecha_entrada'])) }}</td>
                            <td>{{ date('d/m/Y', strtotime($detalle['fecha_salida'])) }}</td>
                            <td>${{ number_format($detalle['precio'], 2) }}</td>
                            <td class="subtotal">${{ number_format($detalle['subtotal'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total">
                Total: ${{ number_format($reserva['total'], 2) }}
            </div>
        </div>
    @endforeach
</body>

</html>