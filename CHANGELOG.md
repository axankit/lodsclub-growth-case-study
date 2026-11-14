# Changelog

Every change made to the live store and ad account, with the result and the way back.

| Date (2026) | Area | Change | Result | Undo |
|---|---|---|---|---|
| 7 Oct | Audit | Mobile journey audit, ad + pixel analysis | Found shipping surprise, no COD, weak landing page, slow pages | – |
| Early Nov | Infra | Hosting cache bug reported with a technical handover doc; host fixed the cache key | Each URL now serves its own page to logged-out visitors | – |
| 11 Nov | Ads | 3 retargeting audiences: cart/checkout no-buy 30d, all visitors 30d, Instagram engagers 60d | ~1k / 1.2k / 2.2k people ready for retargeting | Delete audiences |
| 11 Nov | Website | `/anime/` landing page (V2 anime style), WhatsApp button enabled | Dedicated page for ad traffic | Unpublish page |
| 11 Nov | Website | Anime category page redesign via Elementor Theme Builder template; live Store-API product grid | Visual, sortable, filterable shop | Remove template condition (backup kept) |
| 11 Nov | Website | Mobile tag overlap + mobile blank-page bug fixed; footer logo fixed | Works on Pixel 7 and iPhone 13 | Template revision |
| 11 Nov | Checkout | Free shipping until 20 new purchases; UPI-first wording in Razorpay description | Removes the ₹60 surprise | WooCommerce shipping zone |
| 12 Nov | SEO/GEO | Titles, meta descriptions, OG images, FAQ schema for `/anime/` and the category page | Proper previews on WhatsApp/Instagram + search | Elementor Custom Code snippets |
| 12 Nov | Tracking | Visitor Log tracker: WhatsApp clicks, landing + product page events, `/anime/` column in report | Full funnel in Google Sheets | Backup draft snippet |
| 12 Nov | Product page | Product page v2 (preview → live) | Picker + Buy now + trust + case view | Custom Code snippet → Draft |
| 12 Nov | Catalogue | MRP ₹500 → ₹499 on 40 cases | Consistent 50% OFF | CSV backup of old prices |
| 12 Nov | Performance | `/anime/` strips unused theme CSS/JS, lazy images, preloaded hero | 9.3 s → 3.1 s load, LCP 4.6 s → 2.1 s, 5 → 1.8 MB | Deactivate Code Snippet |
| 12 Nov | Checkout | Phone number required | Couriers always get a number | Customizer setting |
| 12 Nov | Creative | 15 s reel + 2 carousels generated in code | 1 video, 14 cards | – |
| 13 Nov | Ads | Old campaign paused; new Sales campaign built (2 ad sets, 5 ads, UTM on every link) | Live 13 → 20 Nov, ₹3,000 | Pause campaign |
| 14 Nov | Checkout | Mobile Razorpay screen checked: shows UPI apps (GPay/PhonePe/Paytm) directly | No change needed | – |
| 14 Nov | Recovery | WhatsApp payment-link nudge to unpaid orders (pending + failed), daily routine | Warm buyers get a one-tap way back to pay | – |
| 14 Nov | Email | Site email was failing (PHP mail disabled). FluentSMTP installed, connected to the store's own mail server; test email delivered | Order confirmations + failed-payment emails now reach customers | Deactivate FluentSMTP |
| 14 Nov | Product page | "Prepaid · UPI" perk, "Prepaid order – pay at checkout with GPay, PhonePe, Paytm or card" line, badges "Secure UPI" + "Easy replacement", sticky bar "Free delivery · Prepaid UPI" | No surprise at payment | Draft backup snippet of previous code |
| 14 Nov | Cart + checkout | Prepaid note above "Proceed to Checkout"; trust box above the pay button; button "Pay securely with UPI / card"; "Free" delivery instead of "Enter address to calculate" / "Shipping calculated at checkout" | Clear prepaid + trust at the moment of paying | Deactivate Code Snippet "Prepaid + trust lines" |

