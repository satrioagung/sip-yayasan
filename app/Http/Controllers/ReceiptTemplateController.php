<?php

namespace App\Http\Controllers;

use App\Models\ReceiptTemplate;
use App\Services\ReceiptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReceiptTemplateController extends Controller
{
    public function __construct(private ReceiptService $receiptService) {}

    private function institutionId(Request $request): int
    {
        $user = $request->user();
        if ($user->hasRole('Super Admin')) {
            return (int) ($request->session()->get('active_tenant_id')
                ?? abort(403, 'Pilih lembaga aktif terlebih dahulu.'));
        }
        return (int) $user->institution_id;
    }

    public function index(Request $request): View
    {
        $institutionId = $this->institutionId($request);
        $templates = ReceiptTemplate::forInstitution($institutionId)->orderByDesc('is_default')->orderBy('nama_template')->get();
        return view('receipt-templates.index', compact('templates'));
    }

    public function create(Request $request): View
    {
        return view('receipt-templates.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $institutionId = $this->institutionId($request);
        $data = $this->validatedData($request);
        $data['institution_id'] = $institutionId;

        $template = ReceiptTemplate::create($data);

        if ($data['is_default']) {
            $template->setAsDefault();
        }

        return redirect()->route('receipt-templates.index')
            ->with('success', "Template \"{$template->nama_template}\" berhasil disimpan.");
    }

    public function edit(ReceiptTemplate $receiptTemplate, Request $request): View
    {
        $this->authorizeTemplate($receiptTemplate, $request);
        return view('receipt-templates.edit', compact('receiptTemplate'));
    }

    public function update(ReceiptTemplate $receiptTemplate, Request $request): RedirectResponse
    {
        $this->authorizeTemplate($receiptTemplate, $request);
        $data = $this->validatedData($request);

        $receiptTemplate->update($data);

        if ($data['is_default']) {
            $receiptTemplate->setAsDefault();
        }

        return redirect()->route('receipt-templates.index')
            ->with('success', "Template \"{$receiptTemplate->nama_template}\" berhasil diperbarui.");
    }

    public function destroy(ReceiptTemplate $receiptTemplate, Request $request): RedirectResponse
    {
        $this->authorizeTemplate($receiptTemplate, $request);
        $nama = $receiptTemplate->nama_template;
        $receiptTemplate->delete();
        return redirect()->route('receipt-templates.index')
            ->with('success', "Template \"{$nama}\" berhasil dihapus.");
    }

    /** AJAX Preview template dengan data dummy */
    public function preview(ReceiptTemplate $receiptTemplate, Request $request): View
    {
        $this->authorizeTemplate($receiptTemplate, $request);
        $headerHtml = $this->receiptService->previewTemplate($receiptTemplate->header ?? '');
        $footerHtml = $this->receiptService->previewTemplate($receiptTemplate->footer ?? '');
        return view('receipt-templates.preview', compact('receiptTemplate', 'headerHtml', 'footerHtml'));
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'nama_template' => ['required', 'string', 'max:100'],
            'ukuran'        => ['required', 'in:a4,thermal58,thermal80'],
            'header'        => ['nullable', 'string', 'max:5000'],
            'footer'        => ['nullable', 'string', 'max:5000'],
            'show_logo'     => ['boolean'],
            'show_qr'       => ['boolean'],
            'is_default'    => ['boolean'],
        ], [
            'nama_template.required' => 'Nama template wajib diisi.',
            'ukuran.required'        => 'Ukuran struk wajib dipilih.',
        ]);
    }

    private function authorizeTemplate(ReceiptTemplate $t, Request $request): void
    {
        $user = $request->user();
        if (! $user->hasRole('Super Admin') && $t->institution_id !== (int) $user->institution_id) {
            abort(403);
        }
    }
}
