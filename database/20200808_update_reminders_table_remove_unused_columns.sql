/* Change to reminders Database */
USE reminders;
ALTER TABLE reminders
DROP COLUMN day,
DROP COLUMN date,
DROP COLUMN month,
DROP COLUMN year,
DROP COLUMN hour,
DROP COLUMN minute,
DROP COLUMN expression,
DROP COLUMN recurrence_expression