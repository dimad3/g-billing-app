<?php

namespace App\Http\Controllers\Cabinet\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ClientEntitySaveRequest;
use App\Models\User\Bank;
use App\Models\User\Client;
use App\Services\ClientEntityService;

class ClientController extends Controller
{
    public function __construct(protected ClientEntityService $clientEntityService) {}

    public function index()
    {
        // repository is not used because Collection can not be paginated
        ($clients = Client::forUser()
            ->orderBy('id', 'desc')
            ->with(['entity'])
            ->paginate(10));

        return view('cabinet.clients.index', compact('clients'));
    }

    /** Show the form for creating a new resource. */
    public function create()
    {
        return $this->edit(new Client());
    }

    /** Show the form for editing the specified resource. */
    public function edit(Client $client)
    {
        if (!$client) {
            return redirect()->route('cabinet.clients.index')->with('error', 'Client not found.');
        }

        $client->loadMissing('entity.bankAccounts');
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
            'email',
            'due_days',
            'discount_rate',
        ];

        return view('cabinet.clients.create_or_edit', compact('client', 'entityTypes', 'banks', 'requiredInputs'));
    }

    /** Store a newly created resource in storage. */
    public function store(ClientEntitySaveRequest $request)
    {
        try {
            $data = $request->validated();

            // Create client
            $this->clientEntityService->createClient($data);

            return redirect()->route('cabinet.clients.index')
                ->with('success', 'Client created successfully.');
        } catch (\Exception $e) {
            report($e); // Log the exception
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create client. An unexpected error occurred. Please try again later.');
        }
    }

    /** Update the specified resource in storage. */
    public function update(ClientEntitySaveRequest $request, Client $client)
    {
        try {
            $data = $request->validated();

            // Update client
            $this->clientEntityService->updateClient($data, $client);

            return redirect()->route('cabinet.clients.index')
                ->with('success', 'Client updated successfully.');
        } catch (\Exception $e) {
            report($e); // Log the exception
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update client. An unexpected error occurred. Please try again later.');
        }
    }

    /** Remove the specified resource from storage. */
    public function destroy(Client $client)
    {
        try {
            $this->clientEntityService->deleteClient($client);
            return redirect()->route('cabinet.clients.index')
                ->with('success', 'Client deleted successfully.');
        } catch (\Exception $e) {
            report($e); // Log the exception
            return redirect()->back()
                ->withInput()
                // ->with('error', 'Failed to delete client. An unexpected error occurred. Please try again later.');
                ->with('error', $e->getMessage()); // Use the actual exception message
        }
    }

    public function defaultValues(Client $client)
    {
        return response()->json([
            'due_days' => $client->due_days,
            'discount_rate' => $client->discount_rate,
        ]);
    }
}
