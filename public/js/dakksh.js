/**
 * HR Attendance Dashboard JS
 * Modern & interactive dashboard for HR attendance analytics
 */

// Define chart global settings
Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif";
Chart.defaults.color = '#6c757d';
Chart.defaults.borderColor = '#e9ecef';

// Variables to hold chart instances
let attendanceGauge, absenteeismGauge, locationChart, leaveDistributionChart;
let absencesChart, experienceChart, deptAttendanceChart;
let employeeMap, refreshInterval = 15;
let refreshTimer = null;
let officeCharts = {};

// Demo data for the dashboard
const demoData = {
    officeLocations: [
        { id: 'hq', name: 'Headquarters', lat: 40.7128, lng: -74.0060 },
        { id: 'nyc', name: 'New York', lat: 40.7831, lng: -73.9712 },
        { id: 'la', name: 'Los Angeles', lat: 34.0522, lng: -118.2437 },
        { id: 'chicago', name: 'Chicago', lat: 41.8781, lng: -87.6298 },
        { id: 'miami', name: 'Miami', lat: 25.7617, lng: -80.1918 },
        { id: 'dallas', name: 'Dallas', lat: 32.7767, lng: -96.7970 },
        { id: 'seattle', name: 'Seattle', lat: 47.6062, lng: -122.3321 },
        { id: 'boston', name: 'Boston', lat: 42.3601, lng: -71.0589 },
        { id: 'sf', name: 'San Francisco', lat: 37.7749, lng: -122.4194 },
        { id: 'austin', name: 'Austin', lat: 30.2672, lng: -97.7431 }
    ],
    departments: ['Finance', 'IT', 'Sales', 'Human Resources', 'Marketing', 'Administration', 'Customer Support', 'Accounting'],
    branches: ['Main', 'East Wing', 'West Wing', 'North Wing', 'South Wing'],
    attendanceData: {
        totalEmployees: 28,
        daysAbsent: 296,
        daysPresent: 1248,
        unscheduledLeave: 108,
        attendanceRate: 83.01,
        absenteeismRate: 16.99,
        sickLeave: 78,
        casualLeave: 76,
        locationBreakdown: [
            { label: 'In Office', value: 54.57 },
            { label: 'Outside Office', value: 45.43 }
        ],
        leaveDistribution: [
            { label: 'Vacation', value: 36.79 },
            { label: 'Sick', value: 12.26 },
            { label: 'Personal', value: 35.83 },
            { label: 'Family', value: 9.43 },
            { label: 'Other', value: 5.66 }
        ],
        absencesByMonth: [
            { month: 'Dec 2024', count: 1 },
            { month: 'Jan 2025', count: 6 },
            { month: 'Feb 2025', count: 13 },
            { month: 'Mar 2025', count: 4 }
        ],
        employeesByExperience: [
            { range: '0 - 1 Years', count: 13 },
            { range: '1 - 3 Years', count: 4 },
            { range: '3 - 5 Years', count: 5 },
            { range: '5 - 7 Years', count: 1 },
            { range: '7+ Years', count: 7 }
        ],
        attendanceByDepartment: [
            { department: 'Finance', rate: 94.74 },
            { department: 'IT', rate: 91.01 },
            { department: 'Sales', rate: 85.26 },
            { department: 'Human Resources', rate: 82.00 },
            { department: 'Marketing', rate: 81.33 },
            { department: 'Administration', rate: 81.19 },
            { department: 'Customer Support', rate: 80.10 },
            { department: 'Accounting', rate: 63.54 }
        ],
        officeData: [
            {
                id: 'hq',
                name: 'Headquarters',
                employees: 120,
                present: 92,
                absent: 14,
                onLeave: 8,
                wfh: 18,
                attendanceRate: 86.7,
                departments: [
                    { name: 'Finance', total: 22, present: 21, absent: 1, onLeave: 0 },
                    { name: 'IT', total: 38, present: 32, absent: 4, onLeave: 2 },
                    { name: 'Sales', total: 28, present: 22, absent: 3, onLeave: 3 },
                    { name: 'HR', total: 10, present: 8, absent: 1, onLeave: 1 },
                    { name: 'Marketing', total: 22, present: 19, absent: 1, onLeave: 2 }
                ]
            },
            {
                id: 'nyc',
                name: 'New York',
                employees: 85,
                present: 65,
                absent: 9,
                onLeave: 7,
                wfh: 14,
                attendanceRate: 81.2,
                departments: [
                    { name: 'Finance', total: 15, present: 13, absent: 1, onLeave: 1 },
                    { name: 'IT', total: 30, present: 24, absent: 4, onLeave: 2 },
                    { name: 'Sales', total: 20, present: 16, absent: 2, onLeave: 2 },
                    { name: 'HR', total: 8, present: 6, absent: 1, onLeave: 1 },
                    { name: 'Marketing', total: 12, present: 10, absent: 1, onLeave: 1 }
                ]
            },
            {
                id: 'sf',
                name: 'San Francisco',
                employees: 72,
                present: 58,
                absent: 7,
                onLeave: 5,
                wfh: 10,
                attendanceRate: 84.3,
                departments: [
                    { name: 'Finance', total: 12, present: 10, absent: 1, onLeave: 1 },
                    { name: 'IT', total: 25, present: 20, absent: 3, onLeave: 2 },
                    { name: 'Sales', total: 18, present: 16, absent: 1, onLeave: 1 },
                    { name: 'HR', total: 7, present: 6, absent: 1, onLeave: 0 },
                    { name: 'Marketing', total: 10, present: 8, absent: 1, onLeave: 1 }
                ]
            }
        ],
        remoteEmployees: [
            { name: 'John Smith', department: 'Sales', location: 'Home Office', distance: '5.2 miles', lastCheckin: '10:15 AM' },
            { name: 'Emily Johnson', department: 'IT', location: 'Coffee Shop', distance: '2.4 miles', lastCheckin: '9:45 AM' },
            { name: 'Michael Williams', department: 'Marketing', location: 'Client Site', distance: '12.7 miles', lastCheckin: '8:30 AM' },
            { name: 'Jessica Brown', department: 'Finance', location: 'Airport', distance: '18.3 miles', lastCheckin: '11:20 AM' },
            { name: 'David Miller', department: 'Customer Support', location: 'Home Office', distance: '7.8 miles', lastCheckin: '9:00 AM' },
            { name: 'Sarah Wilson', department: 'Human Resources', location: 'Co-working Space', distance: '3.5 miles', lastCheckin: '10:45 AM' }
        ],
        employeeGeolocations: [
            { name: 'John Smith', lat: 40.7282, lng: -74.0776, department: 'Sales' },
            { name: 'Emily Johnson', lat: 40.7325, lng: -73.9866, department: 'IT' },
            { name: 'Michael Williams', lat: 40.6892, lng: -74.0445, department: 'Marketing' },
            { name: 'Jessica Brown', lat: 40.7769, lng: -73.8740, department: 'Finance' },
            { name: 'David Miller', lat: 40.7461, lng: -73.9876, department: 'Customer Support' },
            { name: 'Sarah Wilson', lat: 40.7563, lng: -73.9904, department: 'Human Resources' }
        ]
    }
};

// Initialize dashboard when DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date range picker
    $('#date-range').daterangepicker({
        startDate: '12/14/2024',
        endDate: '7/19/2025',
        opens: 'left',
        locale: {
            format: 'MM/DD/YYYY'
        },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize all charts
    initializeGauges();
    initializeDonutCharts();
    initializeBarCharts();
    initializeOfficeCards();
    
    // Set up event listeners
    setupEventListeners();

    // Show dashboard with a nice fade-in effect
    document.querySelector('.hr-dashboard').style.opacity = '0';
    setTimeout(() => {
        document.querySelector('.hr-dashboard').style.transition = 'opacity 0.5s ease';
        document.querySelector('.hr-dashboard').style.opacity = '1';
    }, 100);
});

// Initialize gauge charts
function initializeGauges() {
    // Attendance Rate Gauge
    const attendanceCtx = document.getElementById('attendance-gauge').getContext('2d');
    
    attendanceGauge = new Chart(attendanceCtx, {
        type: 'gauge',
        data: {
            datasets: [{
                value: demoData.attendanceData.attendanceRate,
                minValue: 0,
                maxValue: 100,
                backgroundColor: getGaugeColors('attendance'),
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            needle: {
                radiusPercentage: 2,
                widthPercentage: 2.2,
                lengthPercentage: 80,
                color: 'rgba(0, 0, 0, 0.8)'
            },
            valueLabel: {
                display: false
            }
        }
    });

    // Absenteeism Rate Gauge
    const absenteeismCtx = document.getElementById('absenteeism-gauge').getContext('2d');
    
    absenteeismGauge = new Chart(absenteeismCtx, {
        type: 'gauge',
        data: {
            datasets: [{
                value: demoData.attendanceData.absenteeismRate,
                minValue: 0,
                maxValue: 100,
                backgroundColor: getGaugeColors('absenteeism'),
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            needle: {
                radiusPercentage: 2,
                widthPercentage: 2.2,
                lengthPercentage: 80,
                color: 'rgba(0, 0, 0, 0.8)'
            },
            valueLabel: {
                display: false
            }
        }
    });
}

// Initialize donut charts
function initializeDonutCharts() {
    // Location Breakdown Chart
    const locationCtx = document.getElementById('location-breakdown-chart').getContext('2d');
    locationChart = new Chart(locationCtx, {
        type: 'doughnut',
        data: {
            labels: demoData.attendanceData.locationBreakdown.map(item => item.label),
            datasets: [{
                data: demoData.attendanceData.locationBreakdown.map(item => item.value),
                backgroundColor: ['#6366f1', '#ec4899'],
                borderColor: 'white',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.label}: ${context.raw.toFixed(1)}%`;
                        }
                    },
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    padding: 10,
                    cornerRadius: 4,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    }
                }
            },
            onClick: function(e, elements) {
                if (elements.length && elements[0].index === 1) { // "Outside Office" clicked
                    const modal = new bootstrap.Modal(document.getElementById('geoLocationModal'));
                    modal.show();
                    setTimeout(initializeEmployeeMap, 500); // Delay to ensure the modal is visible
                }
            }
        }
    });

    // Leave Distribution Chart
    const leaveDistCtx = document.getElementById('leave-distribution-chart').getContext('2d');
    leaveDistributionChart = new Chart(leaveDistCtx, {
        type: 'doughnut',
        data: {
            labels: demoData.attendanceData.leaveDistribution.map(item => item.label),
            datasets: [{
                data: demoData.attendanceData.leaveDistribution.map(item => item.value),
                backgroundColor: ['#fbbf24', '#f87171', '#60a5fa', '#34d399', '#a78bfa'],
                borderColor: 'white',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxWidth: 8,
                        font: {
                            size: 11
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.label}: ${context.raw.toFixed(1)}%`;
                        }
                    },
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    padding: 10,
                    cornerRadius: 4
                }
            }
        }
    });
}

// Initialize bar charts
function initializeBarCharts() {
    // Absences by Month Chart
    const absencesCtx = document.getElementById('absences-month-chart').getContext('2d');
    absencesChart = new Chart(absencesCtx, {
        type: 'line',
        data: {
            labels: demoData.attendanceData.absencesByMonth.map(item => item.month),
            datasets: [{
                label: 'Absences',
                data: demoData.attendanceData.absencesByMonth.map(item => item.count),
                backgroundColor: 'rgba(124, 58, 237, 0.2)',
                borderColor: '#7c3aed',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#7c3aed',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 2,
                        padding: 10
                    },
                    grid: {
                        display: true,
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        padding: 10
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    padding: 10,
                    cornerRadius: 4
                }
            }
        }
    });

    // Employees by Experience Chart
    const experienceCtx = document.getElementById('experience-chart').getContext('2d');
    experienceChart = new Chart(experienceCtx, {
        type: 'bar',
        data: {
            labels: demoData.attendanceData.employeesByExperience.map(item => item.range),
            datasets: [{
                label: 'Employees',
                data: demoData.attendanceData.employeesByExperience.map(item => item.count),
                backgroundColor: '#f59e0b',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 2,
                        padding: 10
                    },
                    grid: {
                        display: true,
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        padding: 10
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    padding: 10,
                    cornerRadius: 4
                }
            }
        }
    });

    // Attendance by Department Chart
    const deptCtx = document.getElementById('dept-attendance-chart').getContext('2d');
    deptAttendanceChart = new Chart(deptCtx, {
        type: 'bar',
        data: {
            labels: demoData.attendanceData.attendanceByDepartment.map(item => item.department),
            datasets: [{
                label: 'Attendance Rate',
                data: demoData.attendanceData.attendanceByDepartment.map(item => item.rate),
                backgroundColor: '#ec4899',
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        },
                        padding: 10
                    },
                    grid: {
                        display: true,
                        drawBorder: false
                    }
                },
                y: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        padding: 10
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.raw.toFixed(1)}%`;
                        }
                    },
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    padding: 10,
                    cornerRadius: 4
                }
            }
        }
    });
}

// Initialize office cards
function initializeOfficeCards() {
    // Set up toggle for office details
    document.querySelectorAll('.btn-toggle-office').forEach(button => {
        button.addEventListener('click', function() {
            const officeCard = this.closest('.office-card');
            officeCard.classList.toggle('expanded');
            
            // Initialize office charts if expanded for the first time
            if (officeCard.classList.contains('expanded')) {
                const officeId = officeCard.dataset.officeId;
                if (!officeCharts[officeId]) {
                    initializeOfficeCharts(officeId);
                }
            }
        });
    });
}

// Initialize office-specific charts
function initializeOfficeCharts(officeId) {
    // Find the office data
    const officeData = demoData.attendanceData.officeData.find(office => office.id === officeId);
    if (!officeData) return;
    
    // Initialize attendance gauge
    const gaugeCtx = document.getElementById(`office-