<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Reporte de Totales</title>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 11pt;
      color: #333;
    }

    h2 {
      text-align: center;
      color: #111;
      margin-bottom: 10px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    th,
    td {
      border: 1px solid #ccc;
      padding: 8px 10px;
      text-align: center;
    }

    th {
      background-color: #f3f4f6;
      font-weight: bold;
    }

    tr:nth-child(even) {
      background-color: #f9fafb;
    }

    .activo {
      color: #16a34a;
      font-weight: bold;
    }

    .inactivo {
      color: #dc2626;
      font-weight: bold;
    }

    .footer {
      text-align: right;
      font-size: 10pt;
      margin-top: 20px;
      color: #555;
    }
  </style>
</head>

<body>
  <h2>Reporte de Totales del Sistema</h2>

  <table>
    <thead>
      <tr>
        <th>Nombre Módulo</th>
        <th>Cantidad Total</th>
        <th>Cantidad Activos</th>
        <th>Cantidad Inactivos</th>
        <th>Estado</th>
        <th>Última Actualización</th>
      </tr>
    </thead>
    <tbody>
      @foreach($totales as $t)
      <tr>
        <td>{{ $t['name'] }}</td>
        <td>{{ $t['count'] ?? 0 }}</td>
        <td>{{ $t['count_active'] ?? 0 }}</td>
        <td>{{ $t['count_inactive'] ?? 0 }}</td>
        @if($t['have_items_active'])
        <td class="activo">Activo</td>
        @else
        <td class="inactivo">Inactivo</td>
        @endif
        <td>{{ \Carbon\Carbon::parse($t['last_updated_max'])->format('d-m-Y h:i A') }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div class="footer">
    Generado el {{ $fecha }}
  </div>
</body>

</html>