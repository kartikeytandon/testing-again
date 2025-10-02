# 🚀 Deploy Laravel CRM to Render.com

## Prerequisites
- GitHub account
- Your code pushed to a GitHub repository
- Node.js installed locally (for testing)

## Step 1: Prepare Your Repository

### 1.1 Push to GitHub
```bash
# If not already in git
git init
git add .
git commit -m "Initial commit"

# Push to GitHub
git remote add origin https://github.com/yourusername/your-crm-repo.git
git push -u origin main
```

### 1.2 Test Production Build Locally
```bash
# Install dependencies
composer install --no-dev --optimize-autoloader
npm install

# Build frontend assets
npm run build

# Test production build
php artisan serve
```

## Step 2: Deploy to Render

### 2.1 Create Render Account
1. Go to [render.com](https://render.com)
2. Sign up with GitHub
3. Authorize Render to access your repositories

### 2.2 Create New Web Service
1. Click **"New +"** → **"Web Service"**
2. Connect your GitHub repository
3. Select your CRM repository

### 2.3 Configure Service Settings

**Basic Settings:**
- **Name:** `crm-module` (or your preferred name)
- **Environment:** `PHP`
- **Region:** `Oregon (US West)` (or closest to your users)
- **Branch:** `main` (or your default branch)
- **Root Directory:** Leave empty (uses root)

**Build & Deploy:**
- **Build Command:** 
  ```bash
  composer install --no-dev --optimize-autoloader && npm install && npm run build
  ```
- **Start Command:** 
  ```bash
  vendor/bin/heroku-php-apache2 public/
  ```

### 2.4 Environment Variables

Add these environment variables in Render dashboard:

**Required Variables:**
```
APP_NAME=CRM Module
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.onrender.com
LOG_CHANNEL=stack
```

**Database Variables (Render will provide these):**
```
DB_CONNECTION=pgsql
DB_HOST=your-db-host
DB_PORT=5432
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password
```

**Cache & Session:**
```
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

**Mail Configuration:**
```
MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME=CRM Module
```

## Step 3: Set Up Database

### 3.1 Create PostgreSQL Database
1. In Render dashboard, click **"New +"** → **"PostgreSQL"**
2. Name it `crm-database`
3. Choose **Free** plan
4. Note down the connection details

### 3.2 Connect Database to Web Service
1. Go to your web service settings
2. Add these environment variables (Render provides them):
   - `DB_CONNECTION=pgsql`
   - `DB_HOST=...` (provided by Render)
   - `DB_PORT=5432`
   - `DB_DATABASE=...` (provided by Render)
   - `DB_USERNAME=...` (provided by Render)
   - `DB_PASSWORD=...` (provided by Render)

## Step 4: Deploy!

### 4.1 Deploy Application
1. Click **"Create Web Service"**
2. Render will start building your application
3. Wait 5-10 minutes for the build to complete

### 4.2 Run Database Migrations
After deployment, you need to run migrations. You can do this by:

**Option A: Using Render Shell (Recommended)**
1. Go to your web service dashboard
2. Click **"Shell"**
3. Run: `php artisan migrate --force`

**Option B: Using Render CLI**
```bash
# Install Render CLI
npm install -g @render/cli

# Login to Render
render login

# Run migrations
render exec --service your-service-name -- php artisan migrate --force
```

## Step 5: Configure Custom Domain (Optional)

### 5.1 Add Custom Domain
1. Go to your web service settings
2. Click **"Custom Domains"**
3. Add your domain (e.g., `crm.yourdomain.com`)
4. Follow DNS instructions

### 5.2 Update Environment Variables
Update `APP_URL` to your custom domain:
```
APP_URL=https://crm.yourdomain.com
```

## Step 6: Post-Deployment Setup

### 6.1 Run Seeders (if needed)
```bash
# In Render Shell
php artisan db:seed
```

### 6.2 Set Up File Storage
```bash
# Create storage link
php artisan storage:link
```

### 6.3 Optimize for Production
```bash
# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Troubleshooting

### Common Issues:

**1. Build Fails**
- Check if all dependencies are in `composer.json` and `package.json`
- Ensure Node.js version is compatible (Render uses Node 18+)

**2. Database Connection Error**
- Verify database environment variables
- Check if database service is running
- Ensure migrations are run

**3. Assets Not Loading**
- Make sure `npm run build` completed successfully
- Check if Vite is configured correctly
- Verify `public/build` directory exists

**4. 500 Internal Server Error**
- Check Render logs for specific error messages
- Verify all environment variables are set
- Ensure `APP_KEY` is generated

### Debug Commands:
```bash
# Check Laravel logs
php artisan log:clear

# Check configuration
php artisan config:show

# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

## Free Tier Limitations

- **750 hours/month** (enough for personal use)
- **512MB RAM**
- **Sleeps after 15 minutes** of inactivity (wakes up on request)
- **No custom SSL** (but HTTPS is provided)

## Cost: $0/month! 🎉

Your Laravel CRM will be live at: `https://your-app-name.onrender.com`

## Next Steps

1. **Monitor Performance** - Check Render dashboard for usage
2. **Set Up Backups** - Use Render's backup features
3. **Add Monitoring** - Consider UptimeRobot for uptime monitoring
4. **Scale Up** - Upgrade to paid plans when needed ($7+/month)

---

**Need Help?** Check Render's documentation or Laravel deployment guides!
