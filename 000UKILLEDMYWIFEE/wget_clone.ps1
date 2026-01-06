param(
    [string]$Url,
    [string]$OutDir = "."
)

if (-not (Test-Path $OutDir)) {
    New-Item -ItemType Directory -Path $OutDir | Out-Null
}

$wc = New-Object System.Net.WebClient

# Download the index HTML for the Internet Archive item
$indexFile = Join-Path $OutDir "index.html"
$wc.DownloadFile($Url, $indexFile)

# Parse all href links that look like files (very simple filter)
$links = Select-String -Path $indexFile -Pattern 'href="([^"]+)"' -AllMatches |
    ForEach-Object { $_.Matches } |
    ForEach-Object { $_.Groups[1].Value } |
    Where-Object {
        $_ -notmatch '^/' -and
        $_ -notmatch '^\?' -and
        $_ -notmatch 'index\.html' -and
        $_ -ne '/'
    }

foreach ($link in $links) {
    $fileUrl = ($Url.TrimEnd("/") + "/" + $link)
    $outFile = Join-Path $OutDir $link
    Write-Host "Downloading $fileUrl -> $outFile"
    $wc.DownloadFile($fileUrl, $outFile)
}
