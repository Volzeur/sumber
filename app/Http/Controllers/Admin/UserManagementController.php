<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::withCount('loanPosts')
            ->withSum('loanPosts', 'amount_needed')
            ->latest()
            ->get();

        return view('admin.users', compact('users'));
    }

    public function toggleRole(User $user)
    {
        $user->update([
            'role' => $user->isAdmin() ? 'user' : 'admin',
        ]);

        return back()->with('success', "Role updated for {$user->name}.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account from admin panel.');
        }

        $name = $user->name;

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete(); // cascades to loan_posts

        return back()->with('success', "User \"{$name}\" and all their posts have been deleted.");
    }
}