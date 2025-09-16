/**
 * Attendance Timepicker Test Script
 * This script validates the TimepickerManager functionality
 */

var AttendanceTest = {
    tests: [],
    results: {
        passed: 0,
        failed: 0,
        total: 0
    },
    
    // Test TimepickerManager initialization
    testTimepickerInit: function() {
        var testName = 'TimepickerManager Initialization';
        try {
            if (typeof TimepickerManager === 'undefined') {
                throw new Error('TimepickerManager is not defined');
            }
            
            if (typeof TimepickerManager.init !== 'function') {
                throw new Error('TimepickerManager.init is not a function');
            }
            
            if (typeof TimepickerManager.destroy !== 'function') {
                throw new Error('TimepickerManager.destroy is not a function');
            }
            
            if (typeof TimepickerManager.cleanup !== 'function') {
                throw new Error('TimepickerManager.cleanup is not a function');
            }
            
            this.pass(testName);
        } catch (e) {
            this.fail(testName, e.message);
        }
    },
    
    // Test time validation
    testTimeValidation: function() {
        var testName = 'Time Validation';
        try {
            if (typeof TimepickerManager.validateTime !== 'function') {
                throw new Error('TimepickerManager.validateTime is not a function');
            }
            
            // Test valid times
            var validTimes = ['00:00', '12:30', '23:59', '09:15'];
            validTimes.forEach(function(time) {
                if (!TimepickerManager.validateTime(time)) {
                    throw new Error('Valid time ' + time + ' failed validation');
                }
            });
            
            // Test invalid times
            var invalidTimes = ['24:00', '12:60', 'abc', '25:30', '12:'];
            invalidTimes.forEach(function(time) {
                if (TimepickerManager.validateTime(time)) {
                    throw new Error('Invalid time ' + time + ' passed validation');
                }
            });
            
            this.pass(testName);
        } catch (e) {
            this.fail(testName, e.message);
        }
    },
    
    // Test time formatting
    testTimeFormatting: function() {
        var testName = 'Time Formatting';
        try {
            if (typeof TimepickerManager.formatTime !== 'function') {
                throw new Error('TimepickerManager.formatTime is not a function');
            }
            
            var testCases = [
                { input: '9:5', expected: '09:05' },
                { input: '12:30', expected: '12:30' },
                { input: '0:0', expected: '00:00' },
                { input: '', expected: '' },
                { input: 'invalid', expected: '' }
            ];
            
            testCases.forEach(function(testCase) {
                var result = TimepickerManager.formatTime(testCase.input);
                if (result !== testCase.expected) {
                    throw new Error('Format test failed: ' + testCase.input + ' -> ' + result + ' (expected: ' + testCase.expected + ')');
                }
            });
            
            this.pass(testName);
        } catch (e) {
            this.fail(testName, e.message);
        }
    },
    
    // Test FormValidator
    testFormValidator: function() {
        var testName = 'Form Validator';
        try {
            if (typeof FormValidator === 'undefined') {
                throw new Error('FormValidator is not defined');
            }
            
            if (typeof FormValidator.validateTimeField !== 'function') {
                throw new Error('FormValidator.validateTimeField is not a function');
            }
            
            if (typeof FormValidator.validateTimeRange !== 'function') {
                throw new Error('FormValidator.validateTimeRange is not a function');
            }
            
            this.pass(testName);
        } catch (e) {
            this.fail(testName, e.message);
        }
    },
    
    // Test ModalManager
    testModalManager: function() {
        var testName = 'Modal Manager';
        try {
            if (typeof ModalManager === 'undefined') {
                throw new Error('ModalManager is not defined');
            }
            
            if (typeof ModalManager.onModalShow !== 'function') {
                throw new Error('ModalManager.onModalShow is not a function');
            }
            
            if (typeof ModalManager.onModalHide !== 'function') {
                throw new Error('ModalManager.onModalHide is not a function');
            }
            
            this.pass(testName);
        } catch (e) {
            this.fail(testName, e.message);
        }
    },
    
    // Test jQuery and Bootstrap dependencies
    testDependencies: function() {
        var testName = 'Dependencies Check';
        try {
            if (typeof jQuery === 'undefined') {
                throw new Error('jQuery is not loaded');
            }
            
            if (typeof jQuery.fn.timepicker === 'undefined') {
                throw new Error('Bootstrap Timepicker plugin is not loaded');
            }
            
            if (typeof bootstrap === 'undefined') {
                throw new Error('Bootstrap 5 is not loaded');
            }
            
            this.pass(testName);
        } catch (e) {
            this.fail(testName, e.message);
        }
    },
    
    // Helper methods
    pass: function(testName) {
        this.results.passed++;
        this.results.total++;
        console.log('✅ PASS: ' + testName);
    },
    
    fail: function(testName, error) {
        this.results.failed++;
        this.results.total++;
        console.error('❌ FAIL: ' + testName + ' - ' + error);
    },
    
    // Run all tests
    runTests: function() {
        console.log('🧪 Running Attendance Timepicker Tests...');
        console.log('==========================================');
        
        this.results = { passed: 0, failed: 0, total: 0 };
        
        this.testDependencies();
        this.testTimepickerInit();
        this.testTimeValidation();
        this.testTimeFormatting();
        this.testFormValidator();
        this.testModalManager();
        
        console.log('==========================================');
        console.log('📊 Test Results:');
        console.log('   Total: ' + this.results.total);
        console.log('   Passed: ' + this.results.passed);
        console.log('   Failed: ' + this.results.failed);
        console.log('   Success Rate: ' + Math.round((this.results.passed / this.results.total) * 100) + '%');
        
        if (this.results.failed === 0) {
            console.log('🎉 All tests passed!');
        } else {
            console.log('⚠️  Some tests failed. Please check the implementation.');
        }
        
        return this.results;
    }
};

// Auto-run tests when script is loaded (only in development)
if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    $(document).ready(function() {
        setTimeout(function() {
            AttendanceTest.runTests();
        }, 2000); // Wait 2 seconds for all scripts to load
    });
}