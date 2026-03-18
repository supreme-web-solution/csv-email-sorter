<x-layouts::app :title="__('Email Filter')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <div class="flex flex-col gap-2">
            <h1 class="text-xl font-semibold text-neutral-900 dark:text-neutral-50">
                Email Filter Engine
            </h1>
            <p class="text-sm text-neutral-600 dark:text-neutral-300">
                Upload your main email list and the list of emails you want to remove.
                We will generate a new file containing only the remaining emails.
            </p>
        </div>

        @if (session('email_filter.preview'))
            @php($preview = session('email_filter.preview'))
            <div id="preview-summary" class="flex flex-col gap-3 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900 dark:border-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-100">
                <div class="flex flex-wrap items-center justify-between gap-3">
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
                        </p>
                    </div>

                    <a
                        href="{{ route('email-filter.download', $preview['token']) }}"
                        class="inline-flex items-center gap-2 rounded-md bg-emerald-600 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white shadow-sm transition hover:bg-emerald-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 focus-visible:ring-offset-emerald-900"
                        id="download-result-btn"
                    >
                        Download result CSV
                    </a>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-950/40 dark:text-red-200">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            id="email-filter-form"
            method="POST"
            action="{{ route('email-filter.run') }}"
            enctype="multipart/form-data"
            class="grid gap-6 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-950/60"
        >
            @csrf

            <div class="grid gap-4 md:grid-cols-2">
                <div class="flex flex-col gap-2">
                    <label for="source_file" class="text-sm font-medium text-neutral-800 dark:text-neutral-100">
                        Source email list
                    </label>
                    <input
                        id="source_file"
                        name="source_file"
                        type="file"
                        accept=".csv,.txt,.xlsx"
                        required
                        class="dropify block w-full cursor-pointer rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-50"
                        data-height="160"
                    >
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">
                        Upload your main list of emails to keep. Supported formats: CSV, XLSX, TXT. One email per line or in the first column.
                    </p>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="exclude_file" class="text-sm font-medium text-neutral-800 dark:text-neutral-100">
                        Emails to exclude
                    </label>
                    <input
                        id="exclude_file"
                        name="exclude_file"
                        type="file"
                        accept=".csv,.txt,.xlsx"
                        required
                        class="dropify block w-full cursor-pointer rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-50"
                        data-height="160"
                    >
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">
                        Upload a list of emails to exclude from the source list. Any email found here will be removed from the result.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-between gap-4">
                <p class="text-xs text-neutral-500 dark:text-neutral-400">
                    When you click <span class="font-semibold">Run engine</span>, a new CSV file will be generated and your browser will start downloading it automatically.
                </p>

                <button
                    id="email-filter-submit"
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 focus-visible:ring-offset-neutral-950 disabled:cursor-not-allowed disabled:opacity-70"
                >
                    <span class="submit-label">Run engine &amp; download result</span>
                    <span class="submit-loading hidden">
                        Processing...
                    </span>
                </button>
            </div>
        </form>
    </div>

    {{-- Dropify assets --}}
    <link
        rel="stylesheet"
        type="text/css"
        href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"
    >

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script
        type="text/javascript"
        src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"
    ></script>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof $.fn.dropify === 'function') {
                $('.dropify').dropify();
            }

            var form = document.getElementById('email-filter-form');
            var submitButton = document.getElementById('email-filter-submit');
            var previewSummary = document.getElementById('preview-summary');
            var downloadBtn = document.getElementById('download-result-btn');
            if (form && submitButton) {
                var labelEl = submitButton.querySelector('.submit-label');
                var loadingEl = submitButton.querySelector('.submit-loading');

                form.addEventListener('submit', function () {
                    submitButton.disabled = true;
                    if (labelEl) labelEl.classList.add('hidden');
                    if (loadingEl) loadingEl.classList.remove('hidden');
                    if (previewSummary) previewSummary.style.display = 'none';
                });
            }
        });
    </script>
</x-layouts::app>

