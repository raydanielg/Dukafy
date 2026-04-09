# SSH Connection Script for Dukafy Server
# Usage: .\connect.ps1

# Server Credentials
$HostIP = "82.25.120.158"
$Port = "65002"
$Username = "u689745589"

Write-Host "================================" -ForegroundColor Cyan
Write-Host "  SSH Connection to Dukafy" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host "Host:     $HostIP" -ForegroundColor Yellow
Write-Host "Port:     $Port" -ForegroundColor Yellow
Write-Host "Username: $Username" -ForegroundColor Yellow
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""

# Connect via SSH
ssh -p $Port $Username@$HostIP
