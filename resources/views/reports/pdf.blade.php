<!DOCTYPE html>
<html>
<head>
    <title>{{ ucfirst(str_replace('-', ' ', $type)) }} Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #005555;
        }
        h2 {
            color: #007777;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #777;
        }
    </style>
</head>
<body>
    <h1>{{ ucfirst(str_replace('-', ' ', $type)) }} Report</h1>
    @if ($data->isEmpty())
        <p class="no-data">No data available for the selected criteria.</p>
    @else
        @foreach ($data as $group => $items)
            <h2>{{ ucfirst($group) }}</h2>
            <table>
                <thead>
                    <tr>
                        @foreach (array_keys((array) $items->first()) as $key)
                            <th>{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            @foreach ($item as $value)
                                <td>{{ $value ?? 'N/A' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @endif
</body>
</html>
