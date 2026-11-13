# 07 · Tracking — seeing the whole funnel

The Meta pixel says *what* happened; it doesn't show *where on the site* people gave up.

## Visitor Log (Google Sheets + Apps Script)
A lightweight script on every page sends one row per event to a Google Sheet:

`Page View → Product View → Phone Selected → Add to Cart → Checkout View → Place Order Click → Payment Page → Page Exit` + **WhatsApp Click**

- Page types: Home, Anime Landing, Anime category, Product, Category, Checkout…
- Scroll depth + time on page for key page types.
- Internal / logged-in traffic excluded.
- A Report tab counts each step per day, including a dedicated `/anime/` landing column.

## Ads
- Every new ad link carries UTM tags (`utm_campaign`, `utm_content` = ad name), so each visitor can be traced to the exact creative.
- Note: the free pixel plugin double-counts some purchases on thank-you page reloads (5 events for 3 paid orders on one day) — flagged for later.
