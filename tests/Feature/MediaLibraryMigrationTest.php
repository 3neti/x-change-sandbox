<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function (): void {
    $this->originalConnection = DB::getDefaultConnection();
    config()->set('database.connections.document_migration_test', [
        'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true,
    ]);
    DB::setDefaultConnection('document_migration_test');
});

afterEach(function (): void {
    DB::setDefaultConnection($this->originalConnection);
    DB::purge('document_migration_test');
});

it('creates the media table without replacing an existing table', function (): void {

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

it('refuses to accept an incompatible existing media table', function (): void {
    Schema::create('media', function ($table): void {
        $table->id();
    });

    $migration = require database_path('migrations/2026_11_11_033258_create_media_table.php');
    expect(fn () => $migration->up())->toThrow(RuntimeException::class, 'missing required columns');
});
