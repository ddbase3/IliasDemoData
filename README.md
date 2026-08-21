# IliasDemoData

BASE3 plugin for a fully managed ILIAS demo dataset.

The plugin creates one repository subtree named `BASE3 Demo Knowledge Lab`, fourteen English demo users, one global demo role, four courses with mixed memberships and course learning-progress states, and a substantial English knowledge corpus for RAG demonstrations.

The current corpus contains:

- 4 courses
- 21 wiki pages
- 12 blog posts
- 12 forum threads
- 18 learning-module pages
- 6 text reference files
- approximately 35,000 words

All repository objects use the ownership description marker `base3-ilias-demo-data:seed:v1`. Demo users use the `external_account` prefix `base3-ilias-demo-data:seed:v1:`. The global role carries the same managed seed marker.

Creation and removal are exposed through the BASE3 administration `Demo Data` tab and use AJAX JSON requests. Repository removal uses the ILIAS repository deletion lifecycle that was verified by the lifecycle test before this full seed was built.

Removal deletes the complete managed subtree, all managed demo users, and the managed global role, then verifies that these managed objects no longer exist.
