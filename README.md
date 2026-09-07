# Cit-E Cycling

A PHP and MySQL web application for managing a national cycling competition. This project was originally developed as a university assignment and has been extended as a portfolio project.

## Features

- Register interest for future cycling events
- Input validation for registration forms
- Secure admin login and logout
- Admin menu for managing the system
- Search individual participants by first name or surname
- Search cycling clubs and view associated participants
- Edit a participant's distance travelled and power output
- Delete participants with confirmation
- Store and retrieve data using MySQL

## Technology Used

- PHP
- MySQL
- HTML
- CSS
- XAMPP for local development
- Git and GitHub

## Local Setup

1. Install and start Apache and MySQL in XAMPP.
2. Copy this project into the XAMPP `htdocs` folder.
3. Open phpMyAdmin at `http://localhost/phpmyadmin`.
4. Import `cycling.sql` to create the database and sample data.
5. Copy the example database configuration:

   ```bash
   cp dbconnect.example.php dbconnect.php
   ```

6. Edit `dbconnect.php` with your local MySQL details.
7. Open the project in a browser, for example:

   ```text
   http://localhost/cycling/
   ```

## Admin Demo Login

Use the demo credentials included in the original assignment database:

```text
Username: admin
Password: password123
```

> These credentials are for demonstration only. A production application must store passwords securely with password hashing.

## Future Improvements

- Public leaderboard for individual cyclists and clubs
- Dashboard with participant and club statistics
- Mobile-first responsive redesign
- Improved form validation and error feedback
- Prepared statements for stronger database security
- Password hashing and role-based admin access
- Pagination and sorting for participant records

## Author

Sushreet
