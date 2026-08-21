<?php declare(strict_types=1);

namespace IliasDemoData\Seed;

final class DemoContentCatalog {

	public static function users(): array {
		return [
			[
							'login' => 'demo.avery.stone',
							'firstname' => 'Avery',
							'lastname' => 'Stone',
							'email' => 'avery.stone@demo.invalid',
						],
			[
							'login' => 'demo.jordan.reed',
							'firstname' => 'Jordan',
							'lastname' => 'Reed',
							'email' => 'jordan.reed@demo.invalid',
						],
			[
							'login' => 'demo.morgan.hayes',
							'firstname' => 'Morgan',
							'lastname' => 'Hayes',
							'email' => 'morgan.hayes@demo.invalid',
						],
			[
							'login' => 'demo.casey.brooks',
							'firstname' => 'Casey',
							'lastname' => 'Brooks',
							'email' => 'casey.brooks@demo.invalid',
						],
			[
							'login' => 'demo.taylor.quinn',
							'firstname' => 'Taylor',
							'lastname' => 'Quinn',
							'email' => 'taylor.quinn@demo.invalid',
						],
			[
							'login' => 'demo.riley.morgan',
							'firstname' => 'Riley',
							'lastname' => 'Morgan',
							'email' => 'riley.morgan@demo.invalid',
						],
			[
							'login' => 'demo.cameron.blake',
							'firstname' => 'Cameron',
							'lastname' => 'Blake',
							'email' => 'cameron.blake@demo.invalid',
						],
			[
							'login' => 'demo.jamie.lee',
							'firstname' => 'Jamie',
							'lastname' => 'Lee',
							'email' => 'jamie.lee@demo.invalid',
						],
			[
							'login' => 'demo.parker.young',
							'firstname' => 'Parker',
							'lastname' => 'Young',
							'email' => 'parker.young@demo.invalid',
						],
			[
							'login' => 'demo.drew.foster',
							'firstname' => 'Drew',
							'lastname' => 'Foster',
							'email' => 'drew.foster@demo.invalid',
						],
			[
							'login' => 'demo.alexis.carter',
							'firstname' => 'Alexis',
							'lastname' => 'Carter',
							'email' => 'alexis.carter@demo.invalid',
						],
			[
							'login' => 'demo.sam.rivera',
							'firstname' => 'Sam',
							'lastname' => 'Rivera',
							'email' => 'sam.rivera@demo.invalid',
						],
			[
							'login' => 'demo.robin.clark',
							'firstname' => 'Robin',
							'lastname' => 'Clark',
							'email' => 'robin.clark@demo.invalid',
						],
			[
							'login' => 'demo.devon.ward',
							'firstname' => 'Devon',
							'lastname' => 'Ward',
							'email' => 'devon.ward@demo.invalid',
						],
		];
	}

	public static function categories(): array {
		return [
			[
							'key' => 'company',
							'title' => '01 Company Reference',
						],
			[
							'key' => 'product',
							'title' => '02 Product Knowledge',
						],
			[
							'key' => 'training',
							'title' => '03 Training Academy',
						],
			[
							'key' => 'engineering',
							'title' => '04 Engineering and Operations',
						],
			[
							'key' => 'edge',
							'title' => '05 Retrieval Edge Cases',
						],
		];
	}

	public static function courses(): array {
		return [
			[
							'key' => 'field_service',
							'title' => 'Field Service Technician Onboarding',
							'memberships' => [
												'demo.jordan.reed' => [
																		'role' => 'tutor',
																		'status' => 'not_attempted',
																		'percentage' => 0,
																	],
												'demo.avery.stone' => [
																		'role' => 'member',
																		'status' => 'completed',
																		'percentage' => 100,
																	],
												'demo.morgan.hayes' => [
																		'role' => 'member',
																		'status' => 'in_progress',
																		'percentage' => 55,
																	],
												'demo.casey.brooks' => [
																		'role' => 'member',
																		'status' => 'not_attempted',
																		'percentage' => 0,
																	],
												'demo.taylor.quinn' => [
																		'role' => 'member',
																		'status' => 'completed',
																		'percentage' => 100,
																	],
												'demo.riley.morgan' => [
																		'role' => 'member',
																		'status' => 'in_progress',
																		'percentage' => 30,
																	],
												'demo.jamie.lee' => [
																		'role' => 'member',
																		'status' => 'completed',
																		'percentage' => 100,
																	],
												'demo.parker.young' => [
																		'role' => 'member',
																		'status' => 'not_attempted',
																		'percentage' => 0,
																	],
												'demo.drew.foster' => [
																		'role' => 'member',
																		'status' => 'in_progress',
																		'percentage' => 70,
																	],
											],
						],
			[
							'key' => 'product_cert',
							'title' => 'Orion X4 Product Certification',
							'memberships' => [
												'demo.jordan.reed' => [
																		'role' => 'tutor',
																		'status' => 'not_attempted',
																		'percentage' => 0,
																	],
												'demo.avery.stone' => [
																		'role' => 'member',
																		'status' => 'in_progress',
																		'percentage' => 75,
																	],
												'demo.morgan.hayes' => [
																		'role' => 'member',
																		'status' => 'not_attempted',
																		'percentage' => 0,
																	],
												'demo.taylor.quinn' => [
																		'role' => 'member',
																		'status' => 'completed',
																		'percentage' => 100,
																	],
												'demo.cameron.blake' => [
																		'role' => 'member',
																		'status' => 'failed',
																		'percentage' => 62,
																	],
												'demo.alexis.carter' => [
																		'role' => 'member',
																		'status' => 'in_progress',
																		'percentage' => 40,
																	],
												'demo.sam.rivera' => [
																		'role' => 'member',
																		'status' => 'not_attempted',
																		'percentage' => 0,
																	],
												'demo.jamie.lee' => [
																		'role' => 'member',
																		'status' => 'completed',
																		'percentage' => 100,
																	],
											],
						],
			[
							'key' => 'safety',
							'title' => 'Safety and Compliance Essentials',
							'memberships' => [
												'demo.robin.clark' => [
																		'role' => 'tutor',
																		'status' => 'not_attempted',
																		'percentage' => 0,
																	],
												'demo.avery.stone' => [
																		'role' => 'member',
																		'status' => 'completed',
																		'percentage' => 100,
																	],
												'demo.casey.brooks' => [
																		'role' => 'member',
																		'status' => 'in_progress',
																		'percentage' => 45,
																	],
												'demo.taylor.quinn' => [
																		'role' => 'member',
																		'status' => 'completed',
																		'percentage' => 100,
																	],
												'demo.riley.morgan' => [
																		'role' => 'member',
																		'status' => 'not_attempted',
																		'percentage' => 0,
																	],
												'demo.cameron.blake' => [
																		'role' => 'member',
																		'status' => 'completed',
																		'percentage' => 100,
																	],
												'demo.sam.rivera' => [
																		'role' => 'member',
																		'status' => 'in_progress',
																		'percentage' => 25,
																	],
												'demo.drew.foster' => [
																		'role' => 'member',
																		'status' => 'completed',
																		'percentage' => 100,
																	],
											],
						],
			[
							'key' => 'lantern',
							'title' => 'Project Lantern Onboarding',
							'memberships' => [
												'demo.devon.ward' => [
																		'role' => 'tutor',
																		'status' => 'not_attempted',
																		'percentage' => 0,
																	],
												'demo.morgan.hayes' => [
																		'role' => 'member',
																		'status' => 'in_progress',
																		'percentage' => 65,
																	],
												'demo.taylor.quinn' => [
																		'role' => 'member',
																		'status' => 'in_progress',
																		'percentage' => 20,
																	],
												'demo.riley.morgan' => [
																		'role' => 'member',
																		'status' => 'completed',
																		'percentage' => 100,
																	],
												'demo.parker.young' => [
																		'role' => 'member',
																		'status' => 'not_attempted',
																		'percentage' => 0,
																	],
												'demo.robin.clark' => [
																		'role' => 'member',
																		'status' => 'completed',
																		'percentage' => 100,
																	],
												'demo.alexis.carter' => [
																		'role' => 'member',
																		'status' => 'in_progress',
																		'percentage' => 50,
																	],
											],
						],
		];
	}

	public static function wikis(): array {
		$definitions = [
			[
							'key' => 'operations',
							'parent' => 'company',
							'title' => 'Cedar Vale Operations Handbook',
							'pages' => [
												[
																		'title' => 'Company profile',
																		'summary' => 'Cedar Vale Instruments is a fictional industrial sensing company used by the BASE3 demo dataset.',
																		'facts' => [
																									'The company headquarters is in North Harbor and its service coordination center is in Ridgefield.',
																									'The support organization uses severity levels S1 through S4, where S1 means a production-stopping safety or availability incident.',
																									'Field technicians use the phrase golden handoff for a transfer that includes owner, timestamp, device serial number, firmware version, and next action.',
																									'The standard support window is 06:00 to 22:00 local site time on business days; S1 incidents are accepted at all hours.',
																									'Demo records intentionally use the reserved demo.invalid mail domain so they cannot be confused with real customer accounts.',
																								],
																		'procedures' => [
																									'Confirm the customer site, device serial number, current firmware, symptom, and safety state before opening an escalation.',
																								],
																		'related' => [
																									'Support Troubleshooting Wiki',
																									'Service Operations Bulletin',
																								],
																	],
												[
																		'title' => 'Escalation policy',
																		'summary' => 'The escalation policy defines ownership, severity, response targets, and handoff requirements for service cases.',
																		'facts' => [
																									'S1 cases target human acknowledgement within 15 minutes and engineering engagement within 30 minutes.',
																									'S2 cases target acknowledgement within one hour and a documented workaround or investigation plan within four hours.',
																									'S3 cases cover degraded but usable behavior and are reviewed during the next staffed service window.',
																									'S4 cases cover requests, documentation questions, and cosmetic issues with no operational impact.',
																									'A case may only be downgraded when the customer-facing impact has changed, not merely because a workaround exists.',
																								],
																		'procedures' => [
																									'Record severity evidence, assign one accountable owner, and add the next checkpoint before transferring a case.',
																								],
																		'related' => [
																									'Project Lantern Wiki',
																									'Orion X4 Knowledge Base',
																								],
																	],
												[
																		'title' => 'Field visit standard',
																		'summary' => 'Field visits follow a repeatable arrival, inspection, change, verification, and handoff sequence.',
																		'facts' => [
																									'Technicians perform a two-minute visual safety scan before touching an installed sensor or opening an enclosure.',
																									'A pre-change snapshot includes serial number, firmware, current calibration date, alarm history, and site contact.',
																									'Any replaced module is labeled with device serial number, date, reason for replacement, and technician initials.',
																									'Verification must include one normal measurement cycle and one alarm-path check when site policy permits it.',
																									'The departure note must state whether the device was returned to service, left isolated, or transferred to another owner.',
																								],
																		'procedures' => [
																									'Capture evidence before change, make one controlled change at a time, verify, then document the handoff.',
																								],
																		'related' => [
																									'Field Service Technician Onboarding',
																								],
																	],
												[
																		'title' => 'Service documentation rules',
																		'summary' => 'Service notes are written so another technician can continue the case without a private briefing.',
																		'facts' => [
																									'Use absolute dates such as 2026-10-15 instead of relative phrases such as next Thursday in durable documentation.',
																									'Separate observed facts from hypotheses and from actions already taken.',
																									'Record firmware versions in full, for example 3.2.4, because maintenance rules may differ between minor releases.',
																									'Do not copy customer secrets, passwords, personal medical details, or unrelated personal information into service notes.',
																									'When a specification changes, keep the superseded value in an explicitly archived document rather than silently editing history.',
																								],
																		'procedures' => [
																									'Write the observation, source, action, result, and next owner as separate statements.',
																								],
																		'related' => [
																									'Archived Specifications',
																									'Current Specifications',
																								],
																	],
												[
																		'title' => 'Demo reporting glossary',
																		'summary' => 'This glossary defines the reporting terms used in the training and course-management demonstrations.',
																		'facts' => [
																									'Enrolled means the user currently holds a course member or tutor role.',
																									'Not attempted means no learning-progress completion has been recorded for the course.',
																									'In progress means activity has started but the completion criterion has not been met.',
																									'Completed means the course learning progress is complete and the course passed flag is set for the demo member.',
																									'Failed means a learning-progress record is explicitly marked failed; this is used in the product certification demo.',
																								],
																		'procedures' => [
																									'Use the ILIAS course membership and learning-progress state as the reporting source, not the narrative profile text.',
																								],
																		'related' => [
																									'Orion X4 Product Certification',
																									'Safety and Compliance Essentials',
																								],
																	],
											],
						],
			[
							'key' => 'orion',
							'parent' => 'product',
							'title' => 'Orion X4 Knowledge Base',
							'pages' => [
												[
																		'title' => 'Orion X4 overview',
																		'summary' => 'The Orion X4 is the primary fictional environmental process sensor in the demo knowledge base.',
																		'facts' => [
																									'The current Orion X4 calibration interval is 90 days for devices running firmware 3.2 or later.',
																									'Before firmware 3.2, the standard calibration interval was 60 days; that older value is retained only in archived material.',
																									'The supported operating temperature for Orion X4 is minus 20 to 55 degrees Celsius.',
																									'The recommended inspection interval is 30 days even when calibration is not yet due.',
																									'The standard battery pack is model CV-BP48 and its planned replacement interval is 24 months under normal duty.',
																									'The Orion X4R is a different ruggedized variant and must not inherit X4 maintenance intervals automatically.',
																								],
																		'procedures' => [
																									'Verify model suffix and firmware before selecting a maintenance procedure.',
																								],
																		'related' => [
																									'Current Specifications',
																									'Archived Specifications',
																									'Orion X4R Quick Reference',
																								],
																	],
												[
																		'title' => 'Calibration procedure',
																		'summary' => 'Calibration restores the Orion X4 measurement reference and records traceable verification data.',
																		'facts' => [
																									'For firmware 3.2 and later, routine calibration is due every 90 days unless a site-specific validated procedure is stricter.',
																									'Allow the device to stabilize for at least 12 minutes after a cold start before beginning reference calibration.',
																									'The zero reference is applied before the span reference; reversing that order invalidates the routine procedure.',
																									'A successful calibration record includes serial number, firmware, reference lot, technician, timestamp, and post-calibration deviation.',
																									'If post-calibration deviation exceeds 1.5 percent, repeat the setup inspection before repeating calibration.',
																								],
																		'procedures' => [
																									'Check model and firmware, stabilize, apply zero reference, apply span reference, verify deviation, and record traceability.',
																								],
																		'related' => [
																									'Field Service Playbook',
																								],
																	],
												[
																		'title' => 'Alarm code E-17',
																		'summary' => 'E-17 indicates airflow obstruction or insufficient sample-path flow on the Orion X4.',
																		'facts' => [
																									'Common causes include a blocked inlet screen, pinched sample tubing, condensed moisture, or a closed isolation valve.',
																									'Do not replace the reference cell solely because E-17 is present; reference-cell drift is associated with E-22 instead.',
																									'After clearing the flow path, run a normal sample cycle before clearing the incident as resolved.',
																									'If E-17 returns within three cycles, inspect the pump current and tubing routing before escalating.',
																									'The informal phrase blocked air channel used in field forums refers to the same failure family as airflow obstruction.',
																								],
																		'procedures' => [
																									'Inspect the inlet, tubing, moisture trap, valve position, and pump behavior in that order.',
																								],
																		'related' => [
																									'Support Troubleshooting Wiki',
																									'Field Service Community',
																								],
																	],
												[
																		'title' => 'Alarm code E-22',
																		'summary' => 'E-22 indicates reference-cell drift outside the accepted stability band.',
																		'facts' => [
																									'E-22 is not an airflow obstruction code and should not be diagnosed with the E-17 checklist alone.',
																									'First verify that the device has completed its warm-up period and that ambient temperature is inside the supported range.',
																									'A calibration attempt is allowed when the drift is marginal and no physical damage is visible.',
																									'Two consecutive E-22 events after a valid calibration require reference-cell inspection and likely replacement.',
																									'Reference-cell replacement requires a new calibration before the device is returned to service.',
																								],
																		'procedures' => [
																									'Confirm warm-up and temperature, perform one controlled calibration, then escalate repeated drift to cell inspection.',
																								],
																		'related' => [
																									'Orion X4 Product Certification',
																								],
																	],
												[
																		'title' => 'Alarm code E-31',
																		'summary' => 'E-31 indicates disagreement between the primary humidity channel and the secondary diagnostic estimate.',
																		'facts' => [
																									'E-31 can be caused by condensation, sensor contamination, connector oxidation, or a damaged humidity element.',
																									'The first response is inspection and drying, not immediate firmware rollback.',
																									'A device that clears E-31 after drying must still complete two stable measurement cycles before return to service.',
																									'Repeated E-31 at a dry site should trigger connector inspection and a controlled sensor comparison.',
																									'E-31 does not change the normal 90-day X4 calibration interval by itself.',
																								],
																		'procedures' => [
																									'Inspect for moisture, dry safely, check connectors, compare channels, and record whether the code recurs.',
																								],
																		'related' => [
																									'Safety and Compliance Essentials',
																								],
																	],
												[
																		'title' => 'Firmware 3.2 maintenance change',
																		'summary' => 'Firmware 3.2 changed the standard X4 calibration interval after validation of improved drift compensation.',
																		'facts' => [
																									'The validated maintenance interval changed from 60 days to 90 days for the standard Orion X4.',
																									'The change does not apply retroactively to unvalidated customer procedures that intentionally require a shorter interval.',
																									'The ruggedized Orion X4R remains on a 45-day calibration interval because its service profile and validation set differ.',
																									'Archived documents dated before the 3.2 release may still state 60 days and are intentionally retained for retrieval testing.',
																									'When two documents disagree, use the document marked current and verify its effective date and model scope.',
																								],
																		'procedures' => [
																									'Identify model, firmware, document status, and effective date before resolving conflicting maintenance statements.',
																								],
																		'related' => [
																									'Engineering Updates Blog',
																									'Retrieval Edge Cases',
																								],
																	],
											],
						],
			[
							'key' => 'lantern',
							'parent' => 'engineering',
							'title' => 'Project Lantern Wiki',
							'pages' => [
												[
																		'title' => 'Project Lantern overview',
																		'summary' => 'Project Lantern is a fictional deployment program used to connect training, operations, and engineering knowledge.',
																		'facts' => [
																									'The pilot starts on 2026-10-15 at North Harbor and Ridgefield sites.',
																									'Phase 1 covers 24 Orion X4 devices and excludes Orion X4R devices from the initial validation set.',
																									'Devon Ward is the demo tutor for Project Lantern Onboarding, not the operational project owner in real life.',
																									'The pilot success criteria include 95 percent telemetry availability, complete calibration traceability, and zero unresolved S1 incidents at phase exit.',
																									'Weekly pilot reviews occur on Tuesdays at 14:00 local North Harbor time during the first six weeks.',
																								],
																		'procedures' => [
																									'Confirm site readiness, device inventory, training completion, and rollback ownership before the pilot start.',
																								],
																		'related' => [
																									'Project Lantern Journal',
																									'Project Lantern Onboarding',
																								],
																	],
												[
																		'title' => 'Pilot site readiness',
																		'summary' => 'Site readiness is assessed separately for infrastructure, people, data, and safety.',
																		'facts' => [
																									'North Harbor requires network segment NH-SENS-04 to be available before device enrollment.',
																									'Ridgefield uses gateway group RF-GW-2 and requires a documented offline buffering test.',
																									'At least one trained local contact and one trained remote service contact must be assigned per site.',
																									'Calibration reference material must be inside its own validity period on the day of commissioning.',
																									'A site cannot be declared ready while an S1 safety issue remains open.',
																								],
																		'procedures' => [
																									'Review infrastructure, contacts, calibration equipment, safety status, and rollback plan as separate readiness gates.',
																								],
																		'related' => [
																									'Project Lantern Onboarding Guide',
																								],
																	],
												[
																		'title' => 'Telemetry naming',
																		'summary' => 'Lantern telemetry uses stable names so dashboards and automated assistants can join data across systems.',
																		'facts' => [
																									'Device streams use the pattern site.device.metric with lowercase site and metric identifiers.',
																									'North Harbor devices use the site prefix nh and Ridgefield devices use rf.',
																									'The canonical temperature metric is temp_c and the canonical humidity metric is humidity_pct.',
																									'A renamed device retains its immutable serial number as the cross-system identity.',
																									'Temporary test channels use the suffix _test and must be removed before phase acceptance.',
																								],
																		'procedures' => [
																									'Map display names to serial numbers first, then apply the canonical telemetry naming rules.',
																								],
																		'related' => [
																									'Telemetry Verification',
																								],
																	],
												[
																		'title' => 'Change control',
																		'summary' => 'Lantern changes are grouped into standard, planned, and emergency classes.',
																		'facts' => [
																									'Standard changes use a pre-approved procedure and still require a recorded result.',
																									'Planned changes require an owner, implementation window, validation step, and rollback step before approval.',
																									'Emergency changes may proceed to protect safety or restore an S1 outage but require retrospective review within one business day.',
																									'Two unrelated configuration changes must not be bundled merely to reduce paperwork.',
																									'A failed change is rolled back before a second hypothesis is tested unless the rollback itself would be unsafe.',
																								],
																		'procedures' => [
																									'Classify the change, record owner and validation, perform one bounded change, verify, and close or roll back.',
																								],
																		'related' => [
																									'Engineering Updates Blog',
																								],
																	],
												[
																		'title' => 'Pilot reporting',
																		'summary' => 'The Lantern reporting pack separates operational health from training readiness.',
																		'facts' => [
																									'Operational health includes telemetry availability, unresolved incident count, calibration compliance, and device replacement count.',
																									'Training readiness includes course enrollment, not-attempted count, in-progress count, completed count, and failed certifications.',
																									'A completed course does not prove site readiness by itself; readiness also requires infrastructure and safety gates.',
																									'A learner may appear in multiple course reports because memberships are intentionally independent.',
																									'The demo dataset is designed so completion percentages are visibly different between courses.',
																								],
																		'procedures' => [
																									'Report course status from ILIAS membership and learning progress, then combine it with operational facts only at presentation time.',
																								],
																		'related' => [
																									'Demo reporting glossary',
																								],
																	],
											],
						],
			[
							'key' => 'troubleshooting',
							'parent' => 'product',
							'title' => 'Support Troubleshooting Wiki',
							'pages' => [
												[
																		'title' => 'First-response triage',
																		'summary' => 'First-response triage aims to establish safety, impact, scope, and reproducibility before changing the device.',
																		'facts' => [
																									'Ask whether the issue affects one device, one site, or multiple sites before selecting a severity.',
																									'Capture the exact alarm code and the time it first appeared.',
																									'Confirm model and firmware because X4 and X4R service rules differ.',
																									'If the device is in a potentially unsafe process state, isolate according to site procedure before troubleshooting.',
																									'Do not clear alarms before capturing the available event history.',
																								],
																		'procedures' => [
																									'Establish safety, impact, scope, model, firmware, alarm evidence, and recent change history.',
																								],
																		'related' => [
																									'Escalation policy',
																								],
																	],
												[
																		'title' => 'Intermittent airflow faults',
																		'summary' => 'Intermittent airflow faults often require observation across several measurement cycles.',
																		'facts' => [
																									'A partially blocked inlet can produce E-17 only under higher sample demand.',
																									'Condensed moisture may move with tubing orientation and make the fault appear random.',
																									'Record pump current during both normal and failing cycles when the diagnostic channel is available.',
																									'Replacing the sensor head before checking the sample path can hide the actual root cause.',
																									'Three clean cycles after corrective action are preferred for intermittent-flow closure.',
																								],
																		'procedures' => [
																									'Reproduce when safe, compare flow-path conditions, correct one cause, and observe three cycles.',
																								],
																		'related' => [
																									'Alarm code E-17',
																								],
																	],
												[
																		'title' => 'Reference drift investigation',
																		'summary' => 'Reference drift diagnosis distinguishes thermal stabilization, contamination, and component aging.',
																		'facts' => [
																									'An E-22 during initial warm-up is investigated differently from repeated E-22 after hours of stable operation.',
																									'Contamination evidence includes residue, discoloration, or a known process exposure outside the normal profile.',
																									'A successful calibration followed by rapid recurrent drift is stronger replacement evidence than one isolated event.',
																									'Document ambient temperature because operating outside the supported range invalidates normal drift expectations.',
																									'After reference-cell replacement, calibration is mandatory before release.',
																								],
																		'procedures' => [
																									'Confirm stabilization, environment, contamination evidence, calibration behavior, and recurrence.',
																								],
																		'related' => [
																									'Alarm code E-22',
																								],
																	],
												[
																		'title' => 'Humidity mismatch investigation',
																		'summary' => 'Humidity mismatch work focuses on moisture, connectors, and channel comparison.',
																		'facts' => [
																									'Visible condensation requires safe drying before electrical connector work.',
																									'Oxidized contacts can create intermittent E-31 events without permanent sensor damage.',
																									'A controlled comparison against a known-good instrument is preferred before replacing the humidity element.',
																									'Record whether the mismatch is constant or follows environmental changes.',
																									'Do not apply the Orion X4R rugged connector procedure to a standard X4 unless the hardware variant is confirmed.',
																								],
																		'procedures' => [
																									'Dry, inspect connectors, compare channels, verify variant, and document recurrence.',
																								],
																		'related' => [
																									'Alarm code E-31',
																								],
																	],
												[
																		'title' => 'When to escalate',
																		'summary' => 'Escalation is required when evidence exceeds the field procedure or when safety and production impact demand faster ownership.',
																		'facts' => [
																									'Escalate immediately for an unresolved S1 condition or any condition outside the approved safe-service boundary.',
																									'Escalate repeated E-22 after one valid calibration because reference-cell inspection may be required.',
																									'Escalate recurring E-17 after inlet, tubing, moisture trap, valve, and pump checks have been completed.',
																									'Include raw evidence and actions already taken so engineering does not repeat field work unnecessarily.',
																									'The escalation owner remains accountable until the receiving team explicitly accepts the handoff.',
																								],
																		'procedures' => [
																									'Attach evidence, state impact, list completed checks, identify the next decision, and confirm acceptance.',
																								],
																		'related' => [
																									'Cedar Vale Operations Handbook',
																								],
																	],
											],
						],
		];
		foreach ($definitions as &$wiki) {
			foreach ($wiki['pages'] as &$page) {
				$page['text'] = self::article(
					$page['title'],
					$page['summary'],
					$page['facts'],
					$page['procedures'],
					$page['related']
				);
			}
			unset($page);
		}
		unset($wiki);
		return $definitions;
	}

	public static function blogs(): array {
		$definitions = [
			[
							'key' => 'engineering_updates',
							'parent' => 'engineering',
							'title' => 'Engineering Updates Blog',
							'posts' => [
												[
																		'title' => '2026-03-04: Firmware 3.2 validation completed',
																		'summary' => 'Firmware 3.2 validation has completed for the standard Orion X4.',
																		'facts' => [
																									'The standard calibration interval is now 90 days for X4 units on firmware 3.2 or later.',
																									'The prior 60-day interval remains visible in archived documentation for historical traceability.',
																									'The X4R ruggedized variant is not part of this interval change and remains at 45 days.',
																									'Field teams should verify model suffix and firmware before quoting the new interval.',
																								],
																	],
												[
																		'title' => '2026-03-18: E-22 diagnostic note',
																		'summary' => 'Engineering clarified the preferred sequence for repeated reference-cell drift.',
																		'facts' => [
																									'Allow full warm-up before judging drift.',
																									'Perform one controlled calibration when the device is physically intact and within temperature range.',
																									'Two consecutive E-22 events after valid calibration trigger reference-cell inspection.',
																								],
																	],
												[
																		'title' => '2026-04-02: E-17 tubing advisory',
																		'summary' => 'A service review found that tight tubing bends caused intermittent E-17 alarms at several demo sites.',
																		'facts' => [
																									'Inspect routing before replacing the pump or sensor head.',
																									'A minimum bend radius should be maintained according to the installed tubing specification.',
																									'Three clean cycles are recommended after correction for intermittent cases.',
																								],
																	],
												[
																		'title' => '2026-04-27: Battery planning update',
																		'summary' => 'The CV-BP48 replacement planning guidance remains 24 months under normal duty.',
																		'facts' => [
																									'High-temperature deployments may require earlier condition-based replacement.',
																									'Battery age does not change the normal calibration interval.',
																									'Replacement records should include pack lot and device serial number.',
																								],
																	],
												[
																		'title' => '2026-05-12: Project Lantern device freeze',
																		'summary' => 'Project Lantern phase-1 hardware scope has been frozen.',
																		'facts' => [
																									'Phase 1 covers 24 standard Orion X4 devices.',
																									'X4R devices remain outside the initial validation set.',
																									'Hardware substitutions require explicit change approval before commissioning.',
																								],
																	],
												[
																		'title' => '2026-06-09: Humidity mismatch field findings',
																		'summary' => 'Engineering summarized recurring E-31 field observations.',
																		'facts' => [
																									'Moisture and connector condition account for most investigated cases in the demo corpus.',
																									'Firmware rollback is not the standard first action for E-31.',
																									'A known-good humidity comparison is preferred before sensor replacement.',
																								],
																	],
											],
						],
			[
							'key' => 'service_bulletin',
							'parent' => 'company',
							'title' => 'Service Operations Bulletin',
							'posts' => [
												[
																		'title' => 'Service bulletin: Golden handoff',
																		'summary' => 'A golden handoff contains enough context for another technician to continue without a private briefing.',
																		'facts' => [
																									'Always include owner, timestamp, serial number, firmware, current impact, actions taken, and next action.',
																									'Do not use vague time references in durable service notes.',
																									'The receiving owner must explicitly accept the handoff.',
																								],
																	],
												[
																		'title' => 'Service bulletin: Severity reminders',
																		'summary' => 'Severity represents current customer impact and safety, not frustration or elapsed time.',
																		'facts' => [
																									'S1 is reserved for production-stopping safety or availability incidents.',
																									'S2 requires rapid investigation but does not meet the S1 threshold.',
																									'A workaround does not automatically justify downgrading severity.',
																								],
																	],
												[
																		'title' => 'Service bulletin: One change at a time',
																		'summary' => 'Field troubleshooting should preserve causal evidence.',
																		'facts' => [
																									'Make one bounded change at a time when safety permits.',
																									'Verify the result before introducing a second hypothesis.',
																									'Capture pre-change evidence so a successful repair remains explainable.',
																								],
																	],
											],
						],
			[
							'key' => 'lantern_journal',
							'parent' => 'engineering',
							'title' => 'Project Lantern Journal',
							'posts' => [
												[
																		'title' => '2026-07-07: North Harbor readiness review',
																		'summary' => 'North Harbor completed its first formal Lantern readiness review.',
																		'facts' => [
																									'Network segment NH-SENS-04 is reserved for the pilot.',
																									'The local contact roster is complete.',
																									'Calibration reference material will be revalidated before the 2026-10-15 start.',
																								],
																	],
												[
																		'title' => '2026-07-21: Ridgefield buffering test',
																		'summary' => 'Ridgefield validated offline buffering through gateway group RF-GW-2.',
																		'facts' => [
																									'The test retained telemetry during a controlled uplink interruption.',
																									'Recovery preserved serial-number identity and canonical metric names.',
																									'One temporary _test channel remains scheduled for removal before acceptance.',
																								],
																	],
												[
																		'title' => '2026-08-11: Training checkpoint',
																		'summary' => 'Training readiness is intentionally uneven to support reporting demonstrations.',
																		'facts' => [
																									'Some demo learners are complete, some are in progress, and some are not attempted.',
																									'Course membership and learning progress are the authoritative demo reporting data.',
																									'Narrative biographies do not determine course status.',
																								],
																	],
											],
						],
		];
		foreach ($definitions as &$blog) {
			foreach ($blog['posts'] as &$post) {
				$post['text'] = self::article($post['title'], $post['summary'], $post['facts']);
			}
			unset($post);
		}
		unset($blog);
		return $definitions;
	}

	public static function forums(): array {
		$definitions = [
			[
							'key' => 'field_forum',
							'course' => 'field_service',
							'title' => 'Field Service Community',
							'threads' => [
												[
																		'title' => 'E-17 after tubing replacement',
																		'summary' => 'A technician reports E-17 after replacing sample tubing and asks which checks should come before component replacement.',
																		'facts' => [
																									'Confirm the inlet screen, tubing routing, moisture trap, isolation valve, and pump behavior.',
																									'A pinched bend can create intermittent flow restriction even when the tubing is new.',
																									'Run three clean cycles after correcting an intermittent restriction.',
																								],
																	],
												[
																		'title' => 'Is 60-day calibration still current?',
																		'summary' => 'A learner found an older document that says the Orion X4 should be calibrated every 60 days.',
																		'facts' => [
																									'For standard X4 units on firmware 3.2 or later, the current interval is 90 days.',
																									'The 60-day value is intentionally retained in archived material.',
																									'The X4R is a separate variant with a 45-day interval.',
																								],
																	],
												[
																		'title' => 'What belongs in a field handoff?',
																		'summary' => 'A learner asks what information is mandatory when transferring a case.',
																		'facts' => [
																									'Include owner, timestamp, serial number, firmware, current impact, actions taken, evidence, and the next action.',
																									'Separate facts from hypotheses.',
																									'Confirm that the receiving owner accepted the handoff.',
																								],
																	],
											],
						],
			[
							'key' => 'product_forum',
							'course' => 'product_cert',
							'title' => 'Product Certification Discussions',
							'threads' => [
												[
																		'title' => 'E-22 versus E-17',
																		'summary' => 'A certification learner confuses reference-cell drift with airflow obstruction.',
																		'facts' => [
																									'E-17 is the airflow obstruction family.',
																									'E-22 is reference-cell drift.',
																									'Repeated E-22 after valid calibration can require reference-cell inspection.',
																								],
																	],
												[
																		'title' => 'X4R interval question',
																		'summary' => 'A learner asks whether the new 90-day X4 interval also applies to X4R.',
																		'facts' => [
																									'No. X4R remains on a 45-day calibration interval in this demo dataset.',
																									'Always identify the model suffix before applying maintenance rules.',
																									'The current X4 specification and X4R quick reference intentionally differ.',
																								],
																	],
												[
																		'title' => 'Calibration order',
																		'summary' => 'A learner asks whether span can be applied before zero during routine X4 calibration.',
																		'facts' => [
																									'The routine procedure applies zero reference before span reference.',
																									'Reversing the order invalidates the routine procedure.',
																									'Record reference lot and post-calibration deviation.',
																								],
																	],
											],
						],
			[
							'key' => 'safety_forum',
							'course' => 'safety',
							'title' => 'Safety Questions',
							'threads' => [
												[
																		'title' => 'Condensation and E-31',
																		'summary' => 'A learner asks whether to open connectors immediately when visible condensation is present.',
																		'facts' => [
																									'Follow site isolation and safe drying requirements before connector work.',
																									'After drying, inspect connector condition and compare humidity channels.',
																									'Two stable measurement cycles are required before return to service after an E-31 clear.',
																								],
																	],
												[
																		'title' => 'When to stop troubleshooting',
																		'summary' => 'A learner asks when a field technician should stop and escalate.',
																		'facts' => [
																									'Stop when work would cross the approved safe-service boundary.',
																									'Escalate unresolved S1 conditions immediately.',
																									'Escalate when required evidence or repair authority exceeds the field procedure.',
																								],
																	],
												[
																		'title' => 'Battery handling',
																		'summary' => 'A learner asks whether planned battery age changes calibration requirements.',
																		'facts' => [
																									'Battery replacement planning and calibration are separate maintenance concerns.',
																									'The CV-BP48 planned interval is 24 months under normal duty.',
																									'Calibration for current standard X4 remains 90 days on firmware 3.2 or later.',
																								],
																	],
											],
						],
			[
							'key' => 'lantern_forum',
							'course' => 'lantern',
							'title' => 'Project Lantern Q and A',
							'threads' => [
												[
																		'title' => 'Pilot start date',
																		'summary' => 'A learner asks when the Lantern pilot begins and which sites participate.',
																		'facts' => [
																									'The pilot starts on 2026-10-15.',
																									'The initial sites are North Harbor and Ridgefield.',
																									'Phase 1 covers 24 standard Orion X4 devices.',
																								],
																	],
												[
																		'title' => 'Telemetry naming',
																		'summary' => 'A learner asks how temperature and humidity metrics should be named.',
																		'facts' => [
																									'The canonical temperature metric is temp_c.',
																									'The canonical humidity metric is humidity_pct.',
																									'Streams use site.device.metric and temporary channels end in _test.',
																								],
																	],
												[
																		'title' => 'Training versus readiness',
																		'summary' => 'A learner asks whether completing onboarding means a site is ready.',
																		'facts' => [
																									'No. Training completion is one readiness input.',
																									'Infrastructure, safety, calibration equipment, contact ownership, and rollback readiness are separate gates.',
																									'Course reporting should not be presented as complete site readiness.',
																								],
																	],
											],
						],
		];
		foreach ($definitions as &$forum) {
			foreach ($forum['threads'] as &$thread) {
				$thread['text'] = self::article($thread['title'], $thread['summary'], $thread['facts']);
			}
			unset($thread);
		}
		unset($forum);
		return $definitions;
	}

	public static function learningModules(): array {
		$definitions = [
			[
							'key' => 'field_lm',
							'course' => 'field_service',
							'title' => 'Field Service Playbook',
							'pages' => [
												[
																		'title' => 'Preparing for a service visit',
																		'summary' => 'Preparation reduces on-site uncertainty and protects traceability.',
																		'facts' => [
																									'Review serial numbers, firmware, current alarms, previous calibration date, and site safety constraints before travel.',
																									'Bring the validated reference set and confirm that its validity extends through the service window.',
																									'Identify the site contact and the escalation contact before arrival.',
																									'Do not assume the device variant from a customer nickname; verify X4 versus X4R explicitly.',
																								],
																	],
												[
																		'title' => 'Arrival and safety scan',
																		'summary' => 'The first on-site activity is a short safety and context scan.',
																		'facts' => [
																									'Confirm the device and process area before touching equipment.',
																									'Look for leaks, heat, damaged enclosures, temporary wiring, and active work by other teams.',
																									'If the process state is unsafe or unclear, stop and coordinate with the site owner.',
																								],
																	],
												[
																		'title' => 'Capturing the baseline',
																		'summary' => 'A baseline preserves evidence before troubleshooting changes the system.',
																		'facts' => [
																									'Capture serial number, firmware, alarm history, measurement state, calibration date, and visible installation condition.',
																									'Write observed facts separately from suspected causes.',
																									'Photographs are useful when site policy permits them and when they do not expose unrelated sensitive information.',
																								],
																	],
												[
																		'title' => 'Controlled troubleshooting',
																		'summary' => 'Controlled troubleshooting limits simultaneous variables.',
																		'facts' => [
																									'For E-17 inspect flow path before component replacement.',
																									'For E-22 confirm warm-up and environment before calibration or cell inspection.',
																									'For E-31 address moisture and connectors before replacing the humidity element.',
																								],
																	],
												[
																		'title' => 'Verification and return to service',
																		'summary' => 'A repair is not complete until normal behavior is demonstrated.',
																		'facts' => [
																									'Run at least one normal measurement cycle after ordinary maintenance.',
																									'Use three clean cycles after correcting an intermittent E-17 condition.',
																									'After E-31 drying, require two stable cycles before return to service.',
																									'After reference-cell replacement, calibration is mandatory.',
																								],
																	],
											],
						],
			[
							'key' => 'product_lm',
							'course' => 'product_cert',
							'title' => 'Orion X4 Certification Manual',
							'pages' => [
												[
																		'title' => 'Model identification',
																		'summary' => 'Certification starts with correct identification of the device family.',
																		'facts' => [
																									'Standard Orion X4 and ruggedized Orion X4R have different operating and calibration requirements.',
																									'The model suffix is part of the maintenance decision.',
																									'Do not infer ruggedized procedures from enclosure appearance alone.',
																								],
																	],
												[
																		'title' => 'Current maintenance intervals',
																		'summary' => 'Maintenance intervals are versioned technical facts.',
																		'facts' => [
																									'Standard X4 on firmware 3.2 or later uses a 90-day calibration interval.',
																									'Archived pre-3.2 X4 guidance states 60 days.',
																									'X4R uses 45 days.',
																									'Inspection remains recommended every 30 days for standard X4.',
																								],
																	],
												[
																		'title' => 'Calibration execution',
																		'summary' => 'Routine calibration follows a strict sequence.',
																		'facts' => [
																									'Stabilize at least 12 minutes after cold start.',
																									'Apply zero reference before span reference.',
																									'Record the reference lot and post-calibration deviation.',
																									'Deviation greater than 1.5 percent triggers setup inspection before a repeat attempt.',
																								],
																	],
												[
																		'title' => 'Alarm families',
																		'summary' => 'Certification requires distinguishing the three primary demo alarm families.',
																		'facts' => [
																									'E-17 means airflow obstruction or insufficient sample-path flow.',
																									'E-22 means reference-cell drift.',
																									'E-31 means humidity-channel mismatch.',
																									'Each alarm family has a different first-response sequence.',
																								],
																	],
												[
																		'title' => 'Variant comparison',
																		'summary' => 'The X4R variant exists to create a realistic retrieval-disambiguation case.',
																		'facts' => [
																									'X4R calibration is 45 days rather than the current 90-day X4 interval.',
																									'X4R temperature range is minus 30 to 65 degrees Celsius.',
																									'Standard X4 temperature range is minus 20 to 55 degrees Celsius.',
																									'Questions that omit the model suffix should be clarified when the distinction matters.',
																								],
																	],
											],
						],
			[
							'key' => 'safety_lm',
							'course' => 'safety',
							'title' => 'Safety Essentials Manual',
							'pages' => [
												[
																		'title' => 'Safe service boundary',
																		'summary' => 'The safe-service boundary defines work a trained technician may perform without additional site authorization.',
																		'facts' => [
																									'Stop when process isolation is required but has not been confirmed.',
																									'Stop when damaged wiring, heat, leakage, or another uncontrolled hazard is present.',
																									'An S1 condition receives immediate escalation even when a temporary workaround exists.',
																								],
																	],
												[
																		'title' => 'Moisture and electrical work',
																		'summary' => 'Moisture changes the order of diagnostic work.',
																		'facts' => [
																									'Visible condensation is addressed through safe isolation and drying before connector manipulation.',
																									'Do not use unapproved heat sources to accelerate drying.',
																									'Record environmental conditions when moisture may explain an intermittent sensor mismatch.',
																								],
																	],
												[
																		'title' => 'Battery handling',
																		'summary' => 'Battery work combines electrical safety and traceability.',
																		'facts' => [
																									'Use only the approved CV-BP48 pack for the standard demo procedure.',
																									'Record pack lot, replacement date, device serial, and technician.',
																									'Damaged or swollen packs are isolated according to site hazardous-material rules.',
																								],
																	],
												[
																		'title' => 'Incident escalation',
																		'summary' => 'Safety escalation prioritizes impact and control of the hazard.',
																		'facts' => [
																									'S1 acknowledgement target is 15 minutes.',
																									'Engineering engagement target for S1 is 30 minutes.',
																									'The case owner remains accountable until another owner explicitly accepts the handoff.',
																								],
																	],
											],
						],
			[
							'key' => 'lantern_lm',
							'course' => 'lantern',
							'title' => 'Project Lantern Onboarding Guide',
							'pages' => [
												[
																		'title' => 'Program scope',
																		'summary' => 'Lantern phase 1 has a deliberately narrow scope.',
																		'facts' => [
																									'The pilot starts 2026-10-15.',
																									'North Harbor and Ridgefield are the initial sites.',
																									'Twenty-four standard Orion X4 devices are in scope.',
																									'X4R devices are excluded from phase 1 validation.',
																								],
																	],
												[
																		'title' => 'Site readiness gates',
																		'summary' => 'Readiness is multi-dimensional rather than a single checklist score.',
																		'facts' => [
																									'Infrastructure, safety, trained contacts, calibration equipment, and rollback planning are separate gates.',
																									'North Harbor requires NH-SENS-04.',
																									'Ridgefield requires gateway group RF-GW-2 and an offline buffering test.',
																								],
																	],
												[
																		'title' => 'Telemetry verification',
																		'summary' => 'Telemetry verification confirms identity, naming, and recovery behavior.',
																		'facts' => [
																									'Streams use site.device.metric.',
																									'The canonical metrics are temp_c and humidity_pct.',
																									'Serial number remains the immutable identity across display-name changes.',
																									'Temporary channels with _test suffix must be removed before acceptance.',
																								],
																	],
												[
																		'title' => 'Reporting and reviews',
																		'summary' => 'Pilot reviews combine operational and training evidence.',
																		'facts' => [
																									'Weekly reviews are Tuesdays at 14:00 local North Harbor time for the first six weeks.',
																									'Training reports show enrollment and learning-progress distributions by course.',
																									'Operational reports show telemetry availability, incidents, calibration compliance, and replacements.',
																								],
																	],
											],
						],
		];
		foreach ($definitions as &$module) {
			foreach ($module['pages'] as &$page) {
				$page['text'] = self::article($page['title'], $page['summary'], $page['facts']);
			}
			unset($page);
		}
		unset($module);
		return $definitions;
	}

	public static function files(): array {
		$definitions = [
			[
							'parent' => 'edge',
							'name' => 'Current Specifications.txt',
							'title' => 'Current Orion X4 Specifications',
							'facts' => [
												'Document status: CURRENT. Effective date: 2026-03-04.',
												'Standard Orion X4 calibration interval: 90 days for firmware 3.2 or later.',
												'Standard Orion X4 operating temperature: -20 to 55 degrees Celsius.',
												'Recommended inspection interval: 30 days.',
												'Battery pack: CV-BP48; planned normal-duty replacement interval: 24 months.',
												'Alarm E-17: airflow obstruction. Alarm E-22: reference-cell drift. Alarm E-31: humidity mismatch.',
											],
						],
			[
							'parent' => 'edge',
							'name' => 'Archived Specifications.txt',
							'title' => 'Archived Orion X4 Specifications',
							'facts' => [
												'Document status: ARCHIVED. Superseded by the 2026-03-04 current specification.',
												'Historical standard Orion X4 calibration interval: 60 days before firmware 3.2 validation.',
												'This file is intentionally retained so retrieval tests must distinguish historical from current guidance.',
												'Do not use this archived interval for a current X4 on firmware 3.2 or later unless an approved local procedure is stricter.',
											],
						],
			[
							'parent' => 'edge',
							'name' => 'Orion X4R Quick Reference.txt',
							'title' => 'Orion X4R Quick Reference',
							'facts' => [
												'Model: Orion X4R ruggedized variant.',
												'Calibration interval: 45 days.',
												'Operating temperature: -30 to 65 degrees Celsius.',
												'Do not apply the standard X4 90-day interval to X4R.',
												'Confirm the model suffix before choosing a procedure.',
											],
						],
			[
							'parent' => 'engineering',
							'name' => 'Project Lantern Pilot Schedule.txt',
							'title' => 'Project Lantern Pilot Schedule',
							'facts' => [
												'Pilot start: 2026-10-15.',
												'Initial sites: North Harbor and Ridgefield.',
												'Phase-1 device count: 24 standard Orion X4 devices.',
												'Weekly pilot review: Tuesday 14:00 local North Harbor time for six weeks.',
												'North Harbor network segment: NH-SENS-04. Ridgefield gateway group: RF-GW-2.',
											],
						],
			[
							'parent' => 'company',
							'name' => 'Service Escalation Quick Reference.txt',
							'title' => 'Service Escalation Quick Reference',
							'facts' => [
												'S1 acknowledgement target: 15 minutes. Engineering engagement target: 30 minutes.',
												'S2 acknowledgement target: one hour. Investigation plan target: four hours.',
												'Golden handoff fields: owner, timestamp, serial number, firmware, impact, evidence, actions, next action.',
												'Severity follows current impact and safety, not customer frustration or case age.',
											],
						],
			[
							'parent' => 'product',
							'name' => 'Orion Alarm Code Matrix.txt',
							'title' => 'Orion Alarm Code Matrix',
							'facts' => [
												'E-17: airflow obstruction or insufficient sample-path flow.',
												'E-22: reference-cell drift outside the accepted stability band.',
												'E-31: humidity-channel disagreement.',
												'Blocked air channel is an informal synonym used for the E-17 failure family.',
												'A repeated E-22 after valid calibration can require reference-cell inspection.',
											],
						],
		];
		foreach ($definitions as &$file) {
			$file['text'] = self::article($file['title'], 'This file is a concise reference document in the BASE3 demo knowledge corpus.', $file['facts']);
		}
		unset($file);
		return $definitions;
	}

	public static function repositoryDefinitions(string $rootTitle): array {
		$definitions = [['type' => 'cat', 'title' => $rootTitle]];
		foreach (self::categories() as $category) $definitions[] = ['type' => 'cat', 'title' => $category['title']];
		foreach (self::courses() as $course) $definitions[] = ['type' => 'crs', 'title' => $course['title']];
		foreach (self::wikis() as $wiki) $definitions[] = ['type' => 'wiki', 'title' => $wiki['title']];
		foreach (self::blogs() as $blog) $definitions[] = ['type' => 'blog', 'title' => $blog['title']];
		foreach (self::forums() as $forum) $definitions[] = ['type' => 'frm', 'title' => $forum['title']];
		foreach (self::learningModules() as $module) $definitions[] = ['type' => 'lm', 'title' => $module['title']];
		foreach (self::files() as $file) $definitions[] = ['type' => 'file', 'title' => $file['name']];
		return $definitions;
	}

	public static function expectedRepositoryObjectCount(): int {
		return 1 + count(self::categories()) + count(self::courses()) + count(self::wikis()) + count(self::blogs()) + count(self::forums()) + count(self::learningModules()) + count(self::files());
	}

	public static function contentStats(): array {
		$wikiPages = 0;
		$blogPosts = 0;
		$forumThreads = 0;
		$learningPages = 0;
		$words = 0;
		foreach (self::wikis() as $wiki) { foreach ($wiki['pages'] as $page) { $wikiPages++; $words += str_word_count($page['text']); } }
		foreach (self::blogs() as $blog) { foreach ($blog['posts'] as $post) { $blogPosts++; $words += str_word_count($post['text']); } }
		foreach (self::forums() as $forum) { foreach ($forum['threads'] as $thread) { $forumThreads++; $words += str_word_count($thread['text']); } }
		foreach (self::learningModules() as $module) { foreach ($module['pages'] as $page) { $learningPages++; $words += str_word_count($page['text']); } }
		foreach (self::files() as $file) $words += str_word_count($file['text']);
		return [
			'users' => count(self::users()),
			'courses' => count(self::courses()),
			'wiki_pages' => $wikiPages,
			'blog_posts' => $blogPosts,
			'forum_threads' => $forumThreads,
			'learning_module_pages' => $learningPages,
			'files' => count(self::files()),
			'approx_words' => $words,
		];
	}

	private static function article(string $title, string $summary, array $facts, array $procedures = [], array $related = []): string {
		$parts = [];
		$parts[] = $title . '. ' . $summary;
		$parts[] = 'Operational context: This document belongs to the fictional Cedar Vale Instruments demo environment. It is deliberately written as durable operational knowledge rather than marketing copy. Dates, model names, alarm codes, maintenance intervals, responsibilities, and procedure boundaries are explicit so retrieval systems can answer precise questions and distinguish current information from historical or variant-specific information.';
		foreach ($facts as $index => $fact) {
			$parts[] = 'Key fact ' . ($index + 1) . ': ' . $fact . ' In practice, this fact should be interpreted together with the device model, firmware version, document status, site context, and effective date. A support engineer should preserve that context when quoting the fact in a case, report, course discussion, or automated answer so that a technically correct sentence is not applied to the wrong variant or time period.';
		}
		if ($procedures !== []) {
			$parts[] = 'Procedure guidance: ' . implode(' ', $procedures) . ' The procedure is intentionally sequential. Record the precondition before acting, make one controlled change at a time when safety allows it, verify the observed result, and document the next owner. If evidence falls outside the approved procedure, escalate instead of improvising an undocumented repair.';
		}
		$parts[] = 'Example scenario: A technician or course participant receives a short question that omits part of the context. The correct response first identifies the relevant product variant, current firmware or program phase, and document status. The responder then cites the applicable operational fact, explains the immediate action, and notes any condition that would change the answer. This pattern is useful for RAG testing because superficially similar questions can require different answers when a model suffix, archive date, or incident severity changes.';
		if ($related !== []) {
			$parts[] = 'Related demo sources: ' . implode(', ', $related) . '. These references intentionally overlap without being identical. A retrieval system should combine them when the question spans domains, but it should not replace a current specification with an archived statement merely because the archived wording is a closer lexical match.';
		}
		$parts[] = 'Quality note: The BASE3 demo corpus is fictional. Its internal facts are consistent by design except where a document is explicitly marked archived, variant-specific, or intentionally conflicting for retrieval evaluation. The purpose is to provide substantial searchable material for RAG, course-management demonstrations, reporting, and assistant tool tests without mixing demo facts with real organizational data.';
		return implode("\n\n", $parts);
	}
}
