// Enhanced JavaScript for Premium Dashboard Experience
document.addEventListener('DOMContentLoaded', function() {
    // Enhance cards with hover animations
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Initialize the attendance chart with improved styling
    initializeAttendanceChart();
    
    // Initialize countdown timer
    initializeCountdownTimer();
    
    // Add pulse effect to the clock in/out buttons
    addPulseEffectToButtons();
    
    // Make tables responsive with horizontal scrolling on mobile
    makeTablesResponsive();
    
    // Add smooth scrolling to all links
    addSmoothScrolling();
    
    // Enhance modal animations
    enhanceModalAnimations();
});

// Initialize attendance chart with improved styling
function initializeAttendanceChart() {
    var ctx = document.getElementById('attendanceChart');
    if (!ctx) return;
    
    ctx = ctx.getContext('2d');
    
    // Get data from the existing chart data
    const presentDays = parseInt(document.querySelector('.card-body small').textContent.split('/')[0].trim());
    const totalDays = parseInt(document.querySelector('.card-body small').textContent.split('/')[1].split('days')[0].trim());
    const absentDays = parseInt(document.querySelectorAll('.card-body small')[1].textContent.split('/')[0].trim());
    const lateDays = parseInt(document.querySelectorAll('.card-body small')[2].textContent.split('/')[0].trim());
    
    // Calculate actual present days (excluding late days)
    const actualPresentDays = presentDays - lateDays;
    
    // Create chart with improved styling
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Present', 'Absent', 'Late'],
            datasets: [{
                data: [actualPresentDays, absentDays, lateDays],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',  // Green for present
                    'rgba(220, 53, 69, 0.8)',  // Red for absent
                    'rgba(255, 193, 7, 0.8)'   // Yellow for late
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',  
                    'rgba(220, 53, 69, 1)',  
                    'rgba(255, 193, 7, 1)'   
                ],
                borderWidth: 2,
                hoverBackgroundColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(255, 193, 7, 1)'
                ],
                hoverBorderColor: '#fff',
                hoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: {
                            family: "'Nunito', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif",
                            size: 12,
                            weight: 600
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#333',
                    bodyColor: '#333',
                    bodyFont: {
                        family: "'Nunito', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif",
                        size: 14
                    },
                    borderColor: 'rgba(0, 0, 0, 0.1)',
                    borderWidth: 1,
                    boxPadding: 10,
                    cornerRadius: 6,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            var label = context.label || '';
                            var value = context.raw || 0;
                            var total = context.dataset.data.reduce((a, b) => a + b, 0);
                            var percentage = Math.round((value / total) * 100);
                            return label + ': ' + value + ' days (' + percentage + '%)';
                        }
                    }
                }
            },
            cutout: '70%',
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 1500,
                easing: 'easeOutQuart'
            }
        }
    });
}

// Initialize countdown timer
/*function initializeCountdownTimer() {
    const timerDisplay = document.getElementById('timer-display');
    if (!timerDisplay) return;
    
    // Get current time
    const now = new Date();
    
    // Calculate end of workday (assuming 18:00 or 6 PM)
    const endOfWorkday = new Date();
    endOfWorkday.setHours(18, 0, 0, 0);
    
    // If current time is after end of workday, show 00:00:00
    if (now >= endOfWorkday) {
        timerDisplay.textContent = '00:00:00';
        return;
    }
    
    // Calculate time difference in seconds
    let timeDiff = Math.floor((endOfWorkday - now) / 1000);
    
    // Update the timer every second
    const timerInterval = setInterval(function() {
        if (timeDiff <= 0) {
            clearInterval(timerInterval);
            timerDisplay.textContent = '00:00:00';
            return;
        }
        
        // Calculate hours, minutes, and seconds
        const hours = Math.floor(timeDiff / 3600);
        const minutes = Math.floor((timeDiff % 3600) / 60);
        const seconds = timeDiff % 60;
        
        // Format time as HH:MM:SS
        const formattedTime = 
            (hours < 10 ? '0' + hours : hours) + ':' +
            (minutes < 10 ? '0' + minutes : minutes) + ':' +
            (seconds < 10 ? '0' + seconds : seconds);
        
        // Update timer display
        timerDisplay.textContent = formattedTime;
        
        // Decrease time difference by 1 second
        timeDiff--;
    }, 1000);
}*/

let firstClockInTime = null;
let timerInterval = null;

function initializeCountdownTimer() {
    const timerDisplay = document.getElementById('timer-display');
    if (!timerDisplay) return;
    
    // Check if this is the first clock-in of the day
    if (firstClockInTime === null) {
        // Get the first clock-in time from the last row of the attendance table
        // The last row in the table contains the first clock-in of the day
        const attendanceRows = document.querySelectorAll('.attendance-table tbody.attendanceBody tr');
        
        if (attendanceRows && attendanceRows.length > 0) {
            // Get the last row (which has the first clock-in)
            const lastRow = attendanceRows[attendanceRows.length - 1];
            const firstClockInElement = lastRow.querySelector('td:first-child');
            
            if (firstClockInElement) {
                const clockInTimeString = firstClockInElement.textContent.trim();
                
                // Create today's date
                const today = new Date();
                
                // Parse the clock-in time 
                const [hours, minutes, seconds] = clockInTimeString.split(':').map(Number);
                
                // Set the time components on today's date
                today.setHours(hours, minutes, seconds, 0);
                
                firstClockInTime = today;
            } else {
                // If no attendance record exists yet, use current time
                firstClockInTime = new Date();
            }
        } else {
            // If no attendance rows exist, use current time
            firstClockInTime = new Date();
        }
    }
    
    // Clear any existing timer
    if (timerInterval) {
        clearInterval(timerInterval);
    }
    
    // Calculate end time (9 hours after first clock-in)
    const endTime = new Date(firstClockInTime);
    endTime.setHours(firstClockInTime.getHours() + 9);
    
    // Calculate initial time difference in seconds
    let timeDiff = Math.floor((endTime - new Date()) / 1000);
    
    // If time difference is negative or zero, show 00:00:00
    if (timeDiff <= 0) {
        timerDisplay.textContent = '00:00:00';
        return;
    }
    
    // Update the timer every second
    timerInterval = setInterval(function() {
        // Get current time for each update
        const now = new Date();
        
        // Recalculate time difference
        timeDiff = Math.floor((endTime - now) / 1000);
        
        if (timeDiff <= 0) {
            clearInterval(timerInterval);
            timerDisplay.textContent = '00:00:00';
            return;
        }
        
        // Calculate hours, minutes, and seconds
        const hours = Math.floor(timeDiff / 3600);
        const minutes = Math.floor((timeDiff % 3600) / 60);
        const seconds = timeDiff % 60;
        
        // Format time as HH:MM:SS
        const formattedTime = 
            (hours < 10 ? '0' + hours : hours) + ':' +
            (minutes < 10 ? '0' + minutes : minutes) + ':' +
            (seconds < 10 ? '0' + seconds : seconds);
        
        // Update timer display
        timerDisplay.textContent = formattedTime;
    }, 1000);
    
    // For debugging
    console.log('First clock-in time:', firstClockInTime);
    console.log('End time (9 hours later):', endTime);
    
    return {
        firstClockInTime,
        endTime,
        stopTimer: () => {
            clearInterval(timerInterval);
            timerInterval = null;
        },
        resetClockIn: () => { 
            firstClockInTime = null; 
            if (timerInterval) {
                clearInterval(timerInterval);
                timerInterval = null;
            }
        }
    };
}

// Call this function when the page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeCountdownTimer();
});

// Add pulse effect to buttons
function addPulseEffectToButtons() {
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        if (!button.disabled) {
            button.classList.add('btn-pulse');
        }
    });
}

// Make tables responsive
function makeTablesResponsive() {
    const tables = document.querySelectorAll('.table');
    tables.forEach(table => {
        const wrapper = document.createElement('div');
        wrapper.classList.add('table-responsive');
        table.parentNode.insertBefore(wrapper, table);
        wrapper.appendChild(table);
    });
}

// Add smooth scrolling to all links
function addSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Enhance modal animations
function enhanceModalAnimations() {
    // Function to open modal with animation
    window.openModal = function(eventId) {
        const event = events.find(e => e.id == eventId);
        if (!event) return;

        document.getElementById('eventTitle').textContent = event.title;
        document.getElementById('eventDate').textContent = formatDate(event.start_date);
        document.getElementById('eventLocation').textContent = event.location || 'Location not specified';
        document.getElementById('eventDescription').textContent = event.description || 'No description available';
        
        const modal = document.getElementById('eventModal');
        modal.classList.remove('hidden');
        modal.classList.add('fade-in');
        
        // Get the modal panel
        const modalPanel = modal.querySelector('.bg-white');
        modalPanel.classList.add('scale-in');
        
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    };
    
    // Function to close modal with animation
    window.closeModal = function() {
        const modal = document.getElementById('eventModal');
        const modalPanel = modal.querySelector('.bg-white');
        
        // Add fade-out animation
        modal.classList.add('fade-out');
        modalPanel.classList.add('scale-out');
        
        // Wait for animation to complete before hiding
        setTimeout(() => {
            modal.classList.remove('fade-in', 'fade-out');
            modal.classList.add('hidden');
            modalPanel.classList.remove('scale-in', 'scale-out');
            document.body.style.overflow = 'auto'; // Restore scrolling
        }, 300);
    };
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('eventModal');
        if (event.target === modal) {
            closeModal();
        }
    };
    
    // Format date function
    window.formatDate = function(dateString) {
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('en-US', options);
    };
    
    // Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
}