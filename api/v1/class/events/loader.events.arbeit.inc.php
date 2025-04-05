<?php
require_once dirname(__DIR__, 1) . "/events/EventDispatcherService.events.arbeit.inc.php";

require_once dirname(__DIR__, 1) . "/events/users/UserCreatedEvent.php";
require_once dirname(__DIR__, 1) . "/events/users/UserDeletedEvent.php";

require_once dirname(__DIR__, 1) . "/events/sickness/SicknessCreatedEvent.php";
require_once dirname(__DIR__, 1) . "/events/sickness/SicknessDeletedEvent.php";
require_once dirname(__DIR__, 1) . "/events/sickness/SicknessUpdatedEvent.php";

require_once dirname(__DIR__, 1) . "/events/vacation/VacationCreatedEvent.php";
require_once dirname(__DIR__, 1) . "/events/vacation/VacationDeletedEvent.php";
require_once dirname(__DIR__, 1) . "/events/vacation/VacationUpdatedEvent.php";

require_once dirname(__DIR__, 1) . "/events/worktimes/EasymodeWorktimePauseStartEvent.php";
require_once dirname(__DIR__, 1) . "/events/worktimes/EasymodeWorktimePauseEndEvent.php";
require_once dirname(__DIR__, 1) . "/events/worktimes/EasymodeWorktimeAddedEvent.php";
require_once dirname(__DIR__, 1) . "/events/worktimes/EasymodeWorktimeEndedEvent.php";
require_once dirname(__DIR__, 1) . "/events/worktimes/FixEasymodeWorktimeEvent.php";
require_once dirname(__DIR__, 1) . "/events/worktimes/WorktimeUnlockedFromReviewEvent.php";
require_once dirname(__DIR__, 1) . "/events/worktimes/WorktimeMarkedForReviewEvent.php";
require_once dirname(__DIR__, 1) . "/events/worktimes/WorktimeAddedEvent.php";
require_once dirname(__DIR__, 1) . "/events/worktimes/WorktimeDeletedEvent.php";

require_once dirname(__DIR__, 1) . "/events/notifications/CreatedNotificationEvent.php";
require_once dirname(__DIR__, 1) . "/events/notifications/DeletedNotificationEvent.php";
require_once dirname(__DIR__, 1) . "/events/notifications/EditedNotificationEvent.php";
require_once dirname(__DIR__, 1) . "/events/notifications/DeletedObsoleteNotificationsEvent.php";

require_once dirname(__DIR__, 1) . "/events/mails/SentMailEvent.php";

require_once dirname(__DIR__, 1) . "/events/exports/generatedExportEvent.php";

require_once dirname(__DIR__, 1) . "/events/auth/LDAPAuthenticationEvent.php";
require_once dirname(__DIR__, 1) . "/events/auth/LoggedInUserEvent.php";
require_once dirname(__DIR__, 1) . "/events/auth/LoggedOutUserEvent.php";
require_once dirname(__DIR__, 1) . "/events/auth/ValidatedLoginEvent.php";

?>