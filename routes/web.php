<?php

use App\Http\Controllers\Cabinet\User\BankController;
use App\Http\Controllers\Cabinet\Document\DocumentController;
use App\Http\Controllers\Cabinet\User\AgentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Cabinet\User\ProfileController;
use App\Http\Controllers\Cabinet\User\ClientController;
use App\Http\Controllers\Cabinet\DashboardController;
use App\Http\Controllers\Cabinet\User\DocumentSettingController;
use App\Http\Controllers\Cabinet\User\SellerController;
use App\Models\User\Client;
use Illuminate\Support\Facades\Route;


// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Cabinet routes
Route::group(
    [
        'prefix' => 'cabinet',
        'as' => 'cabinet.',
        'middleware' => ['auth', 'verified'],
    ],
    function (): void {
        Route::get('/', [DashboardController::class, 'index'])->name('home');

        // User
        Route::resource('clients', ClientController::class)->only(['index', 'create', 'store']);
        Route::resource('clients', ClientController::class)->only(['edit', 'update', 'destroy'])
            ->middleware(['can:manage-client,client']);

        Route::group(
            ['prefix' => 'seller', 'as' => 'seller',],
            function (): void {
                Route::get('/', [SellerController::class, 'edit'])->name('');
                Route::post('/', [SellerController::class, 'store'])->name('.store');
                Route::put('/{entity}', [SellerController::class, 'update'])->name('.update')
                    ->can('manage-seller', 'entity');
            }
        );

        Route::group(
            ['prefix' => 'settings', 'as' => 'settings',],
            function (): void {
                Route::get('/', [DocumentSettingController::class, 'edit'])->name('');
                Route::post('/', [DocumentSettingController::class, 'store'])->name('.store');
                Route::put('/{settings}', [DocumentSettingController::class, 'update'])->name('.update')
                    ->can('manage-documentSetting', 'settings');
            }
        );

        Route::resource('agents', AgentController::class)->only('index', 'create', 'store');
        Route::resource('agents', AgentController::class)->only('edit', 'update', 'destroy')
            ->middleware('can:manage-agent,agent');

        Route::resource('banks', BankController::class)->only('index', 'create', 'store');
        Route::resource('banks', BankController::class)->only('edit', 'update', 'destroy')
            ->middleware('can:manage-bank,bank');

        // Documents
        // Route::resource('documents', DocumentController::class);
        Route::resource('documents', DocumentController::class)->only('index', 'show', 'create', 'store');
        Route::resource('documents', DocumentController::class)->only('edit', 'update', 'destroy')
            ->middleware('can:manage-document,document');
        Route::get('documents/invoice/{document}', [DocumentController::class, 'generateInvoice'])
            ->name('documents.generate-invoice')
            ->middleware('can:manage-document,document');
        // Route::resource('document-items', DocumentItemController::class);
    }
);

Route::get('/api/legal-forms', function () {
    $entityType = request('entity_type');
    ($legalForms = config('static_data.legal_forms.' . $entityType, []));
    return response()->json($legalForms);
})->middleware(['auth']);
// })->middleware(['auth', 'can:manage-client,client']);

Route::get('/api/clients/{client}/default-values', [ClientController::class, 'defaultValues'])
->middleware(['auth', 'can:manage-client,client']);

require __DIR__ . '/auth.php';
