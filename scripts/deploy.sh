#!/usr/bin/env bash
# =============================================================================
# deploy.sh — Build and deploy Darkest Dungeon Companion
#
# Safe to run on every git push. Handles first-time setup automatically.
#
# USAGE:
#   chmod +x scripts/deploy.sh          # once
#   ./scripts/deploy.sh                 # every deploy
#
# ENVIRONMENT OVERRIDES (optional):
#   PHP_BIN=/usr/bin/php8.4 ./scripts/deploy.sh
#   WEB_USER=myuser        ./scripts/deploy.sh
# =============================================================================
set -euo pipefail

# ── Paths ─────────────────────────────────────────────────────────────────────
REPO_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BACKEND_DIR="$REPO_DIR/backend"
FRONTEND_DIR="$REPO_DIR/frontend"

# ── Overridable via env ───────────────────────────────────────────────────────
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
NPM_BIN="${NPM_BIN:-npm}"
WEB_USER="${WEB_USER:-www-data}"

# ── Colours ───────────────────────────────────────────────────────────────────
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
CYAN='\033[0;36m'; BOLD='\033[1m'; NC='\033[0m'
info()  { echo -e "  ${GREEN}✓${NC}  $*"; }
warn()  { echo -e "  ${YELLOW}⚠${NC}  $*"; }
err()   { echo -e "  ${RED}✗${NC}  $*" >&2; exit 1; }
step()  { echo -e "\n${CYAN}${BOLD}── $* ──${NC}"; }

# ── Helpers ───────────────────────────────────────────────────────────────────
detect_fpm_service() {
    for svc in php8.4-fpm php-fpm8.4 php-fpm; do
        systemctl is-active --quiet "$svc" 2>/dev/null && echo "$svc" && return
    done
}

detect_fpm_socket() {
    for sock in \
        /run/php/php8.4-fpm.sock \
        /var/run/php/php8.4-fpm.sock \
        /tmp/php8.4-fpm.sock; do
        [[ -S "$sock" ]] && echo "$sock" && return
    done
}

# ── Preflight ─────────────────────────────────────────────────────────────────
echo -e "\n${BOLD}Darkest Dungeon Companion — Deploy${NC}"
echo    "  Repo : $REPO_DIR"
echo    "  PHP  : $($PHP_BIN -r 'echo PHP_VERSION;' 2>/dev/null || echo 'not found')"
echo    "  Node : $(node --version 2>/dev/null || echo 'not found')"
echo    "  npm  : $($NPM_BIN --version 2>/dev/null || echo 'not found')"
echo    "  Time : $(date '+%Y-%m-%d %H:%M:%S')"

command -v "$PHP_BIN"      >/dev/null || err "php not found — set PHP_BIN"
command -v "$COMPOSER_BIN" >/dev/null || err "composer not found — set COMPOSER_BIN"
command -v "$NPM_BIN"      >/dev/null || err "npm not found — set NPM_BIN"

# ── 1. Git pull ───────────────────────────────────────────────────────────────
step "1/6  Pull latest code"
git -C "$REPO_DIR" pull --ff-only
info "At commit $(git -C "$REPO_DIR" rev-parse --short HEAD)"

# ── 2. Composer ───────────────────────────────────────────────────────────────
step "2/6  Backend — Composer install"
$COMPOSER_BIN install \
    --working-dir="$BACKEND_DIR" \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --quiet
info "Composer dependencies ready"

# ── 3. Laravel .env + database ───────────────────────────────────────────────
step "3/6  Backend — Environment"

if [[ ! -f "$BACKEND_DIR/.env" ]]; then
    warn ".env missing — creating from .env.example (first run)"
    cp "$BACKEND_DIR/.env.example" "$BACKEND_DIR/.env"

    # Flip to production defaults
    sed -i "s/APP_ENV=local/APP_ENV=production/"    "$BACKEND_DIR/.env"
    sed -i "s/APP_DEBUG=true/APP_DEBUG=false/"      "$BACKEND_DIR/.env"

    # Set APP_URL to server's primary IP as a sensible default
    SERVER_IP="$(hostname -I 2>/dev/null | awk '{print $1}')"
    sed -i "s|APP_URL=http://localhost|APP_URL=http://${SERVER_IP}|" "$BACKEND_DIR/.env"

    $PHP_BIN "$BACKEND_DIR/artisan" key:generate --force
    info "APP_KEY generated"
    warn "Edit $BACKEND_DIR/.env and set APP_URL to your real domain before going live"
else
    info ".env exists — skipping (to regenerate, delete $BACKEND_DIR/.env)"
fi

# Create SQLite file if this is a fresh deploy
if [[ ! -f "$BACKEND_DIR/database/database.sqlite" ]]; then
    touch "$BACKEND_DIR/database/database.sqlite"
    info "SQLite database file created"
fi

$PHP_BIN "$BACKEND_DIR/artisan" migrate --force
info "Migrations up to date"

# ── 4. Laravel optimisation caches ───────────────────────────────────────────
step "4/6  Backend — Build caches"
$PHP_BIN "$BACKEND_DIR/artisan" config:cache
$PHP_BIN "$BACKEND_DIR/artisan" route:cache
$PHP_BIN "$BACKEND_DIR/artisan" view:cache
info "Config / route / view caches built"

# ── 5. Frontend build ─────────────────────────────────────────────────────────
step "5/6  Frontend — npm build"
$NPM_BIN ci --prefix "$FRONTEND_DIR" --prefer-offline --silent
info "npm dependencies installed"

$NPM_BIN run build --prefix "$FRONTEND_DIR"
info "Vite build complete → $FRONTEND_DIR/dist"

# ── 6. Permissions + PHP-FPM reload ──────────────────────────────────────────
step "6/6  Permissions and PHP-FPM"

# Laravel requires write access to storage and bootstrap/cache
chmod -R ug+rwX "$BACKEND_DIR/storage" "$BACKEND_DIR/bootstrap/cache"

# If the PHP-FPM pool runs as a different user (e.g. www-data), that user
# needs read access to the repo. Two options:
#   (a) Run this script as root and chown — handled below
#   (b) Set `user = yourusername` in /etc/php/8.4/fpm/pool.d/www.conf
#       and skip the chown entirely (simpler for personal VPS)
if [[ $EUID -eq 0 ]]; then
    chown -R "$WEB_USER:$WEB_USER" \
        "$BACKEND_DIR/storage" \
        "$BACKEND_DIR/bootstrap/cache" \
        "$FRONTEND_DIR/dist"
    info "Ownership set to $WEB_USER"
else
    info "Running as non-root — skipping chown"
    info "If PHP-FPM can't read files, see nginx.conf comments about FPM pool user"
fi

FPM_SVC="$(detect_fpm_service)"
if [[ -n "$FPM_SVC" ]]; then
    sudo systemctl reload "$FPM_SVC"
    info "$FPM_SVC reloaded"
else
    warn "No running PHP-FPM service detected"
    warn "Start it with: sudo systemctl start php8.4-fpm"
fi

# ── Done ──────────────────────────────────────────────────────────────────────
echo -e "\n${GREEN}${BOLD}✓ Deploy complete${NC}  $(date '+%H:%M:%S')"
FPM_SOCK="$(detect_fpm_socket)"
if [[ -z "$FPM_SOCK" ]]; then
    warn "PHP-FPM socket not found — update the upstream in scripts/nginx.conf"
    warn "Find your socket: sudo find /run /var/run /tmp -name 'php*fpm*.sock' 2>/dev/null"
else
    info "PHP-FPM socket: $FPM_SOCK"
fi
echo ""
