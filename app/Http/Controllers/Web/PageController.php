<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(Page $page): View
    {
        abort_unless($page->is_active, 404);

        return view('pages.show', compact('page'));
    }
}
