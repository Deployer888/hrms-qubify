<!DOCTYPE html>
<html>
<head>
    <title>Employee Probation Ending Soon</title>
</head>
<body>
    <p>Dear HR,</p>

    <p>We would like to inform you that the probation period for <strong>{{ $employee->name }}</strong> is about to end in one week.</p>

    <p>Employee Details:</p>
    <ul>
        <li>Name: {{ $employee->name }}</li>
        <li>Designation: {{ $employee->designation->name }}</li>
        <li>Probation End Date: {{ \Carbon\Carbon::parse($employee->company_doj)->addMonths(3)->format('d-m-Y') }}</li>
    </ul>

    <p>Kind regards,</p>
    <p>HRM System</p>
</body>
</html>
