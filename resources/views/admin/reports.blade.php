<x-layouts.admin title="Reports">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Money Circulation Report</h1>
        <a href="{{ route('admin.reports.xlsx', array_filter(['from' => $from, 'to' => $to])) }}"
           class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground hover:bg-primary/90 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
            Download XLSX
        </a>
    </div>

    {{-- Date Range Filter --}}
    <div class="rounded-xl border border-border bg-card p-5 mb-6">
        <form method="GET" action="{{ route('admin.reports') }}">
            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <label for="from" class="block text-xs font-medium text-muted-foreground uppercase tracking-wider mb-1.5">From</label>
                    <input type="date" name="from" id="from" value="{{ $from }}"
                           class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2">
                </div>
                <div class="flex-1">
                    <label for="to" class="block text-xs font-medium text-muted-foreground uppercase tracking-wider mb-1.5">To</label>
                    <input type="date" name="to" id="to" value="{{ $to }}"
                           class="w-full rounded-lg border border-input bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2">
                </div>
                <div>
                    <label for="from" class="block text-xs font-medium text-muted-foreground uppercase tracking-wider mb-1.5">ㅤ</label>
                    <button type="submit"
                            class="rounded-lg bg-primary px-5 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90 transition-colors">
                        Filter
                    </button>
                </div>
                @if($from || $to)
                <div>
                    <label class="block text-xs font-medium text-muted-foreground uppercase tracking-wider mb-1.5">ㅤ</label>
                    <button type="button"
                            onclick="window.location.href='{{ route('admin.reports') }}'"
                            class="rounded-lg border border-border px-5 py-2 text-sm font-medium text-muted-foreground hover:text-foreground hover:bg-muted transition-colors">
                        Clear
                    </button>
                </div>
                @endif
            </div>
            @if($from || $to)
                <p class="mt-3 text-xs text-muted-foreground">
                    Showing:
                    {{ $from ? \Carbon\Carbon::parse($from)->format('M j, Y') : 'beginning' }}
                    -
                    {{ $to ? \Carbon\Carbon::parse($to)->format('M j, Y') : 'now' }}
                </p>
            @endif
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="rounded-xl border border-border bg-card p-5">
            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Total Circulated</p>
            <p class="mt-2 text-xl font-bold">Rp {{ number_format($summary['total_circulated'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-border bg-card p-5">
            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Closed Deals Value</p>
            <p class="mt-2 text-xl font-bold text-success">Rp {{ number_format($summary['total_closed_value'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-border bg-card p-5">
            <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Avg Loan Size</p>
            <p class="mt-2 text-xl font-bold">Rp {{ number_format($summary['avg_loan_size'], 0, ',', '.') }}</p>
        </div>
</div>

    {{-- DataTable --}}
    <div class="rounded-xl border border-border bg-card p-5">
        <h2 class="text-sm font-semibold mb-4">Detailed Circulation Data</h2>
        <table id="circulation-table" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Borrower</th>
                    <th>Title</th>
                    <th>Amount (Rp)</th>
                    <th>Interest</th>
                    <th>Total Repayable</th>
                    <th>Status</th>
                    <th>Repayment</th>
                    <th>Posted</th>
                    <th>Closed</th>
                </tr>
            </thead>
            <tbody>
                @foreach($circulationData as $row)
                    <tr>
                        <td>{{ $row['id'] }}</td>
                        <td>{{ $row['borrower'] }}</td>
                        <td>{{ Str::limit($row['title'], 35) }}</td>
                        <td data-order="{{ $row['amount'] }}">{{ number_format($row['amount'], 0, ',', '.') }}</td>
                        <td>{{ $row['interest_rate'] }}%</td>
                        <td data-order="{{ $row['total_repayable'] }}">{{ number_format($row['total_repayable'], 0, ',', '.') }}</td>
                        <td>
                            @if($row['status'] === 'open')
                                <span class="badge-open">Open</span>
                            @else
                                <span class="badge-closed">Closed</span>
                            @endif
                        </td>
                        <td>{{ $row['repayment'] }}</td>
                        <td>{{ $row['posted_at'] }}</td>
                        <td>{{ $row['closed_at'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#circulation-table').DataTable({
                autoWidth: false,
                order: [[0, 'desc']],
                pageLength: 25,
                layout: {
                    topStart: { pageLength: { menu: [10, 25, 50, 100] } },
                    topEnd: { search: { placeholder: 'Search records...' } },
                    bottomStart: 'info',
                    bottomEnd: { paging: { numbers: 3 } }
                },
                initComplete: function() {
                    $(this).css('width', '100%');
                }
            });
        });
    </script>
    @endpush
</x-layouts.admin>