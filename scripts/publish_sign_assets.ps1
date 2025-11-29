param(
    [switch]$UseLfs,
    [switch]$Push,
    [string]$CommitMessage = "Add sign assets (letters/words)"
)

# Ensure we are in project root (one level up from scripts)
$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Definition
$projectRoot = Join-Path $scriptDir '..' | Resolve-Path -ErrorAction Stop
Set-Location -Path $projectRoot

function Ensure-Git {
    try { git --version | Out-Null; return $true } catch { return $false }
}

if (-not (Ensure-Git)) {
    Write-Error "git not found. Please install git and run again."; exit 1
}

$srcDirs = @("public/storage/signs")
$dest = "public/frontend/signs"
if (-not (Test-Path $dest)) { New-Item -ItemType Directory -Path $dest -Force | Out-Null }

Write-Output "Copying sign assets to $dest..."
foreach ($d in $srcDirs) {
    if (Test-Path $d) {
        Get-ChildItem -Path $d -Recurse -File | ForEach-Object {
            # compute dest path inside public/frontend/signs preserving folder structure
            $srcFull = (Get-Item $d).FullName
            if ($srcFull[-1] -ne '\') { $srcFull = $srcFull + '\' }
            $sub = $_.FullName.Substring($srcFull.Length).TrimStart('\','/')
            $outPath = Join-Path $dest $sub
            $outDir = Split-Path $outPath -Parent
            if (-not (Test-Path $outDir)) { New-Item -ItemType Directory -Path $outDir -Force | Out-Null }
            Copy-Item -Path $_.FullName -Destination $outPath -Force
        }
    }
}

if ($UseLfs) {
    # Track common image types
    if (-not (git lfs --version 2>$null)) {
        Write-Warning "git-lfs not installed. Skipping LFS tracking.";
    } else {
        Write-Output "Registering LFS tracking for image types..."
        git lfs track "public/frontend/signs/**/*.png"
        git lfs track "public/frontend/signs/**/*.jpg"
        git lfs track "public/frontend/signs/**/*.jpeg"
        git lfs track "public/frontend/signs/**/*.webp"
        git add .gitattributes
    }
}

Write-Output "Staging sign asset changes..."
git add --all public/frontend/signs

# Commit if there are changes
$status = git status --porcelain
if ([string]::IsNullOrEmpty($status)) {
    Write-Output "No changes to commit.";
} else {
    git commit -m $CommitMessage
    Write-Output "Committed sign assets.";
    if ($Push) {
        Write-Output "Pushing to origin..."
        git push
    } else {
        Write-Output "Skipping push (use -Push to push changes).";
    }
}

Write-Output "Done. Verify `public/frontend/signs` is committed and pushed.";
