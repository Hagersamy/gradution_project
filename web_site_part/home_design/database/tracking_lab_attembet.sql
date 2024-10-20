-- test attempet--
-- Step 1: Modify the labs table to add score and is_solved columns
ALTER TABLE labs
ADD score INT NOT NULL DEFAULT 0,    -- Score for the lab
ADD is_solved TINYINT(1) DEFAULT 0;  -- Indicates if the lab is solved (0 = No, 1 = Yes)

-- Step 2: Create a new table for tracking user attempts
CREATE TABLE lab_attempts (
    attempt_id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    lab_id INT(11) NOT NULL,
    failed_attempts INT NOT NULL DEFAULT 0, -- Number of failed attempts
    is_solved TINYINT(1) DEFAULT 0,         -- Indicates if the lab has been solved
    PRIMARY KEY (attempt_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (lab_id) REFERENCES labs(lab_id) ON DELETE CASCADE
);
