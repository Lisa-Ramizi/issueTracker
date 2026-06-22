<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Tag;

class IssueTagController extends Controller
{
    public function attach(Issue $issue, Tag $tag)
    {
        return response()->json(['ok' => true]);
    }

    public function detach(Issue $issue, Tag $tag)
    {
        return response()->json(['ok' => true]);
    }
}
