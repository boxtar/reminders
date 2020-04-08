MySQL is running on the Digital Ocean server.

MySQL in terminal does not support IF statements so this won't work:
   ADD COLUMN IF NOT EXISTS month TINYINT UNSIGNED DEFAULT NULL AFTER date;

Need to remove the IF statement like this:
   ADD COLUMN month TINYINT UNSIGNED DEFAULT NULL AFTER date;

Look into Stored Procedures. Several online documents suggest using these for situations like this.

Note: MariaDB in terminal apparently supports IF statements.