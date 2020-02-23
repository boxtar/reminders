/* Change to reminders Database */
USE reminders;

ALTER TABLE reminders
ADD COLUMN user_id INT(11) UNSIGNED,
ADD COLUMN channels TEXT,
ADD FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE;
