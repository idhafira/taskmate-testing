<?php

namespace App\Http\Middleware;

use App\Models\Workspace;
use App\Models\WorkspaceMember;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pemakaian: ->middleware('ws')         (anggota workspace)
 *            ->middleware('ws:leader')  (khusus Project Leader)
 */
class EnsureWorkspaceRole
{
    public function handle(Request $request, Closure $next, ?string $role = null): Response
    {
        $ws = $request->route('workspace');
        if (!$ws instanceof Workspace) {
            $ws = Workspace::findOrFail($ws);
        }

        $member = WorkspaceMember::where('id_workspace', $ws->id_workspace)
            ->where('id_user', $request->user()->id_user)
            ->first();

        abort_if(!$member, 403, 'Anda bukan anggota workspace ini.');
        abort_if($role === 'leader' && $member->peran !== 'leader', 403, 'Hanya Project Leader yang dapat melakukan aksi ini.');

        $request->attributes->set('peran', $member->peran);

        return $next($request);
    }
}
