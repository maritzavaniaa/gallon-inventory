<?php
/**
 * Business Rules Configuration
 * Update ini kalau ada feedback dari stakeholder
 */

return [
    // Quota Enforcement
    'quota_enforcement' => 'block',  // 'warning', 'block', or 'none'
    'allow_override' => true,          // Admin bisa override quota limit
    
    // Transaction Rules
    'allow_negative_qty' => true,      // Allow return transactions
    'allow_future_date' => false,      // Transaksi tidak boleh future date
    'allow_past_month_edit' => false,  // Edit transaksi bulan lalu
    
    // Allowance Rules
    'reset_day_of_month' => 1,         // Tanggal reset kuota
    'auto_generate_allowance' => true, // Auto-generate atau manual : 'true' / 'false'
    
    // Stock Tracking
    'track_inventory' => true,         // Track IN/OUT stock
    'low_stock_threshold' => 10,       // Alert kalau stock < 50
    
    // UI Display
    'warning_threshold' => 20,         // Warning kalau remaining < 20%
    'critical_threshold' => 10,        // Critical kalau remaining < 10%
];
