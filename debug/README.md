# 🔍 Advanced Debugging System for Restaurant Menu System

## 📋 Overview

This comprehensive debugging system provides detailed insights into the Restaurant Menu System's execution, class loading, dependencies, and performance. It's designed to help identify and resolve issues like the "Handler not found: HomeController@index" error.

## 🚀 Quick Start

### 1. Access the Debug Dashboard
```
http://localhost/restaurant-menu-system/debug-index.php?debug=1
```

### 2. Run System Check
```
http://localhost/restaurant-menu-system/debug/system-check.php
```

### 3. Test Application with Debugging
```
http://localhost/restaurant-menu-system/debug-index.php
```

## 📊 Debug Components

### 1. AdvancedDebugger (`AdvancedDebugger.php`)
**Purpose**: Core logging and error tracking system

**Features**:
- Timestamped execution logging
- Memory usage tracking
- Error logging with backtraces
- Class loading verification
- Constant definition tracking
- Route registration logging
- Application state monitoring

**Usage**:
```php
$debugger = AdvancedDebugger::getInstance();
$debugger->log('INFO', 'System started');
$debugger->logError('Something went wrong', 'file.php', 123);
$debugger->logClassLoad('HomeController', '/app/controllers/HomeController.php');
```

### 2. ExecutionTracer (`ExecutionTracer.php`)
**Purpose**: Step-by-step execution tracking with timing

**Features**:
- Nested step tracking
- Execution time measurement
- Memory usage per step
- Parent-child step relationships
- Comprehensive trace reporting

**Usage**:
```php
$tracer = ExecutionTracer::getInstance();
$stepId = $tracer->startStep('APPLICATION_INIT', ['mode' => 'full']);
// ... do work ...
$tracer->endStep($stepId, ['result' => 'success']);
```

### 3. ClassVerifier (`ClassVerifier.php`)
**Purpose**: Comprehensive class loading verification

**Features**:
- File existence checking
- File permission verification
- Class loading validation
- Dependency analysis per class
- Reflection-based dependency detection
- Detailed status reporting

**Usage**:
```php
$verifier = ClassVerifier::getInstance();
$results = $verifier->verifyAllClasses();
$report = $verifier->generateVerificationReport();
```

### 4. DependencyAnalyzer (`DependencyAnalyzer.php`)
**Purpose**: System dependency analysis and optimization

**Features**:
- Dependency mapping
- Circular dependency detection
- Loading order optimization
- System health calculation
- Validation of current loading order
- Recommendation generation

**Usage**:
```php
$analyzer = DependencyAnalyzer::getInstance();
$analysis = $analyzer->analyzeDependencies();
$optimalOrder = $analyzer->getOptimalLoadingOrder();
$health = $analyzer->calculateSystemHealth($analysis);
```

### 5. DebugDashboard (`DebugDashboard.php`)
**Purpose**: Visual debugging interface

**Features**:
- Real-time performance metrics
- Execution trace visualization
- Error log display
- System status overview
- Interactive controls
- Mobile-responsive design

## 📁 File Structure

```
debug/
├── AdvancedDebugger.php          # Core logging system
├── ExecutionTracer.php           # Execution tracking
├── ClassVerifier.php             # Class verification
├── DependencyAnalyzer.php        # Dependency analysis
├── DebugDashboard.php            # Visual interface
├── download-log.php              # Log download utility
├── clear-logs.php                # Log clearing utility
├── system-check.php              # System check utility
├── download-report.php           # Report download utility
├── debug.log                     # Runtime logs (auto-generated)
├── debug_report.json             # Debug report (auto-generated)
├── system_check.json             # System check report (auto-generated)
└── README.md                     # This documentation

debug-index.php                   # Main debug entry point
```

## 🔧 Usage Scenarios

### Scenario 1: "Handler not found" Error
**Problem**: Application shows "Handler not found: HomeController@index"

**Solution**:
1. Access debug dashboard: `debug-index.php?debug=1`
2. Check "Class Verification" section
3. Look for HomeController status
4. Check "Dependency Analysis" for loading order issues
5. Review "Error Log" for detailed error information

### Scenario 2: Application Won't Start
**Problem**: White screen or fatal error on application load

**Solution**:
1. Run system check: `debug/system-check.php`
2. Check PHP version and extensions
3. Verify file permissions
4. Review class loading status
5. Check dependency analysis results

### Scenario 3: Performance Issues
**Problem**: Application is slow or consuming excessive memory

**Solution**:
1. Access debug dashboard: `debug-index.php?debug=1`
2. Review "Performance Metrics" section
3. Check execution time for each step
4. Analyze memory usage patterns
5. Identify bottlenecks in execution trace

### Scenario 4: Route Issues
**Problem**: Routes not working or returning 404 errors

**Solution**:
1. Access debug dashboard: `debug-index.php?debug=1`
2. Check "Router" section for route count
3. Verify route registration in logs
4. Check for route loading errors
5. Review route handler availability

## 📈 Interpreting Results

### Class Verification Results
- **✅ Loaded**: Class successfully loaded and available
- **❌ Failed**: Class file exists but failed to load
- **📁 Missing File**: Class file does not exist
- **🔒 Not Readable**: File exists but not readable

### Dependency Analysis Results
- **🟢 Excellent**: 90-100% system health
- **🟡 Good**: 70-89% system health
- **🟠 Fair**: 50-69% system health
- **🔴 Poor**: Below 50% system health

### Performance Metrics
- **Execution Time**: Total time in milliseconds
- **Memory Usage**: Total memory consumed
- **Peak Memory**: Maximum memory usage
- **Log Entries**: Number of logged events

## 🛠️ Advanced Features

### 1. Custom Logging
```php
$debugger = AdvancedDebugger::getInstance();
$debugger->log('CUSTOM', 'Custom event', ['data' => $customData]);
```

### 2. Step Tracing
```php
$tracer = ExecutionTracer::getInstance();
$stepId = $tracer->startStep('CUSTOM_STEP', ['param' => 'value']);
// ... execute code ...
$tracer->endStep($stepId, ['result' => $result]);
```

### 3. Class Verification
```php
$verifier = ClassVerifier::getInstance();
$result = $verifier->verifyClass('HomeController', [
    'file' => '/app/controllers/HomeController.php',
    'required' => true
]);
```

### 4. Dependency Analysis
```php
$analyzer = DependencyAnalyzer::getInstance();
$optimalOrder = $analyzer->getOptimalLoadingOrder();
$validation = $analyzer->validateCurrentLoadingOrder($currentOrder);
```

## 📊 Report Generation

### Debug Report
Contains comprehensive debugging information:
- Execution timing
- Memory usage
- Error logs
- Class loading status
- Method calls
- Application state changes

**Access**: `debug/debug_report.json` (auto-generated)

### System Check Report
Contains system environment information:
- PHP version and extensions
- Configuration settings
- File permissions
- Class verification results
- Dependency analysis results

**Access**: `debug/system_check.json` (auto-generated)

### Log Files
Contains detailed execution logs:
- Timestamped events
- Memory usage per event
- Error information
- Backtraces

**Access**: `debug/debug.log` (auto-generated)

## 🔍 Troubleshooting

### Common Issues and Solutions

#### 1. Debug Dashboard Not Loading
**Problem**: Debug dashboard shows white screen or errors

**Solution**:
1. Check PHP version (requires 7.4+)
2. Verify file permissions in debug directory
3. Ensure required extensions are loaded
4. Check for syntax errors in debug files

#### 2. Empty Debug Reports
**Problem**: Reports show no data or incomplete information

**Solution**:
1. Ensure debug directory is writable
2. Check file permissions for log files
3. Verify that debug components are properly initialized
4. Run system check to identify issues

#### 3. High Memory Usage
**Problem**: Debug system consumes too much memory

**Solution**:
1. Clear logs regularly using `debug/clear-logs.php`
2. Reduce trace depth in configuration
3. Disable verbose logging when not needed
4. Monitor memory usage in dashboard

#### 4. Slow Performance
**Problem**: Debug system slows down application

**Solution**:
1. Use debug mode only when troubleshooting
2. Disable debug components in production
3. Clear old log files
4. Optimize logging frequency

## 🎯 Best Practices

### 1. Development Mode
- Use debug dashboard during development
- Monitor class loading and dependencies
- Check performance metrics regularly
- Review error logs for issues

### 2. Production Mode
- Disable debug components in production
- Use error logging only for critical issues
- Monitor system health periodically
- Keep debug system updated

### 3. Troubleshooting Workflow
1. Reproduce the issue
2. Enable debug mode
3. Check debug dashboard
4. Review logs and reports
5. Identify root cause
6. Apply fix
7. Verify solution
8. Disable debug mode

## 📞 Support

### Getting Help
1. **Check Documentation**: Review this README thoroughly
2. **Run System Check**: Use `debug/system-check.php`
3. **Review Logs**: Check `debug/debug.log` for errors
4. **Generate Reports**: Use debug dashboard for comprehensive analysis

### Common Error Messages
- **"Class not found"**: Check class verification results
- **"File not readable"**: Check file permissions
- **"Circular dependency"**: Review dependency analysis
- **"Loading order invalid"**: Check optimal loading order

### Report Issues
When reporting issues, please include:
1. PHP version and extensions
2. System check results
3. Debug report (if available)
4. Error logs
5. Steps to reproduce

## 🔄 Updates and Maintenance

### Regular Maintenance
- Clear old log files regularly
- Update debug components with application
- Review system health periodically
- Keep documentation updated

### Version Compatibility
- **PHP**: 7.4+ recommended
- **Extensions**: PDO, JSON, Session, FileInfo
- **Memory**: 64MB+ recommended
- **Disk Space**: 10MB+ for logs

---

**Note**: This debug system is designed for development and troubleshooting. In production environments, consider disabling verbose logging and debug components to optimize performance.