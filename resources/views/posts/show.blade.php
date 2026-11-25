<x-layouts.app :title="$post->title">
    <div class="mb-6">
        <a href="{{ route('feed') }}" class="inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
            Back to feed
        </a>
    </div>

    <article class="rounded-xl border border-border bg-card overflow-hidden">
        {{-- Header --}}
        <div class="p-6 pb-0">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ $post->user->avatar_url }}" alt="" class="h-12 w-12 rounded-full object-cover ring-1 ring-border">
                    <div>
                        <p class="font-semibold">{{ $post->user->name }}</p>
                        <p class="text-xs text-muted-foreground">{{ $post->created_at->format('F j, Y \a\t H:i') }}</p>
                    </div>
                </div>
                @if($post->status === 'closed')
                    <span class="inline-flex items-center rounded-full bg-muted px-3 py-1 text-xs font-medium text-muted-foreground">Closed</span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-success/10 px-3 py-1 text-xs font-medium text-success">
                        Open
                    </span>
                @endif
            </div>

            <h1 class="mt-4 text-2xl font-bold leading-tight">{{ $post->title }}</h1>
        </div>

        {{-- Loan details card --}}
        <div class="mx-6 mt-4 rounded-lg border border-border bg-muted/30 p-4">
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                <div>
                    <p class="text-xs text-muted-foreground uppercase tracking-wider">Amount</p>
                    <p class="mt-1 text-lg font-bold">{{ $post->formatted_amount }}</p>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground uppercase tracking-wider">Interest</p>
                    <p class="mt-1 text-lg font-bold">{{ $post->interest_rate }}%</p>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground uppercase tracking-wider">Repay In</p>
                    <p class="mt-1 text-lg font-bold">{{ $post->formatted_repayment }}</p>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground uppercase tracking-wider">Collateral</p>
                    <p class="mt-1 text-sm font-medium">{{ $post->collateral_description }}</p>
                </div>
            </div>
        </div>

        {{-- Description (rendered HTML from Trix) --}}
        <div class="p-6 prose prose-sm prose-zinc max-w-none">
            {!! $post->description !!}
        </div>

        {{-- Actions --}}
        <div class="border-t border-border px-6 py-4 flex flex-wrap items-center gap-3">
            @if($post->isOpen() && auth()->check() && auth()->id() !== $post->user_id)
                <a href="{{ $post->whatsapp_link }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 rounded-lg bg-whatsapp px-5 py-2.5 text-sm font-medium text-whatsapp-foreground hover:bg-whatsapp/90 transition-colors">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Message on WhatsApp
                </a>
            @endif

            @auth
                @if(auth()->id() === $post->user_id)
                    @if($post->isOpen())
                        <form method="POST" action="{{ route('posts.close', $post) }}"
                              onsubmit="return confirm('Close offers? Your post will be hidden from the feed.')">
                            @csrf @method('PATCH')
                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-destructive/30 text-destructive px-4 py-2.5 text-sm font-medium hover:bg-destructive/5 transition-colors">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                Close Offers
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('posts.edit', $post) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-border px-4 py-2.5 text-sm font-medium hover:bg-muted transition-colors">
                        Edit
                    </a>
                    <a href="{{ route('posts.pdf', $post) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-border px-4 py-2.5 text-sm font-medium hover:bg-muted transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        Download PDF
                    </a>
                    <form method="POST" action="{{ route('posts.destroy', $post) }}"
                          onsubmit="return confirm('Delete this post permanently?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg px-4 py-2.5 text-sm font-medium text-destructive hover:bg-destructive/5 transition-colors">
                            Delete
                        </button>
                    </form>
                @endif
            @endauth
        </div>
    </article>
</x-layouts.app>