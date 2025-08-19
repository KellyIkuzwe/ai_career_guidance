# AI Powered Career Guidance and Job Matching Platform for Rwandan Youth

This is a full-stack web application that provides AI-powered career guidance and job matching services for Rwandan youth. The platform connects job seekers with employers through an advanced AI matching algorithm.

## System Overview

The platform serves three main user types:
1. **Job Seekers** - Young Rwandans looking for employment opportunities
2. **Companies (Employers)** - Organizations posting job listings and seeking candidates
3. **AI System** - Backend service that matches job seekers to appropriate jobs

## Features

### Job Seeker Features
- Registration and Login
- Career Profile Management (education, skills, interests)
- Document Upload (CV, certificates)
- AI-suggested job matches
- Job Application
- Feedback from companies
- Personalized career report downloads

### Company Features
- Registration and Login
- Job/Internship Posting
- Application Review
- AI-ranked candidate suggestions
- Candidate Feedback
- Reports and Analytics

### AI System Features
- Job-Candidate Matching Algorithm
- Career Guidance Report Generation

## Technical Stack

- **Backend:** PHP 8+ (OOP with MVC structure)
- **Frontend:** HTML, CSS (Tailwind), JavaScript (vanilla)
- **Database:** MySQL
- **Server:** Apache (XAMPP)

## Database Schema

The system uses 7 main tables:
1. **User** - User authentication and role information
2. **CareerProfile** - Job seeker skills, education, and interests
3. **JobListing** - Company job postings
4. **Application** - Job applications from seekers
5. **AIMatch** - AI-generated match scores
6. **Feedback** - Communication between companies and job seekers
7. **Report** - Generated career guidance reports

## Installation and Setup

### Prerequisites
- XAMPP (or equivalent with PHP 8+, MySQL, Apache)
- Web browser

### Installation Steps

1. **Setup XAMPP**
   - Install XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Start Apache and MySQL services

2. **Database Setup**
   - Open phpMyAdmin (usually at http://localhost/phpmyadmin)
   - Create a new database named `career_guidance`
   - Import the SQL file from `db/career_guidance.sql`

3. **Application Setup**
   - Clone or extract the project files to your XAMPP's htdocs folder:
     ```
     /xampp/htdocs/ai_career_guidance/
     ```
   - Navigate to http://localhost/ai_career_guidance in your web browser

### Default Login Credentials

For testing purposes, the following accounts are pre-configured:

**Job Seeker:**
- Email: jobseeker@example.com
- Password: 123

**Company:**
- Email: company@example.com
- Password: 123

**Admin:**
- Email: admin@example.com
- Password: 123

## System Structure

- `/css/` - Stylesheet files
- `/db/` - Database files
- `/images/` - System images
- `/js/` - JavaScript files
- `/panel/` - Dashboard and user interface files
  - `/panel/ai/` - AI matching algorithm
  - `/panel/components/` - Reusable UI components
  - `/panel/profiles/` - User profile pictures
  - `/panel/uploads/` - User uploaded documents

## AI Matching Algorithm

The system uses a simple vector-based algorithm to match job seekers with appropriate jobs by:

1. Processing job seeker profiles (skills, interests, education)
2. Analyzing job requirements and descriptions
3. Calculating similarity scores using cosine similarity
4. Ranking matches and storing scores in the AIMatch table

## Future Enhancements

- Integration with external job boards
- Advanced AI features using machine learning
- Mobile application development
- Real-time notifications
- Video interview functionality

## Credits

This system was developed as part of an educational project to serve the Rwandan youth with career guidance and job matching services.