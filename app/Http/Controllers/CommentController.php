<?php

namespace App\Http\Controllers;

use App\Models\Issue;

class CommentController extends Controller
{
    public function store(Issue $issue)
    {
        return response()->json(['ok' => true]);
    }
}
