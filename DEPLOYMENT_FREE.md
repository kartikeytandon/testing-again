# 🆓 Free Deployment Guide for Laravel CRM

## Quick Setup for Railway (Recommended)

### Step 1: Prepare Your Code

1. **Create .env.example file** (if it doesn't exist):
```env
APP_NAME="CRM Module"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-app.railway.app

DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=railway
DB_USERNAME=postgres
DB_PASSWORD=password

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
```

2. **Create Procfile** (for Heroku compatibility):
```
web: vendor/bin/heroku-php-apache2 public/
```

3. **Update composer.json** (add this to scripts section):
```json
"scripts": {
    "post-install-cmd": [
        "php artisan key:generate --force",
        "php artisan migrate --force"
    ]
}
```

### Step 2: Deploy to Railway

1. **Go to [railway.app](https://railway.app)**
2. **Sign up with GitHub**
3. **Click "New Project" → "Deploy from GitHub repo"**
4. **Select your CRM repository**
5. **Railway will auto-detect Laravel and set up:**
   - PHP 8.2
   - PostgreSQL database
   - Web server
   - SSL certificate

### Step 3: Configure Environment Variables

In Railway dashboard, go to your project → Variables tab and add:

```
APP_NAME=CRM Module
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.railway.app
DB_CONNECTION=pgsql
```

Railway will automatically provide:
- `DB_HOST`
- `DB_PORT` 
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

### Step 4: Deploy!

1. **Click "Deploy"**
2. **Wait 2-3 minutes**
3. **Your app will be live at: `https://your-app.railway.app`**

## Alternative: Render.com

### Step 1: Prepare for Render

1. **Create `render.yaml` in your project root:**
```yaml
services:
  - type: web
    name: crm-app
    env: php
    buildCommand: composer install && npm install && npm run build
    startCommand: vendor/bin/heroku-php-apache2 public/
    envVars:
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: false
```

2. **Create `Procfile`:**
```
web: vendor/bin/heroku-php-apache2 public/
```

### Step 2: Deploy to Render

1. **Go to [render.com](https://render.com)**
2. **Sign up with GitHub**
3. **Click "New" → "Web Service"**
4. **Connect your repository**
5. **Select "Laravel" as framework**
6. **Add environment variables**
7. **Deploy!**

## Free Domain Options

### 1. **Freenom** (Completely Free)
- Get `.tk`, `.ml`, `.ga`, `.cf` domains
- Go to [freenom.com](https://freenom.com)
- Register domain (free for 12 months)
- Point to your Railway/Render URL

### 2. **GitHub Pages** (For static sites)
- Not suitable for Laravel, but good for documentation

### 3. **Subdomain Options**
- Railway: `your-app.railway.app`
- Render: `your-app.onrender.com`
- Heroku: `your-app.herokuapp.com`

## Cost Breakdown (100% Free)

| Service | Cost | What You Get |
|---------|------|--------------|
| Railway | $0 | 500 hours/month, 1GB RAM, PostgreSQL |
| Render | $0 | 750 hours/month, 512MB RAM, PostgreSQL |
| Heroku | $0 | 550-1000 hours/month, 512MB RAM |
| Freenom Domain | $0 | Free .tk/.ml/.ga/.cf domains |
| **Total** | **$0** | **Fully functional CRM app** |

## Limitations of Free Tiers

### Railway
- ✅ 500 hours/month (enough for personal use)
- ✅ 1GB RAM
- ✅ PostgreSQL database
- ❌ Sleeps after 30 minutes of inactivity (wakes up on request)

### Render
- ✅ 750 hours/month
- ✅ 512MB RAM
- ✅ PostgreSQL database
- ❌ Sleeps after 15 minutes of inactivity

### Heroku
- ✅ 550-1000 dyno hours/month
- ✅ 512MB RAM
- ❌ Sleeps after 30 minutes of inactivity
- ❌ No custom domain on free tier

## Pro Tips for Free Deployment

1. **Use SQLite for development, PostgreSQL for production**
2. **Optimize your images and assets**
3. **Use CDN for static files (Cloudflare free tier)**
4. **Set up monitoring (UptimeRobot free tier)**
5. **Use GitHub Actions for CI/CD (free for public repos)**

## Troubleshooting

### Common Issues:
1. **App sleeps after inactivity** → Normal for free tiers
2. **Database connection errors** → Check environment variables
3. **Asset loading issues** → Run `npm run build` before deployment
4. **Permission errors** → Check file permissions in storage/

### Quick Fixes:
```bash
# If assets don't load
npm run build

# If database issues
php artisan migrate:fresh --seed

# If permission issues
chmod -R 755 storage bootstrap/cache
```

## Next Steps After Free Deployment

1. **Monitor usage** - Check if you're hitting limits
2. **Add custom domain** - Use Freenom for free domain
3. **Set up backups** - Use Railway/Render's backup features
4. **Scale up** - Upgrade to paid plans when needed ($5-10/month)

---

**Ready to deploy? Start with Railway - it's the easiest and most reliable free option! 🚀**
