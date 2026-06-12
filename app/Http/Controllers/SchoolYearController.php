<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\SchoolYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SchoolYearController extends Controller
{
    private function getInstitutionId(Request $request): int
    {
        $user = $request->user();
        if ($user->hasRole('Super Admin')) {
            return (int) ($request->session()->get('active_tenant_id') ?? abort(403, 'Pilih lembaga aktif terlebih dahulu.'));
        }
        return (int) $user->institution_id;
    }

    public function index(Request $request): View
    {
        $institutionId = $this->getInstitutionId($request);

        $tahunAjaran = SchoolYear::where('institution_id', $institutionId)
            ->orderByDesc('tanggal_mulai')
            ->paginate(15);

        return view('school-years.index', compact('tahunAjaran'));
    }

    public function create(Request $request): View
    {
        $institutionId = $this->getInstitutionId($request);
        return view('school-years.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $institutionId = $this->getInstitutionId($request);

        $data = $request->validate([
            'nama'            => ['required', 'string', 'max:20'],
            'tanggal_mulai'   => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
            'aktif'           => ['boolean'],
        ], [
            'nama.required'            => 'Nama tahun ajaran wajib diisi.',
            'tanggal_mulai.required'   => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after'    => 'Tanggal selesai harus setelah tanggal mulai.',
        ]);

        $data['institution_id'] = $institutionId;
        $data['aktif'] = $request->boolean('aktif');

        DB::transaction(function () use ($data, $institutionId) {
            if ($data['aktif']) {
                SchoolYear::where('institution_id', $institutionId)->update(['aktif' => false]);
            }
            SchoolYear::create($data);
        });

        return redirect()->route('school-years.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function edit(SchoolYear $schoolYear): View
    {
        $this->authorizeYear($schoolYear);
        return view('school-years.edit', compact('schoolYear'));
    }

    public function update(Request $request, SchoolYear $schoolYear): RedirectResponse
    {
        $this->authorizeYear($schoolYear);

        $data = $request->validate([
            'nama'            => ['required', 'string', 'max:20'],
            'tanggal_mulai'   => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
            'aktif'           => ['boolean'],
        ], [
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
        ]);

        $data['aktif'] = $request->boolean('aktif');

        DB::transaction(function () use ($data, $schoolYear) {
            if ($data['aktif']) {
                SchoolYear::where('institution_id', $schoolYear->institution_id)
                    ->where('id', '!=', $schoolYear->id)
                    ->update(['aktif' => false]);
            }
            $schoolYear->update($data);
        });

        return redirect()->route('school-years.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(SchoolYear $schoolYear): RedirectResponse
    {
        $this->authorizeYear($schoolYear);

        if ($schoolYear->aktif) {
            return back()->with('error', 'Tahun ajaran aktif tidak dapat dihapus.');
        }

        $schoolYear->delete();

        return redirect()->route('school-years.index')
            ->with('success', 'Tahun ajaran berhasil dihapus.');
    }

    public function setAktif(SchoolYear $schoolYear): RedirectResponse
    {
        $this->authorizeYear($schoolYear);
        $schoolYear->setAsAktif();

        return back()->with('success', "Tahun ajaran \"{$schoolYear->nama}\" ditetapkan sebagai aktif.");
    }

    private function authorizeYear(SchoolYear $schoolYear): void
    {
        $user = request()->user();
        if (! $user->hasRole('Super Admin') && $schoolYear->institution_id !== $user->institution_id) {
            abort(403);
        }
    }
}
