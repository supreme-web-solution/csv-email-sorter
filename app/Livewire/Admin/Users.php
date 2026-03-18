<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('User Management')]
class Users extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $editingId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function startCreate(): void
    {
        $this->resetValidation();
        $this->editingId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
    }

    public function startEdit(int $id): void
    {
        $this->resetValidation();

        $user = User::query()->findOrFail($id);

        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->password_confirmation = '';
    }

    public function save(): void
    {
        $userId = $this->editingId;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class, 'email')->ignore($userId),
            ],
        ];

        if ($userId) {
            if ($this->password !== '') {
                $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
            }
        } else {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $validated = $this->validate($rules);

        if ($userId) {
            $user = User::query()->findOrFail($userId);
            $user->name = $validated['name'];
            $user->email = $validated['email'];

            if (array_key_exists('password', $validated)) {
                $user->password = $validated['password'];
            }

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();
        } else {
            User::query()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);
        }

        $this->startCreate();
        $this->dispatch('user-saved');
    }

    public function delete(int $id): void
    {
        if ((int) Auth::id() === $id) {
            $this->addError('delete', 'You cannot delete your own account.');

            return;
        }

        User::query()->whereKey($id)->delete();
        $this->dispatch('user-deleted');
    }

    public function render()
    {
        $q = User::query()->orderBy('id', 'desc');

        if ($this->search !== '') {
            $search = '%'.$this->search.'%';
            $q->where(function ($query) use ($search) {
                $query->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search);
            });
        }

        return view('livewire.admin.users', [
            'users' => $q->paginate(10),
        ]);
    }
}

