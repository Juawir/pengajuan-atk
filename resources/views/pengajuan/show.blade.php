@extends('layouts.app')

@section('title', 'Detail Pengajuan')
@section('subtitle', 'Detail pengajuan ATK #' . $pengajuan->id)

@section('content')
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span>/</span>
        <a href="{{ route('pengajuan.index') }}">Data Pengajuan</a>
        <span>/</span>
        <span class="current">Detail #{{ $pengajuan->id }}</span>
    </div>

    <div style="max-width: 800px;">
        <div class="glass-card animate-in" id="detail-card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-file-earmark-text-fill" style="color: var(--accent-primary); margin-right: 8px;"></i>
                    Pengajuan #{{ $pengajuan->id }}
                </div>
                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('pengajuan.edit', $pengajuan->id) }}" class="btn btn-warning btn-sm" id="btn-edit-detail">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="{{ route('pengajuan.index') }}" class="btn btn-ghost btn-sm" id="btn-back-detail">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                {{-- Status Banner --}}
                <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 14px; font-weight: 600; color: var(--text-secondary);">Status:</span>
                    @if($pengajuan->status === 'Pending')
                        <span class="badge badge-pending" style="font-size: 14px; padding: 6px 16px;">
                            <i class="bi bi-clock"></i> Menunggu Persetujuan
                        </span>
                    @elseif($pengajuan->status === 'Disetujui')
                        <span class="badge badge-approved" style="font-size: 14px; padding: 6px 16px;">
                            <i class="bi bi-check-circle-fill"></i> Disetujui
                        </span>
                    @else
                        <span class="badge badge-rejected" style="font-size: 14px; padding: 6px 16px;">
                            <i class="bi bi-x-circle-fill"></i> Ditolak
                        </span>
                    @endif
                </div>

                {{-- Detail Grid --}}
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-item-label">
                            <i class="bi bi-person-fill" style="margin-right: 4px;"></i> Nama Pemohon
                        </div>
                        <div class="detail-item-value">{{ $pengajuan->nama_pemohon }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-item-label">
                            <i class="bi bi-building" style="margin-right: 4px;"></i> Departemen
                        </div>
                        <div class="detail-item-value">{{ $pengajuan->departemen }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-item-label">
                            <i class="bi bi-box-seam" style="margin-right: 4px;"></i> Nama Barang
                        </div>
                        <div class="detail-item-value">{{ $pengajuan->nama_barang }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-item-label">
                            <i class="bi bi-hash" style="margin-right: 4px;"></i> Jumlah
                        </div>
                        <div class="detail-item-value">{{ $pengajuan->jumlah }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-item-label">
                            <i class="bi bi-flag-fill" style="margin-right: 4px;"></i> Prioritas
                        </div>
                        <div class="detail-item-value">
                            <span class="badge badge-priority-{{ strtolower($pengajuan->prioritas ?? 'sedang') }}">
                                {{ $pengajuan->prioritas ?? 'Sedang' }}
                            </span>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-item-label">
                            <i class="bi bi-calendar-event" style="margin-right: 4px;"></i> Tanggal Pengajuan
                        </div>
                        <div class="detail-item-value">{{ $pengajuan->created_at->format('d F Y, H:i') }}</div>
                    </div>
                </div>

                @if($pengajuan->keterangan)
                    <div class="detail-item" style="margin-top: 20px;">
                        <div class="detail-item-label">
                            <i class="bi bi-chat-text-fill" style="margin-right: 4px;"></i> Keterangan
                        </div>
                        <div class="detail-item-value" style="font-size: 14px; font-weight: 400; line-height: 1.7;">
                            {{ $pengajuan->keterangan }}
                        </div>
                    </div>
                @endif

                {{-- Timeline --}}
                <div style="margin-top: 28px;">
                    <div style="font-size: 13px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 14px;">
                        <i class="bi bi-clock-history" style="margin-right: 4px;"></i> Timeline
                    </div>
                    <div style="border-left: 2px solid var(--border-color); padding-left: 20px; margin-left: 8px;">
                        <div style="position: relative; padding-bottom: 16px;">
                            <div style="position: absolute; left: -27px; top: 2px; width: 12px; height: 12px; border-radius: 50%; background: var(--accent-primary); border: 2px solid var(--bg-primary);"></div>
                            <div style="font-size: 13px; font-weight: 600; color: var(--text-primary);">Pengajuan Dibuat</div>
                            <div style="font-size: 12px; color: var(--text-muted);">{{ $pengajuan->created_at->format('d F Y, H:i:s') }}</div>
                        </div>
                        @if($pengajuan->updated_at->ne($pengajuan->created_at))
                            <div style="position: relative; padding-bottom: 16px;">
                                <div style="position: absolute; left: -27px; top: 2px; width: 12px; height: 12px; border-radius: 50%; background: var(--accent-emerald); border: 2px solid var(--bg-primary);"></div>
                                <div style="font-size: 13px; font-weight: 600; color: var(--text-primary);">Terakhir Diperbarui</div>
                                <div style="font-size: 12px; color: var(--text-muted);">{{ $pengajuan->updated_at->format('d F Y, H:i:s') }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
