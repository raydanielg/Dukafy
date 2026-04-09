# Upload Build Files to Server
# This script uploads the public/build folder to the production server

$HostIP = "82.25.120.158"
$Port = "65002"
$User = "u689745589"
$RemotePath = "~/domains/ematokeo.ac.tz/public_html/public/build"
$LocalPath = "public/build"

Write-Host "================================" -ForegroundColor Cyan
Write-Host "  Upload Build to Server" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host "Server: $HostIP`:$Port" -ForegroundColor Yellow
Write-Host "User: $User" -ForegroundColor Yellow
Write-Host "Local: $LocalPath" -ForegroundColor Yellow
Write-Host "Remote: $RemotePath" -ForegroundColor Yellow
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""

# Check if build folder exists
if (-not (Test-Path $LocalPath)) {
    Write-Host "ERROR: Build folder not found at $LocalPath" -ForegroundColor Red
    Write-Host "Please run 'npm run build' first" -ForegroundColor Yellow
    exit 1
}

Write-Host "Step 1: Creating remote directory..." -ForegroundColor Green
ssh -p $Port $User@$HostIP "mkdir -p $RemotePath"

Write-Host "Step 2: Uploading files..." -ForegroundColor Green
# Use tar for faster upload
tar -czf - -C public build | ssh -p $Port $User@$HostIP "tar -xzf - -C ~/domains/ematokeo.ac.tz/public_html/public/"

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "SUCCESS! Build files uploaded." -ForegroundColor Green
    Write-Host ""
    Write-Host "Files uploaded:" -ForegroundColor Cyan
    Get-ChildItem -Path $LocalPath -Recurse | Select-Object -ExpandProperty Name
} else {
    Write-Host "ERROR: Upload failed" -ForegroundColor Red
}
