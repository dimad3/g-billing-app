<?php

namespace App\Http\Controllers\Cabinet\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\BankSaveRequest;
use App\Models\User\Bank;
use App\Services\BankService;

class BankController extends Controller
{
    public function __construct(protected BankService $bankService) {}

    public function index()
    {
        // repository is not used because Collection can not be paginated
        ($banks = Bank::forUser()
            ->orderBy('name')
            ->paginate(20));

        return view('cabinet.banks.index', compact('banks'));
    }

    /** Show the form for creating a new resource. */
    public function create()
    {
        return $this->edit(new Bank());
    }

    /** Show the form for editing the specified resource. */
    public function edit(Bank $bank)
    {
        if (! $bank) {
            return redirect()->route('cabinet.banks.index')->with('error', __('Bank not found.'));
        }

        // Define required fields
        $requiredInputs = [
            'name',
            'bank_code',
        ];

        return view('cabinet.banks.create_or_edit', compact('bank', 'requiredInputs'));
    }

    /** Store a newly created resource in storage. */
    public function store(BankSaveRequest $request)
    {
        try {
            $data = $request->validated();

            // Create bank
            $this->bankService->createBank($data);

            return redirect()->route('cabinet.banks.index')
                ->with('success', __('Bank created successfully.'));
        } catch (\Exception $e) {
            report($e); // Log the exception
            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to create bank. An unexpected error occurred. Please try again later.'));
        }
    }

    /** Update the specified resource in storage. */
    public function update(BankSaveRequest $request, Bank $bank)
    {
        try {
            $data = $request->validated();

            // Update bank
            $this->bankService->updateBank($data, $bank);

            return redirect()->route('cabinet.banks.index')
                ->with('success', __('Bank updated successfully.'));
        } catch (\Exception $e) {
            report($e); // Log the exception
            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to update bank. An unexpected error occurred. Please try again later.'));
        }
    }

    /** Remove the specified resource from storage. */
    public function destroy(Bank $bank)
    {
        try {
            $this->bankService->deleteBank($bank);
            return redirect()->route('cabinet.banks.index')
                ->with('success', __('Bank deleted successfully.'));
        } catch (\Exception $e) {
            report($e); // Log the exception
            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to delete bank. An unexpected error occurred. Please try again later.'));
        }
    }
}
