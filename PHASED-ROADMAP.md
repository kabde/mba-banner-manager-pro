# MBA Banner Manager Pro - Phased Roadmap

This roadmap is intentionally phased. Do not start a new phase until the previous phase has been implemented, tested, audited, and released or explicitly accepted.

## Phase 1 - Admin UI Foundation

Status: completed in `v1.2.0`.

Goal: make the banner and popup admin experience feel like a professional WordPress plugin.

To do:
- Replace the single long metabox with grouped admin sections: Status, Creative, Placement, Targeting, Schedule, Advanced.
- Add a clean preview panel for image and HTML banners.
- Add desktop/mobile preview toggles.
- Add clear empty states for missing image, missing destination URL, and missing placement.
- Add contextual help text without cluttering the form.
- Add admin notices after save when configuration is incomplete.
- Add list-table filters for status, type, placement, and device.
- Add row action to duplicate a banner.

Tests:
- Create image banner and verify saved metadata.
- Create HTML banner and verify saved metadata.
- Switch type from image to HTML and confirm stale fields are deleted correctly.
- Verify admin list filters return expected banners.
- Verify duplicate action copies all expected metadata and creates a draft.
- Test admin UI on desktop and mobile-width browser.

Audit gate:
- Security review of all new form fields, nonces, capabilities, escaping, and redirects.
- UI review for WordPress admin consistency.
- Regression check that existing banners still render after the UI changes.

Audit summary for `v1.2.0`:
- PHP lint passed on all plugin PHP files.
- JavaScript syntax check passed for the admin script.
- New duplicate action uses the plugin capability, nonce verification, safe redirect, and draft status.
- New list-table filters sanitize query input before building meta queries.
- HTML banner preview is isolated in a sandboxed iframe in the admin screen.

## Phase 2 - Targeting Rules

Goal: support real campaign targeting beyond simple placement/device.

To do:
- Add targeting by post type.
- Add targeting by specific posts/pages.
- Add targeting by category/tag/taxonomy.
- Add include/exclude URL path rules.
- Add logged-in/logged-out targeting.
- Add admin/super-admin exclusion option.
- Add start date and end date scheduling.
- Add priority field for ordering banners.
- Add shortcode support by banner ID: `[mba_banner id="123"]`.

Tests:
- Verify each targeting rule independently.
- Verify include and exclude rules together, with exclude winning.
- Verify expired and future banners do not render.
- Verify priority order is stable.
- Verify shortcode by ID works even if placement does not match.
- Verify shortcode rejects invalid IDs or unpublished banners.

Audit gate:
- Query/performance review for targeting logic.
- Security review of targeting inputs.
- Functional audit across homepage, single post, archive, search, and multisite subsite.

## Phase 3 - Popup Controls

Goal: make popup behavior production-grade and not annoying.

To do:
- Add frequency controls: once per session, once per day, once per X days, always.
- Store dismissal in cookie or localStorage with configurable duration.
- Add delay, max displays, and optional scroll-depth trigger.
- Add popup device targeting.
- Add popup page/category targeting using the same targeting engine as banners.
- Add option to hide popup for logged-in users/admins.
- Add close button style controls.
- Add accessibility improvements: focus management, focus return, keyboard trap, ARIA labels.

Tests:
- Verify popup appears according to each frequency mode.
- Verify dismissal persists for the expected duration.
- Verify delay and scroll trigger behavior.
- Verify popup does not appear when targeting excludes the page.
- Verify keyboard Escape closes popup.
- Verify focus does not escape active modal.

Audit gate:
- Accessibility review.
- UX review for interruption/frequency behavior.
- Security review of popup options.

## Phase 4 - Tracking And Analytics

Goal: add useful campaign metrics without overcomplicating the plugin.

To do:
- Track impressions.
- Track clicks.
- Add click redirect endpoint or AJAX click tracking.
- Add daily aggregated stats.
- Add simple dashboard widgets: impressions, clicks, CTR, top banners.
- Add CSV export.
- Add optional GA/GTM dataLayer events.
- Add data retention setting.

Tests:
- Verify impressions count once per rendered banner.
- Verify click counts increment reliably.
- Verify stats do not count admin previews unless enabled.
- Verify CSV export permissions and data.
- Verify data retention cleanup.
- Verify no fatal errors when object cache is enabled or disabled.

Audit gate:
- Privacy review.
- Performance review under high traffic assumptions.
- Database schema/migration review.

## Phase 5 - Gutenberg And Theme Integration

Goal: support modern WordPress placement methods.

To do:
- Add Gutenberg block for selecting a banner or placement.
- Add block preview in editor.
- Add widget/block variation for sidebars.
- Add PHP template function, e.g. `mba_banner_render( $args )`.
- Add documented filters/actions for developers.
- Add setting to disable automatic placement and use manual placements only.

Tests:
- Verify block insertion and rendering in editor.
- Verify frontend block rendering.
- Verify widget area rendering.
- Verify template function handles invalid args safely.
- Verify automatic placement disable setting works.

Audit gate:
- Block editor compatibility review.
- Theme compatibility review with at least two default WordPress themes.
- Developer API review for naming, escaping, and backwards compatibility.

## Phase 6 - Distribution And Updates

Goal: make the plugin safe to install, update, and maintain publicly.

To do:
- Keep GitHub release updater working with `mba-banner-manager-pro.zip`.
- Add changelog to release notes and README.
- Add versioned migrations for new options/schema.
- Add rollback-safe activation checks.
- Add minimum PHP/WordPress guard with admin notice.
- Add build/package script that excludes `.git`, `.DS_Store`, `._*`, and local files.
- Add smoke test checklist before every release.
- Add screenshots/assets for GitHub.

Tests:
- Install from ZIP on fresh WordPress.
- Update from previous release through WordPress admin.
- Network activate on multisite.
- Deactivate/reactivate without data loss.
- Uninstall removes expected data only.
- Confirm package ZIP has correct root folder and no local metadata.

Audit gate:
- Release audit before publishing.
- Fresh install audit.
- Update-path audit from the previous live version.

## Phase 7 - Hardening And Polish

Goal: final quality pass before broad distribution.

To do:
- Convert remaining hardcoded French strings to translation functions.
- Add `.pot` generation workflow.
- Improve code organization into `includes/`, `admin/`, `public/`, `assets/`.
- Add PHPCS WordPress ruleset.
- Add automated linting in GitHub Actions.
- Add unit tests for targeting logic where practical.
- Add manual QA matrix for WordPress versions, PHP versions, single site, and multisite.

Tests:
- Run PHPCS.
- Run PHP lint.
- Run automated tests where available.
- Manual QA on single site and multisite.
- Check frontend with cache plugin enabled.

Audit gate:
- Final code review.
- Final security review.
- Final UX review.
- Final release readiness review.

## Release Rule

Each phase must end with:
- Version bump.
- Clean Git commit.
- Git tag.
- GitHub release.
- WordPress-ready ZIP asset.
- Live or staging deployment test.
- Written audit summary.
