<?php

namespace App\Http\Controllers\Cabinet\Document;

use App\Http\Controllers\Controller;
use App\Models\Document\Document;
use App\Http\Requests\Document\DocumentSaveRequest;
use App\Models\User\Agent;
use App\Models\User\Client;
use App\Services\DocumentService;
use PDF;
use Illuminate\Support\Arr;
use Str;

class DocumentController extends Controller
{
    public function __construct(protected DocumentService $documentService) {}

    public function index()
    {
        // repository is not used because Collection can not be paginated
        ($documents = Document::forUser()
            ->orderBy('id', 'desc')
            ->with(['client.entity'])
            ->paginate(10));


        $documentTypes = config('static_data.document_types'); // Get document types from config
        $statuses = config('static_data.document_statuses'); // Get document statuses from config

        return view('cabinet.documents.index', compact('documents', 'documentTypes', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->edit(new Document());
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        if (!$document) {
            return redirect()->route('cabinet.documents.index')->with('error', 'Document not found.');
        }

        ($document = $document->loadMissing(['client', 'agent', 'documentItems']));

        $clientsCollection = Client::forUser()
            ->select('id')
            ->with('entity')
            ->get();
        // convert collection to plain array
        $notSortedArray = [];
        foreach ($clientsCollection as $client) {
            // $clients[$client['id']] = $client['fullName'];
            $notSortedArray[$client->id] = $client->entity->fullName;
        }
        $clients = Arr::sort($notSortedArray);
        // dd($clients);

        $documentTypes = config('static_data.document_types');
        ($statuses = config('static_data.document_statuses'));

        $agentsCollection = Agent::forUser()->select(['id', 'last_name', 'first_name'])
            ->orderBy('last_name', 'asc')->get()->makeHidden(['last_name', 'first_name'])->toArray();
        // convert to plain array
        $agents = [];
        foreach ($agentsCollection as $agent) {
            $agents[$agent['id']] = $agent['full_name'];
        }
        // dd($agents);

        ($settings = auth()->user()->documentSetting);
        $defaultTaxRate = $settings?->default_tax_rate ?? 21;

        if ($document->exists) {
            $defaultDocumentDate = '';
            $defaultNumber = '';
            $defaultStatus = '';
            $defaultAgentId = '';
        } else {
            // Set default values for new document
            ($defaultDocumentDate = date('Y-m-d'));
            $defaultNumber = $settings?->number_prefix . $settings?->next_number;
            $defaultStatus = 'draft';
            $defaultAgentId = $settings?->default_agent_id;
        }

        // Define required fields
        $requiredInputs = [
            'client_id',
            'document_date',
            'number',
            'document_type',
            'status',
            'due_date',
        ];

        return view('cabinet.documents.create_or_edit', compact(
            'document',
            'clients',
            'documentTypes',
            'statuses',
            'agents',
            'defaultTaxRate',
            'defaultDocumentDate',
            'defaultNumber',
            'defaultStatus',
            'defaultAgentId',
            'requiredInputs',
        ));
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DocumentSaveRequest $request)
    {
        try {
            ($data = $request->validated()); // Extract validated data from the request

            $document = $this->documentService->createDocument($data); // Create document

            return redirect()->route('cabinet.documents.index')
                ->with('success', __('Document created successfully.'));
        } catch (\Exception $e) {
            report($e); // Log the exception
            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to create document. An unexpected error occurred. Please try again later.'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DocumentSaveRequest $request, Document $document)
    {
        // dd($request->validated());
        try {
            $data = $request->validated(); // Extract validated data from the request

            $document = $this->documentService->updateDocument($document, $data); // Update document

            return redirect()->route('cabinet.documents.edit', $document)
                ->with('success', __('Document updated successfully.'));
        } catch (\Exception $e) {
            report($e); // Log the exception
            return redirect()->back()
                ->withInput()
                ->with('error', __('Failed to update document. An unexpected error occurred. Please try again later.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        try {
            $this->documentService->deleteDocument($document);
            return redirect()->route('cabinet.documents.index')
                ->with('success', 'Document deleted successfully.');
        } catch (\Exception $e) {
            report($e); // Log the exception
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to delete document. An unexpected error occurred. Please try again later.');
        }
    }

    public function generateInvoice(Document $document)
    {
        if (!$document) {
            return redirect()->route('cabinet.documents.index')->with('error', 'Document not found.');
        }

        // Fetch document with related client and items
        ($document = $document->loadMissing(['client.entity.bankAccounts.bank', 'agent', 'documentItems']));
        ($documentType =  config("static_data.document_types.{$document->document_type}"));

        $seller = auth()->user()->entity->loadMissing(['bankAccounts.bank']);
        // dd($seller->fullName);
        // dd($seller->toArray());
        $client = $document->client->entity;
        // dump($document->client->entity->getAppends());
        // dd($client);

        $totals = $this->documentService->calculateTotals($document->documentItems, $document->advance_paid);

        $totalInWords = $this->floatToWordsWithCurrency($totals['payable_amount']);

        // Pass data to the view
        $data = [
            'document' => $document,
            'documentType' => $documentType,
            'seller' => $seller,
            'client' => $client,
            'totals' => $totals,
            'totalInWords' => $totalInWords,
        ];

        // Load view file and generate PDF
        $pdf = PDF::loadView('cabinet.documents.invoice', $data);
        $fileName = Str::slug("{$document->document_date}-{$documentType}-{$document->number}", '-') . '.pdf';

        // Return a response with the PDF to show in the browser
        return $pdf->stream($fileName);
    }

    protected function floatToWordsWithCurrency(float $number, string $currency = 'euro', string $locale = 'en'): string
    {
        $formatter = new \NumberFormatter($locale, \NumberFormatter::SPELLOUT);

        $euros = floor($number);
        ($cents = round(($number - $euros) * 100));

        $euroWords = $formatter->format($euros);
        // $centWords = $formatter->format($cents);
        $formattedCents = str_pad((string) $cents, 2, '0', STR_PAD_LEFT);
        // dd((int) $cents === 1);
        $centSuffix = (int) $cents === 1 ? 'cent' : 'cents';

        return ucfirst("{$euroWords} {$currency} and {$formattedCents} {$centSuffix}");
    }
}
