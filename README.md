# BCC University

### ⚠️⚠️⚠️

```
Submissions from 2024 students will have much higher priority than submissions from 2023, SAP, or higher students.
Please take note of this before planning to attempt this freepass challenge.
```

## 💌 Invitation Letter

Embracing the ever-evolving conference management landscape, we recognize the need for a seamless and engaging experience in academic meetings especially in the organizational space. We are embarking on an innovative project to transform the way conferences are hosted and experienced, and we want you to be a part of this journey!

We aim to create a dynamic conference platform that revolutionizes session management, attendee engagement, and administrative oversight. Your contributions will help shape the future of conference management. Together, we can create a platform that enhances knowledge sharing and professional networking while maintaining the highest standards of academic discourse.

Join us in revolutionizing the conference experience. Your insights and expertise are key to making this transformation happen!

## **⭐** Minimum Viable Product (MVP)

As we have mentioned earlier, we need technology that can support BCC Conference in the future. Please consider these features below:

- New user can register account to the system ✔️
- User can login to the system ✔️
- User can edit their profile account ✔️
- User can view all conference sessions ✔️
- User can leave feedback on sessions ✔️
- User can view other user's profile ✔️
- Users can register for sessions during the conference registration period if seats are available ✔️
- Users can only register for one session within a time period ✔️
- Users can create, edit, delete their session proposals ✔️
- Users can only create one session proposal within a time period ✔️
- Users can edit, delete their session ✔️
- Event Coordinator can view all session proposals ✔️
- Event Coordinator can accept or reject user session proposals ✔️
- Event Coordinator can remove sessions ✔️
- Event Coordinator can remove user feedback ✔️
- Admin can add new event coordinators ✔️
- Admin can remove users/event coordinators ✔️

## **🌎** Service Implementation

```
GIVEN => I am a new user
WHEN  => I register to the system
THEN  => System will record and return the user's registration details

GIVEN => I am a user
WHEN  => I log in to the system
THEN  => System will authenticate and grant access based on user credentials

GIVEN => I am a user
WHEN  => I edit my profile account
THEN  => The system will update my account with the new information

GIVEN => I am a user
WHEN  => I view all available conference's sessions
THEN  => System will display all conference sessions with their details

GIVEN => I am a user
WHEN  => I leave feedback on a session
THEN  => System will record my feedback and display it under the session

GIVEN => I am a user
WHEN  => I view other user's profiles
THEN  => System will show the information of other user's profiles

GIVEN => I am a user
WHEN  => I register for conference's sessions
THEN  => System will confirm my registration for the selected session

GIVEN => I am a user
WHEN  => I create a new session proposal
THEN  => System will record and confirm the session creation

GIVEN => I am a user
WHEN => I see my session's proposal details
THEN => System will display my session's proposal details

GIVEN => I am a user
WHEN  => I update my session's proposal details
THEN  => System will apply the changes and confirm the update

GIVEN => I am a user
WHEN  => I delete my session's proposal
THEN  => System will remove the session's proposal

GIVEN => I am a user
WHEN => I see my session details
THEN => System will display my session details

GIVEN => I am a user
WHEN  => I update my session details
THEN  => System will apply the changes and confirm the update

GIVEN => I am a user
WHEN  => I delete my session
THEN  => System will remove the session

GIVEN => I am an event coordinator
WHEN  => I view session proposals
THEN  => System will display all submitted session proposals

GIVEN => I am an event coordinator
WHEN  => I accept or reject the session proposal
THEN  => The system will make the session either be available or unavailable

GIVEN => I am an event coordinator
WHEN  => I remove a session
THEN  => System will delete the session

GIVEN => I am an event coordinator
WHEN  => I remove user feedback
THEN  => System will delete the feedback from the session

GIVEN => I am an admin
WHEN  => I add new event coordinator
THEN  => System will make the account to the system

GIVEN => I am an admin
WHEN  => I remove a user or event coordinator
THEN  => System will delete the account from the system
```

## **👪** Entities and Actors

We want to see your perspective about these problems. You can define various types of entities or actors. One thing for sure, there is no
true or false statement to define the entities. As long as the results are understandable, then go for it! 🚀

## **📘** References

You might be overwhelmed by these requirements. Don't worry, here's a list of some tools that you could use (it's not required to use all of them nor any of them):

1. [Example Project](https://github.com/meong1234/fintech)
2. [Git](https://try.github.io/)
3. [Cheatsheets](https://devhints.io/)
4. [REST API](https://restfulapi.net/)
5. [Insomnia REST Client](https://insomnia.rest/)
6. [Test-Driven Development](https://www.freecodecamp.org/news/test-driven-development-what-it-is-and-what-it-is-not-41fa6bca02a2/)
7. [Clean Architecture](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)
8. [GraphQL](https://graphql.org/)
9. [gRPC](https://grpc.io/)
10. [Docker Compose](https://docs.docker.com/compose/install/)

## **🔪** Accepted Weapons

> BEFORE CHOOSING YOUR LANGUAGE, PLEASE VISIT OUR [CONVENTION](CONVENTION.md) ON THIS PROJECT
>
> **Any code that did not follow the convention will be rejected!**
>
> 1. Golang (preferred)
> 2. Java
> 3. NodeJS
> 4. PHP

You are welcome to use any libraries or frameworks, but we appreciate if you use the popular ones.

## **🎒** Tasks

```
The implementation of this project MUST be in the form of a REST, gRPC, or GraphQL API (choose AT LEAST one type).
```

1. Fork this repository
2. Follow the project convention
3. Finish all service implementations
4. Write the installation guide of your back-end service in the section below

## **🧪** API Installation

Laravel Project
This is a Laravel-based application. Follow the steps below to get started.

Prerequisites
Before you begin, ensure that you have the following installed:

PHP (recommended version 8.0 or higher)
Composer
MySQL
Laravel (you will install it via Composer)
Node.js (for managing front-end assets)
Step 1: Clone the repository
Clone the repository to your local machine:

bash
Salin
Edit
git clone https://github.com/yourusername/your-laravel-project.git
Navigate to the project folder:

bash
Salin
Edit
cd your-laravel-project
Step 2: Install Dependencies
Run the following command to install all required PHP dependencies via Composer:

bash
Salin
Edit
composer install
This will install all the necessary packages defined in the composer.json file.

If you need front-end dependencies (for assets like JavaScript and CSS), you can install them using:

bash
Salin
Edit
npm install
Step 3: Set Up the Environment
Laravel uses environment variables stored in the .env file. To configure your environment:

Copy the .env.example file to a new .env file:

bash
Salin
Edit
cp .env.example .env
Open .env and set the appropriate database connection and other environment settings:

ini
Salin
Edit
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
Make sure that your MySQL server is running, and the database is set up.

Step 4: Generate Application Key
Run the following Artisan command to generate a new application key. This key is used to encrypt user sessions and other encrypted data.

bash
Salin
Edit
php artisan key:generate
Step 5: Run Migrations
Run the database migrations to create the necessary tables:

bash
Salin
Edit
php artisan migrate
If you have seeders set up, you can populate your database with sample data by running:

bash
Salin
Edit
php artisan db:seed
Step 6: Start the Local Development Server
Laravel comes with a built-in development server. To start it, run the following command:

bash
Salin
Edit
php artisan serve
By default, the application will be accessible at http://localhost:8000.

Step 7: Access the Application
Open a web browser and go to the following URL to access the Laravel app:

arduino
Salin
Edit
http://localhost:8000
Step 8: Set Up Front-End Assets (Optional)
If you are using front-end assets such as CSS and JavaScript compiled via Laravel Mix, you can run:

bash
Salin
Edit
npm run dev   # For development build
npm run prod  # For production build
This will compile your assets and place them in the public folder for use in the application.

Additional Notes
Authentication: If your app requires user authentication (e.g., using Laravel Sanctum for API authentication), make sure to configure it properly in the .env file and follow the setup steps in the Laravel documentation.

Testing: If you want to run tests for your application, Laravel provides a robust testing suite. You can run the tests using the following command:

bash
Salin
Edit
php artisan test
Directory Structure
Here’s a brief overview of some key directories and files in a typical Laravel project:

app/: Contains the core application code such as controllers, models, and services.
config/: Contains configuration files for the application.
database/: Contains migrations, seeders, and factories for database interaction.
public/: Contains the front-end assets (CSS, JS) and the index.php file which serves as the entry point for the app.
resources/: Contains views, language files, and raw assets.
routes/: Contains route definitions (e.g., web.php, api.php).
storage/: Used for caching, logs, and file storage.
tests/: Contains test files for your application.
.env: Stores environment-specific variables.
Troubleshooting
Error: Class 'Role' not found
Ensure you’ve installed the spatie/laravel-permission package and run the necessary migrations. Run:

bash
Salin
Edit
composer require spatie/laravel-permission
php artisan migrate
Error: permission does not exist
If you're getting permission-related issues, make sure you’ve run the RoleSeeder or DatabaseSeeder to create the roles.

Error: composer command not found
If Composer is not installed, follow the instructions on Composer's website.

This README covers the essential setup process to get your Laravel application running. If you have any further issues, check the Laravel documentation for more details.

Happy coding!

## **📞** Contact

Have any questions? You can contact either [Tyo](https://www.instagram.com/nandanatyo/) or [Ilham](https://www.instagram.com/iilham_akbar/).

## **🎁** Submission

Please follow the instructions on the [Contributing guide](CONTRIBUTING.md).

![cheers](https://gifsec.com/wp-content/uploads/2022/10/cheers-gif-11.gif)

> This is not the only way to join us.
>
> **But, this is the _one and only way_ to instantly pass.**
