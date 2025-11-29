$tests = @(
    @{text='A'; language='en'},
    @{text='hello'; language='en'},
    @{text='مرحبا'; language='ar'},
    @{text='نعم'; language='ar'}
)

foreach ($t in $tests) {
    $json = $t | ConvertTo-Json -Depth 5
    Write-Host "POSTing: $json"
    try {
        $resp = Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/convert-text' -Method Post -ContentType 'application/json' -Body $json
        Write-Host "Response: " ($resp | ConvertTo-Json -Depth 5)
    } catch {
        Write-Host "Error POSTing: $_"
    }
}
