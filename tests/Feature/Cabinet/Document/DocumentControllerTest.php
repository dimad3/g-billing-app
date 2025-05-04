<?php

namespace Tests\Feature\Cabinet\Document;

use App\Http\Controllers\Cabinet\Document\DocumentController;
use App\Models\User\User;
use App\Models\Document\Document;
use App\Models\Document\DocumentItem;
use App\Models\Entity\Entity;
use App\Models\User\Agent;
use App\Models\User\Client;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DocumentControllerTest extends TestCase
{
    use DatabaseTransactions;

    private DocumentController $controller;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = app(DocumentController::class);

        $this->user = User::factory()->create();
        Auth::login($this->user);
    }

    #[Test]
    public function it_displays_document_index_page_with_documents()
    {
        // Arrange: Create client and related entity
        $client = Client::factory()->for($this->user, 'user')->create();
        $entity = Entity::factory()->for($client, 'entityable')->create();

        // Arrange: Create documents for the user
        $documents = Document::factory()
            ->count(15) // More than pagination limit to test pagination
            ->for($this->user, 'user')
            ->for($client, 'client')
            ->create();

        // Act: Visit the document index page
        $response = $this->get(route('cabinet.documents.index'));

        // Assert: Page loads correctly
        $response->assertStatus(200)
            ->assertViewIs('cabinet.documents.index')
            ->assertViewHas('documents')
            ->assertSee('Invoices List');

        // Assert: Ensure at least one document is visible in response
        // if the orderBy in the controlle->index() is `asc` tthan use `first()`
        $response->assertSee($documents->last()->number);
    }

    #[Test]
    public function it_shows_create_document_form()
    {
        $response = $this->get(route('cabinet.documents.create'));

        $response->assertStatus(200)
            ->assertViewIs('cabinet.documents.create_or_edit')
            ->assertSee('Create New Invoice')
            ->assertSee('Invoice Items')
            ->assertSee('Payable Amount')
            ->assertViewHas([
                'document',
                'clients',
                'documentTypes',
                'statuses',
                'agents',
                'requiredInputs',
                'defaultDocumentDate',
                'defaultNumber',
                'defaultStatus',
                'defaultAgentId'
            ]);
    }

    #[Test]
    public function it_shows_edit_document_form()
    {
        // the issue: sometimes $document->client->entity is null, causing the test to fail
        // $document = Document::factory()->for($this->user)
        //     // ->has(Client::factory()->for(Entity::factory()), 'client')
        //     ->has(Client::factory()->has(Entity::factory(), 'entity'), 'client')
        //     ->has(DocumentItem::factory()->count(3), 'documentItems')
        //     ->createQuietly();
        // $document->load(['client.entity', 'documentItems']); // Ensure the relationships are loaded
        // // dd($document->documentItems);
        // dd($document->client->entity->getAttributes());

        // the issue tesolved
        $client = Client::factory()
            ->has(Entity::factory())
            ->create();

        // $client->load(['entity']); // Ensure the relationships are loaded
        // dd($client->entity?->getAttributes());

        // Create the document with the client
        $document = Document::factory()
            ->for($this->user)
            ->for($client)
            ->has(DocumentItem::factory()->count(3), 'documentItems')
            ->create();

        // $document->load(['client.entity', 'documentItems']); // Ensure the relationships are loaded
        // dd($document->client->entity?->getAttributes());

        $response = $this->get(route('cabinet.documents.edit', $document));

        $response->assertStatus(200)
            ->assertViewIs('cabinet.documents.create_or_edit')
            ->assertSee('Invoice Information')
            // ->assertSee($document->client->entity->fullName)
            ->assertSee('Invoice Items')
            ->assertSee('Payable Amount')
            ->assertViewHas([
                'document',
                'clients',
                'documentTypes',
                'statuses',
                'agents',
                'defaultDocumentDate',
                'defaultNumber',
                'defaultStatus',
                'defaultAgentId',
                'requiredInputs',
            ]);

        // Assert: Check if the document items are passed to the view
        $this->assertEquals(
            $document->documentItems->toArray(),
            $response->viewData('document')->documentItems->toArray()
        );
    }

    #[Test]
    public function it_stores_new_document_successfully()
    {
        $data = $this->getValidBaseData();

        $response = $this->post(route('cabinet.documents.store'), $data);

        $response->assertRedirect(route('cabinet.documents.index'))
            ->assertSessionHas('success', 'Document created successfully.');

        $this->assertDatabaseHas('documents', [
            'number' => $data['number'],
            'client_id' => $data['client_id'],
            'status' => $data['status'],
        ]);

        $document = Document::where('number', $data['number'])->first()->loadMissing('documentItems');

        $this->assertEquals($data['items'][0]['name'], $document->documentItems[0]->name);
        $this->assertEquals($data['items'][0]['unit'], $document->documentItems[0]->unit);
        $this->assertEquals($data['items'][0]['quantity'], $document->documentItems[0]->quantity);
        $this->assertEquals($data['items'][0]['price'], $document->documentItems[0]->price);
        $this->assertEquals($data['items'][0]['discount_rate'], $document->documentItems[0]->discount_rate);
        $this->assertEquals($data['items'][0]['tax_rate'], $document->documentItems[0]->tax_rate);
    }

    #[Test]
    public function it_updates_existing_document_successfully()
    {
        $document = Document::factory()->for($this->user)->create();

        ($updatedData = $this->getValidBaseData());
        $updatedData['number'] = 'Updated number';
        $updatedData['document_date'] = '2029-12-29';
        // $updatedData['total_net_amount'] = $document->total_net_amount + 0.01;
        // $updatedData['document_total'] = $document->document_total - 0.01;
        $updatedData['due_date'] = '2029-12-31';
        $updatedData['delivery_date'] = '2029-12-30';
        $updatedData['transaction_description'] = 'Updated description';
        $updatedData['items'] = [
            ['name' => 'Updated item 1', 'unit' => 'pcs', 'quantity' => 2, 'price' => 100, 'discount_rate' => 10, 'tax_rate' => 21],
            ['name' => 'Updated item 2', 'unit' => 'pcs', 'quantity' => 3, 'price' => 200, 'discount_rate' => 5, 'tax_rate' => 12],
        ];


        // dump([$document->getAttributes(), $updatedData]);
        $response = $this->put(route('cabinet.documents.update', $document), $updatedData);
        // dump(Document::find($document->id)->toArray());
        // dd(session()->all());
        $response->assertStatus(302) // Expected redirect
            ->assertRedirect(route('cabinet.documents.edit', $document))
            ->assertSessionHas('success', 'Document updated successfully.');
        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'number' => 'Updated number',
            'document_date' => '2029-12-29',
            'due_date' => '2029-12-31',
            'delivery_date' => '2029-12-30',
            'transaction_description' => 'Updated description',
        ]);

        $this->assertDatabaseHas('document_items', [
            'document_id' => $document->id,
            'name' => 'Updated item 1',
            'unit' => 'pcs',
            'quantity' => 2,
            'price' => 100,
            'discount_rate' => 10,
            'tax_rate' => 21,
        ]);

        $this->assertDatabaseHas('document_items', [
            'document_id' => $document->id,
            'name' => 'Updated item 2',
            'unit' => 'pcs',
            'quantity' => 3,
            'price' => 200,
            'discount_rate' => 5,
            'tax_rate' => 12,
        ]);
    }

    #[Test]
    public function it_deletes_document_successfully()
    {
        $document = Document::factory()->for($this->user)->create();

        $documentClone = $document->replicate(); // after delete, document may by deleted too

        $response = $this->delete(route('cabinet.documents.destroy', [$document]));

        $response->assertRedirect(route('cabinet.documents.index'))
            ->assertSessionHas('success', 'Document deleted successfully.');

        $this->assertDatabaseMissing('documents', ['id' => $documentClone->id]);
    }

    #[Test]
    public function it_prevents_document_creation_with_invalid_data()
    {
        $invalidData = [
            'client_id' => '', // Required field
            'number' => '', // Required field
            'document_date' => '', // Required field
            'items' => [
                ['quantity' => ''], // Required field
            ],
        ];

        $response = $this->post(route('cabinet.documents.store'), $invalidData);

        $response->assertSessionHasErrors([
            'client_id',
            'number',
            'document_date',
            'items.0.quantity',
        ]);
    }

    #[Test]
    public function it_prevents_unauthorized_access_to_other_users_documents()
    {
        $otherUser = User::factory()->create();
        $otherUserDocument = Document::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->get(route('cabinet.documents.edit', $otherUserDocument));
        $response->assertNotFound();

        $response = $this->put(route('cabinet.documents.update', $otherUserDocument));
        $response->assertNotFound();

        $response = $this->delete(route('cabinet.documents.destroy', $otherUserDocument));
        $response->assertNotFound();
    }

    /** Generate valid base data for a Document instance. */
    protected function getValidBaseData(): array
    {
        $client = Client::factory()->for($this->user)->create();
        $agent = Agent::factory()->for($this->user)->create();

        $document = Document::factory()->make([
            'client_id' => $client->id,
            'agent_id' => $agent->id,
        ]);

        // Generate document items
        $documentItems = DocumentItem::factory()->count(3)->make(); // using make() instead of create() prevent immediate persistence

        // Convert document items to an array and attach to the document data
        return (array_merge($document->toArray(), [
            'items' => $documentItems->toArray(),
        ]));
    }
}
