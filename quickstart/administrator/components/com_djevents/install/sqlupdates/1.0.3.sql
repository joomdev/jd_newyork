# Joomla!4 compatibility

ALTER TABLE #__djev_cats 	CHANGE `description` `description` text DEFAULT NULL,
							CHANGE `params` `params` text DEFAULT NULL;

ALTER TABLE #__djev_events 	CHANGE `intro` `intro` text DEFAULT NULL,
							CHANGE `description` `description` text DEFAULT NULL,
							CHANGE `time` `time` text DEFAULT NULL,
							CHANGE `checked_out` `checked_out` int(11) NOT NULL DEFAULT '0';



