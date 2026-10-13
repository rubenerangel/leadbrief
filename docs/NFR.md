# Non-Functional Requirements

Quality characteristics follow ISO/IEC 25010:2023.

## Context

All inference runs locally on CPU: Intel Core i7-1165G7 (4 cores), ~20 GB RAM, no discrete GPU.
Models served by Ollama 0.40.3: `llama3.2:3b` (generation) and `nomic-embed-text` (embeddings, 768 dimensions).

## Baseline (2026-10-13)

Measured with `scripts/benchmark-baseline.ps1`, laptop on AC power. Warm values are medians of 5 runs.

| Metric | Value |
|---|---|
| Generation, cold (54 input / 31 output tokens) | 6.57 s, of which 3.12 s is model load |
| Generation, warm | 3.00 s |
| Prompt throughput, warm | 149 tokens/s (range 142 to 160) |
| Output throughput, warm | 11.8 tokens/s (range 11.7 to 11.9) |
| Embedding, single text, cold / warm | 0.56 s / 0.04 s |
| Embedding throughput, batch of 20 short texts | 33.7 texts/s |

Limitations: 5 runs and a short prompt. This measures the hardware, not a real brief.

### Projection for a real brief

Assuming ~1,500 input tokens (retrieved chunks plus instructions) and ~400 output tokens of JSON:
~10 s of prompt processing + ~34 s of generation = **~45 s warm**, plus ~3 s when the model is cold.
Output length dominates latency, so the brief schema must stay compact.
This is an estimate; prompt throughput on CPU usually drops with longer prompts.
It will be replaced by a measured p50/p95 in week 2.

## Constraints

| ID | Constraint | Target | Verified by |
|---|---|---|---|
| CON-01 | Inference cost | $0, local models only | Architecture |
| CON-02 | Hardware | Runs on the machine described above, CPU only | Baseline |
| CON-03 | Data | All data is synthetic; no data from any real company | Seeder |

## Quality requirements

| ID | ISO 25010 characteristic | Requirement | Target | Verified by |
|---|---|---|---|---|
| NFR-01 | Functional suitability | Every citation points to a chunk retrieved for that request | 100% (any invalid citation fails the eval) | Evals command |
| NFR-02 | Functional suitability | No claim without a source | 0 forbidden claims in the golden set | Evals command |
| NFR-03 | Functional suitability | With insufficient context, the response says so explicitly | 100% of "no context" golden cases | Evals command |
| NFR-04 | Performance efficiency | Brief generation p95 | Set after week 2 measurement (projection: ~45 s warm) | Evals command (p50/p95) |
| NFR-05 | Performance efficiency | Brief generation never blocks the HTTP request | API returns a job id in < 300 ms; client polls | Manual check |
| NFR-06 | Performance efficiency | Unchanged content is never re-embedded | 0 embedding calls when re-ingesting an unchanged activity | Model call log |
| NFR-07 | Performance efficiency | Identical retrieved chunk set reuses the cached brief | 0 model calls on cache hit | Model call log |
| NFR-08 | Reliability | Model calls have a timeout and bounded retries; a failed job ends in a visible failed state, never hangs or disappears | 100% of jobs end in `completed` or `failed` | Manual check with Ollama stopped |
| NFR-09 | Security | Activity text is treated as data, never as instructions (prompt injection) | 0 injected instructions followed in the golden set's injection cases | Evals command |
| NFR-10 | Security | No data leaves the machine | No external API calls | Architecture |
| NFR-11 | Maintainability | Every model call is logged with model, latency, chunk ids and validation result | 100% of calls | Model call log |
| NFR-12 | Maintainability | Switching the LLM provider requires no changes to application services | New adapter + one binding change in the service provider | Code review (HTTP to SDK switch, week 4) |
| NFR-13 | Flexibility | A fresh clone runs with `docker compose up` plus the documented model pulls | Documented in README | Manual check |

## Out of scope

- **Scalability and concurrency:** single-user local tool. Ollama processes one request per model at a time by default, so multi-user load is not a goal.
- **Availability:** no uptime target; the system runs on a personal laptop.
- **Accessibility and UI performance:** not measured in this phase.
