<?php

/**
 * PaySlip Implementation Validation Script
 * 
 * This script validates that all payslip components are properly implemented
 * and configured correctly.
 */

class PaySlipValidator
{
    private $errors = [];
    private $warnings = [];
    private $passed = [];

    public function validate()
    {
        echo "=== PaySlip Implementation Validation ===\n\n";

        $this->validateModels();
        $this->validateControllers();
        $this->validateViews();
        $this->validateRoutes();
        $this->validateMigrations();
        $this->validateValidation();
        $this->validateErrorHandling();
        $this->validateAssets();

        $this->displayResults();
    }

    private function validateModels()
    {
        echo "Validating Models...\n";

        // Check PaySlip model
        if (class_exists('App\Models\PaySlip')) {
            $this->passed[] = "PaySlip model exists";
            
            $reflection = new ReflectionClass('App\Models\PaySlip');
            
            // Check fillable fields
            if ($reflection->hasProperty('fillable')) {
                $this->passed[] = "PaySlip model has fillable property";
            } else {
                $this->errors[] = "PaySlip model missing fillable property";
            }

            // Check relationships
            if ($reflection->hasMethod('employee')) {
                $this->passed[] = "PaySlip model has employee relationship";
            } else {
                $this->errors[] = "PaySlip model missing employee relationship";
            }

            if ($reflection->hasMethod('creator')) {
                $this->passed[] = "PaySlip model has creator relationship";
            } else {
                $this->warnings[] = "PaySlip model missing creator relationship";
            }

        } else {
            $this->errors[] = "PaySlip model does not exist";
        }

        // Check Employee model updates
        if (class_exists('App\Models\Employee')) {
            $reflection = new ReflectionClass('App\Models\Employee');
            
            if ($reflection->hasMethod('payslips')) {
                $this->passed[] = "Employee model has payslips relationship";
            } else {
                $this->errors[] = "Employee model missing payslips relationship";
            }

            if ($reflection->hasMethod('calculateAttendanceForMonth')) {
                $this->passed[] = "Employee model has attendance calculation method";
            } else {
                $this->errors[] = "Employee model missing attendance calculation method";
            }

        } else {
            $this->errors[] = "Employee model does not exist";
        }
    }

    private function validateControllers()
    {
        echo "Validating Controllers...\n";

        // Check PaySlipController
        if (class_exists('App\Http\Controllers\PaySlipController')) {
            $this->passed[] = "PaySlipController exists";
            
            $reflection = new ReflectionClass('App\Http\Controllers\PaySlipController');
            
            $requiredMethods = [
                'index', 'store', 'search_json', 'paysalary', 
                'bulk_pay_create', 'bulkpayment', 'showemployee', 
                'editemployee', 'updateEmployee', 'destroy'
            ];

            foreach ($requiredMethods as $method) {
                if ($reflection->hasMethod($method)) {
                    $this->passed[] = "PaySlipController has {$method} method";
                } else {
                    $this->errors[] = "PaySlipController missing {$method} method";
                }
            }

        } else {
            $this->errors[] = "PaySlipController does not exist";
        }
    }

    private function validateViews()
    {
        echo "Validating Views...\n";

        $requiredViews = [
            'resources/views/payslip/index.blade.php',
            'resources/views/payslip/create.blade.php',
            'resources/views/payslip/show.blade.php',
            'resources/views/payslip/edit.blade.php',
            'resources/views/payslip/bulkpayment.blade.php'
        ];

        foreach ($requiredViews as $view) {
            if (file_exists($view)) {
                $this->passed[] = "View file exists: " . basename($view);
            } else {
                $this->errors[] = "View file missing: " . basename($view);
            }
        }
    }

    private function validateRoutes()
    {
        echo "Validating Routes...\n";

        // This would require Laravel to be bootstrapped
        // For now, just check if web.php contains payslip routes
        $webRoutes = file_get_contents('routes/web.php');
        
        if (strpos($webRoutes, 'payslip') !== false) {
            $this->passed[] = "Payslip routes appear to be defined";
        } else {
            $this->warnings[] = "Payslip routes may not be defined in web.php";
        }
    }

    private function validateMigrations()
    {
        echo "Validating Migrations...\n";

        $migrationFiles = glob('database/migrations/*_create_pay_slips_table.php');
        
        if (!empty($migrationFiles)) {
            $this->passed[] = "PaySlip migration file exists";
            
            $migrationContent = file_get_contents($migrationFiles[0]);
            
            $requiredColumns = [
                'employee_id', 'net_payble', 'basic_salary', 'salary_month',
                'status', 'allowance', 'commission', 'loan', 'saturation_deduction',
                'other_payment', 'overtime', 'created_by', 'actual_payable_days',
                'total_working_days', 'loss_of_pay_days', 'hra', 'tds',
                'special_allowance', 'total_earnings', 'total_deduction'
            ];

            foreach ($requiredColumns as $column) {
                if (strpos($migrationContent, $column) !== false) {
                    $this->passed[] = "Migration includes {$column} column";
                } else {
                    $this->warnings[] = "Migration may be missing {$column} column";
                }
            }

        } else {
            $this->errors[] = "PaySlip migration file not found";
        }
    }

    private function validateValidation()
    {
        echo "Validating Validation Classes...\n";

        // Check PaySlipRequest
        if (class_exists('App\Http\Requests\PaySlipRequest')) {
            $this->passed[] = "PaySlipRequest validation class exists";
            
            $reflection = new ReflectionClass('App\Http\Requests\PaySlipRequest');
            
            if ($reflection->hasMethod('rules')) {
                $this->passed[] = "PaySlipRequest has rules method";
            } else {
                $this->errors[] = "PaySlipRequest missing rules method";
            }

            if ($reflection->hasMethod('messages')) {
                $this->passed[] = "PaySlipRequest has custom messages";
            } else {
                $this->warnings[] = "PaySlipRequest missing custom messages";
            }

        } else {
            $this->errors[] = "PaySlipRequest validation class does not exist";
        }
    }

    private function validateErrorHandling()
    {
        echo "Validating Error Handling...\n";

        // Check PaySlipErrorHandler service
        if (class_exists('App\Services\PaySlipErrorHandler')) {
            $this->passed[] = "PaySlipErrorHandler service exists";
            
            $reflection = new ReflectionClass('App\Services\PaySlipErrorHandler');
            
            $requiredMethods = [
                'handleGenerationError', 'handleUpdateError', 'handleDataLoadError',
                'handleBulkPaymentError', 'handleDeletionError', 'logSuccess'
            ];

            foreach ($requiredMethods as $method) {
                if ($reflection->hasMethod($method)) {
                    $this->passed[] = "PaySlipErrorHandler has {$method} method";
                } else {
                    $this->errors[] = "PaySlipErrorHandler missing {$method} method";
                }
            }

        } else {
            $this->errors[] = "PaySlipErrorHandler service does not exist";
        }
    }

    private function validateAssets()
    {
        echo "Validating Assets...\n";

        // Check JavaScript validation file
        if (file_exists('public/js/payslip-validation.js')) {
            $this->passed[] = "PaySlip validation JavaScript exists";
        } else {
            $this->warnings[] = "PaySlip validation JavaScript file missing";
        }

        // Check if main view includes necessary CSS/JS
        if (file_exists('resources/views/payslip/index.blade.php')) {
            $indexContent = file_get_contents('resources/views/payslip/index.blade.php');
            
            if (strpos($indexContent, '@push(\'script-page\')') !== false) {
                $this->passed[] = "Index view includes custom scripts";
            } else {
                $this->warnings[] = "Index view may not include custom scripts";
            }

            if (strpos($indexContent, '@push(\'style-page\')') !== false) {
                $this->passed[] = "Index view includes custom styles";
            } else {
                $this->warnings[] = "Index view may not include custom styles";
            }
        }
    }

    private function displayResults()
    {
        echo "\n=== Validation Results ===\n\n";

        echo "✅ PASSED (" . count($this->passed) . "):\n";
        foreach ($this->passed as $pass) {
            echo "  ✓ {$pass}\n";
        }

        if (!empty($this->warnings)) {
            echo "\n⚠️  WARNINGS (" . count($this->warnings) . "):\n";
            foreach ($this->warnings as $warning) {
                echo "  ⚠ {$warning}\n";
            }
        }

        if (!empty($this->errors)) {
            echo "\n❌ ERRORS (" . count($this->errors) . "):\n";
            foreach ($this->errors as $error) {
                echo "  ✗ {$error}\n";
            }
        }

        echo "\n=== Summary ===\n";
        echo "Passed: " . count($this->passed) . "\n";
        echo "Warnings: " . count($this->warnings) . "\n";
        echo "Errors: " . count($this->errors) . "\n";

        if (empty($this->errors)) {
            echo "\n🎉 All critical components are in place!\n";
            if (!empty($this->warnings)) {
                echo "⚠️  Please review warnings for optimal implementation.\n";
            }
        } else {
            echo "\n❌ Critical errors found. Please fix before proceeding.\n";
        }

        echo "\n=== Next Steps ===\n";
        echo "1. Run the automated tests: php artisan test tests/Feature/PaySlipTest.php\n";
        echo "2. Complete the manual testing checklist\n";
        echo "3. Test with real data in a staging environment\n";
        echo "4. Review performance with large datasets\n";
        echo "5. Validate security permissions\n";
    }
}

// Run validation if script is executed directly
if (php_sapi_name() === 'cli') {
    // Simple autoloader for this script
    spl_autoload_register(function ($class) {
        $file = str_replace(['App\\', '\\'], ['app/', '/'], $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    });

    $validator = new PaySlipValidator();
    $validator->validate();
}