<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use App\Services\FileExportStrategy;
use App\Services\PdfFileExportStrategy;
use App\Services\XlsxFileExportStrategy;

class AppServiceProvider extends ServiceProvider
{
  public function register()
  {
  }

  public function boot(): void
  {
    Passport::tokensExpireIn(now()->addDays(15));
    Passport::refreshTokensExpireIn(now()->addDays(30));
    Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    Passport::enablePasswordGrant();
  }
}
