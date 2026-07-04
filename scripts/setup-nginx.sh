#!/usr/bin/env bash
# =============================================================================
# setup-nginx.sh — Install the nginx site config (run ONCE on the server)
#
# USAGE:
#   chmod +x scripts/setup-nginx.sh
#   ./scripts/setup-nginx.sh                    # uses server IP as domain
#   ./scripts/setup-nginx.sh dd.example.com     # uses your real domain
#
# After running, get free HTTPS with:
#   sudo apt install certbot python3-certbot-nginx
#   sudo certbot --nginx -d dd.example.com
# =============================================================================
set -euo pipefail

REPO_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
NGINX_TEMPLATE="$REPO_DIR/scripts/nginx.conf"
SITES_AVAILABLE="/etc/nginx/sites-available/darkestdungeon"
SITES_ENABLED="/etc/nginx/sites-enabled/darkestdungeon"

GREEN='\033[0;32m'; YELLOW='\033[1;33m'; CYAN='\033[0;36m'; BOLD='\033[1m'; NC='\033[0m'
info() { echo -e "  ${GREEN}✓${NC}  $*"; }
warn() { echo -e "  ${YELLOW}⚠${NC}  $*"; }
step() { echo -e "\n${CYAN}${BOLD}── $* ──${NC}"; }

# Default domain = server's primary IP if no arg given
DOMAIN="${1:-$(hostname -I 2>/dev/null | awk '{print $1}')}"
DEPLOY_PATH="$REPO_DIR"

echo -e "\n${BOLD}Darkest Dungeon Companion — Nginx setup${NC}"
echo    "  Domain      : $DOMAIN"
echo    "  Deploy path : $DEPLOY_PATH"

# ── Write config ──────────────────────────────────────────────────────────────
step "Writing nginx site config"
sed \
    -e "s|__DOMAIN__|${DOMAIN}|g" \
    -e "s|__DEPLOY_PATH__|${DEPLOY_PATH}|g" \
    "$NGINX_TEMPLATE" \
    | sudo tee "$SITES_AVAILABLE" > /dev/null
info "Config written to $SITES_AVAILABLE"

# ── Enable site ───────────────────────────────────────────────────────────────
step "Enabling site"
if [[ -L "$SITES_ENABLED" ]]; then
    info "Symlink already exists at $SITES_ENABLED"
else
    sudo ln -s "$SITES_AVAILABLE" "$SITES_ENABLED"
    info "Symlink created: $SITES_ENABLED"
fi

# Disable default site if it still exists (it conflicts on port 80)
if [[ -L /etc/nginx/sites-enabled/default ]]; then
    sudo rm /etc/nginx/sites-enabled/default
    warn "Removed default nginx site (it conflicts on port 80)"
fi

# ── Test + reload ─────────────────────────────────────────────────────────────
step "Testing nginx config"
sudo nginx -t
info "nginx config syntax OK"

sudo systemctl reload nginx
info "nginx reloaded"

# ── Summary ───────────────────────────────────────────────────────────────────
echo -e "\n${GREEN}${BOLD}✓ Nginx configured${NC}"
echo    "  Site live at : http://${DOMAIN}"
echo ""
echo    "  Next steps:"
echo    "  1. Run ./scripts/deploy.sh to do the first full build"
echo    "  2. Edit $BACKEND_DIR/.env and set APP_URL=http://${DOMAIN}"
echo    "  3. For HTTPS:"
echo    "       sudo apt install certbot python3-certbot-nginx"
echo    "       sudo certbot --nginx -d ${DOMAIN}"
echo ""
warn "If PHP-FPM can't read the repo files, edit /etc/php/8.4/fpm/pool.d/www.conf"
warn "and set user/group to your Linux username — see scripts/nginx.conf comments"
echo ""
