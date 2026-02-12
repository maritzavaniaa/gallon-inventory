<?php 
$pageTitle = 'Transaksi Gallon';
ob_start(); 
?>

<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold mb-2" style="color: #1F4396;">Transaksi Gallon Internal</h1>
        <p class="text-lg text-gray-700">Input pengambilan gallon (OUT) atau stok masuk (IN)</p>
    </div>
    
    <div class="flex gap-4">
        <div class="bg-white px-5 py-3 rounded-md border-2 border-gray-200 text-center shadow-sm">
            <p class="text-md text-gray-500 uppercase font-semibold tracking-wider">Stok Internal</p>
            <p class="text-3xl font-bold text-blue-800"><?= $stock['remaining'] ?? 0 ?></p>
        </div>
        <div class="bg-white px-5 py-3 rounded-md border-2 border-gray-200 text-center shadow-sm">
            <p class="text-md text-gray-500 uppercase font-semibold tracking-wider">Keluar Hari Ini</p>
            <p class="text-3xl font-bold text-green-600"><?= $stats_today['out'] ?? 0 ?></p>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['flash'])): ?>
    <?php $flash = $_SESSION['flash']; ?>
    <div class="border-2 px-6 py-4 rounded-lg mb-6 text-lg <?= $flash['success'] ? 'bg-green-50 border-green-500' : 'bg-red-50 border-red-500' ?>">
        <strong class="font-bold <?= $flash['success'] ? 'text-green-700' : 'text-red-700' ?>">
            <?= $flash['success'] ? 'Berhasil!' : 'Gagal!' ?>
        </strong>
        <span class="text-gray-900"><?= $flash['message'] ?? 'Transaksi telah diproses.' ?></span>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<div class="flex bg-gray-200 p-1 rounded-md mb-8 w-max border-2 border-gray-300">
    <button onclick="switchTab('out')" id="btn-out" class="tab-active px-8 py-3 rounded-sm font-bold transition-all flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-blue-600"></span> Gallon Keluar
    </button>
    <button onclick="switchTab('in')" id="btn-in" class="px-8 py-3 rounded-sm font-bold text-gray-600 transition-all flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-green-600"></span> Intern Gallon
    </button>
</div>

<div class="bg-white rounded-md border-2 border-gray-200 shadow-sm">
    
    <div id="panel-out" class="animate-fade-in">
        <div class="py-4 px-6 mb-2" style="background: linear-gradient(135deg, #1F4396 0%, #2E5BBE 100%);">
            <h2 class="text-xl font-bold text-white">FORM DISTRIBUSI GALLON (OUT)</h2>
        </div>

        <div class="p-8 space-y-6">
            <div class="flex gap-2 items-end">
                <div class="flex-1 relative"> 
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Cari Penerima (ID Share)</label>
                    <input type="text" id="search-input" autocomplete="off" placeholder="Ketik minimal 2 angka..." inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                            class="w-full px-4 py-3 rounded-md border-2 border-gray-200 focus:border-blue-500 outline-none transition-all">
                            
                    <div id="search-results" class="hidden absolute z-50 w-full mt-1 bg-white border-2 border-gray-300 rounded-md shadow-xl max-h-60 overflow-y-auto"></div>
                </div>
                <button type="button" onclick="searchSubject()" class="mb-0.5 bg-blue-800 hover:bg-blue-900 text-white px-8 py-3.5 rounded-md font-bold transition-all shadow-md uppercase text-sm tracking-widest">
                    Cari
                </button>
            </div>

            <div id="info-penerima" class="hidden p-6 bg-blue-50 border-2 border-blue-200 rounded-md animate-fade-in">
                <div class="flex justify-between items-center">
                    <div>
                        <h4 id="res-nama" class="font-bold text-blue-900 text-2xl uppercase">---</h4>
                        <p id="res-divisi" class="text-md text-blue-700 font-bold uppercase tracking-wider">Divisi: ---</p>
                    </div>
                    <div class="text-right border-l-4 border-blue-200 pl-8">
                        <p class="text-sm uppercase font-semibold text-blue-600 mb-1 tracking-widest">Sisa Kuota Bulan Ini</p>
                        <p id="res-kuota" class="text-5xl font-black text-blue-800">0</p>
                    </div>
                </div>
            </div>

            <form id="form-actual-out" action="?url=transactions/store-out" method="POST" class="hidden space-y-6 pt-6 border-t-2 border-gray-100">
                <input type="hidden" name="id_share" id="hidden-id-share">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Jumlah Ambil (Gallon)</label>
                        <input type="number" name="quantity" value="1" min="1" required
                               class="w-full px-4 py-3 rounded-md border-2 border-gray-200 focus:border-blue-500 font-bold text-xl outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Tanggal Transaksi</label>
                        <input type="date" name="tsx_date" value="<?= date('Y-m-d') ?>" required
                               class="w-full px-4 py-3 rounded-md border-2 border-gray-200 focus:border-blue-500 outline-none transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Catatan (Optional)</label>
                    <textarea name="notes" placeholder="Contoh: Pengambilan kuota reguler" 
                              class="w-full px-4 py-3 rounded-md border-2 border-gray-200 focus:border-blue-500 outline-none transition-all" rows="2"></textarea>
                </div>

                <button type="submit" class="w-full text-white py-4 rounded-md font-bold text-xl transition-all shadow-lg uppercase tracking-widest"
                        style="background: linear-gradient(135deg, #1F4396 0%, #2E5BBE 100%);">
                    Simpan Transaksi Keluar
                </button>
            </form>
        </div>
    </div>

    <div id="panel-in" class="hidden animate-fade-in">
        <div class="py-4 px-6 mb-2" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <h2 class="text-xl font-bold text-white">FORM RESTOCK INTERN (IN)</h2>
        </div>

        <form action="?url=transactions/store-in" method="POST" class="p-8 space-y-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Dokumen Referensi / Supplier</label>
                <input type="text" name="reference_doc" placeholder="Contoh: PO-2024-001" 
                       class="w-full px-4 py-3 rounded-md border-2 border-gray-200 focus:border-green-500 outline-none transition-all">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Jumlah Gallon Masuk</label>
                    <input type="number" name="quantity" min="1" required placeholder="0"
                           class="w-full px-4 py-3 rounded-md border-2 border-gray-200 focus:border-green-500 font-bold text-xl outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Tanggal Masuk</label>
                    <input type="date" name="tsx_date" value="<?= date('Y-m-d') ?>" required
                           class="w-full px-4 py-3 rounded-md border-2 border-gray-200 focus:border-green-500 outline-none transition-all">
                </div>
            </div>

            <button type="submit" class="w-full text-white py-4 rounded-md font-bold text-xl transition-all shadow-lg uppercase tracking-widest"
                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                Simpan Stok Masuk
            </button>
        </form>
    </div>
</div>

<div class="mt-8 bg-blue-50 border-2 border-blue-300 rounded-xl p-6">
    <div class="flex items-start gap-4">
        <svg class="w-8 h-8 text-blue-600 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
        </svg>
        <div>
            <p class="text-gray-900 font-bold text-lg mb-2">Petunjuk Penggunaan:</p>
            <ul class="text-gray-800 text-base space-y-1 leading-relaxed">
                <li>• Gunakan <strong>ID Share</strong> atau <strong>Nama</strong> untuk mencari penerima gallon.</li>
                <li>• Sistem akan otomatis memblokir transaksi jika <strong>Sisa Kuota</strong> sudah habis (0).</li>
                <li>• Jika ada <strong>kesalahan input transaksi</strong>, silakan hubungi Admin untuk bantuan koreksi.</li>
                <li>• Pastikan jumlah gallon dan tanggal transaksi sudah benar sebelum menekan tombol simpan.</li>
            </ul>
        </div>
    </div>
</div>

<style>
    .tab-active { 
        background-color: #ffffff !important; 
        color: #1F4396 !important; 
        border: 2px solid #cbd5e1;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .animate-fade-in { animation: fadeIn 0.3s ease-out; }
    @keyframes fadeIn { 
        from { opacity: 0; } 
        to { opacity: 1; } 
    }
</style>

<script>
// Logic JS tetap sama seperti sebelumnya, hanya selector CSS yang disesuaikan
function switchTab(type) {
    const btnOut = document.getElementById('btn-out');
    const btnIn = document.getElementById('btn-in');
    const panelOut = document.getElementById('panel-out');
    const panelIn = document.getElementById('panel-in');

    if (type === 'out') {
        btnOut.classList.add('tab-active');
        btnIn.classList.remove('tab-active');
        btnIn.classList.add('text-gray-600');
        panelOut.classList.remove('hidden');
        panelIn.classList.add('hidden');
    } else {
        btnIn.classList.add('tab-active');
        btnOut.classList.remove('tab-active');
        btnOut.classList.add('text-gray-600');
        panelIn.classList.remove('hidden');
        panelOut.classList.add('hidden');
    }
}

let debounceTimer;
document.getElementById('search-input').addEventListener('input', function() {
    const query = this.value;
    const resultsContainer = document.getElementById('search-results');
    clearTimeout(debounceTimer);
    if (query.length < 2) { resultsContainer.classList.add('hidden'); return; }
    debounceTimer = setTimeout(async () => {
        try {
            const response = await fetch(`?url=transactions/search&q=${encodeURIComponent(query)}`);
            const result = await response.json();
            resultsContainer.innerHTML = ''; 
            if (result.success && result.data.length > 0) {
                resultsContainer.classList.remove('hidden');
                result.data.forEach(person => {
                    const item = document.createElement('div');
                    item.className = 'px-4 py-3 hover:bg-blue-50 cursor-pointer border-b-2 border-gray-100 last:border-none transition-colors';
                    item.innerHTML = `
                        <div class="font-bold text-blue-900">${person.name}</div>
                        <div class="text-xs text-gray-600 font-mono">${person.id_share} | ${person.division}</div>
                    `;
                    item.addEventListener('click', () => selectPerson(person));
                    resultsContainer.appendChild(item);
                });
            } else {
                resultsContainer.innerHTML = '<div class="p-4 text-sm text-gray-500 italic">Subjek tidak ditemukan...</div>';
                resultsContainer.classList.remove('hidden');
            }
        } catch (error) { console.error('Search error:', error); }
    }, 400);
});

function selectPerson(person) {
    document.getElementById('search-results').classList.add('hidden');
    document.getElementById('search-input').value = person.name;
    document.getElementById('info-penerima').classList.remove('hidden');
    document.getElementById('res-nama').innerText = person.name;
    document.getElementById('res-divisi').innerText = 'Divisi: ' + person.division;
    document.getElementById('res-kuota').innerText = person.remaining;
    document.getElementById('hidden-id-share').value = person.id_share;
    document.getElementById('form-actual-out').classList.remove('hidden');
    document.getElementById('res-kuota').className = person.remaining <= 0 
        ? 'text-5xl font-black text-red-600' 
        : 'text-5xl font-black text-blue-800';
}

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