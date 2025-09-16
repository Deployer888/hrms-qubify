/**
 * PaySlip Frontend Validation and Error Handling
 */

class PaySlipValidator {
    constructor() {
        this.initializeValidation();
        this.setupErrorHandling();
    }

    initializeValidation() {
        // Form validation for payslip generation
        this.setupGenerationFormValidation();
        
        // Form validation for payslip editing
        this.setupEditFormValidation();
        
        // Form validation for bulk payment
        this.setupBulkPaymentValidation();
    }

    setupGenerationFormValidation() {
        const generationForm = document.querySelector('form[action*="payslip"]');
        if (!generationForm) return;

        generationForm.addEventListener('submit', (e) => {
            const month = document.getElementById('month')?.value;
            const year = document.getElementById('year')?.value;

            const errors = this.validateGenerationForm(month, year);
            
            if (errors.length > 0) {
                e.preventDefault();
                this.showValidationErrors(errors);
                return false;
            }

            // Show loading state
            this.showLoadingState(e.target);
        });
    }

    setupEditFormValidation() {
        const editForm = document.querySelector('form[action*="updateEmployee"]');
        if (!editForm) return;

        // Real-time validation for numeric fields
        const numericFields = editForm.querySelectorAll('input[type="number"]');
        numericFields.forEach(field => {
            field.addEventListener('input', (e) => {
                this.validateNumericField(e.target);
            });

            field.addEventListener('blur', (e) => {
                this.validateNumericField(e.target);
            });
        });

        // Form submission validation
        editForm.addEventListener('submit', (e) => {
            const errors = this.validateEditForm(editForm);
            
            if (errors.length > 0) {
                e.preventDefault();
                this.showValidationErrors(errors);
                return false;
            }

            // Show loading state
            this.showLoadingState(e.target);
        });
    }

    setupBulkPaymentValidation() {
        const bulkForm = document.querySelector('form[action*="bulkpayment"]');
        if (!bulkForm) return;

        bulkForm.addEventListener('submit', (e) => {
            const selectedEmployees = bulkForm.querySelectorAll('input[name="employee[]"]:checked');
            
            if (selectedEmployees.length === 0) {
                e.preventDefault();
                this.showError('Please select at least one employee for bulk payment.');
                return false;
            }

            // Confirmation dialog
            const totalAmount = this.calculateTotalAmount(selectedEmployees);
            const confirmMessage = `Are you sure you want to process payment for ${selectedEmployees.length} employee(s)? Total amount: ${totalAmount}`;
            
            if (!confirm(confirmMessage)) {
                e.preventDefault();
                return false;
            }

            // Show loading state
            this.showLoadingState(e.target);
        });
    }

    validateGenerationForm(month, year) {
        const errors = [];
        const currentDate = new Date();
        const currentMonth = currentDate.getMonth() + 1;
        const currentYear = currentDate.getFullYear();

        // Month validation
        if (!month || month === '') {
            errors.push('Please select a month.');
        } else {
            const monthNum = parseInt(month);
            if (monthNum < 1 || monthNum > 12) {
                errors.push('Please select a valid month.');
            }
        }

        // Year validation
        if (!year || year === '') {
            errors.push('Please select a year.');
        } else {
            const yearNum = parseInt(year);
            const minYear = currentYear - 5;
            const maxYear = currentYear + 1;
            
            if (yearNum < minYear || yearNum > maxYear) {
                errors.push(`Year must be between ${minYear} and ${maxYear}.`);
            }
        }

        // Future date validation
        if (month && year) {
            const selectedDate = new Date(parseInt(year), parseInt(month) - 1, 1);
            const currentMonthStart = new Date(currentYear, currentMonth - 1, 1);
            
            if (selectedDate > currentMonthStart) {
                errors.push('Cannot generate payslips for future months.');
            }
        }

        return errors;
    }

    validateEditForm(form) {
        const errors = [];
        
        // Basic salary validation
        const basicSalary = parseFloat(form.querySelector('#basic_salary')?.value) || 0;
        if (basicSalary <= 0) {
            errors.push('Basic salary must be greater than 0.');
        }
        if (basicSalary > 999999.99) {
            errors.push('Basic salary cannot exceed 999,999.99.');
        }

        // Net payable validation
        const netPayable = parseFloat(form.querySelector('#net_payble')?.value) || 0;
        if (netPayable > 999999.99) {
            errors.push('Net payable amount cannot exceed 999,999.99.');
        }

        // Validate all numeric fields
        const numericFields = form.querySelectorAll('input[type="number"]');
        numericFields.forEach(field => {
            const value = parseFloat(field.value) || 0;
            const fieldName = field.getAttribute('name');
            
            if (value < 0) {
                errors.push(`${this.getFieldDisplayName(fieldName)} cannot be negative.`);
            }
            if (value > 999999.99) {
                errors.push(`${this.getFieldDisplayName(fieldName)} cannot exceed 999,999.99.`);
            }
        });

        // Logical validation
        const allowance = parseFloat(form.querySelector('#allowance')?.value) || 0;
        const commission = parseFloat(form.querySelector('#commission')?.value) || 0;
        const overtime = parseFloat(form.querySelector('#overtime')?.value) || 0;
        const otherPayment = parseFloat(form.querySelector('#other_payment')?.value) || 0;
        const loan = parseFloat(form.querySelector('#loan')?.value) || 0;
        const deduction = parseFloat(form.querySelector('#saturation_deduction')?.value) || 0;

        const totalEarnings = basicSalary + allowance + commission + overtime + otherPayment;
        const totalDeductions = loan + deduction;
        const calculatedNet = totalEarnings - totalDeductions;

        // Allow 10% tolerance for manual adjustments
        const tolerance = Math.abs(calculatedNet * 0.1);
        const difference = Math.abs(netPayable - calculatedNet);

        if (difference > tolerance && calculatedNet > 0) {
            errors.push(`Net payable amount seems incorrect. Expected approximately ${calculatedNet.toFixed(2)} based on earnings and deductions.`);
        }

        return errors;
    }

    validateNumericField(field) {
        const value = parseFloat(field.value);
        const fieldName = field.getAttribute('name');
        
        // Remove existing error styling
        field.classList.remove('is-invalid');
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }

        // Validate value
        if (isNaN(value) || value < 0) {
            this.showFieldError(field, `${this.getFieldDisplayName(fieldName)} must be a positive number.`);
            return false;
        }

        if (value > 999999.99) {
            this.showFieldError(field, `${this.getFieldDisplayName(fieldName)} cannot exceed 999,999.99.`);
            return false;
        }

        return true;
    }

    showFieldError(field, message) {
        field.classList.add('is-invalid');
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        
        field.parentNode.appendChild(errorDiv);
    }

    getFieldDisplayName(fieldName) {
        const fieldNames = {
            'basic_salary': 'Basic salary',
            'net_payble': 'Net payable amount',
            'allowance': 'Allowance',
            'commission': 'Commission',
            'loan': 'Loan',
            'saturation_deduction': 'Deduction',
            'other_payment': 'Other payment',
            'overtime': 'Overtime'
        };
        
        return fieldNames[fieldName] || fieldName.replace('_', ' ');
    }

    calculateTotalAmount(selectedEmployees) {
        let total = 0;
        selectedEmployees.forEach(checkbox => {
            const row = checkbox.closest('tr');
            const amountCell = row.querySelector('td:last-child');
            if (amountCell) {
                const amount = parseFloat(amountCell.textContent.replace(/[^0-9.-]+/g, '')) || 0;
                total += amount;
            }
        });
        
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD' // You can make this dynamic based on site currency
        }).format(total);
    }

    showValidationErrors(errors) {
        const errorHtml = errors.map(error => `<li>${error}</li>`).join('');
        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Validation Error:</strong>
                <ul class="mb-0 mt-2">${errorHtml}</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        this.showAlert(alertHtml);
    }

    showError(message) {
        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error:</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        this.showAlert(alertHtml);
    }

    showSuccess(message) {
        const alertHtml = `
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success:</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        this.showAlert(alertHtml);
    }

    showAlert(alertHtml) {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        
        // Add new alert at the top of the content
        const contentArea = document.querySelector('.content-wrapper') || document.querySelector('main') || document.body;
        contentArea.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }

    showLoadingState(form) {
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            const originalText = submitButton.textContent;
            submitButton.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Processing...';
            
            // Store original text for restoration
            submitButton.dataset.originalText = originalText;
        }
    }

    setupErrorHandling() {
        // Global AJAX error handler
        if (typeof $ !== 'undefined') {
            $(document).ajaxError((event, xhr, settings, thrownError) => {
                console.error('AJAX Error:', thrownError);
                
                let errorMessage = 'An error occurred while processing your request.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 422) {
                    errorMessage = 'Please check your input data and try again.';
                } else if (xhr.status === 403) {
                    errorMessage = 'You do not have permission to perform this action.';
                } else if (xhr.status === 404) {
                    errorMessage = 'The requested resource was not found.';
                } else if (xhr.status >= 500) {
                    errorMessage = 'A server error occurred. Please try again later.';
                }
                
                this.showError(errorMessage);
            });
        }

        // Handle browser back/forward buttons
        window.addEventListener('popstate', () => {
            // Reset any loading states
            const loadingButtons = document.querySelectorAll('button[disabled][data-original-text]');
            loadingButtons.forEach(button => {
                button.disabled = false;
                button.textContent = button.dataset.originalText;
            });
        });
    }
}

// Initialize validation when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new PaySlipValidator();
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PaySlipValidator;
}