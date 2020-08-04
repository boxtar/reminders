USE reminders;

ALTER TABLE reminders
ADD COLUMN next_run DATETIME NOT NULL DEFAULT NOW() AFTER frequency