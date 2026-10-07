<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateWhatsAppTemplateRequest;
use App\Models\WhatsAppTemplate;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppTemplateController extends Controller
{
    public function __construct(private WhatsAppService $whatsAppService)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('whatsapp.template.manage');

        $templates = WhatsAppTemplate::orderByRaw("FIELD(`key`, 'INCIDENT','INVESTIGATING','MAINTENANCE','MONITORING','RESOLVED','CLOSED','CUSTOM')")->get();

        return view('whatsapp-templates.index', compact('templates'));
    }

    public function edit(WhatsAppTemplate $whatsAppTemplate)
    {
        $this->authorize('whatsapp.template.manage');

        return view('whatsapp-templates.edit', ['template' => $whatsAppTemplate]);
    }

    public function update(UpdateWhatsAppTemplateRequest $request, WhatsAppTemplate $whatsAppTemplate)
    {
        $this->whatsAppService->updateTemplate($whatsAppTemplate, [
            'name' => $request->validated()['name'],
            'body' => $request->validated()['body'],
            'is_active' => $request->boolean('is_active'),
        ], $request->user(), $request);

        return redirect()->route('whatsapp-templates.index')->with('status', 'Template berhasil diperbarui.');
    }
}
