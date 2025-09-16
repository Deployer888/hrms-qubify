// Office Management System - Scripts

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the application
    initApp();
    
    // Add event listeners
    setupEventListeners();
    
    // Load charts if they exist
    initCharts();
    
    // Animate elements on scroll
    animateOnScroll();
    
    // Initialize Maps if they exist
    initMaps();
});

// Function to show the office list view
function showOfficeList() {
    // Update current view in local storage
    localStorage.setItem('currentView', 'office-list');
    localStorage.removeItem('selectedOfficeId');
    localStorage.removeItem('selectedEmployeeId');
    
    // Show office list and hide other views
    document.getElementById('office-list-view').style.display = 'block';
    
    const officeDetailView = document.getElementById('office-detail-view');
    if (officeDetailView) officeDetailView.style.display = 'none';
    
    const employeeDetailView = document.getElementById('employee-detail-view');
    if (employeeDetailView) employeeDetailView.style.display = 'none';
}

// Function to show office detail view
function showOfficeDetail(officeId) {
    // Show loading spinner
    showLoadingSpinner();
    
    // Update current view in local storage
    localStorage.setItem('currentView', 'office-detail');
    localStorage.setItem('selectedOfficeId', officeId);
    localStorage.removeItem('selectedEmployeeId');
    
    // Fetch the office data (in a real app, this would be an API call)
    // For this demo, we'll use the already included HTML
    
    // Hide office list and employee detail views
    document.getElementById('office-list-view').style.display = 'none';
    
    const employeeDetailView = document.getElementById('employee-detail-view');
    if (employeeDetailView) employeeDetailView.style.display = 'none';
    
    // Show office detail view
    const officeDetailView = document.getElementById('office-detail-view');
    if (officeDetailView) {
        officeDetailView.style.display = 'block';
        
        // Activate the overview tab by default
        document.querySelectorAll('.tab-item').forEach(tab => {
            tab.classList.remove('active');
            if (tab.getAttribute('data-tab') === 'overview') {
                tab.classList.add('active');
            }
        });
        
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
            if (content.id === 'overview') {
                content.classList.add('active');
            }
        });
        
        // Update office name based on selected office
        const officeName = document.querySelector(`.office-card[data-office-id="${officeId}"] .office-header h3`).textContent;
        const officeLocation = document.querySelector(`.office-card[data-office-id="${officeId}"] .office-location`).textContent;
        
        document.querySelector('#office-detail-view .detail-header h1').textContent = officeName;
        document.querySelector('#office-detail-view .detail-header p').innerHTML = '<i class="fas fa-map-marker-alt"></i> ' + officeLocation;
        
        // Initialize charts
        initCharts();
        
        // Initialize Google Maps
        initMaps();
    }
    
    // Hide loading spinner
    hideLoadingSpinner();
}

// Function to show employee detail view
function showEmployeeDetail(employeeId) {
    // Show loading spinner
    showLoadingSpinner();
    
    // Update current view in local storage
    localStorage.setItem('currentView', 'employee-detail');
    localStorage.setItem('selectedEmployeeId', employeeId);
    
    // Hide office list and office detail views
    document.getElementById('office-list-view').style.display = 'none';
    
    const officeDetailView = document.getElementById('office-detail-view');
    if (officeDetailView) officeDetailView.style.display = 'none';
    
    // Show employee detail view
    const employeeDetailView = document.getElementById('employee-detail-view');
    if (employeeDetailView) {
        employeeDetailView.style.display = 'block';
        
        // Activate the overview tab by default
        document.querySelectorAll('#employee-detail-view .tab-item').forEach(tab => {
            tab.classList.remove('active');
            if (tab.getAttribute('data-tab') === 'overview') {
                tab.classList.add('active');
            }
        });
        
        document.querySelectorAll('#employee-detail-view .tab-content').forEach(content => {
            content.classList.remove('active');
            if (content.id === 'overview') {
                content.classList.add('active');
            }
        });
        
        // Update employee name based on selected employee
        const employeeRow = document.querySelector(`.employee-name[data-employee-id="${employeeId}"]`).closest('tr');
        const employeeName = document.querySelector(`.employee-name[data-employee-id="${employeeId}"]`).textContent;
        const employeePosition = employeeRow.querySelector('.employee-position').textContent;
        const employeeAvatar = employeeRow.querySelector('.employee-avatar').src;
        
        // Update employee profile header
        document.querySelector('#employee-detail-view .profile-img').src = employeeAvatar;
        document.querySelector('#employee-detail-view .profile-info h1').textContent = employeeName;
        document.querySelector('#employee-detail-view .profile-info .designation').textContent = employeePosition;
        
        // Initialize charts
        initCharts();
    }
    
    // Hide loading spinner
    hideLoadingSpinner();
}

// Function to initialize all charts
function initCharts() {
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js is not loaded');
        return;
    }
    
    // Office attendance chart
    const attendanceChartEl = document.getElementById('attendance-chart');
    if (attendanceChartEl) {
        const ctx = attendanceChartEl.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                datasets: [{
                    label: 'Present',
                    data: [235, 230, 240, 225, 220, 60, 40],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#28a745',
                    pointRadius: 4,
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Absent',
                    data: [10, 15, 5, 20, 25, 20, 15],
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#dc3545',
                    pointRadius: 4,
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'On Leave',
                    data: [5, 5, 5, 5, 5, 0, 0],
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#ffc107',
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
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    }
    
    // Department chart
    const deptChartEl = document.getElementById('department-chart');
    if (deptChartEl) {
        const ctx = deptChartEl.getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Engineering', 'Marketing', 'Sales', 'HR', 'Finance'],
                datasets: [{
                    data: [85, 32, 48, 18, 25],
                    backgroundColor: [
                        '#3a8ef6',
                        '#6259ca',
                        '#1bc5bd',
                        '#f64e60',
                        '#ffbe0b'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'right'
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });
    }
    
    // Daily Attendance Chart
    const dailyAttendanceChartEl = document.getElementById('daily-attendance-chart');
    if (dailyAttendanceChartEl) {
        const ctx = dailyAttendanceChartEl.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Attendance Rate',
                    data: [95, 94, 97, 92, 90, 75, 70],
                    backgroundColor: '#3a8ef6',
                    borderWidth: 0,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            max: 100,
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }]
                }
            }
        });
    }
    
    // Monthly Attendance Chart
    const monthlyChartEl = document.getElementById('monthly-attendance-chart');
    if (monthlyChartEl) {
        const ctx = monthlyChartEl.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Attendance Rate',
                    data: [88, 85, 90, 92, 91, 93, 92, 90, 87, 89, 92, 84],
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
                            beginAtZero: false,
                            min: 80,
                            max: 100,
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }]
                }
            }
        });
    }
    
    // Employee monthly attendance chart
    const empMonthlyAttendanceChartEl = document.getElementById('employee-monthly-attendance-chart');
    if (empMonthlyAttendanceChartEl) {
        const ctx = empMonthlyAttendanceChartEl.getContext('2d');
        new Chart(ctx, {
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
                        stacked: true
                    }],
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    }
    
    // Check-in Time Chart
    const checkinTimeChartEl = document.getElementById('checkin-time-chart');
    if (checkinTimeChartEl) {
        const ctx = checkinTimeChartEl.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7', 'Week 8'],
                datasets: [{
                    label: 'Check-in Time',
                    data: ['8:55', '8:45', '8:50', '9:15', '8:40', '8:45', '8:50', '8:45'].map(time => {
                        const [hours, minutes] = time.split(':').map(Number);
                        return hours + minutes / 60;
                    }),
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
                            min: 8,
                            max: 10
                        }
                    }]
                }
            }
        });
    }
}

// Function to initialize Google Maps
function initMaps() {
    // Check if Google Maps API is loaded
    if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
        console.warn('Google Maps API is not loaded');
        return;
    }
    
    const officeMapEl = document.getElementById('office-map');
    if (officeMapEl) {
        const officeLocation = { lat: 40.7484, lng: -73.9857 }; // Empire State Building coordinates
        const map = new google.maps.Map(officeMapEl, {
            zoom: 15,
            center: officeLocation,
        });
        
        // Add marker for office location
        const marker = new google.maps.Marker({
            position: officeLocation,
            map: map,
            title: "Headquarters",
            animation: google.maps.Animation.DROP
        });
        
        // Add circle to represent geofence
        const cityCircle = new google.maps.Circle({
            strokeColor: "#3a8ef6",
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: "#3a8ef6",
            fillOpacity: 0.1,
            map: map,
            center: officeLocation,
            radius: 200, // in meters
        });
    }
}

// Function to animate elements on scroll
function animateOnScroll() {
    window.addEventListener('scroll', function() {
        document.querySelectorAll('.office-card, .stat-card, .info-card').forEach(function(item) {
            if (isElementInViewport(item) && !item.classList.contains('animated')) {
                item.classList.add('animated', 'fadeInUp');
            }
        });
    });
}

// Helper function to check if element is in viewport
function isElementInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

// Function to show loading spinner
function showLoadingSpinner() {
    let loadingOverlay = document.querySelector('.loading-overlay');
    
    if (!loadingOverlay) {
        loadingOverlay = document.createElement('div');
        loadingOverlay.className = 'loading-overlay';
        loadingOverlay.innerHTML = '<div class="spinner"></div>';
        document.body.appendChild(loadingOverlay);
    }
    
    loadingOverlay.style.display = 'flex';
}

// Function to hide loading spinner
function hideLoadingSpinner() {
    const loadingOverlay = document.querySelector('.loading-overlay');
    if (loadingOverlay) {
        loadingOverlay.style.display = 'none';
    }
}

// Initialize the application
function initApp() {
    // Show initial loading spinner
    showLoadingSpinner();
    
    // Load office data from local storage or use demo data
    const currentView = localStorage.getItem('currentView') || 'office-list';
    const officeId = localStorage.getItem('selectedOfficeId') || null;
    const employeeId = localStorage.getItem('selectedEmployeeId') || null;
    
    // Show the appropriate view
    if (currentView === 'office-list') {
        showOfficeList();
    } else if (currentView === 'office-detail' && officeId) {
        showOfficeDetail(officeId);
    } else if (currentView === 'employee-detail' && employeeId) {
        showEmployeeDetail(employeeId);
    } else {
        // Default to office list
        showOfficeList();
    }
    
    // Add loading effect
    setTimeout(() => {
        document.querySelectorAll('.office-card').forEach((card, index) => {
            setTimeout(() => {
                card.classList.add('animated', 'fadeInUp');
            }, 100 * index);
        });
        
        // Hide loading spinner
        hideLoadingSpinner();
    }, 500);
}

// Set up event listeners
function setupEventListeners() {
    // Office list search
    const searchInput = document.getElementById('search-office');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const value = this.value.toLowerCase();
            document.querySelectorAll('.office-card').forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(value) ? 'block' : 'none';
            });
        });
    }
    
    // Office detail button click
    document.querySelectorAll('.btn-view-details').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const officeId = this.getAttribute('data-office-id');
            showOfficeDetail(officeId);
        });
    });
    
    // Back button click from office detail
    const backToOffices = document.getElementById('back-to-offices');
    if (backToOffices) {
        backToOffices.addEventListener('click', function(e) {
            e.preventDefault();
            showOfficeList();
        });
    }
    
    // Employee detail button click
    document.querySelectorAll('.employee-name').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const employeeId = this.getAttribute('data-employee-id');
            showEmployeeDetail(employeeId);
        });
    });
    
    // Back button click from employee detail
    const backToOffice = document.getElementById('back-to-office');
    if (backToOffice) {
        backToOffice.addEventListener('click', function(e) {
            e.preventDefault();
            const officeId = this.getAttribute('data-office-id');
            showOfficeDetail(officeId);
        });
    }
    
    // Tab navigation
    document.querySelectorAll('.tab-item').forEach(tab => {
        tab.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Deactivate all tabs
            document.querySelectorAll('.tab-item').forEach(t => {
                t.classList.remove('active');
            });
            
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Activate selected tab and content
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Employee search in office detail
    const employeeSearch = document.getElementById('employee-search');
    if (employeeSearch) {
        employeeSearch.addEventListener('keyup', function() {
            const value = this.value.toLowerCase();
            document.querySelectorAll('.employee-table tbody tr').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(value) ? 'table-row' : 'none';
            });
        });
    }
    
    // Department filter in office detail
    const departmentFilter = document.getElementById('department-filter');
    if (departmentFilter) {
        departmentFilter.addEventListener('change', function() {
            const value = this.value.toLowerCase();
            
            if (value === '') {
                document.querySelectorAll('.employee-table tbody tr').forEach(row => {
                    row.style.display = 'table-row';
                });
            } else {
                document.querySelectorAll('.employee-table tbody tr').forEach(row => {
                    const departmentCell = row.querySelector('td:nth-child(2)');
                    if (departmentCell) {
                        const department = departmentCell.textContent.toLowerCase();
                        row.style.display = department === value ? 'table-row' : 'none';
                    }
                });
            }
        });
    }