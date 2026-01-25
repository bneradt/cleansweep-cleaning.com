---
name: deploy
description: Deploy the Clean Sweep website to production on the Raspberry Pi
---

# Deploy to Production

Deploy the current changes to production:

1. Commit any uncommitted changes (ask for commit message if needed)
2. Push to origin
3. SSH to brianneradt.com and run:
   ```bash
   cd ~/www/repos/cleansweep-cleaning.brianneradt.com
   git remote update
   git checkout main
   git reset --hard origin/main
   cd ~/www/http-server
   git remote update
   git checkout main
   git reset --hard origin/main
   docker compose stop cleansweep
   docker compose build cleansweep
   docker compose up -d cleansweep
   ```
4. Tail the logs briefly to confirm startup succeeded
