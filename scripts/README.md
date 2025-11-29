Indexing Sign Images

This repository includes a script to index sign images from the `public/storage/signs` directory into the `sign_assets` database table so the app can use DB-backed sign metadata instead of reading file names at runtime.

Usage

1. Ensure `public/storage` is available (for example, run `php artisan storage:link` if needed).
2. Run the indexing script (from project root):

```powershell
php scripts/index_sign_images.php
```

What it does

Generating App Icons

If you want your main application icon to be the same as the app logo, place the logo file at `public/frontend/logo.png` or run the script with a custom source file:

```powershell
php scripts/generate_app_icons.php
// or specify a source path
php scripts/generate_app_icons.php path\to\logo.png
```

This will generate:
- `public/frontend/app-icon-192.png`
- `public/frontend/app-icon-512.png`
- `public/favicon.ico` (when Imagick is available).

You can also upload a new logo via the admin tool (`/admin/icon-tools`) which will store the logo and regenerate icons automatically.

Publishing sign assets to GitHub (optional)

If you want to include all sign images in the GitHub repo (for static deploys / Laravel Cloud), you can run the provided PowerShell script `scripts/publish_sign_assets.ps1`.

1. Optionally enable Git LFS for large images (recommended if you have many/large images):

```powershell
git lfs install
```

2. Run the publish script to copy `public/storage/signs` into `public/frontend/signs`, track with LFS (optional), commit, and optionally push:

```powershell
# Copy, track with LFS, commit but don't push
.\scripts\publish_sign_assets.ps1 -UseLfs

# Copy, track with LFS, commit and push changes
.\scripts\publish_sign_assets.ps1 -UseLfs -Push -CommitMessage "Add sign assets"
```

Notes:
- The script preserves subfolders (letters, words, etc.).
- Pushing changes requires proper Git credentials and write access to the repo.
- Avoid committing user uploads to Git unless they are part of static assets you intend to manage in source control. For large datasets use S3 or alternative storage.


Why you should run it

- Using the DB-backed library ensures the UI and conversion logic rely on explicit metadata (text, key) rather than parsing filenames (which may include underscores, hyphens, or encodings).
- It avoids cases where underscores are interpreted literally in displayed text.
