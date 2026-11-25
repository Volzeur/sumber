<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanPost;
use App\Services\SpreadsheetService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');

        $query = LoanPost::with('user')->latest();

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $posts = $query->get();

        $circulationData = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'borrower' => $post->user->name,
                'title' => $post->title,
                'amount' => $post->amount_needed,
                'interest_rate' => $post->interest_rate,
                'total_repayable' => $post->total_with_interest,
                'status' => $post->status,
                'posted_at' => $post->created_at->format('Y-m-d H:i'),
                'closed_at' => $post->closed_at?->format('Y-m-d H:i') ?? '-',
                'repayment' => $post->formatted_repayment,
            ];
        });

        // Summary uses the same filtered query
        $filteredIds = $posts->pluck('id');

        $summary = [
            'total_circulated' => $posts->sum('amount_needed'),
            'total_closed_value' => $posts->where('status', 'closed')->sum('amount_needed'),
            'total_interest_value' => $posts->where('status', 'closed')->sum('total_with_interest')
                - $posts->where('status', 'closed')->sum('amount_needed'),
            'avg_loan_size' => $posts->avg('amount_needed') ?? 0,
        ];

        return view('admin.reports', compact('circulationData', 'summary', 'from', 'to'));
    }

    public function exportXlsx(Request $request, SpreadsheetService $spreadsheetService)
    {
        $from = $request->get('from');
        $to = $request->get('to');

        return $spreadsheetService->generateCirculationReport($from, $to);
    }
}