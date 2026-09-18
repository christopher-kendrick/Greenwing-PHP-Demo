-- ============================================
-- Create Database
-- ============================================

-- Create the integration_demo database
CREATE DATABASE integration_demo;

-- Select the database for subsequent operations
USE integration_demo;


-- ============================================
-- Create Users Table
-- ============================================

-- Create a table to store user information
CREATE TABLE users (

    -- Unique identifier for each user
    -- Automatically increments for new records
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- User's full name
    -- Cannot be NULL
    name VARCHAR(255) NOT NULL,

    -- User's email address
    -- Must be unique across all users
    -- Cannot be NULL
    email VARCHAR(255) NOT NULL UNIQUE,

    -- Automatically stores the record creation timestamp
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);