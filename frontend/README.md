Frontend (Vercel-only)

Purpose
- Deploy only the compiled frontend assets (Vite build) to Vercel, while the Laravel app runs elsewhere.

How it works
- This folder provides a simple build wrapper that:
  1) Runs the root Vite build (npm run build) from the Laravel project root
  2) Copies public/build into frontend/dist
- You can point Vercel’s Root Directory to this frontend folder and set Output Directory to dist.

Commands
- npm run build
  - Triggers the root build and copies assets to ./dist

Vercel settings
- Root Directory: frontend
- Build Command: npm ci && npm run build
- Output Directory: dist
- Framework preset: Other

Notes
- The Laravel app should set ASSET_URL to the Vercel deployment URL so Blade @vite tags resolve to these assets.

