<?php 
$pageTitle = 'Transaksi Galon';
ob_start(); 
?>

<div class="max-w-4xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">Input Transaksi</h1>
            <p class="text-gray-500 italic">Selamat bertugas, <span class="text-blue-600 font-semibold"><?= $_SESSION['user_name'] ?? 'User' ?></span>!</p>
        </div>
        
        <div class="flex gap-3">
            <div class="bg-white px-5 py-3 rounded-2xl shadow-sm border border-gray-100 text-center">
                <p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Stok Gudang</p>
                <p class="text-2xl font-black text-blue-600"><?= $stock['remaining'] ?? 0 ?></p>
            </div>
            <div class="bg-white px-5 py-3 rounded-2xl shadow-sm border border-gray-100 text-center">
                <p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Keluar Hari Ini</p>
                <p class="text-2xl font-black text-orange-500"><?= $stats_today['out'] ?? 0 ?></p>
            </div>
        </div>
    </div>

    <div class="flex bg-gray-200 p-1 rounded-2xl mb-8 w-max shadow-inner">
        <button onclick="switchTab('out')" id="btn-out" class="tab-active px-8 py-3 rounded-xl font-bold transition-all flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span> Galon Keluar
        </button>
        <button onclick="switchTab('in')" id="btn-in" class="px-8 py-3 rounded-xl font-bold text-gray-500 transition-all flex items-center gap-2">
            Galon Masuk
        </button>
    </div>

    <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100">
        
        <div id="panel-out" class="p-8 animate-fade-in">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800">Distribusi Galon</h2>
                <p class="text-sm text-gray-500">Cari subjek terlebih dahulu untuk validasi jatah.</p>
            </div>

            <div class="space-y-6">
                <div class="flex gap-2 items-end">
                    <div class="flex-1 relative"> 
                        <label class="block text-sm font-semibold text-gray-700 mb-2">ID Share atau Nama</label>
                        <input type="text" id="search-input" autocomplete="off" placeholder="Masukkan ID atau Nama..." 
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all duration-200">
                                
                        <div id="search-results" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-2xl max-h-60 overflow-y-auto"></div>
                    </div>
                    <button type="button" onclick="searchSubject()" class="mb-0.5 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-blue-100">
                        Cari
                    </button>
                </div>

                <div id="info-penerima" class="hidden p-6 bg-blue-50 border border-blue-100 rounded-2xl animate-fade-in">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 id="res-nama" class="font-bold text-blue-900 text-lg">---</h4>
                            <p id="res-divisi" class="text-sm text-blue-700 font-medium">Divisi: ---</p>
                        </div>
                        <div class="text-right border-l border-blue-200 pl-6">
                            <p class="text-xs uppercase font-bold text-blue-500 mb-1 tracking-widest">Sisa Kuota</p>
                            <p id="res-kuota" class="text-4xl font-black text-blue-600">0</p>
                        </div>
                    </div>
                </div>

                <form id="form-actual-out" action="?url=transactions/store-out" method="POST" class="hidden space-y-6 pt-4 border-t border-gray-100">
                    <input type="hidden" name="id_share" id="hidden-id-share">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Ambil</label>
                            <input type="number" name="quantity" value="1" min="1" required
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Transaksi</label>
                            <input type="date" name="tsx_date" value="<?= date('Y-m-d') ?>" required
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan (Opsional)</label>
                        <textarea name="notes" placeholder="Contoh: Pengambilan jatah bulanan" 
                                  class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all" rows="2"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-bold text-lg transition-all shadow-xl shadow-blue-200">
                        Konfirmasi & Simpan Transaksi
                    </button>
                </form>
            </div>
        </div>

        <div id="panel-in" class="p-8 hidden animate-fade-in">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-green-800">Restock Gudang</h2>
                <p class="text-sm text-gray-500">Catat galon yang masuk dari supplier/pembelian.</p>
            </div>

            <form action="?url=transactions/store-in" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Dokumen / Referensi</label>
                    <input type="text" name="reference_doc" placeholder="Contoh: DO-123456 atau Nama Supplier" 
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-green-100 focus:border-green-500 outline-none transition-all">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Galon Masuk</label>
                        <input type="number" name="quantity" min="1" required placeholder="0"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-green-100 focus:border-green-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Masuk</label>
                        <input type="date" name="tsx_date" value="<?= date('Y-m-d') ?>" required
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-green-100 focus:border-green-500 outline-none transition-all">
                    </div>
                </div>

                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-2xl font-bold text-lg transition-all shadow-xl shadow-green-200">
                    Simpan Stok Masuk
                </button>
            </form>
        </div>

    </div>
</div>

<style>
    .tab-active { 
        background-color: white !important; 
        color: #2563eb !important; 
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); 
    }
    .animate-fade-in { animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn { 
        from { opacity: 0; transform: translateY(10px); } 
        to { opacity: 1; transform: translateY(0); } 
    }
</style>

<script>
// Fungsi Tab Switcher
function switchTab(type) {
    const btnOut = document.getElementById('btn-out');
    const btnIn = document.getElementById('btn-in');
    const panelOut = document.getElementById('panel-out');
    const panelIn = document.getElementById('panel-in');

    if (type === 'out') {
        btnOut.classList.add('tab-active');
        btnIn.classList.remove('tab-active');
        btnIn.classList.add('text-gray-500');
        panelOut.classList.remove('hidden');
        panelIn.classList.add('hidden');
    } else {
        btnIn.classList.add('tab-active');
        btnOut.classList.remove('tab-active');
        btnOut.classList.add('text-gray-500');
        panelIn.classList.remove('hidden');
        panelOut.classList.add('hidden');
    }
}

// Fungsi AJAX Search Subject (FR-TSX-01)
let debounceTimer;

document.getElementById('search-input').addEventListener('input', function() {
    const query = this.value;
    const resultsContainer = document.getElementById('search-results');
    
    clearTimeout(debounceTimer);
    
    if (query.length < 2) {
        resultsContainer.classList.add('hidden');
        return;
    }
    
    debounceTimer = setTimeout(async () => {
        try {
            const response = await fetch(`?url=transactions/search&q=${encodeURIComponent(query)}`);
            const result = await response.json();

            resultsContainer.innerHTML = ''; // Kosongkan hasil lama

            if (result.success && result.data.length > 0) {
                resultsContainer.classList.remove('hidden');
                
                result.data.forEach(person => {
                    const item = document.createElement('div');
                    item.className = 'px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-50 last:border-none transition-colors';
                    item.innerHTML = `
                        <div class="font-bold text-gray-800">${person.name}</div>
                        <div class="text-xs text-gray-500">${person.id_share} - ${person.division}</div>
                    `;
                    
                    // Saat salah satu nama di-klik
                    item.addEventListener('click', () => {
                        selectPerson(person);
                    });
                    
                    resultsContainer.appendChild(item);
                });
            } else {
                resultsContainer.innerHTML = '<div class="p-4 text-sm text-gray-500">Tidak ada hasil ditemukan</div>';
                resultsContainer.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Search error:', error);
        }
    }, 400);
});

// Fungsi saat user memilih salah satu orang dari daftar
function selectPerson(person) {
    // Sembunyikan dropdown
    document.getElementById('search-results').classList.add('hidden');
    
    // Isi input search dengan nama yang dipilih (opsional)
    document.getElementById('search-input').value = person.name;

    // Tampilkan Info Box & Form
    document.getElementById('info-penerima').classList.remove('hidden');
    document.getElementById('res-nama').innerText = person.name;
    document.getElementById('res-divisi').innerText = 'Divisi: ' + person.division;
    document.getElementById('res-kuota').innerText = person.remaining;
    
    document.getElementById('hidden-id-share').value = person.id_share;
    document.getElementById('form-actual-out').classList.remove('hidden');
    
    // Warna Kuota
    document.getElementById('res-kuota').className = person.remaining <= 0 
        ? 'text-4xl font-black text-red-600' 
        : 'text-4xl font-black text-blue-600';
}

// Tutup dropdown jika klik di luar input
document.addEventListener('click', function(e) {
    if (!document.getElementById('search-input').contains(e.target)) {
        document.getElementById('search-results').classList.add('hidden');
    }
});
</script>

<?php 
$content = ob_get_clean();
include '../app/views/layouts/main.php';
?>