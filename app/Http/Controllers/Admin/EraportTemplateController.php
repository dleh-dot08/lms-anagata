<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EraportTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EraportTemplateController extends Controller
{
    public function index()
    {
        $templates = EraportTemplate::orderByDesc('id')->paginate(20);
        return view('admin.eraport.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.eraport.templates.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:150'],
            'code' => ['nullable','string','max:50','unique:eraport_templates,code'],
            'layout_type' => ['required','in:html,background_overlay'],

            // html mode
            'view_path' => ['nullable','string','max:255'],
            'html' => ['nullable','string'],
            'css' => ['nullable','string'],

            // overlay mode
            'background_file' => ['nullable','file','mimes:png,jpg,jpeg,pdf','max:5120'],
            'background_path' => ['nullable','string','max:255'],

            'field_map' => ['nullable'], // json string / array
            'config' => ['nullable'],    // json string / array
            'jenjang_id' => ['nullable','integer'],
            'is_active' => ['nullable','boolean'],
        ]);

        $data['is_active'] = (bool)($data['is_active'] ?? true);

        // upload background
        if ($request->hasFile('background_file')) {
            $data['background_path'] = $request->file('background_file')
                ->store('eraport/templates', 'public');
        }

        // decode json fields if needed
        foreach (['field_map','config'] as $key) {
            if (isset($data[$key]) && is_string($data[$key])) {
                $decoded = json_decode($data[$key], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $data[$key] = $decoded;
                }
            }
        }

        $template = EraportTemplate::create($data);

        return redirect()
            ->route('admin.eraport.templates.edit', $template)
            ->with('success', 'Template e-raport dibuat.');
    }

    public function edit(EraportTemplate $eraport_template)
    {
        $template = $eraport_template;
        return view('admin.eraport.templates.edit', compact('template'));
    }

    public function update(Request $request, EraportTemplate $eraport_template)
    {
        $template = $eraport_template;

        $data = $request->validate([
            'name' => ['required','string','max:150'],
            'code' => ['nullable','string','max:50','unique:eraport_templates,code,'.$template->id],
            'layout_type' => ['required','in:html,background_overlay'],
            'view_path' => ['nullable','string','max:255'],
            'html' => ['nullable','string'],
            'css' => ['nullable','string'],
            'background_file' => ['nullable','file','mimes:png,jpg,jpeg,pdf','max:5120'],
            'field_map' => ['nullable'],
            'config' => ['nullable'],
            'jenjang_id' => ['nullable','integer'],
            'is_active' => ['nullable','boolean'],
        ]);

        $data['is_active'] = (bool)($data['is_active'] ?? false);

        if ($request->hasFile('background_file')) {
            if ($template->background_path) {
                Storage::disk('public')->delete($template->background_path);
            }
            $data['background_path'] = $request->file('background_file')
                ->store('eraport/templates', 'public');
        }

        foreach (['field_map','config'] as $key) {
            if (isset($data[$key]) && is_string($data[$key])) {
                $decoded = json_decode($data[$key], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $data[$key] = $decoded;
                }
            }
        }

        $template->update($data);

        return back()->with('success', 'Template e-raport diperbarui.');
    }

    public function destroy(EraportTemplate $eraport_template)
    {
        if ($eraport_template->background_path) {
            Storage::disk('public')->delete($eraport_template->background_path);
        }
        $eraport_template->delete();

        return redirect()
            ->route('admin.eraport.templates.index')
            ->with('success', 'Template e-raport dihapus.');
    }
}
