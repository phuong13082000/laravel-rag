                         ┌───────────────┐
                         │ KnowledgeBase │
                         └───────┬───────┘
                                 │
                         ┌───────▼───────┐
                         │   Documents   │
                         └───────┬───────┘
                                 │
                    Extract → Chunk → Embed
                                 │
                         ┌───────▼───────┐
                         │   pgvector    │
                         └───────┬───────┘
                                 │
               User → Conversation → Message → RAG
                                 │
                 ┌───────────────┼───────────────┐
                 │               │               │
              Summary       Recent 10       Vector Search
                 │               │               │
                 └───────────────┼───────────────┘
                                 │
                           Prompt Builder
                                 │
                              Qwen3
                                 │
                    ┌────────────┴────────────┐
                    │                         │
                Streaming                 Normal
                    │                         │
                   SSE                       JSON
                    │                         │
                    └────────────┬────────────┘
                                 │
                         Assistant Message
                                 │
                             Citation


- PostgreSQL + pgvector
- Ollama bge-m3
- Ollama qwen3:4b
- Redis Queue
- Conversation
- Message
- Citation
- RAG history
- Conversation summary
- Normal Chat
- SSE Streaming