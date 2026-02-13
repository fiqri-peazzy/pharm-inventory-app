<?php

namespace App\Livewire\Accounting;

use App\Models\Account;
use Livewire\Component;
use Livewire\WithPagination;

class CoAIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = '';
    
    // Modal states
    public $showModal = false;
    public $isEdit = false;
    public $accountId;

    // Form data
    public $code, $name, $type, $parent_id, $normal_balance = 'debit', $description, $is_active = true;

    protected $rules = [
        'code' => 'required|unique:accounts,code',
        'name' => 'required',
        'type' => 'required',
        'normal_balance' => 'required|in:debit,credit',
    ];

    public function render()
    {
        $query = Account::with('parent')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter))
            ->orderBy('code');

        return view('livewire.accounting.coa-index', [
            'accounts' => $query->paginate(20),
            'parentAccounts' => Account::whereNull('parent_id')->orderBy('code')->get(),
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->resetForm();
        $this->accountId = $id;
        $account = Account::findOrFail($id);
        
        $this->code = $account->code;
        $this->name = $account->name;
        $this->type = $account->type;
        $this->parent_id = $account->parent_id;
        $this->normal_balance = $account->normal_balance;
        $this->description = $account->description;
        $this->is_active = $account->is_active;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $rules = $this->rules;
        if ($this->isEdit) {
            $rules['code'] = 'required|unique:accounts,code,' . $this->accountId;
        }

        $this->validate($rules);

        $data = [
            'code' => $this->code,
            'name' => $this->name,
            'type' => $this->type,
            'parent_id' => $this->parent_id ?: null,
            'normal_balance' => $this->normal_balance,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        if ($this->isEdit) {
            Account::find($this->accountId)->update($data);
            $msg = 'Akun berhasil diperbarui.';
        } else {
            Account::create($data);
            $msg = 'Akun baru berhasil ditambahkan.';
        }

        $this->showModal = false;
        $this->dispatch('notify', ['type' => 'success', 'message' => $msg]);
    }

    public function toggleStatus($id)
    {
        $account = Account::findOrFail($id);
        $account->is_active = !$account->is_active;
        $account->save();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Status akun diperbarui.']);
    }

    private function resetForm()
    {
        $this->code = '';
        $this->name = '';
        $this->type = '';
        $this->parent_id = null;
        $this->normal_balance = 'debit';
        $this->description = '';
        $this->is_active = true;
        $this->accountId = null;
    }
}
