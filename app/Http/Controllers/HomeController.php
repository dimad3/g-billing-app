<?php

namespace App\Http\Controllers;

use App\Models\Entity\Entity;
use App\Models\User\Client;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        // dd(Carbon::instance(fake()->dateTimeThisMonth()));
        // dd(Client::factory()->create());
        // dd(Entity::find(1)->entityable);
        // dump(Entity::find(1)->user);
        // dump(Entity::find(54)->client);
        // return 1;
        // dd($bankNames = array_column(config('static_data.banks'), 'name'));

        return view('welcome');
    }
}
