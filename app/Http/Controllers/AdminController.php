<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(string $page): View
    {
        if (view()->exists('pages.'.$page)) {
            return view('pages.'.$page);
        } else {
            return view('pages.404');
        }

        //   return view($id);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit(int $id): View
    {
        return view('404');
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
