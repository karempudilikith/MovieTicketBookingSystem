========================================================================
         CINEPASS - MOVIE TICKET BOOKING MANAGEMENT SYSTEM
              COLLEGE MINI PROJECT DOCUMENTATION
========================================================================

Author / Submitter: College Student Mini Project Submission
Technology Stack  : HTML5, CSS3, JavaScript, Bootstrap 5, Vanilla PHP (mysqli)
Database          : MySQL (mysqli extension only - no PDO, no frameworks)
Design Style      : Clean, Professional District/Government Service Portal Dashboard
Color Theme       : Dark Blue/Navy primary theme

========================================================================
TABLE OF CONTENTS
========================================================================
1. Project Introduction & Key Features
2. Folder Structure
3. Step-by-Step Installation & Running Guide (XAMPP)
4. Importing the Database Schema
5. Default Credentials
6. How to Customize the Code Manually (Movies, Theatres, Prices, Seats)
7. Typical College Viva/Oral Questions & Answers

========================================================================
1. PROJECT INTRODUCTION & KEY FEATURES
========================================================================
CinePass is a fully functional web application designed for booking movie
tickets online. It is built using core web development principles, making it 
extremely easy for beginners to understand, explain, and modify for their 
college viva or project review.

CUSTOMER/USER FEATURES:
-----------------------
- Homepage displaying movies currently showing, with dynamic database fetching.
- Global Search bar to filter movies by title, genre, or language.
- Dedicated Movie Details Page showing genre tags, description, duration, and price.
- Date and Theatre selection.
- Interactive Seat Booking Layout (A1-A8, B1-B8, C1-C8):
  * Available seats are highlighted in GREEN.
  * Selected seats are highlighted in BLUE (using vanilla JS click triggers).
  * Booked seats are disabled and colored in RED (fetched from past bookings).
- Dynamic, real-time ticket price calculation based on selected seats count.
- Order confirmation screen displaying summary details before checkout.
- Dummy payment simulation page.
- Post-Redirect-Get (PRG) pattern implementation on booking insertion (prevents
  duplicate reservations if a user refreshes the payment page).
- Personal Booking History page with printing receipt options.

ADMINISTRATOR FEATURES:
-----------------------
- Standalone secure Admin Login Portal.
- Dashboard with metric counters: Total movies, shows, bookings, and users.
- Manage Movies panel (view, add, edit, and delete movie records).
- Upload poster files or input poster filenames stored in the database.
- Manage Showtimes (schedule movies into specific theatres, dates, and times).
- Audit trail view of all bookings made across the entire system.
- Registered Users database records view.

========================================================================
2. FOLDER STRUCTURE
========================================================================
movie-ticket-booking/
│
├── config.php                   <-- Base URL and session definitions
├── db_connect.php               <-- MySQL database connection using mysqli
├── functions.php                <-- Helper functions (checkUser, formatDate, sanitize)
├── index.php                    <-- Homepage showing movie listing and search
├── login.php                    <-- User login screen
├── register.php                 <-- User signup screen
├── logout.php                   <-- Session termination script
├── movie_details.php            <-- Detailed description and showtimes selector
├── book_seats.php               <-- Graphic interactive seat layout
├── booking_confirmation.php     <-- Booking summary check
├── booking_history.php          <-- User booking history dashboard
├── payment_success.php          <-- Inserts booking & shows printable ticket receipt
│
├── includes/
│   ├── header.php               <-- Navigation bar layout (imported globally)
│   └── footer.php               <-- Standard footer and JS references
│
├── admin/
│   ├── admin_login.php          <-- Admin login page
│   ├── dashboard.php            <-- Admin statistics counters
│   ├── movies.php               <-- Admin movies list view
│   ├── add_movie.php            <-- Add new movie record form
│   ├── edit_movie.php           <-- Edit existing movie form
│   ├── delete_movie.php         <-- Delete movie script (cascades database)
│   ├── shows.php                <-- Schedule showtimes & theatres
│   ├── bookings.php             <-- View database bookings
│   └── users.php                <-- View database users
│
├── css/
│   └── style.css                <-- Custom stylesheets (colors, seat graphics)
│
├── js/
│   └── main.js                  <-- Seat click triggers and dynamic price script
│
├── images/                      <-- Put your movie poster JPG/PNG files here
│
├── database/
│   └── movie_booking.sql        <-- Database setup and sample data script
│
└── README.txt                   <-- This documentation file

========================================================================
3. STEP-BY-STEP INSTALLATION & RUNNING GUIDE (XAMPP)
========================================================================
Follow these instructions to run the application on your local machine:

STEP 1: Install XAMPP
---------------------
Download and install XAMPP for Windows from: https://www.apachefriends.org/
Ensure Apache and MySQL services are selected during installation.

STEP 2: Deploy Project Files
----------------------------
1. Copy the entire 'movie-ticket-booking' folder.
2. Paste it inside your XAMPP installation directory under the 'htdocs' folder.
   Default path is: C:\xampp\htdocs\movie-ticket-booking\

STEP 3: Start XAMPP Control Panel
----------------------------------
1. Open the XAMPP Control Panel application.
2. Click the 'Start' button next to Apache.
3. Click the 'Start' button next to MySQL.
4. Ensure both indicators turn green.

========================================================================
4. IMPORTING THE DATABASE SCHEMA
========================================================================
STEP 1: Open phpMyAdmin
-----------------------
Open your web browser and navigate to: http://localhost/phpmyadmin/

STEP 2: Import SQL Script
-------------------------
1. Click on the "Import" tab at the top menu bar.
2. Click "Choose File" and select the 'movie_booking.sql' file from:
   C:\xampp\htdocs\movie-ticket-booking\database\movie_booking.sql
3. Scroll down and click the "Go" or "Import" button.
4. phpMyAdmin will execute the script, create the database named 
   `movie_booking_db`, set up the tables, and insert all default data.

STEP 3: Open the Web Application
--------------------------------
Type the following URL into your browser address bar:
http://localhost/movie-ticket-booking/

========================================================================
5. DEFAULT CREDENTIALS
========================================================================
For testing purposes, the database is pre-populated with these logins:

1. CUSTOMER USER LOGIN:
   - Email: user@gmail.com
   - Password: user123

2. SYSTEM ADMINISTRATOR LOGIN:
   - Email: admin@gmail.com
   - Password: admin123
   (You can log in via http://localhost/movie-ticket-booking/admin/admin_login.php
   or via the standard login page which redirects you based on your role).

========================================================================
6. HOW TO CUSTOMIZE THE CODE MANUALLY
========================================================================

A. HOW TO MANUALLY ADD MOVIES & SET PRICES:
-------------------------------------------
Option 1 (Using Portal): Log in as Admin -> Go to Admin Panel -> Click "Add Movie" 
-> Fill out details (e.g. ticket price, genre, duration) and upload/type poster name.
Option 2 (Directly in DB): Open phpMyAdmin -> Click `movie_booking_db` -> Select `movies` 
table -> Click "Insert" tab at the top -> Fill details and save.

B. HOW TO EDIT THEATRE NAMES AND SHOWTIMES:
-------------------------------------------
Option 1 (Using Portal): Log in as Admin -> Manage Shows -> Input Screen/Theatre name 
and set date/time -> Click "Add Show".
Option 2 (Directly in DB): Open phpMyAdmin -> Select `shows` table -> Click "Edit" 
on any row or "Insert" to create a show manually.

C. HOW TO CHANGE SEATS GRID / EXTEND ROWS (E.G. ADD D1-D8):
------------------------------------------------------------
If you want to add a row 'D' of seats (D1-D8):
1. Open 'book_seats.php' in VS Code.
2. Find the row definition section around Line 90:
   `$rows = ['A', 'B', 'C'];`
3. Simply add 'D' to the array:
   `$rows = ['A', 'B', 'C', 'D'];`
4. Save the file. The booking grid will automatically render D1 to D8, 
   connect it to the Javascript pricing calculations, and block booked seats.
5. In 'database/movie_booking.sql', there's no limit on seats stored since the 
   `seats` column is a `VARCHAR(255)` string storing comma-separated values (e.g. "A1,D3").

========================================================================
7. TYPICAL COLLEGE VIVA/ORAL QUESTIONS & ANSWERS
========================================================================

Q1: What extension are you using to connect PHP to MySQL? Why?
A1: We are using the MySQLi (MySQL Improved) extension. It is object-oriented,
    beginner-friendly, safe, and built directly into PHP. We did not use PDO 
    because MySQLi is highly recommended and easy to understand for simple, 
    single-database projects.

Q2: How do you prevent SQL Injection attacks in this project?
A2: We use prepared statements (`$conn->prepare(...)` and `$stmt->bind_param(...)`) 
    for all query parameters submitted by users (such as registration, login, and 
    booking insertion). This separates SQL instructions from the input data, 
    blocking malicious payloads. For plain string filters like search, we use 
    `mysqli_real_escape_string` (wrapped inside `db_escape` in `functions.php`).

Q3: How does the seat status display as Booked (Red) or Available (Green)?
A3: When a show is loaded, we query the `bookings` table to find all bookings for 
    that specific show ID. We fetch the booked seat strings (e.g. "A1,A2"), split them, 
    and store them in a PHP array (`$booked_seats`).
    While rendering the grid layout in HTML, we run a check: `in_array($seat_name, $booked_seats)`.
    If yes, we apply the CSS class `booked` (Red) and disable clicks.
    If no, we apply the class `available` (Green).

Q4: What is the benefit of the Post-Redirect-Get (PRG) pattern in payment_success.php?
A4: Without PRG, if a user refreshes the success page, the browser re-submits the 
    POST form data, inserting duplicate tickets into the database. By using PRG, the 
    POST handler processes the booking, saves it, and then redirects the browser 
    to a GET URL (`payment_success.php?booking_id=1`). Refreshing the page now only 
    re-fetches the ticket receipt without saving any new database records.

Q5: Where are the movie poster images stored?
A5: The poster images are saved in the `images/` directory. The database stores only 
    the text string of the file name (e.g., 'interstellar.jpg') in the `poster` column 
    of the `movies` table. The HTML reads this and dynamically prefixes the path: 
    `<img src="images/<?php echo $movie['poster']; ?>">`.
========================================================================
