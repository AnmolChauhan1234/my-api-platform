# User Data Management API

## Overview
This project is an API-based system built using PHP and Symfony to manage user data, perform database backups, and restore the database from backup. The API allows the uploading of user data from a CSV file, viewing stored user data, and handling email notifications asynchronously to avoid blocking the API responses.

## Features
- **Upload and Store Data API**: Allows an admin to upload the `data.csv` file containing user data.
- **View Data API**: Allows viewing of all users stored in the database.
- **Backup Database API**: Enables an admin to take a backup of the database.
- **Restore Database API**: Allows an admin to restore the database from a backup.
- **Email Notifications**: Sends email notifications to users after successful data storage (emails are sent asynchronously to avoid blocking the API response).

## Technology Stack
- **Backend**: PHP, Symfony
- **Database**: MySQL
- **Email Service**: Mailgun

## Setup Instructions

### Prerequisites
Ensure that you have the following installed on your system:
- PHP >= 7.4
- Symfony >= 5.x
- MySQL or MariaDB
- Composer

### Installation

#### Clone the Repository
Clone the project repository to your local machine:

```bash
git clone https://github.com/AnmolChauhan1234/my-api-platform.git
cd my-api-platform
