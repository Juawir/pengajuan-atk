@extends('layouts.app')

@section('title', 'Ajukan Pinjaman')
@section('subtitle', 'Buat pengajuan pinjaman barang ke departemen lain')

@section('content')
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span>/</span>
        <a href="{{ route('pinjaman.index') }}">Pinjaman Barang</a>
        <span>/</span>
        <span class="current">Ajukan Pinjaman</span>
    </div>

    <div style="max-width: 720px;">
        <div class="glass-card animate-in" id="create-pinjaman-card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-arrow-left-right" style="color: var(--accent-primary); margin-right: 8px;"></i>
                    Form Pinjaman Barang Antar Departemen
                </div>
            </div>
            <div class="card-body">
                {{-- Info --}}
                <div style="padding: 14px 18px; background: rgba(13, 229, 255, 0.06); border: 1px solid rgba(13, 229, 255, 0.12); border-radius: var(--radius-md); margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
                    <i class="bi bi-info-circle-fill" style="color: var(--accent-cyan); font-size: 16px;"></i>
                    <span style="font-size: 13px; color: var(--text-secondary);">Pinjaman akan dikirim ke departemen tujuan. Departemen tujuan dapat menyetujui atau menolak permintaan Anda.</span>
                </div>

                <form method="POST" action="{{ route('pinjaman.store') }}" id="form-pinjaman">
                    @csrf

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Peminjam</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Departemen Asal</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->isAdmin() ? 'Admin' : auth()->user()->departemen }}" disabled>
                            @if(auth()->user()->isAdmin())
                                <input type="hidden" name="peminjam_departemen" value="Admin">
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="tujuan_departemen">
                            <i class="bi bi-building" style="margin-right: 4px;"></i> Departemen Tujuan <span style="color: var(--accent-primary);">*</span>
                        </label>
                        <div class="combobox-wrapper">
                            <input type="text" name="tujuan_departemen" id="tujuan_departemen" class="form-control" list="dept-list" placeholder="Pilih atau ketik departemen tujuan" value="{{ old('tujuan_departemen') }}" required autocomplete="off">
                            <datalist id="dept-list">
                                @foreach($departemenList as $dept)
                                    <option value="{{ $dept }}">
                                @endforeach
                            </datalist>
                        </div>
                        @error('tujuan_departemen')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="nama_barang">
                                <i class="bi bi-box-seam" style="margin-right: 4px;"></i> Nama Barang <span style="color: var(--accent-primary);">*</span>
                            </label>
                            <input type="text" name="nama_barang" id="nama_barang" class="form-control" placeholder="Masukkan nama barang" value="{{ old('nama_barang') }}" required>
                            @error('nama_barang')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="jumlah">
                                <i class="bi bi-123" style="margin-right: 4px;"></i> Jumlah <span style="color: var(--accent-primary);">*</span>
                            </label>
                            <input type="number" name="jumlah" id="jumlah" class="form-control" placeholder="Jumlah" min="1" value="{{ old('jumlah', 1) }}" required>
                            @error('jumlah')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="tanggal_pinjam">
                                <i class="bi bi-calendar-event" style="margin-right: 4px;"></i> Tanggal Pinjam <span style="color: var(--accent-primary);">*</span>
                            </label>
                            <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" class="form-control" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" required>
                            @error('tanggal_pinjam')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="tanggal_kembali">
                                <i class="bi bi-calendar-check" style="margin-right: 4px;"></i> Tanggal Kembali
                            </label>
                            <input type="date" name="tanggal_kembali" id="tanggal_kembali" class="form-control" value="{{ old('tanggal_kembali') }}">
                            @error('tanggal_kembali')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="alasan">
                            <i class="bi bi-chat-left-text" style="margin-right: 4px;"></i> Alasan Pinjaman
                        </label>
                        <textarea name="alasan" id="alasan" class="form-control" rows="3" placeholder="Jelaskan alasan pinjaman (opsional)">{{ old('alasan') }}</textarea>
                        @error('alasan')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 8px;">
                        <a href="{{ route('pinjaman.index') }}" class="btn btn-ghost" id="btn-cancel-pinjaman">
                            <i class="bi bi-x-lg"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary" id="btn-submit-pinjaman">
                            <i class="bi bi-send-fill"></i> Kirim Pinjaman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
