// This should be placed in office_employee.js

document.addEventListener('DOMContentLoaded', function() {
    function initMap() {
        // This will be implemented in office_employee.js
        alert("Map initialization called");
    }
    // Tab Navigation
    const tabItems = document.querySelectorAll('.tab-item');
    
    tabItems.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all tabs
            tabItems.forEach(item => {
                item.classList.remove('active');
            });
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Hide all tab content
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => {
                content.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabId).classList.add('active');
            
            // Initialize map when location tab is opened
            if (tabId === 'location') {
                initializeMap();
            }
            
            // Ensure charts refresh when switching tabs
            setTimeout(function() {
                window.dispatchEvent(new Event('resize'));
            }, 100);
        });
    });
    
    // Monthly Attendance Chart - Initialize if element exists
    if (document.getElementById('monthly-attendance-chart')) {
        const monthlyAttendanceCtx = document.getElementById('monthly-attendance-chart').getContext('2d');
        const monthlyAttendanceChart = new Chart(monthlyAttendanceCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Present',
                    data: [21, 19, 22, 20, 21, 20, 22, 21, 19, 21, 20, 16],
                    backgroundColor: '#28a745',
                    barPercentage: 0.5,
                    categoryPercentage: 0.8
                }, {
                    label: 'Absent',
                    data: [0, 1, 0, 1, 0, 1, 0, 1, 2, 0, 1, 0],
                    backgroundColor: '#dc3545',
                    barPercentage: 0.5,
                    categoryPercentage: 0.8
                }, {
                    label: 'Late',
                    data: [1, 1, 0, 1, 1, 0, 0, 0, 1, 1, 0, 2],
                    backgroundColor: '#ffc107',
                    barPercentage: 0.5,
                    categoryPercentage: 0.8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    xAxes: [{
                        stacked: true,
                        gridLines: {
                            display: false
                        }
                    }],
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12
                    }
                }
            }
        });
    }
    
    // Check-in Time Chart - Initialize if element exists
    if (document.getElementById('checkin-time-chart')) {
        const checkinTimeCtx = document.getElementById('checkin-time-chart').getContext('2d');
        const checkinTimeChart = new Chart(checkinTimeCtx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7', 'Week 8'],
                datasets: [{
                    label: 'Check-in Time',
                    data: [9.92, 9.75, 9.83, 10.25, 9.67, 9.75, 9.83, 9.75],
                    borderColor: '#6259ca',
                    backgroundColor: 'rgba(98, 89, 202, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#6259ca',
                    pointRadius: 4,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            callback: function(value) {
                                const hours = Math.floor(value);
                                const minutes = Math.round((value - hours) * 60);
                                return `${hours}:${minutes.toString().padStart(2, '0')}`;
                            },
                            min: 9,
                            max: 11
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            display: false
                        }
                    }]
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12
                    }
                }
            }
        });
    }
});

// Map initialization function
function initMap() {
    if (document.getElementById('employee-location-map')) {
        // Default coordinates (can be replaced with actual employee coordinates)
        const employeeLocation = {
            lat: 30.7046,
            lng: 76.7179
        };
        
        // Create map
        const map = new google.maps.Map(document.getElementById('employee-location-map'), {
            center: employeeLocation,
            zoom: 15,
            mapTypeControl: false,
            fullscreenControl: true,
            streetViewControl: false,
            styles: [
                {
                    "featureType": "administrative",
                    "elementType": "labels.text.fill",
                    "stylers": [{"color": "#444444"}]
                },
                {
                    "featureType": "landscape",
                    "elementType": "all",
                    "stylers": [{"color": "#f2f2f2"}]
                },
                {
                    "featureType": "poi",
                    "elementType": "all",
                    "stylers": [{"visibility": "off"}]
                },
                {
                    "featureType": "road",
                    "elementType": "all",
                    "stylers": [{"saturation": -100}, {"lightness": 45}]
                },
                {
                    "featureType": "road.highway",
                    "elementType": "all",
                    "stylers": [{"visibility": "simplified"}]
                },
                {
                    "featureType": "road.arterial",
                    "elementType": "labels.icon",
                    "stylers": [{"visibility": "off"}]
                },
                {
                    "featureType": "transit",
                    "elementType": "all",
                    "stylers": [{"visibility": "off"}]
                },
                {
                    "featureType": "water",
                    "elementType": "all",
                    "stylers": [{"color": "#46bcec"}, {"visibility": "on"}]
                }
            ]
        });
        
        // Create marker
        const marker = new google.maps.Marker({
            position: employeeLocation,
            map: map,
            title: 'Employee Location',
            animation: google.maps.Animation.DROP,
            icon: {
                url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png'
            }
        });
        
        // Add info window
        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div style="width: 200px; text-align: center;">
                    <div style="font-weight: bold; font-size: 16px; margin-bottom: 5px;">Employee Name</div>
                    <div>Designation</div>
                    <div style="margin-top: 8px; font-size: 12px;">Last seen: ${new Date().toLocaleTimeString()}</div>
                </div>
            `
        });
        
        marker.addListener('click', () => {
            infoWindow.open(map, marker);
        });
        
        // Get address from coordinates using Geocoder
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ location: employeeLocation }, (results, status) => {
            if (status === 'OK') {
                if (results[0]) {
                    document.getElementById('current-address').textContent = results[0].formatted_address;
                }
            }
        });
    }
}

// Initialize map if location tab is selected by default
if (document.querySelector('.tab-item[data-tab="location"].active')) {
    // Wait for Google Maps API to load
    window.addEventListener('load', function() {
        if (typeof google !== 'undefined') {
            initMap();
        }
    });
}