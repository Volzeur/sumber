<x-layouts.admin title="Users">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">User Management</h1>
        <span class="text-sm text-muted-foreground">{{ $users->count() }} users</span>
    </div>

    <div class="rounded-xl border border-border bg-card p-5">
        <table id="users-table" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Posts</th>
                    <th>Total Requested (Rp)</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>
                            <div class="user-cell">
                                <img src="{{ $user->avatar_url }}" alt="">
                                <span>{{ $user->name }}</span>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->isAdmin())
                                <span class="badge-admin">Admin</span>
                            @else
                                <span class="badge-user">User</span>
                            @endif
                        </td>
                        <td>{{ $user->loan_posts_count }}</td>
                        <td data-order="{{ $user->loan_posts_sum_amount_needed ?? 0 }}">
                            {{ number_format($user->loan_posts_sum_amount_needed ?? 0, 0, ',', '.') }}
                        </td>
                        <td data-order="{{ $user->created_at->timestamp }}">{{ $user->created_at->format('Y-m-d') }}</td>
                        <td>
                            @if($user->id !== auth()->id())
                                <div class="actions-cell">
                                    <form method="POST" action="{{ route('admin.users.toggle-role', $user) }}"
                                          onsubmit="return confirm('Toggle role for {{ $user->name }}?')">
                                        @csrf @method('PATCH')
                                        <button class="btn-action">
                                            {{ $user->isAdmin() ? 'Demote' : 'Promote' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                          onsubmit="return confirm('Delete {{ $user->name }} and ALL their posts? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button class="btn-action btn-danger">Delete</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-xs text-muted-foreground">You</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#users-table').DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                layout: {
                    topStart: { pageLength: { menu: [10, 25, 50, 100] } },
                    topEnd: { search: { placeholder: 'Search users...' } },
                    bottomStart: 'info',
                    bottomEnd: { paging: { numbers: 3 } }
                },
                columnDefs: [
                    { orderable: false, targets: -1 }
                ]
            });
        });
    </script>
    @endpush
</x-layouts.admin>