# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Clean Sweep Cleaning Services - a static business website with PHP backend for a residential/commercial cleaning company in Champaign-Urbana, IL.

**Tech Stack**: HTML5, CSS3, Vanilla JavaScript, PHP 8.2, Nginx, Docker (Alpine Linux)

## Development

This is a static website with no build tools. Edit HTML/CSS/JS files directly in `mysite/`.

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
mysite/                     # Web application root
├── index.html              # Homepage
├── about.html              # About page
├── services.html           # Services listing
├── contact.html            # Contact/Quote form
├── gallery.html            # Portfolio gallery
├── api/
│   └── send-email.php      # Contact form handler (sends to pdixon701@gmail.com)
├── css/
│   └── style.css           # Main stylesheet with CSS variables for theming
├── js/
│   └── main.js             # Mobile menu, form submission, smooth scrolling
└── images/
    ├── gallery/            # Portfolio images
    └── services/           # Service images
```

### Key Files

- **Dockerfile**: Alpine Linux container with Nginx + PHP 8.2-FPM
- **nginx.conf**: Web server config (gzip, 30-day static caching, security headers, PHP routing)
- **mysite/api/send-email.php**: Contact form endpoint with input validation and email sending

### Frontend Patterns

- CSS variables for theming: `--primary-color` (green), `--secondary-color` (blue), `--accent-color` (orange)
- Mobile-responsive with hamburger menu toggle
- Form submissions via fetch API with JSON responses

### PHP Backend

- Single endpoint for contact form (`POST /api/send-email.php`)
- Validates email format and sanitizes inputs (strips HTML tags)
- Returns JSON responses: `{"success": true/false, "message": "..."}`
