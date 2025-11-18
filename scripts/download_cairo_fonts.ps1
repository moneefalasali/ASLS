# Robust Download Cairo font weights (woff2) and generate local cairo.css
$ErrorActionPreference = 'Stop'

$dest = Join-Path $PSScriptRoot "..\public\frontend\fonts\cairo"
New-Item -ItemType Directory -Force -Path $dest | Out-Null

$cssUrl = "https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap"
Write-Host "Fetching font CSS from $cssUrl ..."

try {
    $response = Invoke-WebRequest -Uri $cssUrl -Headers @{ 'User-Agent' = 'Mozilla/5.0 (Windows NT)'} -UseBasicParsing -ErrorAction Stop
    $cssText = $response.Content
} catch {
    Write-Error "Failed to fetch Google Fonts CSS: $_"
    exit 1
}

# First try to match weight + url pairs (preferred)
$pattern1 = 'font-weight:\s*(\d+).*?url\((https?:\\/\\/[^)]+?\\.woff2)[^)]+\)'
$matches1 = [regex]::Matches($cssText, $pattern1, [System.Text.RegularExpressions.RegexOptions]::Singleline)

$fontEntries = @()

if ($matches1.Count -gt 0) {
    foreach ($m in $matches1) {
        $weight = $m.Groups[1].Value
        $url = $m.Groups[2].Value
        $fontEntries += [PSCustomObject]@{ Weight = $weight; Url = $url }
    }
} else {
    Write-Warning "Did not find weight+url pairs. Falling back to any .woff2 URLs and inferring weights."
    $pattern2 = 'https?:\\/\\/[^)''\"\s]+\\.woff2'
    $matches2 = [regex]::Matches($cssText, $pattern2)
    $seen = @{}
    foreach ($m in $matches2) {
        $url = $m.Value
        if (-not $seen.ContainsKey($url)) {
            $seen[$url] = $true
            # infer weight from filename or query
            $lower = $url.ToLower()
            if ($lower -match '700|bold') { $weight = 700 }
            elseif ($lower -match '600|semibold') { $weight = 600 }
            elseif ($lower -match '500|medium') { $weight = 500 }
            elseif ($lower -match '300|light') { $weight = 300 }
            else { $weight = 400 }
            $fontEntries += [PSCustomObject]@{ Weight = $weight; Url = $url }
        }
    }
}

if ($fontEntries.Count -eq 0) {
    Write-Warning "No .woff2 font URLs found in fetched CSS. Aborting."
    exit 0
}

# Download each entry, avoid duplicates for same weight by appending an index
$downloaded = @{}
foreach ($entry in $fontEntries) {
    $w = $entry.Weight
    $url = $entry.Url
    $fileName = "cairo-$w.woff2"
    $outPath = Join-Path $dest $fileName
    $idx = 1
    while (Test-Path $outPath) {
        # if same URL already downloaded, skip
        if ($downloaded.ContainsKey($url)) { break }
        $idx++
        $fileName = "cairo-$w-$idx.woff2"
        $outPath = Join-Path $dest $fileName
    }
    if ($downloaded.ContainsKey($url)) { Write-Host "Already downloaded: $url -> $downloaded[$url]"; continue }
    Write-Host "Downloading weight $w -> $fileName"
    try {
        Invoke-WebRequest -Uri $url -OutFile $outPath -Headers @{ 'User-Agent' = 'Mozilla/5.0 (Windows NT)'} -UseBasicParsing -ErrorAction Stop
        $downloaded[$url] = $fileName
    } catch {
        Write-Warning "Failed to download $url : $_"
    }
}

# Generate cairo.css mapping the downloaded files
$localCssPath = Join-Path $dest "cairo.css"
$lines = @()
$lines += "/* Cairo local @font-face generated on $(Get-Date -Format o) */"
foreach ($kv in $downloaded.GetEnumerator()) {
    $url = $kv.Key
    $fileName = $kv.Value
    # infer weight from filename
    if ($fileName -match 'cairo-(\d+)(?:-|\.)') { $weight = $Matches[1] } else { $weight = 400 }
    $webUrl = "/frontend/fonts/cairo/$fileName"
    $lines += "@font-face {"
    $lines += "  font-family: 'Cairo';"
    $lines += "  font-style: normal;"
    $lines += "  font-weight: $weight;"
    $lines += "  src: url('$webUrl') format('woff2');"
    $lines += "  font-display: swap;"
    $lines += "}"
    $lines += ""
}
$lines | Out-File -FilePath $localCssPath -Encoding utf8
Write-Host "Generated local CSS: $localCssPath"

Write-Host "Done. If files downloaded successfully, clear browser cache and unregister service worker, then hard reload the site."