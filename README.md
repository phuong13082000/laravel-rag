                    USER
                      │
                      ▼
               POST /chat
                      │
                      ▼
                RagService
                 /       \
                /         \
               ▼           ▼
           Search       Prompt Builder
              │              │
           BGE-M3            │
              │              │
         pgvector            │
              │              │
          Top-K chunks ──────┘
                 │
                 ▼
              Prompt
                 │
                 ▼
              Qwen3:4b
                 │
                 ▼
               Answer
