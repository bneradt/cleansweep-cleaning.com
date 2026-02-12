# Clean Sweep Cleaning Services

A static business website for a residential/commercial cleaning company in Champaign-Urbana, IL.

## Tech Stack

- **Frontend**: Static WordPress export (HTML, CSS, JavaScript)
- **Backend**: PHP 8.4-FPM (contact form only)
- **Server**: Nginx on Alpine Linux
- **Email**: Brevo SMTP via msmtp
- **Container**: Docker (Nginx + PHP-FPM + msmtp)

## Local Development

Build and run the Docker container:

```bash
docker build -t cleansweep .
docker run -p 8080:80 cleansweep
```

Then visit http://localhost:8080.

To test the contact form locally, pass SMTP credentials as environment variables:

```bash
docker run -p 8080:80 \
  --env-file app.env \
  cleansweep
```

## Contact Form and Email

The contact form submits to `/api/send-email.php`, which sends email via PHP's `mail()` function. The container uses [msmtp](https://marlam.de/msmtp/) as a sendmail replacement to relay mail through Brevo's SMTP service.

### Brevo SMTP Setup

1. Copy the environment template:

   ```bash
   cp app.env.example app.env
   ```

2. Log in to [Brevo](https://app.brevo.com/) and get your SMTP credentials from **Settings > SMTP & API**.

3. Fill in your credentials in `app.env`.

4. Verify the sending domain (`cleansweep-cleaning.com`) in Brevo:
   - Go to **Settings > Senders, Domains & Dedicated IPs > Domains**.
   - Add `cleansweep-cleaning.com`.
   - Add the required DNS records (DKIM, SPF/Return-Path) in [Cloudflare](https://dash.cloudflare.com/).

### How It Works

The original WordPress site used Gravity Forms. In the static export:

1. Gravity Forms HTML/CSS is preserved for styling.
2. `contact-form-handler.js` intercepts form submissions.
3. Form data is sent to `/api/send-email.php` via fetch API.
4. `send-email.php` validates inputs and sends email to `pdixon701@gmail.com`.
5. At container startup, `start.sh` generates `/etc/msmtprc` from environment variables so credentials are never baked into the image.

## Deployment

Use the `/deploy` Claude Code skill, or manually:

```bash
# On the server:
cd ~/www/repos/cleansweep-cleaning.com
git remote update
git checkout main
git reset --hard origin/main
cd ~/www/http-server
docker compose stop cleansweep
docker compose build cleansweep
docker compose up -d cleansweep
```

The site runs behind Apache Traffic Server (ATS) which handles TLS termination and routing.

## Architecture

```
static-site/                          # Web application root
├── index.html                        # Homepage
├── about/index.html                  # About page
├── our-services/index.html           # Services listing
├── contact/index.html                # Contact/Quote form
├── gallery/index.html                # Portfolio gallery
├── contact-form-handler.js           # Intercepts Gravity Forms submissions
├── api/
│   └── send-email.php                # Contact form handler (sends to pdixon701@gmail.com)
├── wp-content/                       # WordPress static assets
│   ├── themes/                       # Theme CSS, images
│   ├── plugins/                      # Plugin assets (fonts, icons)
│   └── uploads/                      # Gallery images
└── wp-includes/                      # WordPress core assets (JS, CSS)

Dockerfile                            # Alpine Linux + Nginx + PHP-FPM + msmtp
nginx.conf                            # Gzip, static caching, security headers, PHP routing
start.sh                              # Generates msmtp config from env vars, starts services
app.env.example                       # SMTP credential template
```
