# CarePlus — Hospital Appointment Booking System

A web-based healthcare scheduling portal built with PHP MVC, MySQL, and JavaScript.
Developed as part of the Web Technologies course at AIUB — Spring 2025–26.

---

## Pages & Views

### 🏠 Homepage
The public-facing landing page of CarePlus. Displays the CarePlus branding, a brief description of the system, featured doctors pulled from the database, and a call-to-action for patients to register or login. No authentication required.
&nbsp;

<img width="1920" height="2197" alt="Index" src="https://github.com/user-attachments/assets/efe84f7e-313d-431e-bf60-26f5304c0b98" />

&nbsp;

---

### 🔐 Authentication

#### Register
Patient self-registration form. Collects name, email, password, date of birth, blood group, and phone number. All fields are validated server-side. Passwords are hashed with bcrypt before storage. Redirects to login on success.

<img width="1920" height="989" alt="Register" src="https://github.com/user-attachments/assets/2c83b6a2-71c2-4745-9d59-f63f38f11ad5" />

#### Login
Single login form for all three roles — Patient, Doctor, and Admin. After credential verification, each role is redirected to their respective dashboard. Deactivated accounts are blocked with an appropriate message.

<img width="1920" height="869" alt="Login" src="https://github.com/user-attachments/assets/489c14f3-77a2-4893-b115-2c6fd55ef758" />

---

### 👤 Patient Pages

#### Patient Home
The patient's dashboard after login. Shows a welcome message, upcoming appointment count, and quick navigation to browse doctors or view appointments.

<img width="1920" height="869" alt="Patient Dashboard" src="https://github.com/user-attachments/assets/c876896f-1f74-4818-abbd-75eaa965ffe5" />

&nbsp;

#### Browse Doctors
Displays all active doctors as cards with photo, name, specialization, and consultation fee. A specialization filter dropdown re-renders the cards via AJAX without a page reload.

<img width="1920" height="869" alt="image" src="https://github.com/user-attachments/assets/87fb16f8-0f13-463c-a689-fc19b14ec507" />

&nbsp;

#### Book Appointment
Doctor profile and booking flow. Shows the next available dates based on the doctor's configured availability days. Selecting a date fetches available time slots via AJAX, excluding already-booked times. Submitting the form performs a server-side slot re-check before confirming the booking.

<img width="1920" height="869" alt="image" src="https://github.com/user-attachments/assets/1cba742d-14b8-4eb0-9b48-e7f7de02cffe" />

&nbsp;

#### Booking Confirmation
Shown after a successful booking. Displays the generated appointment ID (APPT-XXXX), doctor name, date, time, and reason submitted by the patient.

<img width="1920" height="869" alt="image" src="https://github.com/user-attachments/assets/56972401-e4e3-4a18-95cb-89b50ec21117" />

&nbsp;

#### Browse Appointments
Lists all appointments for the logged-in patient with tab filters for All, Pending, Confirmed, Completed, and Cancelled. Pending appointments have a Cancel button that fires an AJAX request and updates the row in place. Cancelled appointments show a Cancellation Note column.

<img width="1920" height="869" alt="image" src="https://github.com/user-attachments/assets/3432997c-f94b-4146-9da0-1685fad1a672" />

&nbsp;

#### Edit Profile
Allows patients to update their name, date of birth, blood group, and phone number. Includes a separate password change section that requires the current password before saving a new one.

<img width="1920" height="1094" alt="image" src="https://github.com/user-attachments/assets/3ad9b5da-5d0d-4136-93ff-6feb9fb99c8b" />

&nbsp;

---

### 🩺 Doctor Pages

#### Dashboard
The doctor's main page after login. Shows stat cards for today's confirmed appointments, completed count, remaining, and no-shows. The today's appointments table displays only Confirmed appointments — doctors can mark each as Completed (removes the row via AJAX) or No-Show. Below the table, a weekly schedule grid renders dynamically from 9:00 AM to 10:00 PM in 30-minute slots across Monday to Friday. Clicking an appointment block opens a detail modal showing the appointment ID, patient name, time, status, and full reason.

<img width="1920" height="2594" alt="image" src="https://github.com/user-attachments/assets/5c60842e-4593-4484-ada7-2c44e46b5434" />

&nbsp;

---

### 🛠 Admin Pages

#### Dashboard
The admin's main page. Displays stat cards for total doctors, active doctors, total appointments, and today's appointment count. The table below shows only today's and tomorrow's Pending appointments. Admins can update any row's status through a confirmation modal — selecting Cancelled shows a required note textarea. Cancelled rows are removed from the table via AJAX after confirmation.

<img width="1920" height="869" alt="image" src="https://github.com/user-attachments/assets/616fe482-13fa-4808-b26f-312d1967463d" />

&nbsp;

#### Appointments Management
Full appointment list with filter controls for doctor, date, and status applied via GET parameters. Rows with Cancelled or Completed status show a dash instead of action buttons. Pending rows show a Confirm button. All non-cancelled rows show a Cancel button that requires a cancellation note via modal.

<img width="1920" height="869" alt="image" src="https://github.com/user-attachments/assets/92ef0376-df34-47d1-b99f-659b4c7ae183" />

&nbsp;

#### Doctors Management
Manage doctor profiles. Includes an add/edit form with fields for name, email, password, specialization, consultation fee, photo upload (JPEG/PNG max 2MB), bio, and weekly availability day checkboxes. The doctor list table shows all doctors with their stats and an edit action. Deleting a doctor deactivates their account rather than permanently removing them.

<img width="1920" height="1431" alt="image" src="https://github.com/user-attachments/assets/216c6e28-ccfb-4f17-a42d-1423a4ab6641" />

&nbsp;

#### Specializations Management
Manage medical specializations with full CRUD. Duplicate names are blocked on add and edit. Deletion is restricted if any doctor is currently assigned to the specialization.

<img width="1920" height="1093" alt="image" src="https://github.com/user-attachments/assets/6c356a4b-9b3a-4727-b910-c9eccfa9dd4a" />

&nbsp;

#### Users Management
Lists all registered users — patients, doctors, and admins — with their role and active status. Each row has an Activate/Deactivate toggle that fires an AJAX request and updates the badge in place without a page reload.

<img width="1920" height="869" alt="image" src="https://github.com/user-attachments/assets/446465de-a1f3-490a-b6b5-ebeb4d750474" />

&nbsp;

---

## Tech Stack

| Layer | Technology |
|---|---|
| Frontend | HTML5, CSS3, JavaScript |
| Backend | PHP 8 (MVC)|
| Database | MySQL via phpMyAdmin |
| Auth | PHP Sessions + bcrypt |
| Version Control | Git / GitHub |
| Server | XAMPP / Apache |

---

## Team

| Task | Feature | Member |
|---|---|---|
| Task 1 | Patient & Staff Authentication | Wamia Fairooz Rafa |
| Task 2 | Doctor & Schedule Management | Rinvila Chowdhury |
| Task 3 | Appointment Booking | Zuhayer Hasan |
| Task 4 | Appointment Dashboard & Status Management | Md. Faiyaj Islam |

---
### Submitted To: Rifat Al Mamun Rudro
---
> Course: Web Technologies — AIUB Spring 2025–26 | Group 03
