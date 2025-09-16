<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <h1>Attendance Management</h1>

    <!-- Filter Form -->
    <form id="attendanceForm">
        <div>
            <label for="typeSelect">Type:</label>
            <select id="typeSelect" name="type">
                <option value="monthly">Monthly</option>
                <option value="daily">Daily</option>
            </select>
        </div>

        <div>
            <label for="monthInput">Month (yyyy-mm):</label>
            <input type="text" id="monthInput" name="month" placeholder="Enter month (e.g., 2025-02)">
        </div>

        <div>
            <label for="dateInput">Date (yyyy-mm-dd):</label>
            <input type="text" id="dateInput" name="date" placeholder="Enter date (e.g., 2025-02-01)">
        </div>

        <div>
            <label for="branchSelect">Branch:</label>
            <select id="branchSelect" name="branch">
                <option value="">All</option>
                <option value="1">Branch 1</option>
                <option value="2">Branch 2</option>
            </select>
        </div>

        <div>
            <label for="departmentSelect">Department:</label>
            <select id="departmentSelect" name="department">
                <option value="">All</option>
                <option value="1">Department 1</option>
                <option value="2">Department 2</option>
            </select>
        </div>

        <div>
            <button type="button" id="fetchAttendanceBtn">Get Attendance</button>
        </div>
    </form>

    <!-- Results Section -->
    <h2>Attendance Results:</h2>
    <div id="attendanceResults"></div>

    <h2>Leave Days:</h2>
    <div id="leaveDaysResults"></div>

    <h2>Errors:</h2>
    <div id="errorMessages"></div>

    <script>
        $(document).ready(function() {
            // Handle form submission (button click)
            $('#fetchAttendanceBtn').click(function() {
                // Get values from the form
                var type = $('#typeSelect').val();
                var month = $('#monthInput').val();
                var date = $('#dateInput').val();
                var branch = $('#branchSelect').val();
                var department = $('#departmentSelect').val();

                // Perform the AJAX request
                $.ajax({
                    url: 'json/my-test',  // Replace with the actual URL to your Laravel endpoint
                    type: 'GET',
                    data: {
                        type: type,
                        month: month,
                        date: date,
                        branch: branch,
                        department: department
                    },
                    success: function(response) {
                        // Clear previous errors or results
                        $('#errorMessages').html('');
                        $('#attendanceResults').html('');
                        $('#leaveDaysResults').html('');

                        // Handle the response (rendering data into HTML)
                        if (response.attendance_employee) {
                            var attendanceHtml = '<ul>';
                            $.each(response.attendance_employee, function(index, employee) {
                                attendanceHtml += '<li>' + employee.date + ' - ' + employee.status + '</li>';
                            });
                            attendanceHtml += '</ul>';
                            $('#attendanceResults').html(attendanceHtml);
                        }

                        if (response.leave_days) {
                            var leaveDaysHtml = '<ul>';
                            $.each(response.leave_days, function(index, leaveDay) {
                                leaveDaysHtml += '<li>' + leaveDay + '</li>';
                            });
                            leaveDaysHtml += '</ul>';
                            $('#leaveDaysResults').html(leaveDaysHtml);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Display error messages
                        $('#attendanceResults').html('');
                        $('#leaveDaysResults').html('');

                        var errorMessage = xhr.status + ': ' + xhr.statusText;
                        $('#errorMessages').html('Error - ' + errorMessage);
                    }
                });
            });
        });
    </script>

</body>
</html>
