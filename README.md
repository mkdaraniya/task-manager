TaskManager - A Laravel-Based Project Management Tool
Welcome to TaskManager, a free, open-source project management application built with Laravel 11. This tool is designed to streamline task organization, team collaboration, and project tracking with a modern, user-friendly interface inspired by tools like JIRA. It includes features like a Kanban board, real-time updates, calendar integration, and activity logging, making it ideal for teams and individuals managing projects of any size.
About the Developer
Hi, I'm Milind Daraniya, a passionate developer dedicated to creating efficient and user-friendly solutions. Feel free to reach out or explore more about my work:

Email: mkdaraniya@gmail.com
Website: https://milinddaraniya.com
GitHub: github.com/mkdaraniya (Update with your GitHub profile)

If you find TaskManager useful, please star this repository or share your feedback. Contributions are welcome!
Features
TaskManager is packed with powerful features to enhance project management:

Kanban Board: Organize tasks with a drag-and-drop interface, similar to JIRA, for seamless workflow management across different boards (e.g., To Do, In Progress, Done).
Real-Time Updates: Powered by Laravel Reverb for live updates on task changes, team activities, and notifications.
Calendar Integration: View and manage tasks in a FullCalendar-powered calendar with filters for status, priority, and assignee.
Activity Logging: Track all actions (create, edit, update, delete) for tickets, teams, time logs, and subtasks, with detailed logs stored in the database.
Team Management: Create teams, assign roles, and manage members with a professional UI featuring modals for adding/editing members.
Task Management: Create, update, and assign tasks with support for subtasks, priorities, and due dates.
User Profiles: Manage user profiles with avatar uploads, bio, contact details, and password updates, all secured with Laravel authentication.
AJAX-Powered Interface: Smooth, asynchronous updates for task and team operations without page reloads.
Dynamic Dashboard: Displays real-time stats, interactive charts, and recent activity logs for quick project insights.
Role-Based Access: Uses spatie/laravel-permission for granular role and permission management.
Notifications: Receive alerts for task assignments, updates, and team changes.
Export Options: Generate reports in PDF and CSV formats for tasks and project summaries.
Responsive Design: Fully responsive UI that works seamlessly on mobile and desktop devices.

Prerequisites
Before setting up TaskManager, ensure you have the following installed:

PHP >= 8.1
Composer
MySQL or any supported database
Laravel 11 compatible server environment (e.g., Laravel Valet, Homestead, or a local server like XAMPP)
Redis (optional, for Laravel Reverb)

Installation
Follow these steps to set up TaskManager locally:

Clone the Repository:
git clone https://github.com/mkdaraniya/task-manager.git
cd task-manager


Install Dependencies:
composer install


Set Up Environment:

Copy the .env.example file to .env:cp .env.example .env


Update .env with your database credentials and other settings (e.g., APP_URL, DB_CONNECTION).
Generate an application key:php artisan key:generate




Run Migrations:
php artisan migrate


Set Up Storage:

Create a symbolic link for file storage:php artisan storage:link


Ensure the storage/app/public/avatars directory exists and is writable:mkdir -p storage/app/public/avatars
chmod -R 775 storage




Install Frontend Assets:

Serve the Application:
php artisan serve

Access the app at http://localhost:8000.


Configuration

Database: Ensure your database is set up in .env. Run php artisan migrate to create tables for users, teams, tasks, projects, time logs, and activity logs.
File Uploads: Avatars are stored in storage/app/public/avatars. Ensure the directory is writable.
Permissions: Use spatie/laravel-permission for role-based access. Run php artisan permission:cache-reset after modifying permissions.
FullCalendar: The calendar uses the FullCalendar library (CDN included). Ensure internet access for CDN or install locally via npm.
Image Processing: Requires intervention/image for avatar resizing. Install with:composer require intervention/image



Usage

Register/Login: Create an account or log in to access the dashboard.
Create a Project: Navigate to the Projects section to create a new project and assign team members.
Manage Tasks: Use the Kanban board to create, drag, and drop tasks across columns. Assign tasks to team members and set priorities/due dates.
View Calendar: Access the Calendar view to see tasks as events, filter by status, priority, or assignee, and click events for details.
Manage Teams: Create teams, add members, and assign roles (e.g., Owner, Member) via the Teams UI.
Check Activity Logs: View the activity log on the dashboard to track all actions across the system.
Update Profile: Edit your profile, upload an avatar, and update contact details from the Profile page.
Export Data: Generate PDF or CSV reports from the dashboard or project views.

Contributing
Contributions are welcome! To contribute:

Fork the repository.
Create a new branch (git checkout -b feature/your-feature).
Commit your changes (git commit -m 'Add your feature').
Push to the branch (git push origin feature/your-feature).
Open a Pull Request.

Please ensure your code follows Laravel coding standards and includes tests where applicable.
Troubleshooting

Tasks not visible in calendar: Ensure FullCalendar CDN is loaded, check console logs for errors, and verify task data in the database. Clear caches with php artisan cache:clear.
Avatar upload fails: Check storage/app/public/avatars permissions and ensure intervention/image is installed.
Reverb not working: Verify Redis is running and Reverb settings in .env are correct. Start the Reverb server with php artisan reverb:start.
Permission errors: Run php artisan permission:cache-reset and check spatie/laravel-permission configuration.

License
TaskManager is open-source software licensed under the MIT License.
Contact
For questions, feedback, or support, please contact me:

Email: mkdaraniya@gmail.com
Website: https://milinddaraniya.com

Thank you for using TaskManager! 🚀
