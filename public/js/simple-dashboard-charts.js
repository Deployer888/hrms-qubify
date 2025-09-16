/**
 * Simple Dashboard Charts - Direct Implementation
 */

console.log('🚀 Loading Simple Dashboard Charts...');

// Wait for everything to load
$(document).ready(function() {
    console.log('📋 DOM Ready - Starting Chart Initialization...');
    
    // Wait a bit for Chart.js to load
    setTimeout(function() {
        if (typeof Chart === 'undefined') {
            console.error('❌ Chart.js not loaded!');
            return;
        }
        
        console.log('✅ Chart.js loaded, initializing charts...');
        initializeAllCharts();
    }, 1000);
});

function initializeAllCharts() {
    // Get data from the dashboard
    const dashboardData = document.getElementById('dashboard-data');
    if (!dashboardData) {
        console.error('❌ Dashboard data container not found');
        return;
    }
    
    // Extract data
    const present = parseInt(dashboardData.dataset.presentCount) || 15;
    const absent = parseInt(dashboardData.dataset.absentCount) || 3;
    const late = parseInt(dashboardData.dataset.lateCount) || 2;
    const leave = parseInt(dashboardData.dataset.leaveCount) || 1;
    const attendanceRate = parseInt(dashboardData.dataset.overallPercentage) || 85;
    
    console.log('📊 Chart Data:', { present, absent, late, leave, attendanceRate });
    
    // Initialize each chart
    initPieChart(present, absent, late, leave);
    initLineChart();
    initBarChart(present, absent, late, leave);
    initGauge(attendanceRate);
}

function initPieChart(present, absent, late, leave) {
    const canvas = document.getElementById('attendance-breakdown-chart');
    if (!canvas) {
        console.warn('❌ Pie chart canvas not found');
        return;
    }
    
    console.log('🥧 Creating pie chart...');
    const ctx = canvas.getContext('2d');
    
    try {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Absent', 'Late', 'On Leave'],
                datasets: [{
                    data: [present, absent, late, leave],
                    backgroundColor: [
                        '#4facfe', // Present - Blue
                        '#fa709a', // Absent - Pink
                        '#fcb69f', // Late - Orange
                        '#a8edea'  // Leave - Teal
                    ],
                    borderWidth: 0,
                    cutout: '60%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: { size: 12, weight: '500' }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((context.parsed / total) * 100) : 0;
                                return `${context.label}: ${context.parsed} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
        console.log('✅ Pie chart created successfully');
    } catch (error) {
        console.error('❌ Error creating pie chart:', error);
    }
}

function initLineChart() {
    const canvas = document.getElementById('weekly-trend-chart');
    if (!canvas) {
        console.warn('❌ Line chart canvas not found');
        return;
    }
    
    console.log('📈 Creating line chart...');
    const ctx = canvas.getContext('2d');
    
    // Sample data for last 6 days (excluding Saturday)
    const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sun'];
    const attendanceData = [88, 92, 85, 90, 94, 70]; // Sample data
    
    try {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: days,
                datasets: [{
                    label: 'Attendance Rate',
                    data: attendanceData,
                    borderColor: '#4facfe',
                    backgroundColor: 'rgba(79, 172, 254, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#4facfe',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Attendance: ${context.parsed.y}%`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 12 } }
                    },
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            },
                            font: { size: 12 }
                        }
                    }
                }
            }
        });
        console.log('✅ Line chart created successfully');
    } catch (error) {
        console.error('❌ Error creating line chart:', error);
    }
}

function initBarChart(present, absent, late, leave) {
    const canvas = document.getElementById('employee-status-chart');
    if (!canvas) {
        console.warn('❌ Bar chart canvas not found');
        return;
    }
    
    console.log('📊 Creating bar chart...');
    const ctx = canvas.getContext('2d');
    
    try {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Present', 'Absent', 'Late', 'On Leave'],
                datasets: [{
                    data: [present, absent, late, leave],
                    backgroundColor: [
                        '#4facfe', // Present - Blue
                        '#fa709a', // Absent - Pink
                        '#fcb69f', // Late - Orange
                        '#a8edea'  // Leave - Teal
                    ],
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y', // Horizontal bars
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.parsed.x} employees`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { size: 12 } }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { size: 12, weight: '500' } }
                    }
                }
            }
        });
        console.log('✅ Bar chart created successfully');
    } catch (error) {
        console.error('❌ Error creating bar chart:', error);
    }
}

function initGauge(percentage) {
    console.log('🎯 Initializing gauge with', percentage + '%');
    
    const needle = document.getElementById('gauge-needle');
    const progressArc = document.getElementById('gauge-progress-arc');
    const percentageDisplay = document.getElementById('gauge-percentage-display');
    
    if (!needle || !progressArc || !percentageDisplay) {
        console.warn('❌ Gauge elements not found');
        return;
    }
    
    try {
        // Calculate rotation angle (-90 to 90 degrees for 0% to 100%)
        const angle = -90 + (percentage * 1.8);
        
        // Update needle rotation
        needle.setAttribute('transform', `rotate(${angle} 160 170)`);
        
        // Update progress arc
        const circumference = 346;
        const progress = (percentage / 100) * circumference;
        progressArc.setAttribute('stroke-dasharray', `${progress} ${circumference}`);
        
        // Update percentage display
        percentageDisplay.textContent = `${percentage}%`;
        
        // Update color based on percentage
        let color = '#ef4444'; // Red for low
        if (percentage >= 75) color = '#10b981'; // Green for high
        else if (percentage >= 50) color = '#f59e0b'; // Yellow for medium
        
        progressArc.setAttribute('stroke', color);
        
        console.log('✅ Gauge initialized successfully');
    } catch (error) {
        console.error('❌ Error initializing gauge:', error);
    }
}

// Test function
function testCharts() {
    console.log('🧪 Testing chart functionality...');
    
    const canvases = [
        'attendance-breakdown-chart',
        'weekly-trend-chart',
        'employee-status-chart'
    ];
    
    canvases.forEach(canvasId => {
        const canvas = document.getElementById(canvasId);
        if (canvas) {
            console.log(`✅ ${canvasId} canvas found`);
        } else {
            console.warn(`❌ ${canvasId} canvas NOT found`);
        }
    });
    
    // Test gauge elements
    const gaugeElements = ['gauge-needle', 'gauge-progress-arc', 'gauge-percentage-display'];
    gaugeElements.forEach(elementId => {
        const element = document.getElementById(elementId);
        if (element) {
            console.log(`✅ ${elementId} element found`);
        } else {
            console.warn(`❌ ${elementId} element NOT found`);
        }
    });
    
    // Test data container
    const dashboardData = document.getElementById('dashboard-data');
    if (dashboardData) {
        console.log('✅ Dashboard data container found');
        console.log('📊 Available data:', dashboardData.dataset);
    } else {
        console.warn('❌ Dashboard data container NOT found');
    }
}

// Run test after a delay
setTimeout(testCharts, 2000);

console.log('📝 Simple Dashboard Charts script loaded');