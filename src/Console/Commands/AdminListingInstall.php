<?php

declare(strict_types=1);

namespace Brackets\AdminListing\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

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

    public function __construct(private readonly Filesystem $filesystem)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->alterEncryptCookiesMiddleware();

        $this->info('Package brackets/admin-listing installed');
    }

    private function strReplaceInFile(
        string $filePath,
        string $find,
        string $replaceWith,
        ?string $ifRegexNotExists = null,
    ): bool|int {
        $content = $this->filesystem->get($filePath);
        if ($ifRegexNotExists !== null && preg_match($ifRegexNotExists, $content)) {
            return false;
        }

        return $this->filesystem->put($filePath, str_replace($find, $replaceWith, $content));
    }

    private function alterEncryptCookiesMiddleware(): void
    {
        $this->strReplaceInFile(
            app_path('Http/Middleware/EncryptCookies.php'),
            '->withMiddleware(function (Middleware $middleware) {',
            '->withMiddleware(function (Middleware $middleware) {
    $middleware->encryptCookies(except: [
        \'per_page\',
    ]);',
            '|\'per_page\'|',
        );
    }
}
