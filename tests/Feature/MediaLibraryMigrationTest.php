<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

it('creates the media table without replacing an existing table', function (): void {
    config()->set('database.default', 'sqlite');
    config()->set('database.connections.sqlite.database', ':memory:');
    DB::purge('sqlite');

    $migration = require database_path('migrations/2026_11_11_033258_create_media_table.php');
    $migration->up();
    expect(Schema::hasColumns('media', ['model_type', 'model_id', 'disk', 'file_name']))->toBeTrue();

    DB::table('media')->insert([
        'model_type' => 'document', 'model_id' => 1, 'collection_name' => 'documents',
        'name' => 'example', 'file_name' => 'example.pdf', 'disk' => 'local', 'size' => 1,
        'manipulations' => '{}', 'custom_properties' => '{}', 'generated_conversions' => '{}', 'responsive_images' => '{}',
    ]);
    $migration->up();
    expect(DB::table('media')->count())->toBe(1);
});
