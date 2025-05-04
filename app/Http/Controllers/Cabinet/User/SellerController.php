<?php

namespace App\Http\Controllers\Cabinet\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\SellerSaveRequest;
use App\Models\User\Bank;
use App\Models\Entity\Entity;
use App\Services\SellerEntityService;

class SellerController extends Controller
{
    public function __construct(protected SellerEntityService $sellerEntityService) {}

    /** Show the form for editing the specified resource. */
    public function edit()
    {
        $entity = auth()->user()->entity;
        ($entity = $entity ? $entity->loadMissing('bankAccounts') : new Entity());

        $entityTypes = config('static_data.entity_types');
        $banks = Bank::forUser()->orderBy('name', 'asc')->get();
        // Define required fields
        $requiredInputs = [
            'entity_type',
            'legal_form',
            'name',
            'first_name',
            'last_name',
            'id_number',
        ];

        return view('cabinet.seller.edit', compact('entity', 'entityTypes', 'banks', 'requiredInputs'));
    }

    /** Store a newly created resource in storage. */
    public function store(SellerSaveRequest $request)
    {
        try {
            ($data = $request->validated());

            // Create entity
            $this->sellerEntityService->createEntity($data);

            return redirect()->route('cabinet.seller')
                ->with('success', __('Seller data saved successfully.'));
        } catch (\Exception $e) {
            report($e); // Log the exception
            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to save seller data. An unexpected error occurred. Please try again later.'));
        }
    }

    /** Update the specified resource in storage. */
    public function update(SellerSaveRequest $request, Entity $entity)
    {
        try {
            $data = $request->validated();

            // Update entity
            $this->sellerEntityService->updateEntity($data, $entity);

            return redirect()->route('cabinet.seller')
                ->with('success', __('Seller updated successfully.'));
        } catch (\Exception $e) {
            report($e); // Log the exception
            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to update seller. An unexpected error occurred. Please try again later.'));
        }
    }

    // /** Remove the specified resource from storage. */
    // public function destroy(Entity $entity)
    // {
    //     try {
    //         // $this->clientEntityService->deleteClient($entity);
    //         return redirect()->route('cabinet.clients.index')
    //             ->with('success', __('Client deleted successfully.'));
    //     } catch (\Exception $e) {
    //         report($e); // Log the exception
    //         return redirect()->back()
    //             ->withInput()
    //             ->with('error', __('Failed to delete client. An unexpected error occurred. Please try again later.'));
    //     }
    // }
}
