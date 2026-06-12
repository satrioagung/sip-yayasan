<?php

namespace App\Http\Controllers;

use App\Exports\StudentsExport;
use App\Exports\StudentsTemplateExport;
use App\Imports\StudentsImport;
use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    /** Ambil institution_id user yang login */
    private function institutionId(Request $request): int
    {
        $user = $request->user();
        if ($user->hasRole('Super Admin')) {
            return (int) ($request->session()->get('active_tenant_id') ?? abort(403, 'Pilih lembaga aktif terlebih dahulu.'));
        }
        return (int) $user->institution_id;
    }

    /** Daftar siswa dengan search realtime & filter kelas */
    public function index(Request $request): View
    {
        $institutionId = $this->institutionId($request);

        $siswa = Student::with('class')
            ->forInstitution($institutionId)
            ->when($request->search, function ($q) use ($request) {
                $s = $request->search;
                $q->where(fn ($q2) =>
                    $q2->where('nama_lengkap', 'ilike', "%{$s}%")
                       ->orWhere('nis', 'ilike', "%{$s}%")
                       ->orWhere('nisn', 'ilike', "%{$s}%")
                );
            })
            ->when($request->class_id, fn ($q) => $q->where('class_id', $request->class_id))
            ->when($request->status === 'aktif', fn ($q) => $q->aktif())
            ->when($request->status === 'nonaktif', fn ($q) => $q->where('aktif', false))
            ->orderBy('nama_lengkap')
            ->paginate(20)
            ->withQueryString();

        $kelas = SchoolClass::where('institution_id', $institutionId)
            ->aktif()->orderBy('nama_kelas')->get();

        $totalSiswa   = Student::forInstitution($institutionId)->aktif()->count();
        $totalLaki    = Student::forInstitution($institutionId)->aktif()->where('jenis_kelamin', 'L')->count();
        $totalPerempuan = Student::forInstitution($institutionId)->aktif()->where('jenis_kelamin', 'P')->count();

        return view('students.index', compact('siswa', 'kelas', 'totalSiswa', 'totalLaki', 'totalPerempuan'));
    }

    /** Form tambah siswa */
    public function create(Request $request): View
    {
        $institutionId = $this->institutionId($request);
        $kelas = SchoolClass::where('institution_id', $institutionId)
            ->aktif()->orderBy('nama_kelas')->get();

        return view('students.create', compact('kelas'));
    }

    /** Simpan siswa baru */
    public function store(Request $request): RedirectResponse
    {
        $institutionId = $this->institutionId($request);

        $data = $request->validate([
            'nis'           => ['required', 'string', 'max:20',
                               "unique:students,nis,NULL,id,institution_id,{$institutionId},deleted_at,NULL"],
            'nisn'          => ['nullable', 'string', 'max:10'],
            'nama_lengkap'  => ['required', 'string', 'max:150'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'class_id'      => ['nullable', 'exists:classes,id'],
            'tanggal_lahir' => ['nullable', 'date_format:d/m/Y'],
            'tempat_lahir'  => ['nullable', 'string', 'max:100'],
            'alamat'        => ['nullable', 'string', 'max:500'],
            'no_hp_ortu'    => ['nullable', 'string', 'max:20'],
            'nama_ortu'     => ['nullable', 'string', 'max:100'],
            'aktif'         => ['boolean'],
        ], $this->messages());

        $data['institution_id'] = $institutionId;
        $data['aktif'] = $request->boolean('aktif', true);

        // Parse tanggal lahir dd/mm/yyyy → Y-m-d
        if (! empty($data['tanggal_lahir'])) {
            $data['tanggal_lahir'] = \Carbon\Carbon::createFromFormat('d/m/Y', $data['tanggal_lahir'])->format('Y-m-d');
        }

        Student::create($data);

        return redirect()->route('students.index')
            ->with('success', "Siswa \"{$data['nama_lengkap']}\" berhasil ditambahkan.");
    }

    /** Detail siswa */
    public function show(Student $student): View
    {
        $this->authorizeStudent($student);
        $student->load('class.schoolYear');
        return view('students.show', compact('student'));
    }

    /** Form edit siswa */
    public function edit(Request $request, Student $student): View
    {
        $this->authorizeStudent($student);
        $institutionId = $this->institutionId($request);
        $kelas = SchoolClass::where('institution_id', $institutionId)
            ->aktif()->orderBy('nama_kelas')->get();

        return view('students.edit', compact('student', 'kelas'));
    }

    /** Update data siswa */
    public function update(Request $request, Student $student): RedirectResponse
    {
        $this->authorizeStudent($student);
        $institutionId = $student->institution_id;

        $data = $request->validate([
            'nis'           => ['required', 'string', 'max:20',
                               "unique:students,nis,{$student->id},id,institution_id,{$institutionId},deleted_at,NULL"],
            'nisn'          => ['nullable', 'string', 'max:10'],
            'nama_lengkap'  => ['required', 'string', 'max:150'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'class_id'      => ['nullable', 'exists:classes,id'],
            'tanggal_lahir' => ['nullable', 'date_format:d/m/Y'],
            'tempat_lahir'  => ['nullable', 'string', 'max:100'],
            'alamat'        => ['nullable', 'string', 'max:500'],
            'no_hp_ortu'    => ['nullable', 'string', 'max:20'],
            'nama_ortu'     => ['nullable', 'string', 'max:100'],
            'aktif'         => ['boolean'],
        ], $this->messages());

        $data['aktif'] = $request->boolean('aktif', true);

        if (! empty($data['tanggal_lahir'])) {
            $data['tanggal_lahir'] = \Carbon\Carbon::createFromFormat('d/m/Y', $data['tanggal_lahir'])->format('Y-m-d');
        } else {
            $data['tanggal_lahir'] = null;
        }

        $student->update($data);

        return redirect()->route('students.index')
            ->with('success', "Data siswa \"{$student->nama_lengkap}\" berhasil diperbarui.");
    }

    /** Soft delete siswa */
    public function destroy(Student $student): RedirectResponse
    {
        $this->authorizeStudent($student);
        $nama = $student->nama_lengkap;
        $student->delete();

        return redirect()->route('students.index')
            ->with('success', "Siswa \"{$nama}\" berhasil dihapus.");
    }

    /** Bulk delete siswa terpilih */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $institutionId = $this->institutionId($request);

        $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:students,id'],
        ], [
            'ids.required' => 'Pilih minimal 1 siswa untuk dihapus.',
        ]);

        $jumlah = Student::forInstitution($institutionId)
            ->whereIn('id', $request->ids)
            ->delete();

        return redirect()->route('students.index')
            ->with('success', "{$jumlah} siswa berhasil dihapus.");
    }


    /** Export ke Excel */
    public function export(Request $request)
    {
        $institutionId = $this->institutionId($request);
        $filename = 'data-siswa-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(
            new StudentsExport($institutionId, $request->class_id),
            $filename
        );
    }

    /** Download template Excel */
    public function template(Request $request)
    {
        $institutionId = $this->institutionId($request);
        $isSuperAdmin  = $request->user()->hasRole('Super Admin');

        return Excel::download(
            new StudentsTemplateExport($institutionId, $isSuperAdmin),
            'template-import-siswa.xlsx'
        );
    }

    /** Halaman import Excel */
    public function importForm(Request $request): View
    {
        $institutionId = $this->institutionId($request);
        $kelas = SchoolClass::where('institution_id', $institutionId)
            ->aktif()->orderBy('nama_kelas')->get();
        return view('students.import', compact('kelas'));
    }

    /** Proses import Excel */
    public function importProcess(Request $request): RedirectResponse
    {
        $institutionId = $this->institutionId($request);

        $request->validate([
            'file'     => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
            'class_id' => ['nullable', 'exists:classes,id'],
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes'    => 'File harus berformat XLSX, XLS, atau CSV.',
            'file.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $import = new StudentsImport($institutionId, $request->class_id);
        Excel::import($import, $request->file('file'));

        $berhasil  = count($import->berhasil);
        $diperbarui = count($import->diperbarui);
        $gagal     = count($import->gagal);

        $pesan = "Import selesai: {$berhasil} siswa baru ditambahkan";
        if ($diperbarui > 0) $pesan .= ", {$diperbarui} siswa diperbarui";
        if ($gagal > 0)      $pesan .= ", {$gagal} baris gagal.";
        else                 $pesan .= '.';

        $session = $gagal > 0 ? 'error' : 'success';

        return redirect()->route('students.index')->with($session, $pesan);
    }

    private function authorizeStudent(Student $student): void
    {
        $user = request()->user();
        if (! $user->hasRole('Super Admin') && $student->institution_id !== (int) $user->institution_id) {
            abort(403);
        }
    }

    private function messages(): array
    {
        return [
            'nis.required'           => 'NIS wajib diisi.',
            'nis.unique'             => 'NIS sudah digunakan oleh siswa lain di lembaga ini.',
            'nama_lengkap.required'  => 'Nama lengkap wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in'       => 'Jenis kelamin harus Laki-laki (L) atau Perempuan (P).',
            'tanggal_lahir.date_format' => 'Format tanggal lahir harus dd/mm/yyyy (contoh: 17/08/2009).',
        ];
    }
}
