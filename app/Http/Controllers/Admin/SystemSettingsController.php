<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SystemSettingsController extends Controller
{
    private function getSettings(string $group): array
    {
        return DB::table('system_settings')
            ->where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }

    private function upsertSettings(string $group, array $values): void
    {
        foreach ($values as $key => $value) {
            DB::table('system_settings')->updateOrInsert(
                ['group' => $group, 'key' => $key],
                ['value' => $value, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    public function general()
    {
        $settings = $this->getSettings('general');

        return view('admin.system_settings.general', compact('settings'));
    }

    public function updateGeneral(Request $request)
    {
        $data = $request->validate([
            'app_name' => ['nullable', 'string', 'max:255'],
            'timezone' => ['nullable', 'string', 'max:255'],
            'default_currency' => ['nullable', 'string', 'max:10'],
        ]);

        $this->upsertSettings('general', $data);

        return redirect()->back();
    }

    public function businessDefaults()
    {
        $settings = $this->getSettings('business_defaults');

        return view('admin.system_settings.business_defaults', compact('settings'));
    }

    public function updateBusinessDefaults(Request $request)
    {
        $data = $request->validate([
            'default_low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'default_payment_method' => ['nullable', 'string', 'max:255'],
        ]);

        $this->upsertSettings('business_defaults', $data);

        return redirect()->back();
    }

    public function email()
    {
        $settings = $this->getSettings('email');

        return view('admin.system_settings.email', compact('settings'));
    }

    public function updateEmail(Request $request)
    {
        $data = $request->validate([
            'from_name' => ['nullable', 'string', 'max:255'],
            'from_address' => ['nullable', 'email', 'max:255'],
            'smtp_host' => ['nullable', 'string', 'max:255'],
            'smtp_port' => ['nullable', 'integer', 'min:1'],
            'smtp_username' => ['nullable', 'string', 'max:255'],
            'smtp_password' => ['nullable', 'string', 'max:255'],
            'smtp_encryption' => ['nullable', 'string', 'max:20'],
        ]);

        $this->upsertSettings('email', $data);

        return redirect()->back();
    }

    public function smsWhatsapp()
    {
        $settings = $this->getSettings('sms_whatsapp');

        return view('admin.system_settings.sms_whatsapp', compact('settings'));
    }

    public function updateSmsWhatsapp(Request $request)
    {
        $data = $request->validate([
            'provider' => ['nullable', 'string', 'max:255'],
            'api_key' => ['nullable', 'string', 'max:255'],
            'sender_id' => ['nullable', 'string', 'max:255'],
            'whatsapp_enabled' => ['nullable'],
        ]);

        $data['whatsapp_enabled'] = isset($data['whatsapp_enabled']) ? '1' : '0';

        $this->upsertSettings('sms_whatsapp', $data);

        return redirect()->back();
    }

    public function backup()
    {
        $settings = $this->getSettings('backup');

        return view('admin.system_settings.backup', compact('settings'));
    }

    public function updateBackup(Request $request)
    {
        $data = $request->validate([
            'backup_enabled' => ['nullable'],
            'backup_schedule' => ['nullable', 'string', 'max:255'],
            'backup_disk' => ['nullable', 'string', 'max:255'],
            'backup_retention_days' => ['nullable', 'integer', 'min:1'],
        ]);

        $data['backup_enabled'] = isset($data['backup_enabled']) ? '1' : '0';

        $this->upsertSettings('backup', $data);

        return redirect()->back();
    }

    public function maintenance()
    {
        $settings = $this->getSettings('maintenance');

        return view('admin.system_settings.maintenance', compact('settings'));
    }

    public function updateMaintenance(Request $request)
    {
        $data = $request->validate([
            'maintenance_enabled' => ['nullable'],
            'maintenance_message' => ['nullable', 'string', 'max:255'],
        ]);

        $data['maintenance_enabled'] = isset($data['maintenance_enabled']) ? '1' : '0';

        $this->upsertSettings('maintenance', $data);

        return redirect()->back();
    }

    public function clearCache(Request $request)
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        return redirect()->back();
    }

    public function health()
    {
        $health = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'env' => config('app.env'),
            'debug' => (bool) config('app.debug'),
            'timezone' => config('app.timezone'),
            'db_connection' => config('database.default'),
            'db_database' => config('database.connections.' . config('database.default') . '.database'),
        ];

        return view('admin.system_settings.health', compact('health'));
    }
}
