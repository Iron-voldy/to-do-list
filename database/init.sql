-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS todo_app;
USE todo_app;

-- Create task table
CREATE TABLE IF NOT EXISTS task (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_completed_created (completed, created_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert some sample data for testing
INSERT INTO task (title, description, completed) VALUES
('Buy books', 'Buy books for the next school year', FALSE),
('Clean home', 'Need to clean the hall room', FALSE),
('Takehome assignment', 'Finish the mid-term assignment', FALSE);
