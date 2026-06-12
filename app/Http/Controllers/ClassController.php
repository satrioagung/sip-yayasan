<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\SchoolYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClassController extends Controller
{
    private function institutionId(Request $request): int
    {
        $user = $request->user();
        if ($user->hasRole('Super Admin')) {
            return (int) ($request->session()->get('active_tenant_id') ?? abort(403));
        }
        return (int) $user->institution_id;
    }

    public function index(Request $request): View
    {
        $institutionId = $this->institutionId($request);

        $kelas = SchoolClass::with('schoolYear')
            ->where('institution_id', $institutionId)
            ->when($request->search, fn ($q) =>
                $q->where('nama_kelas', 'ilike', "%{$request->search}%")
                  ->orWhere('tingkat', 'ilike', "%{$request->search}%")
            )
            ->when($request->tahun_ajaran_id, fn ($q) =>
                $q->where('school_year_id', $request->tahun_ajaran_id)
            )
            ->orderBy('tingkat')->orderBy('nama_kelas')
            ->paginate(20)
            ->withQueryString();

        $tahunAjaran = SchoolYear::where('institution_id', $institutionId)
            ->orderByDesc('tanggal_mulai')->get();

        return view('classes.index', compact('kelas', 'tahunAjaran'));
    }

    public function create(Request $request): View
    {
        $institutionId = $this->institutionId($request);
        $tahunAjaran = SchoolYear::where('institution_id', $institutionId)
            ->orderByDesc('tanggal_mulai')->get();

        return view('classes.create', compact('tahunAjaran'));
    }

    public function store(Request $request): RedirectResponse
    {
        $institutionId = $this->institutionId($request);

        $data = $request->validate([
            'school_year_id' => ['required', 'exists:school_years,id'],
            'nama_kelas'     => ['required', 'string', 'max:50'],
            'tingkat'        => ['required', 'string', 'max:10'],
            'jurusan'        => ['nullable', 'string', 'max:50'],
            'wali_kelas'     => ['nullable', 'string', 'max:100'],
            'aktif'          => ['boolean'],
        ], [
            'school_year_id.required' => 'Tahun ajaran wajib dipilih.',
            'nama_kelas.required'     => 'Nama kelas wajib diisi.',
            'tingkat.required'        => 'Tingkat wajib diisi.',
        ]);

        $data['institution_id'] = $institutionId;
        $data['aktif'] = $request->boolean('aktif', true);

        SchoolClass::create($data);

        return redirect()->route('classes.index')
            ->with('success', "Kelas \"{$data['nama_kelas']}\" berhasil ditambahkan.");
    }

    public function edit(SchoolClass $class): View
    {
        $this->authorize_class($class);
        $tahunAjaran = SchoolYear::where('institution_id', $class->institution_id)
            ->orderByDesc('tanggal_mulai')->get();

        return view('classes.edit', compact('class', 'tahunAjaran'));
    }

    public function update(Request $request, SchoolClass $class): RedirectResponse
    {
        $this->authorize_class($class);

        $data = $request->validate([
            'school_year_id' => ['required', 'exists:school_years,id'],
            'nama_kelas'     => ['required', 'string', 'max:50'],
            'tingkat'        => ['required', 'string', 'max:10'],
            'jurusan'        => ['nullable', 'string', 'max:50'],
            'wali_kelas'     => ['nullable', 'string', 'max:100'],
            'aktif'          => ['boolean'],
        ]);

        $data['aktif'] = $request->boolean('aktif', true);
        $class->update($data);

        return redirect()->route('classes.index')
            ->with('success', "Kelas \"{$class->nama_kelas}\" berhasil diperbarui.");
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        $this->authorize_class($class);
        $nama = $class->nama_kelas;
        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', "Kelas \"{$nama}\" berhasil dihapus.");
    }

    private function authorize_class(SchoolClass $class): void
    {
        $user = request()->user();
        if (! $user->hasRole('Super Admin') && $class->institution_id !== (int) $user->institution_id) {
            abort(403);
        }
    }
}
