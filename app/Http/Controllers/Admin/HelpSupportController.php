<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HelpSupportController extends Controller
{
    // Documentation
    public function documentation()
    {
        return view('admin.help.documentation');
    }

    // Support Tickets
    public function tickets()
    {
        $tickets = DB::table('support_tickets')
            ->leftJoin('users', 'users.id', '=', 'support_tickets.user_id')
            ->select('support_tickets.*', 'users.name as user_name')
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return view('admin.help.tickets.index', compact('tickets'));
    }

    public function ticketShow($id)
    {
        $ticket = DB::table('support_tickets')
            ->leftJoin('users', 'users.id', '=', 'support_tickets.user_id')
            ->select('support_tickets.*', 'users.name as user_name')
            ->where('support_tickets.id', $id)
            ->first();

        if (!$ticket) abort(404);

        $messages = DB::table('support_messages')
            ->leftJoin('users', 'users.id', '=', 'support_messages.user_id')
            ->select('support_messages.*', 'users.name as user_name')
            ->where('support_ticket_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.help.tickets.show', compact('ticket', 'messages'));
    }

    public function ticketReply(Request $request, $id)
    {
        $request->validate(['message' => 'required']);

        DB::table('support_messages')->insert([
            'support_ticket_id' => $id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'is_admin_reply' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('support_tickets')->where('id', $id)->update(['updated_at' => now(), 'status' => 'in_progress']);

        return redirect()->back()->with('success', 'Reply sent successfully');
    }

    // System Info
    public function systemInfo()
    {
        $info = [
            'App Name' => config('app.name'),
            'Laravel Version' => app()->version(),
            'PHP Version' => PHP_VERSION,
            'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'Database' => config('database.default'),
            'Timezone' => config('app.timezone'),
            'Environment' => config('app.env'),
            'Debug Mode' => config('app.debug') ? 'Enabled' : 'Disabled',
            'Upload Max Filesize' => ini_get('upload_max_filesize'),
            'Post Max Size' => ini_get('post_max_size'),
            'Memory Limit' => ini_get('memory_limit'),
        ];

        return view('admin.help.system_info', compact('info'));
    }

    // Contact Developer
    public function contactDeveloper()
    {
        return view('admin.help.contact_developer');
    }
}
