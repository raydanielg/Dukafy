<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExtrasController extends Controller
{
    private function readLogFile(?string $path, int $maxLines = 400): array
    {
        if (!$path || !is_file($path)) {
            return ['path' => $path, 'exists' => false, 'lines' => []];
        }

        $content = @file($path, FILE_IGNORE_NEW_LINES);
        if ($content === false) {
            return ['path' => $path, 'exists' => true, 'lines' => []];
        }

        $tail = array_slice($content, -$maxLines);

        return ['path' => $path, 'exists' => true, 'lines' => $tail];
    }

    public function modulesIndex()
    {
        $modules = DB::table('modules')->orderBy('id', 'desc')->paginate(20);

        return view('admin.extras.modules.index', compact('modules'));
    }

    public function modulesCreate()
    {
        return view('admin.extras.modules.create');
    }

    public function modulesStore(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'enabled' => ['nullable'],
        ]);

        $slug = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);

        DB::table('modules')->insert([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'enabled' => isset($data['enabled']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.extras.modules.index');
    }

    public function modulesEdit(int $id)
    {
        $module = DB::table('modules')->where('id', $id)->first();
        abort_if(!$module, 404);

        return view('admin.extras.modules.edit', compact('module'));
    }

    public function modulesUpdate(Request $request, int $id)
    {
        $module = DB::table('modules')->where('id', $id)->first();
        abort_if(!$module, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'enabled' => ['nullable'],
        ]);

        $slug = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);

        DB::table('modules')->where('id', $id)->update([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'enabled' => isset($data['enabled']),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.extras.modules.edit', $id);
    }

    public function modulesDestroy(int $id)
    {
        DB::table('modules')->where('id', $id)->delete();

        return redirect()->route('admin.extras.modules.index');
    }

    public function announcementsIndex()
    {
        $announcements = DB::table('announcements')->orderBy('id', 'desc')->paginate(20);

        return view('admin.extras.announcements.index', compact('announcements'));
    }

    public function announcementsCreate()
    {
        return view('admin.extras.announcements.create');
    }

    public function announcementsStore(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'is_active' => ['nullable'],
            'published_at' => ['nullable', 'date'],
        ]);

        DB::table('announcements')->insert([
            'title' => $data['title'],
            'body' => $data['body'] ?? null,
            'is_active' => isset($data['is_active']),
            'published_at' => $data['published_at'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.extras.announcements.index');
    }

    public function announcementsEdit(int $id)
    {
        $announcement = DB::table('announcements')->where('id', $id)->first();
        abort_if(!$announcement, 404);

        return view('admin.extras.announcements.edit', compact('announcement'));
    }

    public function announcementsUpdate(Request $request, int $id)
    {
        $announcement = DB::table('announcements')->where('id', $id)->first();
        abort_if(!$announcement, 404);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'is_active' => ['nullable'],
            'published_at' => ['nullable', 'date'],
        ]);

        DB::table('announcements')->where('id', $id)->update([
            'title' => $data['title'],
            'body' => $data['body'] ?? null,
            'is_active' => isset($data['is_active']),
            'published_at' => $data['published_at'] ?? null,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.extras.announcements.edit', $id);
    }

    public function announcementsDestroy(int $id)
    {
        DB::table('announcements')->where('id', $id)->delete();

        return redirect()->route('admin.extras.announcements.index');
    }

    public function cronJobsIndex()
    {
        $jobs = DB::table('cron_jobs')->orderBy('id', 'desc')->paginate(20);

        return view('admin.extras.cron_jobs.index', compact('jobs'));
    }

    public function cronJobsCreate()
    {
        return view('admin.extras.cron_jobs.create');
    }

    public function cronJobsStore(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'command' => ['required', 'string', 'max:255'],
            'schedule' => ['nullable', 'string', 'max:255'],
            'enabled' => ['nullable'],
        ]);

        DB::table('cron_jobs')->insert([
            'name' => $data['name'],
            'command' => $data['command'],
            'schedule' => $data['schedule'] ?? null,
            'enabled' => isset($data['enabled']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.extras.cron_jobs.index');
    }

    public function cronJobsEdit(int $id)
    {
        $job = DB::table('cron_jobs')->where('id', $id)->first();
        abort_if(!$job, 404);

        return view('admin.extras.cron_jobs.edit', compact('job'));
    }

    public function cronJobsUpdate(Request $request, int $id)
    {
        $job = DB::table('cron_jobs')->where('id', $id)->first();
        abort_if(!$job, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'command' => ['required', 'string', 'max:255'],
            'schedule' => ['nullable', 'string', 'max:255'],
            'enabled' => ['nullable'],
        ]);

        DB::table('cron_jobs')->where('id', $id)->update([
            'name' => $data['name'],
            'command' => $data['command'],
            'schedule' => $data['schedule'] ?? null,
            'enabled' => isset($data['enabled']),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.extras.cron_jobs.edit', $id);
    }

    public function cronJobsDestroy(int $id)
    {
        DB::table('cron_jobs')->where('id', $id)->delete();

        return redirect()->route('admin.extras.cron_jobs.index');
    }

    public function systemLogs()
    {
        $log = $this->readLogFile(storage_path('logs/laravel.log'));

        return view('admin.extras.system_logs', compact('log'));
    }
}
