# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Clean Sweep Cleaning Services - a static business website with PHP backend for a residential/commercial cleaning company in Champaign-Urbana, IL.

**Tech Stack**: WordPress static export, PHP 8.4, Nginx, Docker (Alpine Linux)

## Development

This is a static export of the original WordPress site. The site files are in `static-site/` directory.

**Source Sites:**
- `oldsite/` - Original WordPress installation from Mainstreethosting (archived, not used)
- `mysite/` - Previous custom static site (archived, not used)
- `static-site/` - **ACTIVE** - Static export from live WordPress site

### Local Development

Run the Docker container locally:
```bash
docker build -t cleansweep .
docker run -p 8080:80 cleansweep
```

Then visit `http://localhost:8080`

### Deployment

Use the `/deploy` skill to deploy to production. The skill will:
1. Commit any uncommitted changes
2. Push to GitHub (`bneradt/cleansweep-cleaning.brianneradt.com`)
3. SSH to `brianneradt.com` and rebuild the Docker container

**Server Structure** (on Raspberry Pi):
```
~/www/
├── repos/cleansweep-cleaning.brianneradt.com/  # This repo
├── http-server/                                 # Docker compose orchestration
├── etc/letsencrypt/                            # TLS certs
└── logs/                                       # Container logs
```

The site runs as the `cleansweep` service in the main `docker-compose.yml` in `~/www/http-server/`. Traffic is routed through Apache Traffic Server (ATS) which handles TLS termination and caching.

## Architecture

```
static-site/                          # Web application root (static WordPress export)
├── index.html                        # Homepage
├── about/index.html                  # About page
├── our-services/index.html           # Services listing
├── contact/index.html                # Contact/Quote form
├── gallery/index.html                # Portfolio gallery
├── contact-form-handler.js           # JavaScript to intercept Gravity Forms
├── api/
│   └── send-email.php                # Contact form handler (sends to pdixon701@gmail.com)
├── wp-content/                       # WordPress static assets
│   ├── themes/                       # Theme CSS, images
│   ├── plugins/                      # Plugin assets (fonts, icons)
│   └── uploads/                      # Gallery images
└── wp-includes/                      # WordPress core assets (JS, CSS)
```

### Key Files

- **Dockerfile**: Alpine Linux container with Nginx + PHP 8.4-FPM
- **nginx.conf**: Web server config (gzip, 30-day static caching, security headers, PHP routing)
- **static-site/api/send-email.php**: Contact form endpoint with input validation and email sending
- **static-site/contact-form-handler.js**: Intercepts WordPress Gravity Forms and submits to PHP backend

### Contact Form

The original WordPress site used Gravity Forms plugin. In the static export:
- Gravity Forms HTML/CSS is preserved for styling consistency
- **contact-form-handler.js** intercepts form submissions
- Form data is sent to **/api/send-email.php** via fetch API
- Emails sent to: **pdixon701@gmail.com** (Peter Dixon)

### PHP Backend

- Single endpoint for contact form (`POST /api/send-email.php`)
- Validates email format and sanitizes inputs (strips HTML tags)
- Returns JSON responses: `{"success": true/false, "message": "..."}`
