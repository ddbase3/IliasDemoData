# Privacy for IliasDemoData

## Processing and purpose

This optional ILIAS plugin creates managed demonstration content for RAG, learning and reporting tests. Its lifecycle service creates a marked repository subtree, demo user accounts, a global role, course assignments, learning progress, and sample knowledge texts. The associated admin display reads ILIAS lifecycle status. The demo user accounts and course data are test data, not intended as production personal data.

## Storage and retention

Demo objects, role grants, users and learning progress are stored by the existing ILIAS repository, RBAC and user infrastructure. The plugin marks its managed records with its explicit ownership prefix. Removal uses the existing ILIAS deletion lifecycle and only targets managed objects and identities. Retention is otherwise controlled by the ILIAS host and its backups. No new database or remote storage is introduced by this settings integration.

## Access and recipients

The administration entry is discoverable via `ISettingsTab`. Direct lifecycle HTML/JSON actions now require explicit native administrator and feature authorization. Create and removal actions also require a session-bound CSRF token. Data may be accessible to ILIAS users according to the configured ILIAS permissions. Any external replication or backup policy depends on the deployment and cannot be verified from these sources.

## Security and gaps

The plugin relies on the existing ILIAS identity, RBAC and repository services and the BASE3 authorization authority. This source snapshot does not establish the installation's backup retention, external recipients, actual remote transfer policy or operational deletion of backup copies. Deployments must verify these aspects independently.

### Settings integration (November 2026)

Managed demo-data creation/removal requires native administrator and feature authorization and a valid session-bound CSRF token. Existing deletion scope remains limited to the module-owned markers.
