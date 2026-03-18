<x-layouts::app :title="__('Email Filter Results')">
    <div class="mx-auto w-full max-w-6xl space-y-6 p-6">
        <div class="space-y-1">
            <h1 class="text-xl font-semibold text-neutral-900 dark:text-neutral-50">Email Filter Results</h1>
            <p class="text-sm text-neutral-600 dark:text-neutral-300">
                Recent runs of the email filter engine.
            </p>
        </div>

        <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-950/60">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
                    <thead class="bg-neutral-50 text-left text-xs font-semibold uppercase tracking-wide text-neutral-700 dark:bg-neutral-900/40 dark:text-neutral-200">
                        <tr>
                            <th class="px-4 py-3">
                                <input type="checkbox" id="select-all" onclick="toggleSelectAll(this)">
                            </th>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3 text-right">Source</th>
                            <th class="px-4 py-3 text-right">Exclude</th>
                            <th class="px-4 py-3 text-right">Result</th>
                            <th class="px-4 py-3">Created</th>
                            <th class="px-4 py-3">Download</th>
                            <th class="px-4 py-3">Delete</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 text-sm text-neutral-800 dark:divide-neutral-800 dark:text-neutral-100">
                        <form id="bulk-delete-form" method="POST" action="{{ route('email-filter.results.bulkDelete') }}">
                            @csrf
                            @method('DELETE')
                            @forelse ($results as $result)
                                <tr class="hover:bg-neutral-50/70 dark:hover:bg-neutral-900/30">
                                    <td class="px-4 py-3">
                                        <input type="checkbox" name="ids[]" value="{{ $result->id }}">
                                    </td>
                                    <td class="px-4 py-3">{{ $result->id }}</td>
                                    <td class="px-4 py-3">{{ $result->user?->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums">{{ number_format($result->source_count) }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums">{{ number_format($result->exclude_count) }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums">{{ number_format($result->result_count) }}</td>
                                    <td class="px-4 py-3">{{ $result->created_at?->format('Y-m-d H:i') }}</td>
                                    <td class="px-4 py-3">
                                        <a
                                            href="{{ route('email-filter.download', $result->token) }}"
                                            class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 focus-visible:ring-offset-neutral-950"
                                        >
                                            Download
                                        </a>
                                    </td>
                                    <td class="px-4 py-3">
                                        <form method="POST" action="{{ route('email-filter.results.delete', $result->id) }}" style="display:inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-red-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 focus-visible:ring-offset-neutral-950" onclick="return confirm('Delete this result?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-4 py-6 text-center text-sm text-neutral-500 dark:text-neutral-400" colspan="9">
                                        No results yet.
                                    </td>
                                </tr>
                            @endforelse
                        </form>
                        @empty
                            <tr>
                                <td class="px-4 py-6 text-center text-sm text-neutral-500 dark:text-neutral-400" colspan="7">
                                    No results yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            {{ $results->links() }}
        </div>
    </div>
</x-layouts::app>
