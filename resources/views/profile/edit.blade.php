<x-layouts.app title="Settings">
    <h1 class="text-xl font-semibold mb-6">Profile Settings</h1>

    <div class="rounded-xl border border-border bg-card p-6">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')

            {{-- Current avatar --}}
            <div class="flex items-center gap-4">
                <img src="{{ $user->avatar_url }}" alt="" class="h-16 w-16 rounded-full object-cover ring-2 ring-border">
                <div>
                    <label for="avatar" class="block text-sm font-medium mb-1">Change Avatar</label>
                    <input type="file" name="avatar" id="avatar" accept="image/*"
                           class="text-sm text-muted-foreground file:mr-3 file:rounded-lg file:border file:border-border file:bg-muted file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-foreground hover:file:bg-muted/80">
                    @error('avatar')
                        <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="name" class="block text-sm font-medium mb-1.5">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                       class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2">
                @error('name')
                    <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">Email</label>
                <input type="email" value="{{ $user->email }}" disabled
                       class="w-full rounded-lg border border-input bg-muted px-3 py-2 text-sm text-muted-foreground cursor-not-allowed">
                <p class="mt-1 text-xs text-muted-foreground">Email cannot be changed.</p>
            </div>

            <div>
                <label for="whatsapp_number" class="block text-sm font-medium mb-1.5">WhatsApp Number</label>
                <input type="text" name="whatsapp_number" id="whatsapp_number"
                       value="{{ old('whatsapp_number', $user->whatsapp_number) }}" required
                       class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2">
                @error('whatsapp_number')
                    <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="bio" class="block text-sm font-medium mb-1.5">Bio</label>
                <textarea name="bio" id="bio" rows="3" maxlength="500"
                          class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2">{{ old('bio', $user->bio) }}</textarea>
                @error('bio')
                    <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="rounded-lg bg-primary px-6 py-2.5 text-sm font-medium text-primary-foreground hover:bg-primary/90 transition-colors">
                Save Changes
            </button>
        </form>
    </div>
    
    {{-- Danger Zone --}}
    <div class="mt-8 rounded-xl border border-destructive/20 bg-card p-6">
        <h2 class="text-lg font-semibold text-destructive mb-1">Delete Account</h2>
        <p class="text-sm text-muted-foreground mb-5">Permanently delete your account and all your loan posts. This cannot be undone.</p>

        <form method="POST" action="{{ route('profile.destroy') }}"
              onsubmit="return confirm('Are you absolutely sure? All your posts will be permanently deleted.')">
            @csrf
            @method('DELETE')

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium mb-1.5">Confirm your password</label>
                <input type="password" name="password" id="password" required
                       placeholder="Enter your password to confirm"
                       class="w-full max-w-sm rounded-lg border border-input bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2">
                @error('password')
                    <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="rounded-lg bg-destructive px-5 py-2 text-sm font-medium text-destructive-foreground hover:bg-destructive/90 transition-colors">
                Delete My Account
            </button>
        </form>
    </div>
</x-layouts.app>