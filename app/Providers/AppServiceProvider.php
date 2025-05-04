<?php

namespace App\Providers;

use App\Models\Entity\Entity;
use App\Models\User\Client;
use App\Models\User\User;
use App\Policies\SellerPolicy;
use App\Repositories\AgentRepository;
use App\Repositories\BankRepository;
use App\Repositories\ClientRepository;
use App\Repositories\DocumentRepository;
use App\Repositories\EntityBankAccountRepository;
use App\Repositories\EntityRepository;
use App\Repositories\Interfaces\AgentRepositoryInterface;
use App\Repositories\Interfaces\BankRepositoryInterface;
use App\Repositories\Interfaces\ClientRepositoryInterface;
use App\Repositories\Interfaces\DocumentRepositoryInterface;
use App\Repositories\Interfaces\EntityBankAccountRepositoryInterface;
use App\Repositories\Interfaces\EntityRepositoryInterface;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // If you want a single shared instance across the app, use singleton() instead of bind():
        // This is useful for performance optimization if the repository does not need multiple instances.
        // $this->app->singleton(ClientRepositoryInterface::class, ClientRepository::class);

        // // choose only one repository you want to use
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(EntityRepositoryInterface::class, EntityRepository::class);
        // $this->app->bind(EntityBankAccountRepositoryInterface::class, EntityBankAccountRepository::class);
        $this->app->bind(AgentRepositoryInterface::class, AgentRepository::class);
        $this->app->bind(BankRepositoryInterface::class, BankRepository::class);
        $this->app->bind(DocumentRepositoryInterface::class, DocumentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // If any other models are morphed but not mapped, Laravel
        // will throw a `ClassMorphViolationException` exception.
        Relation::enforceMorphMap([
            'user' => User::class,
            'client' => Client::class,
        ]);

        // // You can enforce a morph map using the single call shown above,
        // // or you can also use the standalone requireMorphMap method on the Relation class:
        // // Turn morph map enforcement on (new in 8.59.0).
        // Relation::requireMorphMap();
        // // And then map your morphs in the standard way.
        // Relation::morphMap([
        //     'user' => User::class,
        //     'client' => Client::class,
        // ]);

        // register policies and their corresponding models
        // if policies are not automatically discoved (does not follow naming convention)
        Gate::policy(Entity::class, SellerPolicy::class);
    }
}
