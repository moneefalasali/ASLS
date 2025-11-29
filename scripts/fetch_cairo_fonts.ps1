$out = 'public/frontend/fonts/cairo'
if(-not (Test-Path $out)) { New-Item -ItemType Directory -Path $out | Out-Null }
$ua = @{ 'User-Agent' = 'Mozilla/5.0 (Windows NT) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117 Safari/537.36' }
$cssUrl = 'https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap'
Write-Output "Fetching CSS: $cssUrl"
$css = (Invoke-WebRequest -Uri $cssUrl -UseBasicParsing -Headers $ua).Content
if(-not $css){ Write-Error "Failed to download CSS"; exit 1 }

$blocks = [regex]::Matches($css, '@font-face\s*\{[^}]+\}')
if($blocks.Count -eq 0){ Write-Error "No @font-face blocks found"; exit 1 }

$map = @{ 400 = 'cairo-regular.woff2'; 600 = 'cairo-medium.woff2'; 700 = 'cairo-bold.woff2' }

foreach($b in $blocks){
    $blockText = $b.Value
    $w = 400
    $m = [regex]::Match($blockText, 'font-weight\s*:\s*(\d+)', 'IgnoreCase')
    if($m.Success){ $w = [int]$m.Groups[1].Value }
    $u = [regex]::Match($blockText, "url\((https://[^)]+)\)\s*format\('\w+'\)", 'IgnoreCase')
    if(-not $u.Success){ $u = [regex]::Match($blockText, "url\((https://[^)]+)\)", 'IgnoreCase') }
    if($u.Success){
        $url = $u.Groups[1].Value
        if($map.ContainsKey($w)){
            $dest = Join-Path $out $map[$w]
            Write-Output "Downloading weight $w -> $dest"
            try{
                Invoke-WebRequest -Uri $url -OutFile $dest -UseBasicParsing -Headers $ua
                $len = (Get-Item $dest).Length
                Write-Output "Saved $dest ($len bytes)"
            } catch {
                Write-Warning "Failed to download $url : $_"
            }
        } else {
            Write-Output "Skipping weight $w (no mapping)"
        }
    } else {
        Write-Warning "No url found in block for weight $w"
    }
}

# Show results
Get-ChildItem -Path $out | Select-Object Name,Length | Format-Table -AutoSize
