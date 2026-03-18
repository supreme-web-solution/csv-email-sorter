<section class="w-full">
    <div class="mx-auto w-full max-w-6xl space-y-6 p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="space-y-1">
                <flux:heading size="lg">{{ __('User Management') }}</flux:heading>
                <flux:text variant="subtle">{{ __('Create, update, and delete users.') }}</flux:text>
            </div>

            <div class="w-full sm:w-72">
                <flux:input wire:model.live="search" :label="__('Search')" type="text" placeholder="name or email" />
            </div>
        </div>

        @error('delete')
            <flux:callout variant="danger" icon="x-circle" heading="{{ $message }}"/>
        @enderror

        <div class="grid gap-6 lg:grid-cols-5">
            <div class="lg:col-span-2">
                <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-950/60">
                    <div class="border-b border-neutral-200 p-4 dark:border-neutral-800">
                        <flux:heading>
                            {{ $editingId ? __('Edit User') : __('Create User') }}
                        </flux:heading>
                    </div>

                    <form wire:submit="save" class="space-y-4 p-4">
                        <flux:input wire:model="name" :label="__('Name')" type="text" required />
                        <flux:input wire:model="email" :label="__('Email')" type="email" required />

                        <flux:input
                            wire:model="password"
                            :label="__('Password')"
                            type="password"
                            :placeholder="$editingId ? __('Leave blank to keep current password') : ''"
                            viewable
                        />
                        <flux:input
                            wire:model="password_confirmation"
                            :label="__('Confirm password')"
                            type="password"
                            viewable
                        />

                        <div class="flex items-center gap-2">
                            <flux:button variant="primary" type="submit" class="flex-1">
                                {{ $editingId ? __('Update') : __('Create') }}
                            </flux:button>

                            @if ($editingId)
                                <flux:button type="button" variant="outline" wire:click="startCreate">
                                    {{ __('Cancel') }}
                                </flux:button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-3">
                <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-950/60">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
                            <thead class="bg-neutral-50 text-left text-xs font-semibold uppercase tracking-wide text-neutral-700 dark:bg-neutral-900/40 dark:text-neutral-200">
                                <tr>
                                    <th class="px-4 py-3">ID</th>
                                    <th class="px-4 py-3">Name</th>
                                    <th class="px-4 py-3">Email</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200 text-sm text-neutral-800 dark:divide-neutral-800 dark:text-neutral-100">
                                @forelse ($users as $user)
                                    <tr class="hover:bg-neutral-50/70 dark:hover:bg-neutral-900/30">
                                        <td class="px-4 py-3 tabular-nums">{{ $user->id }}</td>
                                        <td class="px-4 py-3">{{ $user->name }}</td>
                                        <td class="px-4 py-3">{{ $user->email }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex justify-end gap-2">
                                                <flux:button size="sm" variant="outline" type="button" wire:click="startEdit({{ $user->id }})">
                                                    {{ __('Edit') }}
                                                </flux:button>

                                                <flux:button
                                                    size="sm"
                                                    variant="danger"
                                                    type="button"
                                                    x-data
                                                    x-on:click.prevent="confirm('{{ __('Delete this user?') }}') && $wire.delete({{ $user->id }})"
                                                >
                                                    {{ __('Delete') }}
                                                </flux:button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="px-4 py-6 text-center text-sm text-neutral-500 dark:text-neutral-400" colspan="4">
                                            No users found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-neutral-200 p-4 dark:border-neutral-800">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

