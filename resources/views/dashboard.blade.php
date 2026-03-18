<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
        @if (session('email_filter.preview'))
            @php($preview = session('email_filter.preview'))
            <div id="preview-summary" class="flex flex-col gap-3 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900 dark:border-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-100 mt-6">
                <div class="space-y-1">
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-800 dark:text-emerald-200">
                        Preview summary
                    </p>
                    <p class="text-sm">
                        <span class="font-medium">Source File:</span> {{ number_format($preview['main']) }}
                        &nbsp;&middot;&nbsp;
                        <span class="font-medium">Exclude File:</span> {{ number_format($preview['remove']) }}
                        &nbsp;&middot;&nbsp;
                        <span class="font-medium">Result File:</span> {{ number_format($preview['result']) }}
                        &nbsp;&middot;&nbsp;
                        <span class="font-medium">Deleted:</span> {{ isset($preview['deleted']) ? number_format($preview['deleted']) : 0 }}
                    </p>
                </div>
            </div>
        @endif
    </div>
</x-layouts::app>
