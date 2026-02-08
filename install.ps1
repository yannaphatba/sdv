$ErrorActionPreference = "Stop"

$project = "vehicle-system"

composer create-project laravel/laravel $project
Set-Location $project

$kit = (Get-Item "..\laravel_upgrade_kit").FullName

robocopy "$kit\app" ".\app" /E
robocopy "$kit\routes" ".\routes" /E
robocopy "$kit\resources" ".\resources" /E
robocopy "$kit\database" ".\database" /E
robocopy "$kit\public" ".\public" /E

Copy-Item ".env.example" ".env"
php artisan key:generate
php artisan migrate --seed

Write-Host "Done. Run: cd $project; php artisan serve"
