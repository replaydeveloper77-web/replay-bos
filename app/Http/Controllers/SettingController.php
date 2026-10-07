<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use App\Services\AuditService;

class SettingController extends Controller
{
    private const KEYS = [
        'company_name', 'company_address', 'company_phone', 'company_email', 'ppn_rate', 'registration_fee',
        'bank_name', 'bank_account_number', 'bank_account_holder',
        'company_representative_name', 'company_representative_title', 'company_representative_ktp',
        'company_akta_notaris', 'company_sk_menkumham', 'company_business_field',
    ];

    public function __construct(private AuditService $auditService)
    {
    }

    public function edit()
    {
        $this->authorize('settings.manage');

        $settings = collect(self::KEYS)->mapWithKeys(fn ($key) => [$key => Setting::get($key)]);
        $settings['ppn_rate'] = $settings['ppn_rate'] ?? '11';
        $settings['registration_fee'] = $settings['registration_fee'] ?? '0';
        $settings['bank_name'] = $settings['bank_name'] ?? 'BANK BRI';
        $settings['bank_account_number'] = $settings['bank_account_number'] ?? '002801001940304';
        $settings['bank_account_holder'] = $settings['bank_account_holder'] ?? 'REPLAY INTI MEDIA';
        $settings['company_business_field'] = $settings['company_business_field'] ?? 'Internet Service Provider (ISP)';

        return view('settings.edit', compact('settings'));
    }

    public function update(UpdateSettingsRequest $request)
    {
        $old = collect(self::KEYS)->mapWithKeys(fn ($key) => [$key => Setting::get($key)])->all();

        foreach ($request->validated() as $key => $value) {
            Setting::set($key, $value);
        }

        $this->auditService->log(
            actor: $request->user(),
            request: $request,
            module: 'SETTINGS',
            action: 'UPDATE_SETTINGS',
            target: 'Setting',
            oldValue: $old,
            newValue: $request->validated(),
        );

        return redirect()->route('settings.edit')->with('status', 'Pengaturan berhasil disimpan.');
    }
}
