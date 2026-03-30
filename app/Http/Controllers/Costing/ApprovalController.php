<?php

namespace App\Http\Controllers\Costing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index()
    {
        // Langsung arahin ke file yang lu punya
        return view('cost_section.approval_pr');
    }
}