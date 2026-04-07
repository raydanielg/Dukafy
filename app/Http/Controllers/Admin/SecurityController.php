<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SecurityController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function loginSecurity()
    {
        $settings = DB::table('security_settings')->where('id', 1)->first();

        return view('admin.security.login_security', compact('settings'));
    }

    public function updateLoginSecurity(Request $request)
    {
        $data = $request->validate([
            'two_factor_enabled' => ['nullable'],
            'session_timeout_minutes' => ['nullable', 'integer', 'min:5'],
            'max_failed_attempts' => ['required', 'integer', 'min:1'],
            'lockout_minutes' => ['required', 'integer', 'min:1'],
        ]);

        DB::table('security_settings')->where('id', 1)->update([
            'two_factor_enabled' => isset($data['two_factor_enabled']),
            'session_timeout_minutes' => $data['session_timeout_minutes'] ?? null,
            'max_failed_attempts' => $data['max_failed_attempts'],
            'lockout_minutes' => $data['lockout_minutes'],
            'updated_at' => now(),
        ]);

        return redirect()->back();
    }

    public function passwordPolicy()
    {
        $settings = DB::table('security_settings')->where('id', 1)->first();

        return view('admin.security.password_policy', compact('settings'));
    }

    public function updatePasswordPolicy(Request $request)
    {
        $data = $request->validate([
            'password_min_length' => ['required', 'integer', 'min:6', 'max:64'],
            'password_require_uppercase' => ['nullable'],
            'password_require_lowercase' => ['nullable'],
            'password_require_number' => ['nullable'],
            'password_require_symbol' => ['nullable'],
            'password_expire_days' => ['nullable', 'integer', 'min:1'],
            'password_history_count' => ['nullable', 'integer', 'min:1'],
        ]);

        DB::table('security_settings')->where('id', 1)->update([
            'password_min_length' => $data['password_min_length'],
            'password_require_uppercase' => isset($data['password_require_uppercase']),
            'password_require_lowercase' => isset($data['password_require_lowercase']),
            'password_require_number' => isset($data['password_require_number']),
            'password_require_symbol' => isset($data['password_require_symbol']),
            'password_expire_days' => $data['password_expire_days'] ?? null,
            'password_history_count' => $data['password_history_count'] ?? null,
            'updated_at' => now(),
        ]);

        return redirect()->back();
    }

    public function ipWhitelisting()
    {
        $ips = DB::table('ip_whitelists')->orderByDesc('id')->get();

        return view('admin.security.ip_whitelisting', compact('ips'));
    }

    public function addIpWhitelist(Request $request)
    {
        $data = $request->validate([
            'ip_address' => ['required', 'string', 'max:45'],
            'label' => ['nullable', 'string', 'max:255'],
        ]);

        DB::table('ip_whitelists')->updateOrInsert(
            ['ip_address' => $data['ip_address']],
            ['label' => $data['label'] ?? null, 'enabled' => true, 'updated_at' => now(), 'created_at' => now()]
        );

        return redirect()->back();
    }

    public function blockedIps()
    {
        $ips = DB::table('blocked_ips')->orderByDesc('id')->get();

        return view('admin.security.blocked_ips', compact('ips'));
    }

    public function blockIp(Request $request)
    {
        $data = $request->validate([
            'ip_address' => ['required', 'string', 'max:45'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        DB::table('blocked_ips')->updateOrInsert(
            ['ip_address' => $data['ip_address']],
            ['reason' => $data['reason'] ?? null, 'updated_at' => now(), 'created_at' => now()]
        );

        return redirect()->back();
    }

    public function databaseEncryption()
    {
        $settings = DB::table('security_settings')->where('id', 1)->first();

        return view('admin.security.database_encryption', compact('settings'));
    }

    public function updateDatabaseEncryption(Request $request)
    {
        $data = $request->validate([
            'database_encryption_enabled' => ['nullable'],
        ]);

        DB::table('security_settings')->where('id', 1)->update([
            'database_encryption_enabled' => isset($data['database_encryption_enabled']),
            'updated_at' => now(),
        ]);

        return redirect()->back();
    }

    public function backupSecurity()
    {
        $settings = DB::table('security_settings')->where('id', 1)->first();

        return view('admin.security.backup_security', compact('settings'));
    }

    public function updateBackupSecurity(Request $request)
    {
        $data = $request->validate([
            'backup_encryption_enabled' => ['nullable'],
            'backup_password' => ['nullable', 'string', 'max:255'],
        ]);

        DB::table('security_settings')->where('id', 1)->update([
            'backup_encryption_enabled' => isset($data['backup_encryption_enabled']),
            'backup_password' => $data['backup_password'] ?? null,
            'updated_at' => now(),
        ]);

        return redirect()->back();
    }

    public function auditLog()
    {
        $logs = DB::table('admin_activity_logs')
            ->leftJoin('users', 'users.id', '=', 'admin_activity_logs.user_id')
            ->select('admin_activity_logs.*', 'users.name as user_name', 'users.email as user_email')
            ->orderByDesc('admin_activity_logs.id')
            ->paginate(15);

        return view('admin.security.audit_log', compact('logs'));
    }

    public function sessionManagement()
    {
        $sessions = DB::table('sessions')
            ->leftJoin('users', 'users.id', '=', 'sessions.user_id')
            ->select('sessions.*', 'users.name as user_name', 'users.email as user_email')
            ->orderByDesc('last_activity')
            ->paginate(15);

        return view('admin.security.session_management', compact('sessions'));
    }

    public function apiSecurity()
    {
        $keys = DB::table('api_keys')->orderByDesc('id')->get();

        return view('admin.security.api_security', compact('keys'));
    }

    public function createApiKey(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $plain = 'dukafy_' . Str::random(32);

        DB::table('api_keys')->insert([
            'name' => $data['name'],
            'key_hash' => hash('sha256', $plain),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('new_api_key', $plain);
    }

    public function revokeApiKey(int $id)
    {
        DB::table('api_keys')->where('id', $id)->update([
            'revoked_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back();
    }

    public function dataRetentionPolicy()
    {
        $policy = DB::table('data_retention_policies')->where('id', 1)->first();

        return view('admin.security.data_retention', compact('policy'));
    }

    public function updateDataRetentionPolicy(Request $request)
    {
        $data = $request->validate([
            'sales_days' => ['nullable', 'integer', 'min:1'],
            'logs_days' => ['nullable', 'integer', 'min:1'],
            'login_history_days' => ['nullable', 'integer', 'min:1'],
        ]);

        DB::table('data_retention_policies')->where('id', 1)->update([
            'sales_days' => $data['sales_days'] ?? null,
            'logs_days' => $data['logs_days'] ?? null,
            'login_history_days' => $data['login_history_days'] ?? null,
            'updated_at' => now(),
        ]);

        return redirect()->back();
    }

    public function securityAlerts()
    {
        $alerts = DB::table('security_alert_settings')->where('id', 1)->first();

        return view('admin.security.security_alerts', compact('alerts'));
    }

    public function updateSecurityAlerts(Request $request)
    {
        $data = $request->validate([
            'email_on_new_location_login' => ['nullable'],
            'whatsapp_on_password_change' => ['nullable'],
            'alert_on_backup_failure' => ['nullable'],
        ]);

        DB::table('security_alert_settings')->where('id', 1)->update([
            'email_on_new_location_login' => isset($data['email_on_new_location_login']),
            'whatsapp_on_password_change' => isset($data['whatsapp_on_password_change']),
            'alert_on_backup_failure' => isset($data['alert_on_backup_failure']),
            'updated_at' => now(),
        ]);

        return redirect()->back();
    }
}
