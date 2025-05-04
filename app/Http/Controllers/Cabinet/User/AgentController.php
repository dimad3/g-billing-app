<?php

namespace App\Http\Controllers\Cabinet\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\AgentSaveRequest;
use App\Models\User\Agent;
use App\Services\AgentService;

class AgentController extends Controller
{
    public function __construct(protected AgentService $agentService) {}

    public function index()
    {
        // dd('index');
        // repository is not used because Collection can not be paginated
        ($agents = Agent::forUser()
            ->orderBy('last_name')
            ->paginate(10));

        return view('cabinet.agents.index', compact('agents'));
    }

    /** Show the form for creating a new resource. */
    public function create()
    {
        return $this->edit(new Agent());
    }

    /** Show the form for editing the specified resource. */
    public function edit(Agent $agent)
    {
        if (! $agent) {
            return redirect()->route('cabinet.agents.index')->with('error', __('Employee not found.'));
        }

        $roles = config('static_data.roles');

        // Define required fields
        $requiredInputs = [
            'first_name',
            'last_name',
            'position',
            'email',
            'role',
        ];

        return view('cabinet.agents.create_or_edit', compact('agent', 'roles', 'requiredInputs'));
    }

    /** Store a newly created resource in storage. */
    public function store(AgentSaveRequest $request)
    {
        try {
            $data = $request->validated();

            // Create agent
            $this->agentService->createAgent($data);

            return redirect()->route('cabinet.agents.index')
                ->with('success', __('Employee created successfully.'));
        } catch (\Exception $e) {
            report($e); // Log the exception
            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to create agent. An unexpected error occurred. Please try again later.'));
        }
    }

    /** Update the specified resource in storage. */
    public function update(AgentSaveRequest $request, Agent $agent)
    {
        try {
            $data = $request->validated();

            // Update agent
            $this->agentService->updateAgent($data, $agent);

            return redirect()->route('cabinet.agents.index')
                ->with('success', __('Employee updated successfully.'));
        } catch (\Exception $e) {
            report($e); // Log the exception
            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to update agent. An unexpected error occurred. Please try again later.'));
        }
    }

    /** Remove the specified resource from storage. */
    public function destroy(Agent $agent)
    {
        try {
            $this->agentService->deleteAgent($agent);
            return redirect()->route('cabinet.agents.index')
                ->with('success', __('Employee deleted successfully.'));
        } catch (\Exception $e) {
            report($e); // Log the exception
            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to delete agent. An unexpected error occurred. Please try again later.'));
        }
    }
}
