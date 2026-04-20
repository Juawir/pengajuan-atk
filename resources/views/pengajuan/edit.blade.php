@extends('layouts.app')

@section('title', 'Edit Pengajuan')
@section('subtitle', 'Ubah data pengajuan ATK')

@section('content')
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span>/</span>
        <a href="{{ route('pengajuan.index') }}">Data Pengajuan</a>
        <span>/</span>
        <span class="current">Edit Pengajuan</span>
    </div>

    <div style="max-width: 720px;">
        <div class="glass-card animate-in" id="edit-form-card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-pencil-square" style="color: var(--accent-amber); margin-right: 8px;"></i>
                    Edit Pengajuan #{{ $pengajuan->id }}
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('pengajuan.update', $pengajuan->id) }}" id="form-edit">
                    @csrf
                    @method('PUT')

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="nama_pemohon">Nama Pemohon <span style="color: var(--accent-rose);">*</span></label>
                            <input type="text" name="nama_pemohon" id="nama_pemohon" class="form-control" value="{{ old('nama_pemohon', $pengajuan->nama_pemohon) }}" required>
                            @error('nama_pemohon')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="departemen">Departemen <span style="color: var(--accent-primary);">*</span></label>
                            @if(auth()->user()->isAdmin())
                                <div class="combobox-wrapper">
                                    <input type="text" name="departemen" id="departemen" class="form-control" list="departemen-list" placeholder="Pilih atau ketik departemen" value="{{ old('departemen', $pengajuan->departemen) }}" required autocomplete="off">
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

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="nama_barang">Nama Barang <span style="color: var(--accent-primary);">*</span></label>
                            <input type="text" name="nama_barang" id="nama_barang" class="form-control" value="{{ old('nama_barang', $pengajuan->nama_barang) }}" required>
                            @error('nama_barang')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="jumlah">Jumlah <span style="color: var(--accent-primary);">*</span></label>
                            <input type="number" name="jumlah" id="jumlah" class="form-control" value="{{ old('jumlah', $pengajuan->jumlah) }}" min="1" required>
                            @error('jumlah')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="prioritas">Prioritas</label>
                        <select name="prioritas" id="prioritas" class="form-control">
                            @foreach(['Rendah', 'Sedang', 'Tinggi'] as $prio)
                                <option value="{{ $prio }}" {{ old('prioritas', $pengajuan->prioritas) === $prio ? 'selected' : '' }}>{{ $prio }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control">{{ old('keterangan', $pengajuan->keterangan) }}</textarea>
                    </div>

                    <div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 8px;">
                        <a href="{{ route('pengajuan.index') }}" class="btn btn-ghost" id="btn-cancel-edit">
                            <i class="bi bi-x-lg"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary" id="btn-submit-edit">
                            <i class="bi bi-check-lg"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
