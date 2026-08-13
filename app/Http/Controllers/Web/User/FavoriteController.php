<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(): View
    {
        $favorites = Favorite::where('user_id', auth()->id())
            ->with(['listing' => fn ($q) => $q->with(['category', 'images'])])
            ->latest('id')
            ->paginate(20);

        return view('favorites.index', compact('favorites'));
    }
}
