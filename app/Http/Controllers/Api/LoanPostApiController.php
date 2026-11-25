<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoanPost;
use Illuminate\Http\Request;

class LoanPostApiController extends Controller
{
    public function index(Request $request)
    {
        $posts = LoanPost::with('user:id,name,avatar')
            ->open()
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json($posts);
    }

    public function show(LoanPost $post)
    {
        $post->load('user:id,name,avatar,whatsapp_number');

        return response()->json([
            'post' => $post,
            'whatsapp_link' => $post->whatsapp_link,
            'formatted_amount' => $post->formatted_amount,
            'formatted_total' => $post->formatted_total,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:10000',
            'amount_needed' => 'required|numeric|min:10000|max:999999999',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'collateral_description' => 'required|string|max:500',
            'repayment_period' => 'required|integer|min:1|max:365',
            'repayment_unit' => 'required|in:days,weeks,months,years',
        ]);

        $post = $request->user()->loanPosts()->create($validated);

        return response()->json($post->load('user'), 201);
    }

    public function update(Request $request, LoanPost $post)
    {
        if ($request->user()->id !== $post->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:10000',
            'amount_needed' => 'required|numeric|min:10000|max:999999999',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'collateral_description' => 'required|string|max:500',
            'repayment_period' => 'required|integer|min:1|max:365',
            'repayment_unit' => 'required|in:days,weeks,months,years',
        ]);

        $post->update($validated);

        return response()->json($post);
    }

    public function destroy(Request $request, LoanPost $post)
    {
        if ($request->user()->id !== $post->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function close(Request $request, LoanPost $post)
    {
        if ($request->user()->id !== $post->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if (!$post->isOpen()) {
            return response()->json(['message' => 'Already closed'], 422);
        }

        $post->update(['status' => 'closed', 'closed_at' => now()]);

        return response()->json($post);
    }
}   