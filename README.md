                         Laravel RAG
                             │
              ┌──────────────┴──────────────┐
              │                             │
          Documents                     Search
              │                             │
          Queue Job                     Query
              │                             │
          PDF Extract                   BGE-M3
              │                             │
           Chunking                         │
              │                             │
       document_chunks ◄────────────── vector search
              │
          BGE-M3
              │
        vector(1024)
              │
         PostgreSQL + pgvector
