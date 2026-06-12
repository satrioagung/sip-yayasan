@extends('layouts.app')
@section('title', 'Data Siswa')
@section('page-title', 'Data Siswa')
@section('breadcrumb', 'Beranda / Data Master / Data Siswa')

@section('content')
<div class="space-y-5"
     x-data="siswaTable()"
     x-init="init()">

    {{-- ========== STAT CARDS ========== --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($totalSiswa) }}</p>
                <p class="text-xs text-gray-500">Total Siswa Aktif</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($totalLaki) }}</p>
                <p class="text-xs text-gray-500">Laki-laki</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-pink-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($totalPerempuan) }}</p>
                <p class="text-xs text-gray-500">Perempuan</p>
            </div>
        </div>
    </div>

    {{-- ========== TOOLBAR ========== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <div class="flex flex-wrap items-center gap-3">

            {{-- Search --}}
            <div class="relative flex-1 min-w-[220px]">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input type="text"
                       id="search"
                       name="search"
                       placeholder="Cari nama, NIS, atau NISN…"
                       value="{{ request('search') }}"
                       class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       @input.debounce.500ms="doSearch()">
            </div>

            {{-- Filter Kelas --}}
            <select id="filter-kelas"
                    class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    @change="doSearch()">
                <option value="">Semua Kelas</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id }}" {{ request('class_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>

            {{-- Filter Status --}}
            <select id="filter-status"
                    class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    @change="doSearch()">
                <option value="">Semua Status</option>
                <option value="aktif"    {{ request('status') === 'aktif'    ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>

            <div class="ml-auto flex items-center gap-2">
                <a href="{{ route('students.import.form') }}" class="btn-secondary text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Import
                </a>
                <a href="{{ route('students.export', request()->query()) }}" class="btn-secondary text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export
                </a>
                <a href="{{ route('students.create') }}" class="btn-primary text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Siswa
                </a>
            </div>
        </div>

        {{-- Bulk action bar (muncul jika ada yang dipilih) --}}
        <div x-show="selected.length > 0" x-transition
             class="mt-3 flex items-center gap-3 bg-blue-50 border border-blue-200 rounded-xl px-4 py-2.5">
            <span class="text-sm font-semibold text-blue-700" x-text="`${selected.length} siswa dipilih`"></span>
            <button type="button"
                    class="ml-auto flex items-center gap-1.5 text-sm font-medium text-red-600 hover:text-red-800
                           bg-white border border-red-200 rounded-xl px-3 py-1.5 hover:bg-red-50 transition-colors"
                    @click="confirmBulkDelete()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Hapus Terpilih
            </button>
            <button type="button" class="text-sm text-gray-500 hover:text-gray-800" @click="clearSelected()">
                Batalkan
            </button>
        </div>
    </div>

    {{-- ========== HIDDEN BULK DELETE FORM ========== --}}
    <form id="bulk-delete-form" method="POST" action="{{ route('students.bulkDestroy') }}" class="hidden">
        @csrf
        @method('DELETE')
        <template x-for="id in selected" :key="id">
            <input type="hidden" name="ids[]" :value="id">
        </template>
    </form>

    {{-- ========== TABEL SISWA ========== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-4 py-3 w-10">
                            {{-- Select All --}}
                            <input type="checkbox"
                                   class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                                   :checked="isAllSelected()"
                                   :indeterminate="isIndeterminate()"
                                   @change="toggleAll($event.target.checked)">
                        </th>
                        <th class="px-4 py-3">Siswa</th>
                        <th class="px-4 py-3 hidden sm:table-cell">Kelas</th>
                        <th class="px-4 py-3 hidden md:table-cell">Jenis Kelamin</th>
                        <th class="px-4 py-3 hidden lg:table-cell">No. HP Ortu</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($siswa as $s)
                    <tr class="hover:bg-gray-50/60 transition-colors group"
                        :class="selected.includes({{ $s->id }}) ? 'bg-blue-50/60' : ''">

                        {{-- Checkbox --}}
                        <td class="px-4 py-3">
                            <input type="checkbox"
                                   class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                                   :checked="selected.includes({{ $s->id }})"
                                   @change="toggleOne({{ $s->id }}, $event.target.checked)">
                        </td>

                        {{-- Siswa info --}}
                        <td class="px-4 py-3">
                            <a href="{{ route('students.show', $s) }}" class="flex items-center gap-3 group/link">
                                <img src="{{ $s->foto_url }}" alt="{{ $s->nama_lengkap }}"
                                     class="w-9 h-9 rounded-xl object-cover flex-shrink-0 shadow-sm">
                                <div>
                                    <p class="font-semibold text-gray-800 group-hover/link:text-blue-600 transition-colors">
                                        {{ $s->nama_lengkap }}
                                    </p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $s->nis }}</p>
                                </div>
                            </a>
                        </td>

                        {{-- Kelas --}}
                        <td class="px-4 py-3 hidden sm:table-cell">
                            @if($s->class)
                                <span class="text-xs font-medium bg-blue-50 text-blue-700 px-2.5 py-1 rounded-lg">
                                    {{ $s->class->nama_kelas }}
                                </span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>

                        {{-- Jenis Kelamin --}}
                        <td class="px-4 py-3 hidden md:table-cell">
                            @if($s->jenis_kelamin === 'L')
                                <span class="flex items-center gap-1 text-indigo-600">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 9a4 4 0 118 0 4 4 0 01-8 0zm4-7a9 9 0 100 18A9 9 0 0013 2z"/>
                                    </svg>
                                    Laki-laki
                                </span>
                            @else
                                <span class="flex items-center gap-1 text-pink-500">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M11 11a4 4 0 108 0 4 4 0 00-8 0zm4-7a9 9 0 100 18A9 9 0 0015 4z"/>
                                    </svg>
                                    Perempuan
                                </span>
                            @endif
                        </td>

                        {{-- No HP --}}
                        <td class="px-4 py-3 hidden lg:table-cell text-gray-500 font-mono text-xs">
                            {{ $s->no_hp_ortu ?? '—' }}
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3">
                            @if($s->aktif)
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-green-700 bg-green-50 border border-green-100 rounded-full px-2.5 py-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 bg-gray-100 border border-gray-200 rounded-full px-2.5 py-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Nonaktif
                                </span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('students.show', $s) }}"
                                   class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                   title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('students.edit', $s) }}"
                                   class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                   title="Ubah">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('students.destroy', $s) }}"
                                      data-confirm="Hapus siswa &quot;{{ $s->nama_lengkap }}&quot;? Data tidak dapat dikembalikan.">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center">
                                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-700">Belum ada siswa</p>
                                    <p class="text-sm text-gray-400 mt-0.5">Tambah siswa manual atau import dari Excel.</p>
                                </div>
                                <a href="{{ route('students.create') }}" class="btn-primary text-sm mt-1">
                                    + Tambah Siswa Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ========== FOOTER: info + pagination ========== --}}
        @if($siswa->total() > 0)
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-4 border-t border-gray-100 bg-gray-50/50">
            <p class="text-sm text-gray-500">
                Menampilkan <span class="font-semibold text-gray-700">{{ $siswa->firstItem() }}–{{ $siswa->lastItem() }}</span>
                dari <span class="font-semibold text-gray-700">{{ number_format($siswa->total()) }}</span> siswa
            </p>

            {{-- Pagination --}}
            <div class="flex items-center gap-1">
                {{-- Prev --}}
                @if($siswa->onFirstPage())
                    <span class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-xl cursor-not-allowed">‹</span>
                @else
                    <a href="{{ $siswa->previousPageUrl() }}"
                       class="px-3 py-1.5 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 transition-colors">‹</a>
                @endif

                @foreach($siswa->getUrlRange(max(1, $siswa->currentPage()-2), min($siswa->lastPage(), $siswa->currentPage()+2)) as $page => $url)
                    @if($page == $siswa->currentPage())
                        <span class="px-3 py-1.5 text-sm font-semibold bg-blue-600 text-white border border-blue-600 rounded-xl">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                           class="px-3 py-1.5 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 transition-colors">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if($siswa->hasMorePages())
                    <a href="{{ $siswa->nextPageUrl() }}"
                       class="px-3 py-1.5 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 transition-colors">›</a>
                @else
                    <span class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-xl cursor-not-allowed">›</span>
                @endif
            </div>
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
function siswaTable() {
    return {
        selected: [],

        init() {
            // Restore checkbox indeterminate state
            this.$watch('selected', () => {
                const cb = document.querySelector('thead input[type=checkbox]');
                if (cb) cb.indeterminate = this.isIndeterminate();
            });
        },

        toggleAll(checked) {
            const ids = @json($siswa->pluck('id'));
            this.selected = checked ? [...ids] : [];
        },

        toggleOne(id, checked) {
            if (checked) {
                if (! this.selected.includes(id)) this.selected.push(id);
            } else {
                this.selected = this.selected.filter(i => i !== id);
            }
        },

        isAllSelected() {
            const ids = @json($siswa->pluck('id'));
            return ids.length > 0 && ids.every(id => this.selected.includes(id));
        },

        isIndeterminate() {
            const ids = @json($siswa->pluck('id'));
            const sel = ids.filter(id => this.selected.includes(id));
            return sel.length > 0 && sel.length < ids.length;
        },

        clearSelected() {
            this.selected = [];
        },

        async confirmBulkDelete() {
            const n = this.selected.length;
            const result = await SwalKonfirm({
                title: `Hapus ${n} siswa?`,
                text: `Sebanyak ${n} siswa yang dipilih akan dihapus dan tidak dapat dikembalikan.`,
                confirmText: `Ya, Hapus ${n} Siswa`,
                icon: 'warning',
            });
            if (result.isConfirmed) {
                document.getElementById('bulk-delete-form').submit();
            }
        },

        doSearch() {
            const search   = document.getElementById('search').value;
            const classId  = document.getElementById('filter-kelas').value;
            const status   = document.getElementById('filter-status').value;

            const url = new URL(window.location.href);
            url.searchParams.set('search',   search);
            url.searchParams.set('class_id', classId);
            url.searchParams.set('status',   status);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        },
    }
}
</script>
@endpush
