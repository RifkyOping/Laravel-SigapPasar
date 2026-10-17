<x-app-layout>
    <x-slot name="title">Manajemen Pengguna</x-slot>

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1 style="font-size:1.4rem;font-weight:700;margin:0 0 0.25rem;">Manajemen Akun Pengguna & Peran</h1>
            <p style="color:var(--clr-ink-2);font-size:0.85rem;margin:0;">Kelola hak akses aktor sistem: Pimpinan, Admin, Pengawas, Verifikator, dan Petugas.</p>
        </div>
        <a href="{{ route('users.create') }}" class="sp-btn sp-btn-primary">
            + Tambah Pengguna Baru
        </a>
    </div>

    <div class="sp-card" style="padding:0;overflow:hidden;">
        <div class="sp-table-wrap" style="border:none;border-radius:0;">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>Nama Pengguna</th>
                        <th>Email & NIP</th>
                        <th>Peran (Role)</th>
                        <th>Instansi / Wilayah</th>
                        <th>Telepon</th>
                        <th>Status</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                    <tr>
                        <td class="cell-primary">
                            {{ $u->name }}
                        </td>
                        <td>
                            <div>{{ $u->email }}</div>
                            <small style="color:var(--clr-ink-3);">NIP: {{ $u->nip ?? '-' }}</small>
                        </td>
                        <td>
                            <span class="sp-badge {{ $u->role === 'pimpinan' ? 'badge-intervensi' : (in_array($u->role, ['super_admin','admin']) ? 'badge-ditugaskan' : ($u->role === 'petugas_lapangan' ? 'badge-di_lapangan' : 'badge-baru')) }}">
                                {{ $u->role_label }}
                            </span>
                        </td>
                        <td style="font-size:0.85rem;color:var(--clr-ink-2);">
                            {{ $u->instansi ?? '-' }}
                        </td>
                        <td style="font-size:0.85rem;color:var(--clr-ink-3);">
                            {{ $u->phone ?? '-' }}
                        </td>
                        <td>
                            <span class="sp-badge {{ $u->is_active ? 'badge-selesai' : 'badge-hoaks' }}">
                                {{ $u->is_active ? 'Aktif' : 'Non-aktif' }}
                            </span>
                        </td>
                        <td style="text-align:right;white-space:nowrap;">
                            <a href="{{ route('users.edit', $u) }}" class="sp-btn sp-btn-ghost sp-btn-sm">
                                Edit
                            </a>
                            @if($u->is_active && $u->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $u) }}" style="display:inline;" onsubmit="return confirm('Nonaktifkan pengguna ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="sp-btn sp-btn-danger sp-btn-sm" style="margin-left:0.3rem;">
                                    Nonaktifkan
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:3rem 0;color:var(--clr-ink-3);">
                            Belum ada pengguna terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div style="padding:1rem 1.5rem;border-top:1px solid var(--clr-border);">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
