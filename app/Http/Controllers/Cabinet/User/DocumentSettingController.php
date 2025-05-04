<?php

namespace App\Http\Controllers\Cabinet\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\DocumentSettingSaveRequest;
use App\Models\User\Agent;
use App\Models\User\DocumentSetting;
use Illuminate\Support\Facades\Auth;

class DocumentSettingController extends Controller
{
    /** Show the form for editing the specified resource. */
    public function edit()
    {
        $settings = auth()->user()->documentSetting;
        ($settings = $settings ? $settings->loadMissing('defaultAgent') : new DocumentSetting());

        // ($agentsCollection = Agent::forUser()
        //     ->select('id', 'first_name', 'last_name',)
        //     ->orderBy('last_name', 'asc')->get());

        // $agents = [];
        // foreach ($agentsCollection as $agent) {
        //     $agents[$agent->id] = "{$agent->last_name} {$agent->first_name}";
        // }
        $agentsCollection = Agent::forUser()->select(['id', 'last_name', 'first_name'])
            ->orderBy('last_name', 'asc')->get()->makeHidden(['last_name', 'first_name'])->toArray();

        // convert to plain array
        $agents = [];
        foreach ($agentsCollection as $agent) {
            $agents[$agent['id']] = $agent['full_name'];
        }
        // dd($agents);
        // Define required fields
        $requiredInputs = [
            'number_prefix',
            'next_number',
            'default_agent_id',
            'default_tax_rate',
        ];

        return view('cabinet.settings.edit', compact('settings', 'agents', 'requiredInputs'));
    }

    /** Store a newly created resource in storage. */
    public function store(DocumentSettingSaveRequest $request)
    {
        try {
            ($data = $request->validated());
            ($data['user_id'] = Auth::id());

            DocumentSetting::create($data);

            return redirect()->route('cabinet.settings')
                ->with('success', __('Settings data saved successfully.'));
        } catch (\Exception $e) {
            report($e); // Log the exception
            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to save settings data. An unexpected error occurred. Please try again later.'));
        }
    }

    /** Update the specified resource in storage. */
    public function update(DocumentSettingSaveRequest $request, DocumentSetting $settings)
    {
        try {
            ($data = $request->validated());
            ($settings);
            $updated = $settings->update($data);

            return redirect()->route('cabinet.settings')
                ->with('success', __('Settings updated successfully.'));
        } catch (\Exception $e) {
            report($e); // Log the exception
            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to update settings. An unexpected error occurred. Please try again later.'));
        }
    }
}
