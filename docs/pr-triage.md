# PR Triage — ETB-1

Date: 2026-07-07
Author: Codex agent

## Summary

17 open PRs reviewed. Outcome:

| Category | Count |
|---|---|
| Merged (dependabot) | 2 |
| Superseded by refactor | 12 |
| Rebase-and-merge (valuable) | 3 |

## Detailed Triage

| # | Title | Branch | Verdict | Rationale |
|---|---|---|---|---|
| 1 | Rozbudowa nawigacji, strony głównej i stopki | codex/implement-website-updates-and-features | **Superseded** | Large UI diff (+606/-170); will be re-implemented in refactor track |
| 2 | Update nav styling, search suggestions, carousels | codex/update-website-design-and-functionality | **Superseded** | Conflicts with main; CSS/JS changes best re-done |
| 3 | Role-based user system with per-role profiles | codex/integrate-user-roles-and-access-management | **Rebase + merge** | Core RBAC infrastructure; already partially in main — rebase and verify |
| 4 | Account panel, registration, trainer role | codex/adjust-account-management-features | **Rebase + merge** | Contains trainer role (wanted for ETB-5); account panel improvements |
| 5 | Branded auth panel, email activation, admin bootstrap | codex/add-customized-login-screen-features | **Superseded** | Changes partially in main; auth panel exists |
| 6 | Account view, match panel, AppSetting | codex/implement-account-management-features | **Superseded** | AppSetting model already in main; match panel to be refactored |
| 7 | CheckRole middleware + RBAC protections | codex/implement-authentication-and-role-based-access-control | **Rebase + merge** | Clean `CheckRole` middleware; small diff, high value |
| 8 | Players module with policy CRUD | codex/implement-players-management-module | **Superseded** | Player model + controller already in main |
| 9 | News module with RBAC policy | codex/implement-news-module-with-role-based-permissions | **Superseded** | News model and controller exist in main |
| 10 | Fix news routing + Matches module CRUD | codex/fix-routing-error-and-clean-routes | **Superseded** | MatchController + StoreMatchRequest now in main |
| 11 | Fix runtime errors, matches, routes | codex/audit-and-fix-backend-issues | **Superseded** | Fixes applied in later PRs (now in main) |
| 12 | Fix malformed profile Blade | codex/fix-malformed-blade-view-for-profile-page | **Superseded** | Profile refactor planned in ETB-6 |
| 13 | Load dynamic data on profile page | codex/fix-profilecontroller-to-provide-dynamic-data | **Superseded** | Profile refactor planned in ETB-6 |
| 14 | Rename MatchModel → MatchGame | codex/refactor-matchmodel-to-matchgame | **Superseded** | Rename already present in main |
| 15 | Admin UX: toasts, modals, scheduling | codex/implement-complete-admin-panel-ux-system | **Superseded** | Partial in main; to be re-implemented |
| 19 | Bump symfony/routing (composer) | dependabot/composer/… | **Merge** | Safe dependency bump |
| 20 | Bump axios (npm) | dependabot/npm_and_yarn/… | **Merge** | Safe dependency bump |

## What to Rebase-and-Merge

PRs #3, #4, #7 contain infrastructure we want. After refactor branch is stable:

1. `gh pr checkout 3 && git rebase main && gh pr merge`
2. `gh pr checkout 4 && git rebase main && gh pr merge`
3. `gh pr checkout 7 && git rebase main && gh pr merge`
