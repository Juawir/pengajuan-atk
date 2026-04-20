@extends('layouts.app')

@section('title', 'Detail Pinjaman')
@section('subtitle', 'Informasi lengkap pengajuan pinjaman')

@section('content')
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span>/</span>
        <a href="{{ route('pinjaman.index') }}">Pinjaman Barang</a>
        <span>/</span>
        <span class="current">Detail #{{ $pinjaman->id }}</span>
    </div>

    <div style="max-width: 820px;">
        {{-- Status Header --}}
        <div class="glass-card animate-in" style="padding: 22px 28px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;" id="status-header">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 50px; height: 50px; border-radius: var(--radius-md); background: linear-gradient(135deg, rgba(230, 33, 23, 0.15), rgba(13, 229, 255, 0.15)); display: flex; align-items: center; justify-content: center; font-size: 22px; color: var(--accent-primary);">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <div>
                    <div style="font-size: 18px; font-weight: 700; font-family: 'Space Grotesk', 'Inter', sans-serif; color: var(--text-primary);">Pinjaman #{{ $pinjaman->id }}</div>
                    <div style="font-size: 13px; color: var(--text-muted);">Diajukan {{ $pinjaman->created_at->format('d M Y, H:i') }}</div>
                </div>
            </div>
            <div>
                @if($pinjaman->status === 'Pending')
                    <span class="badge badge-pending" style="font-size: 14px; padding: 6px 16px;"><i class="bi bi-clock"></i> Pending</span>
                @elseif($pinjaman->status === 'Disetujui')
                    <span class="badge badge-approved" style="font-size: 14px; padding: 6px 16px;"><i class="bi bi-check-lg"></i> Disetujui</span>
                @else
                    <span class="badge badge-rejected" style="font-size: 14px; padding: 6px 16px;"><i class="bi bi-x-lg"></i> Ditolak</span>
                @endif
            </div>
        </div>

        {{-- Detail Grid --}}
        <div class="glass-card animate-in" style="margin-bottom: 20px;" id="detail-card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-info-circle-fill" style="color: var(--accent-cyan); margin-right: 8px;"></i>
                    Detail Pinjaman
                </div>
            </div>
            <div class="card-body">
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-item-label">Peminjam</div>
                        <div class="detail-item-value">{{ $pinjaman->peminjam_nama }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-item-label">Departemen Asal</div>
                        <div class="detail-item-value">{{ $pinjaman->peminjam_departemen }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-item-label">Departemen Tujuan</div>
                        <div class="detail-item-value" style="color: var(--accent-cyan);">{{ $pinjaman->tujuan_departemen }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-item-label">Nama Barang</div>
                        <div class="detail-item-value">{{ $pinjaman->nama_barang }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-item-label">Jumlah</div>
                        <div class="detail-item-value">{{ $pinjaman->jumlah }} unit</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-item-label">Tanggal Pinjam</div>
                        <div class="detail-item-value">{{ $pinjaman->tanggal_pinjam->format('d M Y') }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-item-label">Tanggal Kembali</div>
                        <div class="detail-item-value">{{ $pinjaman->tanggal_kembali ? $pinjaman->tanggal_kembali->format('d M Y') : 'Belum ditentukan' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-item-label">Direspon Oleh</div>
                        <div class="detail-item-value">{{ $pinjaman->responder ? $pinjaman->responder->name : '-' }}</div>
                    </div>
                </div>

                @if($pinjaman->alasan)
                    <div class="detail-item" style="margin-top: 16px;">
                        <div class="detail-item-label">Alasan Pinjaman</div>
                        <div class="detail-item-value" style="font-size: 14px; font-weight: 400; line-height: 1.6;">{{ $pinjaman->alasan }}</div>
                    </div>
                @endif

                @if($pinjaman->catatan_response)
                    <div class="detail-item" style="margin-top: 16px; border-color: rgba(230, 33, 23, 0.15);">
                        <div class="detail-item-label">Catatan Response</div>
                        <div class="detail-item-value" style="font-size: 14px; font-weight: 400; line-height: 1.6;">{{ $pinjaman->catatan_response }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Action Card (untuk departemen tujuan / admin) --}}
        @php
            $user = auth()->user();
            $canRespond = $user->isAdmin() || $pinjaman->tujuan_departemen === $user->departemen;
        @endphp

        @if($canRespond)
            <div class="glass-card animate-in" id="action-card">
                <div class="card-header">
                    <div class="card-header-title">
                        <i class="bi bi-chat-square-dots-fill" style="color: var(--accent-primary); margin-right: 8px;"></i>
                        Respon Pinjaman
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pinjaman.updateStatus', $pinjaman) }}" id="form-response">
                        @csrf
                        @method('PATCH')

                        <div class="form-group">
                            <label class="form-label" for="catatan_response">Catatan (opsional)</label>
                            <textarea name="catatan_response" id="catatan_response" class="form-control" rows="3" placeholder="Tambahkan catatan respon...">{{ old('catatan_response', $pinjaman->catatan_response) }}</textarea>
                        </div>

                        <div style="display: flex; gap: 10px; justify-content: flex-end;">
                            <button type="submit" name="status" value="Ditolak" class="btn btn-danger" id="btn-reject">
                                <i class="bi bi-x-circle"></i> Tolak
                            </button>
                            <button type="submit" name="status" value="Pending" class="btn btn-warning" id="btn-pending">
                                <i class="bi bi-hourglass-split"></i> Pending
                            </button>
                            <button type="submit" name="status" value="Disetujui" class="btn btn-success" id="btn-approve">
                                <i class="bi bi-check-circle"></i> Setujui
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Back Button --}}
        <div style="margin-top: 20px;">
            <a href="{{ route('pinjaman.index') }}" class="btn btn-ghost">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
@endsection
