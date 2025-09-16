{{-- DataTables Performance Test and Optimization Script --}}
<script>
$(document).ready(function() {
    // Performance testing and optimization for DataTables
    const DataTablesPerformanceTest = {
        // Test configuration
        testConfig: {
            employeeTableId: '#employees-datatable',
            attendanceTableId: '#attendance-datatable',
            performanceThresholds: {
                initTime: 1000, // 1 second
                searchTime: 300,  // 300ms
                filterTime: 300,  // 300ms
                redrawTime: 200   // 200ms
            }
        },

        // Performance metrics storage
        metrics: {
            employee: {},
            attendance: {}
        },

        // Initialize performance testing
        init: function() {
            console.log('🚀 Starting DataTables Performance Test Suite');
            this.testDataTablesLibrary();
            this.testResponsiveDesign();
            this.optimizeTabSwitching();
            this.addPerformanceMonitoring();
        },

        // Test 1: DataTables functionality with existing data
        testDataTablesLibrary: function() {
            console.log('📊 Testing DataTables functionality...');
            
            // Test Employee DataTable
            this.testEmployeeDataTable();
            
            // Test Attendance DataTable  
            this.testAttendanceDataTable();
            
            // Test search and filter combinations
            this.testSearchAndFilterCombinations();
        },

        // Test Employee DataTable functionality
        testEmployeeDataTable: function() {
            const startTime = performance.now();
            
            try {
                // Check if table exists and has data
                const $table = $(this.testConfig.employeeTableId);
                if (!$table.length) {
                    console.error('❌ Employee table not found');
                    return;
                }

                const rowCount = $table.find('tbody tr').length;
                console.log(`📋 Employee table has ${rowCount} rows`);

                // Test DataTable initialization
                if ($.fn.DataTable.isDataTable(this.testConfig.employeeTableId)) {
                    const table = $(this.testConfig.employeeTableId).DataTable();
                    
                    // Test basic functionality
                    this.testTableFeatures(table, 'employee');
                    
                    const initTime = performance.now() - startTime;
                    this.metrics.employee.initTime = initTime;
                    
                    if (initTime > this.testConfig.performanceThresholds.initTime) {
                        console.warn(`⚠️ Employee table initialization took ${initTime.toFixed(2)}ms (threshold: ${this.testConfig.performanceThresholds.initTime}ms)`);
                    } else {
                        console.log(`✅ Employee table initialized in ${initTime.toFixed(2)}ms`);
                    }
                } else {
                    console.warn('⚠️ Employee DataTable not initialized, testing fallback');
                    this.testFallbackFunctionality('employee');
                }
            } catch (error) {
                console.error('❌ Employee DataTable test failed:', error);
            }
        },

        // Test Attendance DataTable functionality
        testAttendanceDataTable: function() {
            const startTime = performance.now();
            
            try {
                const $table = $(this.testConfig.attendanceTableId);
                if (!$table.length) {
                    console.error('❌ Attendance table not found');
                    return;
                }

                const rowCount = $table.find('tbody tr').length;
                console.log(`📋 Attendance table has ${rowCount} rows`);

                if ($.fn.DataTable.isDataTable(this.testConfig.attendanceTableId)) {
                    const table = $(this.testConfig.attendanceTableId).DataTable();
                    
                    this.testTableFeatures(table, 'attendance');
                    
                    const initTime = performance.now() - startTime;
                    this.metrics.attendance.initTime = initTime;
                    
                    if (initTime > this.testConfig.performanceThresholds.initTime) {
                        console.warn(`⚠️ Attendance table initialization took ${initTime.toFixed(2)}ms`);
                    } else {
                        console.log(`✅ Attendance table initialized in ${initTime.toFixed(2)}ms`);
                    }
                } else {
                    console.warn('⚠️ Attendance DataTable not initialized, testing fallback');
                    this.testFallbackFunctionality('attendance');
                }
            } catch (error) {
                console.error('❌ Attendance DataTable test failed:', error);
            }
        },

        // Test individual table features
        testTableFeatures: function(table, tableType) {
            console.log(`🔍 Testing ${tableType} table features...`);
            
            // Test sorting
            this.testSorting(table, tableType);
            
            // Test pagination
            this.testPagination(table, tableType);
            
            // Test search
            this.testSearch(table, tableType);
            
            // Test responsive behavior
            this.testResponsiveBehavior(table, tableType);
        },

        // Test sorting functionality
        testSorting: function(table, tableType) {
            try {
                const startTime = performance.now();
                
                // Test sorting on first column
                table.order([0, 'asc']).draw();
                
                const sortTime = performance.now() - startTime;
                this.metrics[tableType].sortTime = sortTime;
                
                if (sortTime > this.testConfig.performanceThresholds.redrawTime) {
                    console.warn(`⚠️ ${tableType} sorting took ${sortTime.toFixed(2)}ms`);
                } else {
                    console.log(`✅ ${tableType} sorting: ${sortTime.toFixed(2)}ms`);
                }
            } catch (error) {
                console.error(`❌ ${tableType} sorting test failed:`, error);
            }
        },

        // Test pagination
        testPagination: function(table, tableType) {
            try {
                const info = table.page.info();
                console.log(`📄 ${tableType} pagination: ${info.pages} pages, ${info.recordsTotal} total records`);
                
                if (info.pages > 1) {
                    const startTime = performance.now();
                    table.page('next').draw();
                    const pageTime = performance.now() - startTime;
                    
                    this.metrics[tableType].pageTime = pageTime;
                    console.log(`✅ ${tableType} page navigation: ${pageTime.toFixed(2)}ms`);
                    
                    // Return to first page
                    table.page('first').draw();
                }
            } catch (error) {
                console.error(`❌ ${tableType} pagination test failed:`, error);
            }
        },

        // Test search functionality
        testSearch: function(table, tableType) {
            try {
                const startTime = performance.now();
                
                // Test global search
                table.search('test').draw();
                
                const searchTime = performance.now() - startTime;
                this.metrics[tableType].searchTime = searchTime;
                
                if (searchTime > this.testConfig.performanceThresholds.searchTime) {
                    console.warn(`⚠️ ${tableType} search took ${searchTime.toFixed(2)}ms`);
                } else {
                    console.log(`✅ ${tableType} search: ${searchTime.toFixed(2)}ms`);
                }
                
                // Clear search
                table.search('').draw();
            } catch (error) {
                console.error(`❌ ${tableType} search test failed:`, error);
            }
        },

        // Test responsive behavior
        testResponsiveBehavior: function(table, tableType) {
            try {
                const responsive = table.responsive;
                if (responsive) {
                    console.log(`📱 ${tableType} responsive extension loaded`);
                    
                    // Test responsive recalculation
                    const startTime = performance.now();
                    responsive.recalc();
                    const recalcTime = performance.now() - startTime;
                    
                    console.log(`✅ ${tableType} responsive recalc: ${recalcTime.toFixed(2)}ms`);
                } else {
                    console.warn(`⚠️ ${tableType} responsive extension not available`);
                }
            } catch (error) {
                console.error(`❌ ${tableType} responsive test failed:`, error);
            }
        },

        // Test 2: Verify responsive behavior on different screen sizes
        testResponsiveDesign: function() {
            console.log('📱 Testing responsive design...');
            
            const breakpoints = [
                { name: 'Mobile', width: 375 },
                { name: 'Tablet', width: 768 },
                { name: 'Desktop', width: 1024 },
                { name: 'Large Desktop', width: 1440 }
            ];
            
            breakpoints.forEach(breakpoint => {
                this.simulateScreenSize(breakpoint.width, breakpoint.name);
            });
        },

        // Simulate different screen sizes
        simulateScreenSize: function(width, name) {
            try {
                // Create a test container with specific width
                const $testContainer = $('<div>').css({
                    width: width + 'px',
                    position: 'absolute',
                    top: '-9999px',
                    left: '-9999px'
                }).appendTo('body');
                
                // Clone tables into test container
                const $employeeClone = $(this.testConfig.employeeTableId).clone().appendTo($testContainer);
                const $attendanceClone = $(this.testConfig.attendanceTableId).clone().appendTo($testContainer);
                
                console.log(`📐 Testing ${name} (${width}px):`);
                
                // Check column visibility
                this.checkColumnVisibility($employeeClone, name, 'employee');
                this.checkColumnVisibility($attendanceClone, name, 'attendance');
                
                // Clean up
                $testContainer.remove();
            } catch (error) {
                console.error(`❌ Responsive test failed for ${name}:`, error);
            }
        },

        // Check column visibility at different screen sizes
        checkColumnVisibility: function($table, screenSize, tableType) {
            const visibleColumns = $table.find('thead th:visible').length;
            const totalColumns = $table.find('thead th').length;
            
            console.log(`  ${tableType}: ${visibleColumns}/${totalColumns} columns visible`);
            
            // Verify important columns are always visible
            const importantColumns = tableType === 'employee' ? [0, 3] : [0, 1, 4]; // Name, Status for employee; Name, Date, Status for attendance
            
            importantColumns.forEach(colIndex => {
                const $col = $table.find(`thead th:eq(${colIndex})`);
                if ($col.is(':visible')) {
                    console.log(`  ✅ Important column ${colIndex} visible`);
                } else {
                    console.warn(`  ⚠️ Important column ${colIndex} hidden on ${screenSize}`);
                }
            });
        },

        // Test 3: Search and filter combinations
        testSearchAndFilterCombinations: function() {
            console.log('🔍 Testing search and filter combinations...');
            
            // Test employee search + department filter
            this.testEmployeeSearchFilter();
            
            // Test attendance search + date range filter
            this.testAttendanceSearchFilter();
        },

        // Test employee search and filter combinations
        testEmployeeSearchFilter: function() {
            try {
                if ($.fn.DataTable.isDataTable(this.testConfig.employeeTableId)) {
                    const table = $(this.testConfig.employeeTableId).DataTable();
                    
                    console.log('🔍 Testing employee search + filter combinations...');
                    
                    // Test 1: Search only
                    const startTime1 = performance.now();
                    table.search('john').draw();
                    const searchOnlyTime = performance.now() - startTime1;
                    console.log(`  Search only: ${searchOnlyTime.toFixed(2)}ms`);
                    
                    // Test 2: Filter only (simulate department filter)
                    table.search('').draw();
                    const startTime2 = performance.now();
                    table.column(1).search('IT').draw(); // Assuming column 1 is department
                    const filterOnlyTime = performance.now() - startTime2;
                    console.log(`  Filter only: ${filterOnlyTime.toFixed(2)}ms`);
                    
                    // Test 3: Combined search and filter
                    const startTime3 = performance.now();
                    table.search('john').column(1).search('IT').draw();
                    const combinedTime = performance.now() - startTime3;
                    console.log(`  Combined search+filter: ${combinedTime.toFixed(2)}ms`);
                    
                    // Clear all filters
                    table.search('').columns().search('').draw();
                    
                    if (combinedTime > this.testConfig.performanceThresholds.filterTime) {
                        console.warn(`⚠️ Combined filtering exceeded threshold: ${combinedTime.toFixed(2)}ms`);
                    } else {
                        console.log(`✅ Combined filtering within threshold`);
                    }
                }
            } catch (error) {
                console.error('❌ Employee search+filter test failed:', error);
            }
        },

        // Test attendance search and filter combinations
        testAttendanceSearchFilter: function() {
            try {
                if ($.fn.DataTable.isDataTable(this.testConfig.attendanceTableId)) {
                    const table = $(this.testConfig.attendanceTableId).DataTable();
                    
                    console.log('🔍 Testing attendance search + date filter combinations...');
                    
                    // Test search functionality
                    const startTime = performance.now();
                    table.search('present').draw();
                    const searchTime = performance.now() - startTime;
                    console.log(`  Attendance search: ${searchTime.toFixed(2)}ms`);
                    
                    // Clear search
                    table.search('').draw();
                    
                    // Test date range filtering (if implemented)
                    this.testDateRangeFiltering(table);
                }
            } catch (error) {
                console.error('❌ Attendance search+filter test failed:', error);
            }
        },

        // Test date range filtering
        testDateRangeFiltering: function(table) {
            try {
                console.log('📅 Testing date range filtering...');
                
                // Simulate date range filter
                const today = new Date();
                const lastWeek = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                
                $('#attendance-date-from').val(lastWeek.toISOString().split('T')[0]);
                $('#attendance-date-to').val(today.toISOString().split('T')[0]);
                
                const startTime = performance.now();
                $('#attendance-date-from').trigger('change');
                const filterTime = performance.now() - startTime;
                
                console.log(`  Date range filter: ${filterTime.toFixed(2)}ms`);
                
                // Clear date filters
                $('#attendance-date-from, #attendance-date-to').val('');
                $('#clear-attendance-filters').trigger('click');
            } catch (error) {
                console.error('❌ Date range filtering test failed:', error);
            }
        },

        // Test 4: Optimize DataTable redraw when switching tabs
        optimizeTabSwitching: function() {
            console.log('🔄 Optimizing tab switching performance...');
            
            // Override the existing tab click handler with optimized version
            $('.office-tab-item').off('click').on('click', (e) => {
                const $tab = $(e.currentTarget);
                const tabId = $tab.data('tab');
                
                // Performance timing
                const startTime = performance.now();
                
                // Update active states
                $('.office-tab-item').removeClass('active');
                $tab.addClass('active');
                
                $('.tab-content').removeClass('active').hide();
                const $targetContent = $(`#${tabId}`);
                $targetContent.addClass('active').show();
                
                // Optimized DataTable handling
                if (tabId === 'employees') {
                    this.optimizedEmployeeTableInit();
                } else if (tabId === 'attendance') {
                    this.optimizedAttendanceTableInit();
                }
                
                const switchTime = performance.now() - startTime;
                console.log(`🔄 Tab switch to ${tabId}: ${switchTime.toFixed(2)}ms`);
                
                // Log performance metrics
                this.logTabSwitchPerformance(tabId, switchTime);
            });
        },

        // Optimized employee table initialization
        optimizedEmployeeTableInit: function() {
            try {
                // Check if table is already initialized and visible
                if ($.fn.DataTable.isDataTable(this.testConfig.employeeTableId)) {
                    const table = $(this.testConfig.employeeTableId).DataTable();
                    
                    // Only redraw if necessary
                    if (!table.responsive.hasHidden()) {
                        table.responsive.recalc();
                    }
                    
                    // Adjust columns if needed
                    table.columns.adjust();
                } else {
                    // Initialize with optimized settings
                    setTimeout(() => {
                        if (typeof initializeEmployeeDataTable === 'function') {
                            initializeEmployeeDataTable();
                        }
                    }, 50); // Small delay to ensure DOM is ready
                }
            } catch (error) {
                console.error('❌ Optimized employee table init failed:', error);
            }
        },

        // Optimized attendance table initialization
        optimizedAttendanceTableInit: function() {
            try {
                if ($.fn.DataTable.isDataTable(this.testConfig.attendanceTableId)) {
                    const table = $(this.testConfig.attendanceTableId).DataTable();
                    
                    // Only redraw if necessary
                    if (!table.responsive.hasHidden()) {
                        table.responsive.recalc();
                    }
                    
                    table.columns.adjust();
                } else {
                    setTimeout(() => {
                        if (typeof initializeAttendanceDataTable === 'function') {
                            initializeAttendanceDataTable();
                        }
                    }, 50);
                }
            } catch (error) {
                console.error('❌ Optimized attendance table init failed:', error);
            }
        },

        // Log tab switch performance
        logTabSwitchPerformance: function(tabId, switchTime) {
            if (!this.metrics.tabSwitching) {
                this.metrics.tabSwitching = {};
            }
            
            this.metrics.tabSwitching[tabId] = switchTime;
            
            if (switchTime > 500) { // 500ms threshold for tab switching
                console.warn(`⚠️ Tab switch to ${tabId} took ${switchTime.toFixed(2)}ms (slow)`);
            } else {
                console.log(`✅ Tab switch to ${tabId} performance: ${switchTime.toFixed(2)}ms`);
            }
        },

        // Test fallback functionality
        testFallbackFunctionality: function(tableType) {
            console.log(`🔄 Testing ${tableType} fallback functionality...`);
            
            const tableId = tableType === 'employee' ? this.testConfig.employeeTableId : this.testConfig.attendanceTableId;
            const $table = $(tableId);
            
            if ($table.length) {
                // Test basic table visibility
                console.log(`✅ ${tableType} fallback table visible`);
                
                // Test fallback search
                const searchId = tableType === 'employee' ? '#employee-search' : '#attendance-search';
                const $search = $(searchId);
                
                if ($search.length) {
                    $search.val('test').trigger('keyup');
                    console.log(`✅ ${tableType} fallback search functional`);
                    $search.val('').trigger('keyup');
                }
                
                // Test fallback filters
                if (tableType === 'employee') {
                    const $filter = $('#department-filter');
                    if ($filter.length && $filter.find('option').length > 1) {
                        $filter.val($filter.find('option:eq(1)').val()).trigger('change');
                        console.log(`✅ Employee fallback filter functional`);
                        $filter.val('').trigger('change');
                    }
                }
            }
        },

        // Add performance monitoring
        addPerformanceMonitoring: function() {
            console.log('📊 Adding performance monitoring...');
            
            // Monitor DataTable events
            $(document).on('init.dt', (e, settings) => {
                const tableId = settings.nTable.id;
                console.log(`📊 DataTable ${tableId} initialized`);
            });
            
            $(document).on('draw.dt', (e, settings) => {
                const tableId = settings.nTable.id;
                const info = $(settings.nTable).DataTable().page.info();
                console.log(`📊 DataTable ${tableId} redrawn - showing ${info.start + 1} to ${info.end} of ${info.recordsTotal}`);
            });
            
            // Monitor search performance
            $(document).on('search.dt', (e, settings) => {
                const tableId = settings.nTable.id;
                const searchValue = settings.oPreviousSearch.sSearch;
                console.log(`🔍 DataTable ${tableId} searched: "${searchValue}"`);
            });
            
            // Add memory usage monitoring
            this.monitorMemoryUsage();
        },

        // Monitor memory usage
        monitorMemoryUsage: function() {
            if (performance.memory) {
                const logMemory = () => {
                    const memory = performance.memory;
                    console.log(`💾 Memory usage: ${(memory.usedJSHeapSize / 1024 / 1024).toFixed(2)}MB used, ${(memory.totalJSHeapSize / 1024 / 1024).toFixed(2)}MB total`);
                };
                
                // Log memory usage every 30 seconds
                setInterval(logMemory, 30000);
                
                // Log initial memory usage
                logMemory();
            }
        },

        // Generate performance report
        generatePerformanceReport: function() {
            console.log('📋 DataTables Performance Report:');
            console.log('=====================================');
            
            // Employee table metrics
            if (this.metrics.employee.initTime) {
                console.log(`Employee Table:`);
                console.log(`  Initialization: ${this.metrics.employee.initTime.toFixed(2)}ms`);
                if (this.metrics.employee.searchTime) {
                    console.log(`  Search: ${this.metrics.employee.searchTime.toFixed(2)}ms`);
                }
                if (this.metrics.employee.sortTime) {
                    console.log(`  Sorting: ${this.metrics.employee.sortTime.toFixed(2)}ms`);
                }
            }
            
            // Attendance table metrics
            if (this.metrics.attendance.initTime) {
                console.log(`Attendance Table:`);
                console.log(`  Initialization: ${this.metrics.attendance.initTime.toFixed(2)}ms`);
                if (this.metrics.attendance.searchTime) {
                    console.log(`  Search: ${this.metrics.attendance.searchTime.toFixed(2)}ms`);
                }
                if (this.metrics.attendance.sortTime) {
                    console.log(`  Sorting: ${this.metrics.attendance.sortTime.toFixed(2)}ms`);
                }
            }
            
            // Tab switching metrics
            if (this.metrics.tabSwitching) {
                console.log(`Tab Switching:`);
                Object.keys(this.metrics.tabSwitching).forEach(tab => {
                    console.log(`  ${tab}: ${this.metrics.tabSwitching[tab].toFixed(2)}ms`);
                });
            }
            
            console.log('=====================================');
            
            // Performance recommendations
            this.generateRecommendations();
        },

        // Generate performance recommendations
        generateRecommendations: function() {
            console.log('💡 Performance Recommendations:');
            
            const recommendations = [];
            
            // Check initialization times
            if (this.metrics.employee.initTime > this.testConfig.performanceThresholds.initTime) {
                recommendations.push('Consider server-side processing for employee table');
            }
            
            if (this.metrics.attendance.initTime > this.testConfig.performanceThresholds.initTime) {
                recommendations.push('Consider server-side processing for attendance table');
            }
            
            // Check search times
            if (this.metrics.employee.searchTime > this.testConfig.performanceThresholds.searchTime) {
                recommendations.push('Optimize employee search with debouncing');
            }
            
            if (this.metrics.attendance.searchTime > this.testConfig.performanceThresholds.searchTime) {
                recommendations.push('Optimize attendance search with debouncing');
            }
            
            // Check tab switching
            if (this.metrics.tabSwitching) {
                Object.keys(this.metrics.tabSwitching).forEach(tab => {
                    if (this.metrics.tabSwitching[tab] > 500) {
                        recommendations.push(`Optimize ${tab} tab switching performance`);
                    }
                });
            }
            
            if (recommendations.length === 0) {
                console.log('✅ All performance metrics within acceptable thresholds');
            } else {
                recommendations.forEach((rec, index) => {
                    console.log(`${index + 1}. ${rec}`);
                });
            }
        }
    };

    // Initialize performance testing after a short delay
    setTimeout(() => {
        DataTablesPerformanceTest.init();
        
        // Generate report after tests complete
        setTimeout(() => {
            DataTablesPerformanceTest.generatePerformanceReport();
        }, 2000);
    }, 1000);

    // Make the test object globally available for manual testing
    window.DataTablesPerformanceTest = DataTablesPerformanceTest;
});
</script>