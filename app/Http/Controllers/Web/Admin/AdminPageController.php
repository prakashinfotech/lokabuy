<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePageRequest;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminPageController extends Controller
{
    public function index(): View
    {
        $pages = Page::orderBy('slug')->get();

        return view('admin.pages.index', compact('pages'));
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(UpdatePageRequest $request, Page $page): RedirectResponse
    {
        $page->update([
            'title' => $request->validated()['title'],
            'content' => $request->validated()['content'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.pages.index')
            ->with('success', "'{$page->title}' updated successfully.");
    }
}
