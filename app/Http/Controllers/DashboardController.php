<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\Tag;
use DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return redirect()->route('images.index');
    }
}
