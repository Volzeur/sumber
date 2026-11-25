@props(['post', 'showActions' => false])

<article class="border-b border-border py-5 first:pt-0 last:border-b-0">
    <div class="flex gap-3">
        {{-- Avatar column (Threads-style) --}}
        <div class="flex flex-col items-center">
            <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->name }}"
                 class="h-10 w-10 rounded-full object-cover ring-1 ring-border">
            {{-- Thread line --}}
            <div class="w-px flex-1 bg-border mt-2"></div>
        </div>

        {{-- Content column --}}
        <div class="flex-1 min-w-0">
            {{-- Header --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold">{{ $post->user->name }}</span>
                    <span class="text-xs text-muted-foreground">
                        @if($post->updated_at->gt($post->created_at->addSecond()))
                            edited {{ $post->updated_at->diffForHumans() }}
                        @else
                            {{ $post->created_at->diffForHumans() }}
                        @endif
                    </span>
                </div>
                @if($post->status === 'closed')
                    <span class="inline-flex items-center rounded-full bg-muted px-2.5 py-0.5 text-xs font-medium text-muted-foreground">
                        Closed
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 rounded-full bg-success/10 px-2.5 py-0.5 text-xs font-medium text-success">
                        Open
                    </span>
                @endif
            </div>

            {{-- Title --}}
            <a href="{{ route('posts.show', $post) }}" class="block mt-1">
                <h3 class="text-base font-semibold leading-snug hover:underline">{{ $post->title }}</h3>
            </a>

            {{-- Description preview (strip HTML, truncate) --}}
            <p class="mt-1 text-sm text-muted-foreground line-clamp-2">
                {{ Str::limit(strip_tags(html_entity_decode($post->description, ENT_QUOTES, 'UTF-8')), 150) }}
            </p>

            {{-- Loan details chips --}}
            <div class="mt-3 flex flex-wrap gap-2">
                <div class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-muted/50 px-3 py-1.5">
                    <svg class="h-3.5 w-3.5 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" /></svg>
                    <span class="text-sm font-semibold">{{ $post->formatted_amount }}</span>
                </div>
                <div class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-muted/50 px-3 py-1.5">
                    <svg class="h-3.5 w-3.5 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" /></svg>
                    <span class="text-sm">{{ $post->interest_rate }}% interest</span>
                </div>
                <div class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-muted/50 px-3 py-1.5">
                    <svg class="h-3.5 w-3.5 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                    <span class="text-sm">{{ Str::limit($post->collateral_description, 30) }}</span>
                </div>
                <div class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-muted/50 px-3 py-1.5">
                    <svg class="h-3.5 w-3.5 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="text-sm">{{ $post->formatted_repayment }}</span>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="mt-3 flex items-center gap-2">
                @if($post->isOpen() && auth()->check() && auth()->id() !== $post->user_id)
                    <a href="{{ $post->whatsapp_link }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-1.5 rounded-lg bg-whatsapp px-4 py-2 text-sm font-medium text-whatsapp-foreground hover:bg-whatsapp/90 transition-colors">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Message on WhatsApp
                    </a>
                @endif

                @if($showActions && auth()->check() && auth()->id() === $post->user_id)
                    @if($post->isOpen())
                        <form method="POST" action="{{ route('posts.close', $post) }}"
                              onsubmit="return confirm('Close offers? Your post will no longer appear in the feed.')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3 py-2 text-sm font-medium hover:bg-muted transition-colors">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                Close Offers
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('posts.edit', $post) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3 py-2 text-sm font-medium hover:bg-muted transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                        Edit
                    </a>
                @endif
            </div>
        </div>
    </div>
</article>