<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInstitutionRequest;
use App\Http\Requests\UpdateInstitutionRequest;
use App\Models\Institution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class InstitutionController extends Controller
{
    /**
     * Daftar semua lembaga.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Institution::class);

        $lembaga = Institution::withTrashed()
            ->when($request->search, fn ($q) =>
                $q->where('name', 'ilike', "%{$request->search}%")
                  ->orWhere('code', 'ilike', "%{$request->search}%")
            )
            ->when($request->status === 'aktif',    fn ($q) => $q->aktif()->whereNull('deleted_at'))
            ->when($request->status === 'nonaktif', fn ($q) => $q->where('is_active', false)->whereNull('deleted_at'))
            ->when($request->status === 'dihapus',  fn ($q) => $q->onlyTrashed())
            ->when(! $request->status,              fn ($q) => $q->whereNull('deleted_at')) // default: sembunyikan trashed
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('institutions.index', compact('lembaga'));
    }

    /**
     * Form tambah lembaga.
     */
    public function create(): View
    {
        $this->authorize('create', Institution::class);
        $jenjang = Institution::daftarJenjang();
        return view('institutions.create', compact('jenjang'));
    }

    /**
     * Simpan lembaga baru.
     */
    public function store(StoreInstitutionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('institutions/logos', 'public');
        }

        Institution::create($data);

        return redirect()->route('institutions.index')
            ->with('success', 'Lembaga berhasil ditambahkan.');
    }

    /**
     * Detail lembaga.
     */
    public function show(Institution $institution): View
    {
        // Super Admin bisa lihat semua (ditangani policy before())
        $this->authorize('view', $institution);
        $institution->load('users');
        return view('institutions.show', compact('institution'));
    }

    /**
     * Form edit lembaga.
     */
    public function edit(Institution $institution): View
    {
        $this->authorize('update', $institution);
        $jenjang = Institution::daftarJenjang();
        return view('institutions.edit', compact('institution', 'jenjang'));
    }

    /**
     * Perbarui data lembaga.
     */
    public function update(UpdateInstitutionRequest $request, Institution $institution): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('logo')) {
            if ($institution->logo) {
                Storage::disk('public')->delete($institution->logo);
            }
            $data['logo'] = $request->file('logo')->store('institutions/logos', 'public');
        }

        $institution->update($data);

        return redirect()->route('institutions.index')
            ->with('success', 'Data lembaga berhasil diperbarui.');
    }

    /**
     * Hapus logo lembaga saja.
     */
    public function hapusLogo(Institution $institution): RedirectResponse
    {
        $this->authorize('update', $institution);

        if ($institution->logo) {
            Storage::disk('public')->delete($institution->logo);
            $institution->update(['logo' => null]);
        }

        return back()->with('success', 'Logo lembaga berhasil dihapus.');
    }

    /**
     * Soft delete lembaga.
     */
    public function destroy(Institution $institution): RedirectResponse
    {
        $this->authorize('delete', $institution);

        $institution->delete();

        return redirect()->route('institutions.index')
            ->with('success', "Lembaga \"{$institution->name}\" berhasil dihapus.");
    }

    /**
     * Toggle status aktif/nonaktif lembaga.
     */
    public function toggleAktif(Institution $institution): RedirectResponse
    {
        $this->authorize('update', $institution);

        $institution->update(['is_active' => ! $institution->is_active]);

        $status = $institution->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Lembaga \"{$institution->name}\" berhasil {$status}.");
    }

    /**
     * Pulihkan lembaga yang dihapus.
     * Menggunakan ID biasa (bukan route model binding) agar soft-deleted bisa ditemukan.
     */
    public function pulihkan(string $id): RedirectResponse
    {
        $institution = Institution::withTrashed()->findOrFail((int) $id);
        $this->authorize('restore', $institution);

        $institution->restore();

        return redirect()->route('institutions.index')
            ->with('success', "Lembaga \"{$institution->name}\" berhasil dipulihkan.");
    }
}
