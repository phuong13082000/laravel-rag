<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            'CREATE INDEX document_chunks_embedding_hnsw_index
             ON document_chunks
             USING hnsw (embedding vector_cosine_ops)'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(
            'DROP INDEX IF EXISTS document_chunks_embedding_hnsw_index'
        );
    }
};
