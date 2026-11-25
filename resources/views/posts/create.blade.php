<x-layouts.app title="New Loan Request">
    <div class="mb-6">
        <a href="{{ route('feed') }}" class="inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
            Back to feed
        </a>
    </div>

    <div class="rounded-xl border border-border bg-card p-6">
        <h1 class="text-xl font-semibold mb-1">Create Loan Request</h1>
        <p class="text-sm text-muted-foreground mb-6">Describe what you need and what you can offer.</p>

        <form method="POST" action="{{ route('posts.store') }}" class="space-y-5">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium mb-1.5">Title</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                       placeholder="e.g. Need capital for small business"
                       class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2">
                @error('title')
                    <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">Description</label>
                <x-wysiwyg name="description" :value="old('description', '')" />
                @error('description')
                    <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="amount_needed" class="block text-sm font-medium mb-1.5">Amount Needed (Rp)</label>
                    <input type="number" name="amount_needed" id="amount_needed" value="{{ old('amount_needed') }}"
                           min="10000" step="1000" required placeholder="5000000"
                           class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2">
                    @error('amount_needed')
                        <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="interest_rate" class="block text-sm font-medium mb-1.5">Interest Rate (%)</label>
                    <input type="number" name="interest_rate" id="interest_rate" value="{{ old('interest_rate') }}"
                           min="0" max="100" step="0.5" required placeholder="5"
                           class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2">
                    <p class="mt-1 text-xs text-muted-foreground">Percentage you're willing to pay on top of the loan.</p>
                    @error('interest_rate')
                        <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="collateral_description" class="block text-sm font-medium mb-1.5">Collateral</label>
                <input type="text" name="collateral_description" id="collateral_description"
                       value="{{ old('collateral_description') }}" required
                       placeholder="e.g. BPKB Motor Honda Beat 2022, Gold 10g, etc."
                       class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2">
                @error('collateral_description')
                    <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">Repayment Period</label>
                <div class="flex gap-3">
                    <input type="number" name="repayment_period" id="repayment_period"
                           value="{{ old('repayment_period', 1) }}" min="1" max="365" required
                           class="w-24 rounded-lg border border-input bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2">
                    <select name="repayment_unit" id="repayment_unit" required
                            class="rounded-lg border border-input bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2">
                        <option value="days" {{ old('repayment_unit') === 'days' ? 'selected' : '' }}>Days</option>
                        <option value="weeks" {{ old('repayment_unit') === 'weeks' ? 'selected' : '' }}>Weeks</option>
                        <option value="months" {{ old('repayment_unit', 'months') === 'months' ? 'selected' : '' }}>Months</option>
                        <option value="years" {{ old('repayment_unit') === 'years' ? 'selected' : '' }}>Years</option>
                    </select>
                </div>
                <p class="mt-1 text-xs text-muted-foreground">How long you need to repay the loan.</p>
                @error('repayment_period')
                    <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                @enderror
                @error('repayment_unit')
                    <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="rounded-lg bg-primary px-6 py-2.5 text-sm font-medium text-primary-foreground hover:bg-primary/90 transition-colors">
                    Post Loan Request
                </button>
                <a href="{{ route('feed') }}" class="rounded-lg px-4 py-2.5 text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.app>