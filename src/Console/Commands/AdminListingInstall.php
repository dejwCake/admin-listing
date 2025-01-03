<?php

namespace Brackets\AdminListing\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AdminListingInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $signature = 'admin-listing:install';

    /**
     * The console command description.
     *
     * @var string
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $description = 'Install a brackets/admin-listing package';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->alterEncryptCookiesMiddleware();

        $this->info('Package brackets/admin-listing installed');
    }

    private function strReplaceInFile($fileName, $find, $replaceWith)
    {
        $content = File::get($fileName);
        return File::put($fileName, str_replace($find, $replaceWith, $content));
    }

    private function alterEncryptCookiesMiddleware(): void
    {
        // change app/Http/Middleware/EncryptCookies to accept frontend-generated 'per_page' cookie from vue
        $this->strReplaceInFile(
            app_path('Http/Middleware/EncryptCookies.php'),
            "//",
            "'per_page'"
        );
    }
}
