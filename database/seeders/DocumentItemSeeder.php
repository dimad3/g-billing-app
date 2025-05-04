<?php

namespace Database\Seeders;

use App\Models\Document\Document;
use App\Models\Document\DocumentItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Document::all()->each(function ($document) {
            DocumentItem::factory(rand(1, 5))->create(['document_id' => $document->id]);
        });
    }
}
