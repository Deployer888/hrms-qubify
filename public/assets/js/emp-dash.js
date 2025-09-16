$(document).ready(function() {
    let timerInterval;
    
    // Function to format time in HH:MM:SS format
    function formatTime(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const remainingSeconds = seconds % 60;

        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
    }

    // Function to start the timer
    function startTimer(startTime) {
        if(startTime){
            const startTimestamp = Date.parse(startTime);
            localStorage.setItem('startTime', startTimestamp);
        }else{
            startTimestamp = Date.now();
            localStorage.setItem('startTime', startTimestamp);
        }
        localStorage.setItem('isRunning', 'true');
        updateTimer();
        timerInterval = setInterval(updateTimer, 1000);
    }

    // Function to update the timer display
    function updateTimer() {
        const startTimestamp = parseInt(localStorage.getItem('startTime'));
        if (!isNaN(startTimestamp)) {
            const currentTime = Date.now();
            const elapsedTimeInSeconds = Math.floor((currentTime - startTimestamp) / 1000);
            document.getElementById('timer-display').textContent = formatTime(elapsedTimeInSeconds);
        } else {
            document.getElementById('timer-display').textContent = "00:00:00";
        }
    }

    function convertToFormattedTime(timeString) {
        var timeParts = timeString.split(':');
        var hours = parseInt(timeParts[0], 10);
        var minutes = parseInt(timeParts[1], 10);
        var seconds = parseInt(timeParts[2], 10);

        // Convert hours and minutes to seconds
        var totalSeconds = (hours * 3600) + (minutes * 60) + seconds;

        // Create a formatted string based on the time parts
        var formattedTime = '';

        // Add hours if greater than 0
        if (hours > 0) {
            formattedTime += hours + 'hr ';
        }

        // Add minutes if greater than 0 or if hours are present
        if (minutes > 0 || hours > 0) {
            formattedTime += minutes + 'm ';
        }

        // Always add seconds
        formattedTime += seconds + 's';

        // Trim any extra spaces at the end
        return {
            totalSeconds: totalSeconds,
            formattedTime: formattedTime.trim()
        };
    }

    // Function to stop the timer
    function stopTimer() {
        clearInterval(timerInterval);
        localStorage.setItem('isRunning', 'false');
    }

    // Function to initialize the timer on page load
    function initializeTimer() {
        const isRunning = localStorage.getItem('isRunning') === 'true';
        if (isRunning) {
            updateTimer();
            timerInterval = setInterval(updateTimer, 1000);
        }
    }

    $('#clock_in').click(function() {
        alert('aaaaa');
        let currentTime = new Date().toLocaleTimeString('en-GB', { hour12: false });
        startTimer();
        $('#clock_in').attr('disabled', 'disabled');
        $('#clock_in').addClass('disabled');
        $.ajax({
            url: "{{ route('attendanceemployee.attendance') }}",
            type: "POST",
            data: {
                _token: $('input[name="_token"]').val(),
                time: currentTime
            },
            success: function(response) {
                toastr.success('Clocked in successfully at ' + currentTime);
                var totalRest = convertToFormattedTime(response.totalRest);
                var late = convertToFormattedTime(response.late);

                $('#clock_out').removeAttr('disabled');
                $('#clock_out').removeClass('disabled');

                var timeDisplay = '';
                if (response.totalRest === "00:00:00") {
                    timeDisplay = `${late.formattedTime} (Late)`;
                } else {
                    timeDisplay = `${totalRest.formattedTime} (Rest)`;
                }

                $('.attendanceBody').prepend(`
                    <tr>
                        <td>${currentTime}</td>
                        <td></td>
                        <td></td>
                        <td>${timeDisplay}</td>
                    </tr>
                `);

                // Check if it's the employee's birthday
                if (response.is_birthday != '') {
                    setTimeout(function() {
                        $('canvas#birthday').toggleClass('d-none');
                        bdayAnimation();
                        $('#musicModal').modal('show');
                        $('div.modal-backdrop').removeClass('modal-backdrop');
                        var audioPlayer = document.getElementById('audioPlayer');
                        audioPlayer.load();
                        audioPlayer.play();
                    }, 100);
                }
            },
            error: function(response) {
                if (response.responseJSON) {
                    alert(response.responseJSON[0]);
                } else {
                    alert('Error clocking in');
                }
            }
        });
    });

    $('#clock_out').click(function() {
        let currentTime = new Date().toLocaleTimeString('en-GB', { hour12: false });
        let attendanceId = document.getElementById('att_id').value;
        stopTimer();
        $.ajax({
            url: "{{ route('attendanceemployee.update', ':id') }}".replace(':id', attendanceId),
            type: "POST",
            data: {
                _token: $('input[name="_token"]').val(),
                _method: 'PUT',
                time: currentTime
            },
            success: function(response) {
                if(response == 'success'){
                    toastr.success('Clocked out successfully at ' + currentTime);
                    $('#clock_out').attr('disabled');
                    $('#clock_in').removeAttr('disabled');
                    $('#clock_out').addClass('disabled');
                    $('#clock_in').removeClass('disabled');
                    location.reload();
                }
                else{
                    console.log(response.message);
                    toastr.error('ERROR !!');
                }
            },
            error: function(response) {
                alert('Error clocking out.');
                location.reload();
            }
        });
    });
    
    // Initialize the timer
    initializeTimer();
});