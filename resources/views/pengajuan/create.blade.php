@extends('layouts.app')

@section('title', 'Buat Pengajuan')
@section('subtitle', 'Tambahkan pengajuan alat tulis kantor baru')

@section('content')
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span>/</span>
        <a href="{{ route('pengajuan.index') }}">Data Pengajuan</a>
        <span>/</span>
        <span class="current">Buat Pengajuan</span>
    </div>

    <div style="max-width: 820px;">
        <div class="glass-card animate-in" id="create-form-card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-plus-circle-fill" style="color: var(--accent-primary); margin-right: 8px;"></i>
                    Form Pengajuan ATK
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('pengajuan.store') }}" id="form-create" enctype="multipart/form-data">
                    @csrf

                    {{-- Info Pemohon --}}
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="nama_pemohon">Nama Pemohon <span style="color: var(--accent-primary);">*</span></label>
                            <input type="text" name="nama_pemohon" id="nama_pemohon" class="form-control" placeholder="Masukkan nama pemohon" value="{{ old('nama_pemohon', auth()->user()->name) }}" required>
                            @error('nama_pemohon')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="departemen">Departemen <span style="color: var(--accent-primary);">*</span></label>
                            @if(auth()->user()->isAdmin())
                                <div class="combobox-wrapper">
                                    <input type="text" name="departemen" id="departemen" class="form-control" list="departemen-list" placeholder="Pilih atau ketik departemen" value="{{ old('departemen') }}" required autocomplete="off">
                                    <datalist id="departemen-list">
                                        <option value="IT">
                                        <option value="HRD">
                                        <option value="Keuangan">
                                        <option value="Marketing">
                                        <option value="Operasional">
                                        <option value="Umum">
                                    </datalist>
                                </div>
                            @else
                                <input type="text" class="form-control" value="{{ auth()->user()->departemen }}" disabled>
                                <input type="hidden" name="departemen" value="{{ auth()->user()->departemen }}">
                            @endif
                            @error('departemen')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Daftar Barang --}}
                    <div class="form-group">
                        <div class="items-section-header">
                            <label class="form-label" style="margin-bottom: 0;">Daftar Barang <span style="color: var(--accent-primary);">*</span></label>
                            <button type="button" class="btn-add-item" id="btn-add-item" onclick="addItemRow()">
                                <i class="bi bi-plus-lg"></i> Tambah Barang
                            </button>
                        </div>

                        <div id="items-container">
                            {{-- Row 1 (default) --}}
                            <div class="item-row" data-index="0">
                                <div class="item-row-number">1</div>
                                <div class="item-row-fields">
                                    <div class="item-thumb" title="Klik untuk lihat detail">
                                        <i class="bi bi-image" style="font-size: 18px; color: var(--text-muted);"></i>
                                    </div>
                                    <div class="item-field-barang">
                                        <div class="combobox-wrapper">
                                            <input type="text" name="items[0][nama_barang]" class="form-control item-input" list="barang-list" placeholder="Pilih atau ketik nama barang" required autocomplete="off" oninput="updateItemThumb(this)">
                                        </div>
                                        <div class="foto-upload-area" style="display: none;">
                                            <input type="file" name="items[0][foto]" accept="image/*" class="foto-file-input" id="foto-input-0" onchange="previewFoto(this)" style="display: none;">
                                            <button type="button" class="btn-upload-foto" onclick="this.previousElementSibling.click()">
                                                <i class="bi bi-camera-fill"></i> Upload Foto
                                            </button>
                                            <div class="foto-preview-wrapper" style="display: none;">
                                                <img class="foto-preview-img" src="" alt="Preview">
                                                <button type="button" class="foto-remove-btn" onclick="removeFotoPreview(this)" title="Hapus foto">&times;</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item-field-jumlah">
                                        <input type="number" name="items[0][jumlah]" class="form-control" placeholder="Jumlah" min="1" value="1" required>
                                    </div>
                                    <button type="button" class="btn-remove-item" onclick="removeItemRow(this)" title="Hapus barang" disabled>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Image Lightbox Modal --}}
                        <div class="item-lightbox" id="itemLightbox" onclick="closeLightbox()">
                            <div class="item-lightbox-content" onclick="event.stopPropagation()">
                                <button type="button" class="item-lightbox-close" onclick="closeLightbox()">&times;</button>
                                <img id="lightboxImage" src="" alt="Detail Barang">
                                <div class="item-lightbox-caption" id="lightboxCaption"></div>
                            </div>
                        </div>

                        {{-- Single shared datalist for all item inputs --}}
                        <datalist id="barang-list"></datalist>

                        @error('items')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                        @error('items.*.nama_barang')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="prioritas">Prioritas</label>
                            <select name="prioritas" id="prioritas" class="form-control">
                                <option value="Rendah" {{ old('prioritas') === 'Rendah' ? 'selected' : '' }}>Rendah</option>
                                <option value="Sedang" {{ old('prioritas', 'Sedang') === 'Sedang' ? 'selected' : '' }}>Sedang</option>
                                <option value="Tinggi" {{ old('prioritas') === 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="keterangan">Keterangan</label>
                            <input type="text" name="keterangan" id="keterangan" class="form-control" placeholder="Keterangan tambahan (opsional)" value="{{ old('keterangan') }}">
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 8px;">
                        <a href="{{ route('pengajuan.index') }}" class="btn btn-ghost" id="btn-cancel-create">
                            <i class="bi bi-x-lg"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary" id="btn-submit-create">
                            <i class="bi bi-send-fill"></i> Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Daftar Barang ATK
    const daftarBarang = [
        // === KERTAS ===
        'Kertas HVS A4 70gr (1 Rim)',
        'Kertas HVS A4 80gr (1 Rim)',
        'Kertas HVS F4/Folio 70gr (1 Rim)',
        'Kertas HVS F4/Folio 80gr (1 Rim)',
        'Kertas Buram (1 Rim)',
        'Kertas Foto Glossy A4',
        'Kertas Foto Glossy 4R',
        'Kertas Continuous Form 9.5 x 11',
        'Kertas Kalkir A4',
        'Kertas Karbon',
        'Kertas Amplop Putih Polos',
        'Kertas Amplop Coklat Besar',
        'Kertas Amplop Coklat Kecil',
        'Kertas Label Stiker A4',
        'Kertas NCR/Rangkap 2 Ply',
        'Kertas NCR/Rangkap 3 Ply',

        // === ALAT TULIS ===
        'Pulpen Hitam',
        'Pulpen Biru',
        'Pulpen Merah',
        'Pulpen Pilot G-2 Hitam',
        'Pulpen Pilot G-2 Biru',
        'Pulpen Pilot G-2 Merah',
        'Pulpen Snowman Hitam',
        'Pulpen Standard AE7 Hitam',
        'Pulpen Standard AE7 Biru',
        'Pensil 2B',
        'Pensil HB',
        'Pensil Mekanik 0.5mm',
        'Pensil Mekanik 0.7mm',
        'Isi Pensil Mekanik 0.5mm',
        'Isi Pensil Mekanik 0.7mm',
        'Penghapus Pensil',
        'Penghapus Papan Tulis',

        // === SPIDOL & MARKER ===
        'Spidol Whiteboard Hitam',
        'Spidol Whiteboard Biru',
        'Spidol Whiteboard Merah',
        'Spidol Whiteboard Hijau',
        'Spidol Permanen Hitam',
        'Spidol Permanen Biru',
        'Spidol Permanen Merah',
        'Spidol Snowman Besar Hitam',
        'Spidol Snowman Besar Biru',
        'Spidol Kecil/OHP Hitam',
        'Highlighter Kuning',
        'Highlighter Hijau',
        'Highlighter Pink',
        'Highlighter Biru',
        'Highlighter Set (5 Warna)',
        'Marker Kabel / Label',

        // === KOREKSI ===
        'Tip-Ex / Correction Pen',
        'Correction Tape',
        'Correction Tape Refill',

        // === LEM & PEREKAT ===
        'Lem Kertas / Glue Stick Besar',
        'Lem Kertas / Glue Stick Kecil',
        'Lem Fox Putih 150gr',
        'Lem Fox Putih 350gr',
        'Lem Cair / Lem UHU',
        'Lem Tembak (Glue Gun)',
        'Isi Lem Tembak (Glue Stick)',
        'Double Tape Kecil',
        'Double Tape Besar',
        'Double Tape Busa / Foam',
        'Selotip / Lakban Bening Kecil',
        'Selotip / Lakban Bening Besar',
        'Lakban Coklat / OPP Tape',
        'Lakban Hitam / Isolasi',
        'Isolasi Kertas / Masking Tape',
        'Dispenser Selotip',

        // === PENJEPIT & STAPLER ===
        'Stapler Kecil (HD-10)',
        'Stapler Besar (HD-50)',
        'Isi Staples No.10',
        'Isi Staples No.3',
        'Isi Staples 23/6',
        'Isi Staples 23/10',
        'Isi Staples 23/13',
        'Staple Remover / Pembuka Staples',
        'Binder Clip No.107 (Kecil)',
        'Binder Clip No.155 (Sedang)',
        'Binder Clip No.200 (Besar)',
        'Paper Clip Kecil (1 Box)',
        'Paper Clip Besar (1 Box)',
        'Penjepit Kertas / Jepitan',
        'Push Pin / Paku Payung (1 Box)',

        // === GUNTING, CUTTER & PENGGARIS ===
        'Gunting Besar',
        'Gunting Kecil',
        'Cutter Besar',
        'Cutter Kecil',
        'Isi Cutter Besar',
        'Isi Cutter Kecil',
        'Penggaris Besi 30cm',
        'Penggaris Plastik 30cm',
        'Penggaris Plastik 50cm',

        // === MAP, ORDNER & ARSIP ===
        'Map Plastik Kancing (F4)',
        'Map Plastik L Transparan (F4)',
        'Map Kertas (1 Pack)',
        'Map Snelhecter Plastik',
        'Map Snelhecter Kertas',
        'Ordner/Map Besar Tebal',
        'Clear Holder 20 Lembar',
        'Clear Holder 40 Lembar',
        'Clear Holder 60 Lembar',
        'Display Book 20 Pocket',
        'Display Book 40 Pocket',
        'Buku Ekspedisi',
        'Stopmap Folio',
        'Box File / Kotak Arsip',
        'Hanging Folder',

        // === BUKU & CATATAN ===
        'Buku Tulis A5 (1 Lusin)',
        'Buku Folio Bergaris 100 Lembar',
        'Buku Folio Polos 100 Lembar',
        'Buku Agenda / Diary',
        'Sticky Notes / Post-it Kecil',
        'Sticky Notes / Post-it Sedang',
        'Sticky Notes / Post-it Besar',
        'Sticky Notes Warna (Set 5)',
        'Sticky Notes Transparan',
        'Index Tab / Pembatas Warna',
        'Sticky Bookmark / Flag Warna',
        'White Board Kecil (60x90)',
        'White Board Besar (90x120)',

        // === TINTA & CARTRIDGE ===
        'Tinta Printer Canon Hitam',
        'Tinta Printer Canon Warna (C/M/Y)',
        'Tinta Printer Epson Hitam',
        'Tinta Printer Epson Warna (C/M/Y)',
        'Tinta Printer HP Hitam',
        'Tinta Printer HP Warna',
        'Toner Printer LaserJet Hitam',
        'Toner Printer LaserJet Warna',
        'Cartridge Printer Canon',
        'Cartridge Printer HP',
        'Pita Printer Dot Matrix',

        // === MEDIA PENYIMPANAN ===
        'Flash Disk 8GB',
        'Flash Disk 16GB',
        'Flash Disk 32GB',
        'Flash Disk 64GB',
        'CD-R Kosong (1 Pack)',
        'DVD-R Kosong (1 Pack)',
        'CD/DVD Case / Tempat CD',

        // === PERALATAN LAIN ===
        'Kalkulator 12 Digit',
        'Kalkulator Printing',
        'Bantalan Stempel / Bak Stempel',
        'Tinta Stempel Hitam',
        'Tinta Stempel Biru',
        'Tinta Stempel Merah',
        'Stempel Tanggal / Dater',
        'Numerator / Stempel Nomor',
        'Karet Gelang (1 Pack)',
        'Tali Rafia',
        'Tempat Pensil / Desk Organizer',
        'Letter Tray / Bak Surat',
        'Paper Tray 3 Susun',
        'Tempat Kartu Nama',
        'Mouse Pad',
        'Tas Dokumen / Document Bag',
        'ID Card Holder / Name Tag',
        'Tali ID Card',
        'Badge Reel / Yoyo ID Card',
        'Jam Dinding',
        'Baterai AA (1 Pack)',
        'Baterai AAA (1 Pack)',
    ];

    let itemIndex = 1;

    // === IMAGE MAPPING BY CATEGORY ===
    const imageMap = {
        kertas:     { icon: '📄', img: 'https://images.unsplash.com/photo-1586953208270-767889fa9b0e?w=400&h=300&fit=crop', label: 'Kertas / Paper' },
        amplop:     { icon: '✉️', img: 'https://images.unsplash.com/photo-1579547944212-c4f4961a8dd8?w=400&h=300&fit=crop', label: 'Amplop / Envelope' },
        pulpen:     { icon: '🖊️', img: 'https://images.unsplash.com/photo-1585336261022-680e295ce3fe?w=400&h=300&fit=crop', label: 'Pulpen / Pen' },
        pensil:     { icon: '✏️', img: 'https://images.unsplash.com/photo-1513542789411-b6a5d4f31634?w=400&h=300&fit=crop', label: 'Pensil / Pencil' },
        spidol:     { icon: '🖍️', img: 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?w=400&h=300&fit=crop', label: 'Spidol / Marker' },
        highlighter: { icon: '🔆', img: 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?w=400&h=300&fit=crop', label: 'Highlighter' },
        tipex:      { icon: '🔧', img: 'https://images.unsplash.com/photo-1583485088034-697b5bc54ccd?w=400&h=300&fit=crop', label: 'Tip-Ex / Correction' },
        correction: { icon: '🔧', img: 'https://images.unsplash.com/photo-1583485088034-697b5bc54ccd?w=400&h=300&fit=crop', label: 'Correction Tape' },
        lem:        { icon: '🧴', img: 'https://images.unsplash.com/photo-1611229461650-6af4e3e29bf0?w=400&h=300&fit=crop', label: 'Lem / Glue' },
        selotip:    { icon: '📦', img: 'https://images.unsplash.com/photo-1611229461650-6af4e3e29bf0?w=400&h=300&fit=crop', label: 'Selotip / Tape' },
        lakban:     { icon: '📦', img: 'https://images.unsplash.com/photo-1611229461650-6af4e3e29bf0?w=400&h=300&fit=crop', label: 'Lakban / Tape' },
        stapler:    { icon: '📎', img: 'https://images.unsplash.com/photo-1568738351265-c29a1e04e12d?w=400&h=300&fit=crop', label: 'Stapler' },
        staples:    { icon: '📎', img: 'https://images.unsplash.com/photo-1568738351265-c29a1e04e12d?w=400&h=300&fit=crop', label: 'Isi Staples' },
        binder:     { icon: '📎', img: 'https://images.unsplash.com/photo-1568738351265-c29a1e04e12d?w=400&h=300&fit=crop', label: 'Binder Clip' },
        clip:       { icon: '📎', img: 'https://images.unsplash.com/photo-1568738351265-c29a1e04e12d?w=400&h=300&fit=crop', label: 'Paper Clip' },
        gunting:    { icon: '✂️', img: 'https://images.unsplash.com/photo-1590257003876-39e3e0ee9d4a?w=400&h=300&fit=crop', label: 'Gunting / Scissors' },
        cutter:     { icon: '🔪', img: 'https://images.unsplash.com/photo-1590257003876-39e3e0ee9d4a?w=400&h=300&fit=crop', label: 'Cutter' },
        penggaris:  { icon: '📏', img: 'https://images.unsplash.com/photo-1590257003876-39e3e0ee9d4a?w=400&h=300&fit=crop', label: 'Penggaris / Ruler' },
        map:        { icon: '📁', img: 'https://images.unsplash.com/photo-1614036417651-efe5912149d8?w=400&h=300&fit=crop', label: 'Map / Folder' },
        ordner:     { icon: '📁', img: 'https://images.unsplash.com/photo-1614036417651-efe5912149d8?w=400&h=300&fit=crop', label: 'Ordner' },
        clear:      { icon: '📁', img: 'https://images.unsplash.com/photo-1614036417651-efe5912149d8?w=400&h=300&fit=crop', label: 'Clear Holder' },
        buku:       { icon: '📒', img: 'https://images.unsplash.com/photo-1531988042231-d39a9cc12a9a?w=400&h=300&fit=crop', label: 'Buku / Notebook' },
        sticky:     { icon: '📝', img: 'https://images.unsplash.com/photo-1531988042231-d39a9cc12a9a?w=400&h=300&fit=crop', label: 'Sticky Notes' },
        tinta:      { icon: '🖨️', img: 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?w=400&h=300&fit=crop', label: 'Tinta Printer' },
        toner:      { icon: '🖨️', img: 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?w=400&h=300&fit=crop', label: 'Toner Printer' },
        cartridge:  { icon: '🖨️', img: 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?w=400&h=300&fit=crop', label: 'Cartridge' },
        flash:      { icon: '💾', img: 'https://images.unsplash.com/photo-1618410320928-25228d811631?w=400&h=300&fit=crop', label: 'Flash Disk / USB' },
        kalkulator: { icon: '🧮', img: 'https://images.unsplash.com/photo-1611532736597-de2d4265fba3?w=400&h=300&fit=crop', label: 'Kalkulator' },
        stempel:    { icon: '📌', img: 'https://images.unsplash.com/photo-1583485088034-697b5bc54ccd?w=400&h=300&fit=crop', label: 'Stempel / Stamp' },
        baterai:    { icon: '🔋', img: 'https://images.unsplash.com/photo-1619641805634-3919a96c4464?w=400&h=300&fit=crop', label: 'Baterai / Battery' },
        whiteboard: { icon: '📋', img: 'https://images.unsplash.com/photo-1532619675605-1ede6c2ed2b0?w=400&h=300&fit=crop', label: 'White Board' },
    };

    // === CUSTOM BARANG (dari pengajuan yang disetujui) ===
    const customBarang = @json($customBarang ?? []);
    const customImageMap = {};

    customBarang.forEach(item => {
        // Tambahkan ke daftarBarang jika belum ada
        if (!daftarBarang.some(b => b.toLowerCase() === item.nama_barang.toLowerCase())) {
            daftarBarang.push(item.nama_barang);
        }
        // Tambahkan foto ke custom image map
        if (item.foto_path) {
            customImageMap[item.nama_barang.toLowerCase()] = {
                icon: '📦',
                img: '/storage/' + item.foto_path,
                label: item.nama_barang,
            };
        }
    });

    // Sort ulang daftar barang agar rapi
    daftarBarang.sort((a, b) => a.localeCompare(b, 'id'));

    function getItemImage(name) {
        if (!name) return null;
        const lower = name.toLowerCase();

        // Cek imageMap kategori bawaan dulu
        for (const [keyword, data] of Object.entries(imageMap)) {
            if (lower.includes(keyword)) return data;
        }

        // Cek customImageMap (dari pengajuan disetujui)
        for (const [itemName, data] of Object.entries(customImageMap)) {
            if (lower === itemName) return data;
        }

        return null;
    }

    function updateItemThumb(input) {
        const row = input.closest('.item-row');
        const thumb = row.querySelector('.item-thumb');
        const uploadArea = row.querySelector('.foto-upload-area');
        const data = getItemImage(input.value);
        const isInList = input.value && daftarBarang.some(b => b.toLowerCase() === input.value.toLowerCase());

        if (data) {
            thumb.innerHTML = `<img src="${data.img}" alt="${data.label}" loading="lazy">`;
            thumb.classList.add('has-image');
            thumb.onclick = () => openLightbox(data.img, data.label);
            // Hide upload area for listed items
            if (uploadArea) uploadArea.style.display = 'none';
        } else {
            // Check if there's a manually uploaded photo preview
            const previewWrapper = uploadArea ? uploadArea.querySelector('.foto-preview-wrapper') : null;
            const hasManualPhoto = previewWrapper && previewWrapper.style.display !== 'none';

            if (hasManualPhoto) {
                const previewImg = previewWrapper.querySelector('.foto-preview-img');
                thumb.innerHTML = `<img src="${previewImg.src}" alt="Preview" loading="lazy">`;
                thumb.classList.add('has-image');
                thumb.onclick = () => openLightbox(previewImg.src, input.value || 'Barang Manual');
            } else {
                thumb.innerHTML = `<i class="bi bi-image" style="font-size: 18px; color: var(--text-muted);"></i>`;
                thumb.classList.remove('has-image');
                thumb.onclick = null;
            }

            // Show upload area for manual input (not empty, not in list)
            if (uploadArea && input.value.trim().length > 0 && !isInList) {
                uploadArea.style.display = 'flex';
            } else if (uploadArea) {
                uploadArea.style.display = 'none';
            }
        }
    }

    function previewFoto(fileInput) {
        const row = fileInput.closest('.item-row');
        const previewWrapper = row.querySelector('.foto-preview-wrapper');
        const previewImg = row.querySelector('.foto-preview-img');
        const uploadBtn = row.querySelector('.btn-upload-foto');
        const thumb = row.querySelector('.item-thumb');
        const nameInput = row.querySelector('.item-input');

        if (fileInput.files && fileInput.files[0]) {
            const file = fileInput.files[0];

            // Validate file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran foto maksimal 2MB!');
                fileInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewWrapper.style.display = 'flex';
                uploadBtn.style.display = 'none';

                // Update thumbnail
                thumb.innerHTML = `<img src="${e.target.result}" alt="Preview" loading="lazy">`;
                thumb.classList.add('has-image');
                thumb.onclick = () => openLightbox(e.target.result, nameInput.value || 'Barang Manual');
            };
            reader.readAsDataURL(file);
        }
    }

    function removeFotoPreview(btn) {
        const row = btn.closest('.item-row');
        const previewWrapper = row.querySelector('.foto-preview-wrapper');
        const previewImg = row.querySelector('.foto-preview-img');
        const uploadBtn = row.querySelector('.btn-upload-foto');
        const fileInput = row.querySelector('.foto-file-input');
        const thumb = row.querySelector('.item-thumb');

        previewWrapper.style.display = 'none';
        previewImg.src = '';
        uploadBtn.style.display = 'inline-flex';
        fileInput.value = '';

        // Reset thumbnail
        thumb.innerHTML = `<i class="bi bi-image" style="font-size: 18px; color: var(--text-muted);"></i>`;
        thumb.classList.remove('has-image');
        thumb.onclick = null;
    }

    function openLightbox(imgSrc, caption) {
        document.getElementById('lightboxImage').src = imgSrc;
        document.getElementById('lightboxCaption').textContent = caption;
        document.getElementById('itemLightbox').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        document.getElementById('itemLightbox').classList.remove('active');
        document.body.style.overflow = '';
    }

    // Close lightbox with Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeLightbox();
    });

    // Populate the shared datalist
    function populateDatalist() {
        const datalist = document.getElementById('barang-list');
        const fragment = document.createDocumentFragment();

        daftarBarang.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item;
            fragment.appendChild(opt);
        });

        datalist.innerHTML = '';
        datalist.appendChild(fragment);
    }

    // Initialize datalist
    populateDatalist();

    function addItemRow() {
        const container = document.getElementById('items-container');
        const rowCount = container.querySelectorAll('.item-row').length;

        const row = document.createElement('div');
        row.className = 'item-row item-row-enter';
        row.setAttribute('data-index', itemIndex);
        row.innerHTML = `
            <div class="item-row-number">${rowCount + 1}</div>
            <div class="item-row-fields">
                <div class="item-thumb" title="Klik untuk lihat detail">
                    <i class="bi bi-image" style="font-size: 18px; color: var(--text-muted);"></i>
                </div>
                <div class="item-field-barang">
                    <div class="combobox-wrapper">
                        <input type="text" name="items[${itemIndex}][nama_barang]" class="form-control item-input" list="barang-list" placeholder="Pilih atau ketik nama barang" required autocomplete="off" oninput="updateItemThumb(this)">
                    </div>
                    <div class="foto-upload-area" style="display: none;">
                        <input type="file" name="items[${itemIndex}][foto]" accept="image/*" class="foto-file-input" id="foto-input-${itemIndex}" onchange="previewFoto(this)" style="display: none;">
                        <button type="button" class="btn-upload-foto" onclick="this.previousElementSibling.click()">
                            <i class="bi bi-camera-fill"></i> Upload Foto
                        </button>
                        <div class="foto-preview-wrapper" style="display: none;">
                            <img class="foto-preview-img" src="" alt="Preview">
                            <button type="button" class="foto-remove-btn" onclick="removeFotoPreview(this)" title="Hapus foto">&times;</button>
                        </div>
                    </div>
                </div>
                <div class="item-field-jumlah">
                    <input type="number" name="items[${itemIndex}][jumlah]" class="form-control" placeholder="Jumlah" min="1" value="1" required>
                </div>
                <button type="button" class="btn-remove-item" onclick="removeItemRow(this)" title="Hapus barang">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;

        container.appendChild(row);

        // Animate in
        requestAnimationFrame(() => row.classList.remove('item-row-enter'));

        itemIndex++;
        updateRowNumbers();
        updateRemoveButtons();

        // Scroll to new row
        row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function removeItemRow(btn) {
        const row = btn.closest('.item-row');
        row.classList.add('item-row-exit');
        setTimeout(() => {
            row.remove();
            updateRowNumbers();
            updateRemoveButtons();
        }, 250);
    }

    function updateRowNumbers() {
        const rows = document.querySelectorAll('#items-container .item-row');
        rows.forEach((row, i) => {
            row.querySelector('.item-row-number').textContent = i + 1;
        });
    }

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('#items-container .item-row');
        rows.forEach((row, i) => {
            const btn = row.querySelector('.btn-remove-item');
            btn.disabled = rows.length <= 1;
        });
    }
</script>
@endsection
