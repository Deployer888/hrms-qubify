<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: center;
            border: 1px solid black;
        }
        th {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Attendance Report</h1>
    <div>
        {!! $htmlContent !!}
    </div>
</body>
</html>
