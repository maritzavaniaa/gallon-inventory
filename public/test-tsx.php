<?php
// public/test-tsx.php - FINAL CLEAN VERSION
require_once '../app/config/database.php';
require_once '../app/models/MonthlyAllowance.php';
require_once '../app/models/Transaction.php';

$database = new Database();
$db = $database->getConnection();

// 1. Inisialisasi Model
$allowanceModel = new MonthlyAllowance($db);
$transactionModel = new Transaction($db);

// 2. SETUP: Login & Generate (Wajib untuk test)
$_SESSION['user_id'] = 1; 
$targetMonth = date('Y-m-01');
$allowanceModel->generate($targetMonth);

// 3. LOGIC TEST: Input Stok & Transaksi
// Kita cek dulu, kalau stok masih 0, baru kita isi 50 agar tidak dobel saat refresh
$currentInv = $transactionModel->getCurrentStock();
if ($currentInv['stock_in'] == 0) {
    $transactionModel->createIn([
        'quantity' => 50,
        'tsx_date' => date('Y-m-d'),
        'reference_doc' => 'INIT-STOCK',
        'notes' => 'Stok Awal Testing'
    ]);
}

// Simulasi Transaksi OUT (Ambil 2 Galon)
$idSample = '00012345'; // PASTIKAN ID INI ADA DI TABEL SUBJECT
$resultOut = $transactionModel->createOut([
    'id_share' => $idSample,
    'quantity' => 2,
    'tsx_date' => date('Y-m-d'),
    'notes' => 'Test Transaksi Rutin'
]);

// 4. TAMPILAN DASHBOARD TEST
echo "<div style='max-width: 900px; margin: 20px auto; font-family: sans-serif;'>";
echo "<h1 style='color: #2c3e50; text-align: center;'>🧪 Galon System Logic Test</h1>";

// BOX 1: STATUS GUDANG
$stock = $transactionModel->getCurrentStock();
echo "<div style='background: #27ae60; color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;'>";
echo "<h2>📦 Status Stok Gudang</h2>";
echo "<p>Total Masuk: <b>{$stock['stock_in']}</b> | Total Keluar: <b>{$stock['stock_out']}</b></p>";
echo "<h1 style='margin: 0;'>Sisa di Gudang: {$stock['remaining']} Galon</h1>";
echo "</div>";

// BOX 2: JATAH KARYAWAN (SUBJECT)
echo "<div style='background: white; padding: 20px; border-radius: 10px; border: 1px solid #ddd; margin-bottom: 20px;'>";
echo "<h3 style='color: #2980b9;'>👤 Detail Jatah Subjek ($idSample)</h3>";
$subjectInfo = $allowanceModel->getSubjectAllowance($idSample, $targetMonth);
echo "<ul>
        <li>Bulan: {$subjectInfo['allowance_month']}</li>
        <li>Jatah Bulanan: {$subjectInfo['allowance']}</li>
        <li>Sudah Dipakai: {$subjectInfo['used']}</li>
        <li><b>Sisa Jatah: {$subjectInfo['remaining']}</b></li>
      </ul>";
echo "</div>";

// BOX 3: SUMMARY BULANAN
echo "<div style='background: white; padding: 20px; border-radius: 10px; border: 1px solid #ddd;'>";
echo "<h3 style='color: #f39c12;'>🗓️ Summary Keseluruhan ($targetMonth)</h3>";
$summary = $allowanceModel->getMonthlySummary($targetMonth);
echo "<pre style='background: #f4f4f4; padding: 10px;'>" . print_r($summary, true) . "</pre>";
echo "</div>";

echo "<p style='text-align: center; margin-top: 20px;'><a href='?url=dashboard'>← Kembali ke Dashboard</a></p>";
echo "</div>";