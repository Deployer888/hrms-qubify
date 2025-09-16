# Leave Calculation System Documentation

## Overview

This document explains the paid leave calculation system implemented in the HRMS application. The system automatically accrues 1.5 paid leave days per month for eligible employees, with a 3-month cliff period and proper handling of employees who joined before April 2024.

## Business Rules

### Core Accrual Rules

1. **Monthly Accrual**: Employees receive 1.5 paid leave days per eligible month
2. **Cliff Period**: 3-month waiting period from adjusted date of joining
3. **Minimum Date**: Employees who joined before April 2024 have their accrual calculated from April 2024
4. **Eligibility**: Employee must be active on the last day of the month to receive accrual

### Calculation Formula

```
Adjusted DOJ = max(employee.company_doj, '2024-04-01')
Accrual Start Month = Adjusted DOJ + 3 months
Expected Balance = Eligible Months Count × 1.5
Current Balance = Total Accrued - Total Taken (Approved)
Available Balance = Current Balance - Pending Applications
```

## System Architecture

### Core Components

1. **AccrualCalculator Service** (`app/Services/AccrualCalculator.php`)
   - Pure calculation logic for leave accruals
   - Handles DOJ adjustments and cliff period calculations
   - Determines eligible months and expected balances

2. **MonthlyAccrualService** (`app/Services/MonthlyAccrualService.php`)
   - Processes monthly cron accruals
   - Handles batch processing of all eligible employees
   - Implements idempotency checks

3. **RealTimeBalanceService** (`app/Services/RealTimeBalanceService.php`)
   - Calculates current balances from ledger entries
   - Provides detailed balance breakdowns
   - Includes caching for performance

4. **LeaveAccrualLedger Model** (`app/Models/LeaveAccrualLedger.php`)
   - Audit trail for all accrual transactions
   - Supports cron, backfill, and manual entries
   - Prevents duplicate entries with unique constraints

5. **Employee Model Enhancements** (`app/Models/Employee.php`)
   - Added accrual-related methods and attributes
   - Real-time balance calculation integration
   - Validation and discrepancy detection methods

### Database Schema

#### LeaveAccrualLedger Table
```sql
CREATE TABLE leave_accrual_ledger (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id BIGINT UNSIGNED NOT NULL,
    year_month VARCHAR(7) NOT NULL, -- Format: YYYY-MM
    amount DECIMAL(6,2) NOT NULL,
    source ENUM('cron', 'backfill', 'manual') NOT NULL,
    note TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cron_accrual (employee_id, year_month, source)
);
```

## Console Commands

### Monthly Accrual Processing
```bash
# Process current month accruals
php artisan leaves:monthly-accrual

# Process specific month
php artisan leaves:monthly-accrual --month=2025-09

# Dry run mode
php artisan leaves:monthly-accrual --dry-run
```

### Balance Verification
```bash
# Verify all employee balances
php artisan leaves:verify-balances

# Verify specific employee
php artisan leaves:verify-balances --employee=123

# Auto-fix discrepancies
php artisan leaves:verify-balances --fix

# Generate detailed report
php artisan leaves:verify-balances --report
```

### Backfill Operations
```bash
# Backfill all employees
php artisan leaves:backfill-balances

# Backfill specific date range
php artisan leaves:backfill-balances --from=2024-07 --to=2024-12

# Backfill specific employee
php artisan leaves:backfill-balances --employee=123
```

### System Monitoring
```bash
# System health check
php artisan leaves:monitor --health

# Check balance discrepancies
php artisan leaves:monitor --discrepancies

# Performance metrics
php artisan leaves:monitor --performance

# All monitoring checks
php artisan leaves:monitor --all
```

### Report Generation
```bash
# Executive summary
php artisan leaves:generate-reports --type=summary

# Balance verification report
php artisan leaves:generate-reports --type=balance

# Discrepancy analysis
php artisan leaves:generate-reports --type=discrepancy

# System health report
php artisan leaves:generate-reports --type=health

# Historical trend analysis
php artisan leaves:generate-reports --type=trend --months=12

# Export to CSV
php artisan leaves:generate-reports --type=balance --format=csv --export
```

## Maintenance Procedures

### Daily Maintenance

1. **Monitor System Health**
   ```bash
   php artisan leaves:monitor --health
   ```

2. **Check for Balance Discrepancies**
   ```bash
   php artisan leaves:monitor --discrepancies
   ```

### Monthly Maintenance

1. **Verify Monthly Accrual Processing**
   ```bash
   # Check if last month was processed
   php artisan leaves:verify-balances --report
   ```

2. **Run Monthly Accrual (if not automated)**
   ```bash
   php artisan leaves:monthly-accrual
   ```

3. **Generate Monthly Reports**
   ```bash
   php artisan leaves:generate-reports --type=summary --export
   ```

### Quarterly Maintenance

1. **Comprehensive System Validation**
   ```bash
   php artisan leaves:verify-balances
   ```

2. **Historical Trend Analysis**
   ```bash
   php artisan leaves:generate-reports --type=trend --months=12
   ```

3. **Performance Review**
   ```bash
   php artisan leaves:monitor --performance
   ```

## Troubleshooting Guide

### Common Issues

#### 1. Balance Discrepancies
**Symptoms**: Employee balance doesn't match expected amount
**Diagnosis**:
```bash
php artisan leaves:verify-balances --employee=<ID>
```
**Resolution**:
```bash
# Check for missing months
php artisan leaves:backfill-balances --employee=<ID>

# Manual correction if needed
php artisan leaves:verify-balances --employee=<ID> --fix
```

#### 2. Missing Monthly Accruals
**Symptoms**: No accrual entries for recent months
**Diagnosis**:
```bash
php artisan leaves:monitor --health
```
**Resolution**:
```bash
# Process missing month
php artisan leaves:monthly-accrual --month=YYYY-MM

# Verify cron job is running
# Check Laravel scheduler configuration
```

#### 3. Slow Balance Calculations
**Symptoms**: Long response times for balance queries
**Diagnosis**:
```bash
php artisan leaves:monitor --performance
```
**Resolution**:
- Clear balance cache: `php artisan cache:clear`
- Check database indexes
- Review query performance

#### 4. Duplicate Accrual Entries
**Symptoms**: Multiple entries for same employee/month
**Diagnosis**: Check database for duplicate entries
**Resolution**: Database constraints should prevent this, but if it occurs:
```sql
-- Remove duplicates (keep first entry)
DELETE t1 FROM leave_accrual_ledger t1
INNER JOIN leave_accrual_ledger t2 
WHERE t1.id > t2.id 
AND t1.employee_id = t2.employee_id 
AND t1.year_month = t2.year_month 
AND t1.source = t2.source;
```

### Error Codes and Solutions

| Error | Description | Solution |
|-------|-------------|----------|
| `Target class [App\Models\AccrualCalculator] does not exist` | Service binding issue | Use full namespace: `\App\Services\AccrualCalculator::class` |
| `Employee missing DOJ` | Employee has no date of joining | Update employee record with valid DOJ |
| `Negative accrued balance detected` | Data integrity issue | Run balance verification and correction |
| `High error rate in monthly accrual processing` | System performance issue | Check logs, review employee data quality |

## Performance Optimization

### Database Optimization

1. **Indexes**: Ensure proper indexes exist
   ```sql
   -- Employee table
   CREATE INDEX idx_employees_doj ON employees(company_doj);
   CREATE INDEX idx_employees_active ON employees(is_active);
   
   -- Leave accrual ledger
   CREATE INDEX idx_ledger_employee_month ON leave_accrual_ledger(employee_id, year_month);
   CREATE INDEX idx_ledger_source ON leave_accrual_ledger(source);
   ```

2. **Query Optimization**: Use batch processing for large datasets

3. **Caching**: Balance calculations are cached for 5 minutes

### Memory Management

1. **Chunked Processing**: Large datasets processed in batches of 100-200 employees
2. **Memory Limits**: Monitor memory usage during batch operations
3. **Garbage Collection**: Ensure proper cleanup in long-running processes

## Security Considerations

### Data Integrity

1. **Audit Trail**: All balance changes recorded in ledger
2. **Immutable Records**: Ledger entries are append-only
3. **Unique Constraints**: Prevent duplicate accruals
4. **Transaction Safety**: Database transactions ensure consistency

### Access Control

1. **Command Permissions**: Restrict console command access
2. **API Security**: Implement proper authentication for balance queries
3. **Logging**: All operations logged for audit purposes

## Backup and Recovery

### Data Backup

1. **Regular Backups**: Include `leave_accrual_ledger` table in backups
2. **Point-in-Time Recovery**: Maintain transaction logs
3. **Testing**: Regularly test backup restoration

### Recovery Procedures

1. **Balance Restoration**: Use ledger entries to recalculate balances
2. **Rollback Scripts**: Available for emergency corrections
3. **Data Validation**: Verify integrity after recovery

## Integration Points

### External Systems

1. **HRMS Integration**: Employee data synchronization
2. **Payroll System**: Balance data for leave deductions
3. **Reporting Tools**: Export capabilities for external reporting

### API Endpoints

1. **Balance Query**: Get employee balance information
2. **Accrual History**: Retrieve accrual transaction history
3. **Validation**: Check balance accuracy and discrepancies

## Future Enhancements

### Planned Features

1. **Automated Alerts**: Email notifications for discrepancies
2. **Dashboard Integration**: Real-time balance monitoring
3. **Advanced Reporting**: More detailed analytics and trends
4. **Mobile API**: Support for mobile applications

### Scalability Considerations

1. **Database Partitioning**: For large datasets
2. **Caching Strategy**: Enhanced caching for better performance
3. **Microservices**: Potential separation of accrual logic
4. **Load Balancing**: For high-availability deployments

## Support and Maintenance Contacts

- **Technical Lead**: [Contact Information]
- **Database Administrator**: [Contact Information]
- **HR System Administrator**: [Contact Information]
- **Emergency Contact**: [24/7 Support Information]

---

*Last Updated: September 1, 2025*
*Version: 1.0*