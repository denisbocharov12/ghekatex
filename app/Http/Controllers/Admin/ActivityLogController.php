<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('ActivityLog/Index', [
            'items' => Activity::query()
                ->with('causer:id,name')
                ->latest()
                ->paginate((int) $request->integer('per_page', 30) ?: 30)
                ->withQueryString(),
        ]);
    }
}
