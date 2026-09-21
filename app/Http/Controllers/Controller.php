<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * 角色授權輔助方法（統一管理，不在各 Controller 重複實作）
     *
     * @param  'viewer'|'editor'|'admin'  $minRole
     */
    protected function authorizeRole(string $minRole): void
    {
        $user = Auth::user();

        match ($minRole) {
            'admin'  => $user->isAdmin()  || abort(403, '需要管理員權限。'),
            'editor' => $user->isEditor() || abort(403, '權限不足，需要編輯以上角色。'),
            'viewer' => $user->isViewer() || abort(403, '需要登入帳號。'),
            default  => abort(403),
        };
    }
}
