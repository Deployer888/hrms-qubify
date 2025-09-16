$(document).ready(function() {
    let timerInterval;
    // bdayAnimation();
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
            // document.getElementById('timer-display').textContent = formatTime(elapsedTimeInSeconds);
        } else {
            // document.getElementById('timer-display').textContent = "00:00:00";
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
        $.ajax({
            url: "{{ url('attendanceemployee/current-timer-state') }}",
            type: "GET",
            success: function(response) {
                if (response.clock_in) {
                    startTimer(response.clock_in);
                    $('#clock_in').attr('disabled', 'disabled');
                    $('#clock_out').removeAttr('disabled');
                }
            }
        });
    }

    // Event listeners for clock in and clock out
     // Add event listeners once the DOM content is fully loaded
        $(document).ready(function() {

            $('#clock_in').click(function() {
                let currentTime = new Date().toLocaleTimeString('en-GB', { hour12: false });
                startTimer();
                $('#clock_in').attr('disabled', 'disabled');
                $('#clock_in').addClass('disabled');
                $.ajax({
                    url: clockInUrl,
                    type: "POST",
                    data: {
                        _token: csrfToken,
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
                            }, 100); // Show modal after 1 minute (60000 milliseconds)
                        }
                    },
                    error: function(response) {
                        if (response.responseJSON) {
                            alert(response.responseJSON[0]);
                        } else {
                            alert('Error clocking in');
                        }
                        // location.reload();
                    }
                });
            });

            $('#clock_out').click(function() {
                let currentTime = new Date().toLocaleTimeString('en-GB', { hour12: false });
                let attendanceId = document.getElementById('att_id').value;
                stopTimer();
                $.ajax({
                    url: clockOutUrl.replace(':id', attendanceId),
                    type: "POST",
                    data: {
                        _token: csrfToken,
                        _method: 'PUT',
                        time: currentTime
                    },
                    success: function(response) {
                        //console.log(response);
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

        function bdayAnimation(){
    // helper functions
const PI2 = Math.PI * 2
const random = (min, max) => Math.random() * (max - min + 1) + min | 0
const timestamp = _ => new Date().getTime()

// container
class Birthday {
  constructor() {
    this.resize()

    // create a lovely place to store the firework
    this.fireworks = []
    this.counter = 0

  }

  resize() {
    this.width = canvas.width = window.innerWidth
    let center = this.width / 2 | 0
    this.spawnA = center - center / 4 | 0
    this.spawnB = center + center / 4 | 0

    this.height = canvas.height = window.innerHeight
    this.spawnC = this.height * .1
    this.spawnD = this.height * .5

  }

  onClick(evt) {
     let x = evt.clientX || evt.touches && evt.touches[0].pageX
     let y = evt.clientY || evt.touches && evt.touches[0].pageY

     let count = random(3,5)
     for(let i = 0; i < count; i++) this.fireworks.push(new Firework(
        random(this.spawnA, this.spawnB),
        this.height,
        x,
        y,
        random(0, 260),
        random(30, 110)))

     this.counter = -1

  }

  update(delta) {
    ctx.globalCompositeOperation = 'hard-light'
    ctx.fillStyle = `rgba(20,20,20,${ 7 * delta })`
    ctx.fillRect(0, 0, this.width, this.height)

    ctx.globalCompositeOperation = 'lighter'
    for (let firework of this.fireworks) firework.update(delta)

    // if enough time passed... create new new firework
    this.counter += delta * 3 // each second
    if (this.counter >= 1) {
      this.fireworks.push(new Firework(
        random(this.spawnA, this.spawnB),
        this.height,
        random(0, this.width),
        random(this.spawnC, this.spawnD),
        random(0, 360),
        random(30, 110)))
      this.counter = 0
    }

    // remove the dead fireworks
    if (this.fireworks.length > 1000) this.fireworks = this.fireworks.filter(firework => !firework.dead)

  }
}

class Firework {
  constructor(x, y, targetX, targetY, shade, offsprings) {
    this.dead = false
    this.offsprings = offsprings

    this.x = x
    this.y = y
    this.targetX = targetX
    this.targetY = targetY

    this.shade = shade
    this.history = []
  }
  update(delta) {
    if (this.dead) return

    let xDiff = this.targetX - this.x
    let yDiff = this.targetY - this.y
    if (Math.abs(xDiff) > 3 || Math.abs(yDiff) > 3) { // is still moving
      this.x += xDiff * 2 * delta
      this.y += yDiff * 2 * delta

      this.history.push({
        x: this.x,
        y: this.y
      })

      if (this.history.length > 20) this.history.shift()

    } else {
      if (this.offsprings && !this.madeChilds) {

        let babies = this.offsprings / 2
        for (let i = 0; i < babies; i++) {
          let targetX = this.x + this.offsprings * Math.cos(PI2 * i / babies) | 0
          let targetY = this.y + this.offsprings * Math.sin(PI2 * i / babies) | 0

          birthday.fireworks.push(new Firework(this.x, this.y, targetX, targetY, this.shade, 0))

        }

      }
      this.madeChilds = true
      this.history.shift()
    }

    if (this.history.length === 0) this.dead = true
    else if (this.offsprings) {
        for (let i = 0; this.history.length > i; i++) {
          let point = this.history[i]
          ctx.beginPath()
          ctx.fillStyle = 'hsl(' + this.shade + ',100%,' + i + '%)'
          ctx.arc(point.x, point.y, 1, 0, PI2, false)
          ctx.fill()
        }
      } else {
      ctx.beginPath()
      ctx.fillStyle = 'hsl(' + this.shade + ',100%,50%)'
      ctx.arc(this.x, this.y, 1, 0, PI2, false)
      ctx.fill()
    }

  }
}

let canvas = document.getElementById('birthday')
let ctx = canvas.getContext('2d')

let then = timestamp()

let birthday = new Birthday
window.onresize = () => birthday.resize()
document.onclick = evt => birthday.onClick(evt)
document.ontouchstart = evt => birthday.onClick(evt)

  ;(function loop(){
  	requestAnimationFrame(loop)

  	let now = timestamp()
  	let delta = now - then

    then = now
    birthday.update(delta / 1000)


  })()
}
});