$base = "http://localhost:11434"
$gen  = "llama3.2:3b"
$emb  = "nomic-embed-text"

function Post($path, $body) {
    Invoke-RestMethod -Uri "$base$path" -Method Post -ContentType "application/json" `
        -Body ($body | ConvertTo-Json -Depth 5)
}
function Sec($ns) { [math]::Round($ns / 1e9, 2) }

# Unload models so the first run is truly cold
ollama stop $gen 2>$null | Out-Null
ollama stop $emb 2>$null | Out-Null

Write-Host "`n== Generation ($gen) =="
1..6 | ForEach-Object {
    $body = @{
        model   = $gen
        prompt  = "Note #$_. Summarize in two sentences: the client said the price is too high and asked for a follow-up call next month."
        stream  = $false
        options = @{ temperature = 0; seed = 42; num_predict = 120 }
    }
    $r = Post "/api/generate" $body
    $kind = if ($_ -eq 1) { "cold" } else { "warm" }
    [pscustomobject]@{
        run = $_; kind = $kind
        total_s = Sec $r.total_duration; load_s = Sec $r.load_duration
        in_tok = $r.prompt_eval_count; out_tok = $r.eval_count
        tok_per_s = [math]::Round($r.eval_count / ($r.eval_duration / 1e9), 1)
    }
} | Format-Table -AutoSize

Write-Host "== Embeddings ($emb) =="
$q = "search_query: what objection did the client raise?"
$cold  = Post "/api/embed" @{ model = $emb; input = $q }
$warm  = Post "/api/embed" @{ model = $emb; input = $q }
$texts = 1..20 | ForEach-Object { "search_document: Call note $_. The client raised a pricing objection and asked for a discount." }
$batch = Post "/api/embed" @{ model = $emb; input = $texts }

[pscustomobject]@{
    single_cold_s = Sec $cold.total_duration
    single_warm_s = Sec $warm.total_duration
    batch20_s     = Sec $batch.total_duration
    texts_per_s   = [math]::Round(20 / ($batch.total_duration / 1e9), 1)
} | Format-Table -AutoSize
