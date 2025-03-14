<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Hotel</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1, h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Hotel StayWise</h1>
    <h2>Reportes Estadísticos</h2>

    <h3>1. Reporte de Ocupación del Hotel</h3>
    <table>
        <thead>
            <tr>
                <th>Mes</th>
                <th>Hab. Ocupadas</th>
                <th>Total Hab.</th>
                <th>% Ocupación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ocupacion as $data)
                <tr>
                    <td>{{ $data['mes'] }}</td>
                    <td>{{ $data['ocupadas'] }}</td>
                    <td>{{ $data['total'] }}</td>
                    <td>{{ $data['porcentaje'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>2. Reporte de estado de reservas por mes</h3>
    <table>
        <thead>
            <tr>
                <th>Reservas Canceladas</th>
                <th>Reservas Pendientes</th>
                <th>Reservas Confirmadas</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $reservas['canceladas'] }}</td>
                <td>{{ $reservas['pendientes'] }}</td>
                <td>{{ $reservas['confirmadas'] }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
