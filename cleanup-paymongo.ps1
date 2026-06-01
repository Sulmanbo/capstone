# cleanup-paymongo.ps1
# Removes the obsolete PayMongo files left over from the previous payments.zip.
# Safe to run repeatedly — only deletes if the files exist.

$obsolete = @(
    "app\Services\PayMongoService.php",
    "app\Http\Controllers\PayMongoWebhookController.php"
)

foreach ($f in $obsolete) {
    if (Test-Path $f) {
        Remove-Item $f -Force
        Write-Host "Deleted $f" -ForegroundColor Green
    } else {
        Write-Host "Already gone: $f" -ForegroundColor DarkGray
    }
}

# Also remove the empty Services folder if it's now empty
if ((Test-Path "app\Services") -and ((Get-ChildItem "app\Services" | Measure-Object).Count -eq 0)) {
    Remove-Item "app\Services" -Force
    Write-Host "Removed empty app\Services folder" -ForegroundColor Green
}

Write-Host ""
Write-Host "Cleanup done. You can also remove these env vars (no longer used):" -ForegroundColor Yellow
Write-Host "  PAYMONGO_PUBLIC_KEY"
Write-Host "  PAYMONGO_SECRET_KEY"
Write-Host "  PAYMONGO_WEBHOOK_SECRET"
