# Code

| Folder | What it is | Where it runs |
|---|---|---|
| `landing-page/` | `/anime/` landing page HTML, SEO head block, build script | WordPress page (block HTML, `elementor_canvas` template) |
| `category-page/` | Shop template HTML, live Store-API product loader, FAQ + SEO blocks, build script | Elementor Theme Builder product-archive template |
| `product-page/` | Product page v2 — one self-contained `<style>` + `<script>` snippet | Elementor Custom Code, body end, all product pages |
| `performance/` | PHP rule that dequeues unused theme CSS/JS on `/anime/`, lazy-loads images, preloads the hero | Code Snippets plugin |
| `creative-generators/` | HTML frame templates + Playwright scripts that render the reel and carousel cards; OG image generator | Local (Node + Playwright + ffmpeg) |

Sensitive values (phone numbers, emails, tracking endpoints, account IDs) are replaced with `*_REDACTED` placeholders.
