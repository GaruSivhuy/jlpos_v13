#!/usr/bin/env bash
#
# Runs on the server. `make deploy` pipes this file over SSH, so it never needs to exist there.
# Usage: deploy.sh <app-path> <branch>

set -euo pipefail

main() {
    local app_path="${1:-/var/www/jlpos}"
    local branch="${2:-master}"

    cd "$app_path"

    local previous
    previous=$(git rev-parse HEAD)

    git fetch --quiet origin "$branch"
    # --force drops edits made on the server to tracked files, so a deploy can never be blocked by them.
    git checkout --quiet --force -B "$branch" "origin/$branch"

    # Installing packages is the slow part, so only do it when the dependencies changed.
    if [ ! -d vendor ] || git diff --name-only "$previous" HEAD | grep -qE '^composer\.(json|lock)$'; then
        composer install --no-dev --optimize-autoloader --no-interaction
    fi

    php artisan migrate --force
    php artisan filament:assets
    php artisan optimize
    php artisan filament:optimize

    echo "Deployed $(git rev-parse --short HEAD) to $app_path"
}

main "$@"
