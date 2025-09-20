# 🚀 TaskManager - A Laravel-Based Project Management Tool

Welcome to **TaskManager**, a free, open-source project management application built with **Laravel 11**.  
This tool is designed to streamline **task organization, team collaboration, and project tracking** with a modern, user-friendly interface inspired by tools like **JIRA**.

✨ Features include a **Kanban board**, **real-time updates**, **calendar integration**, and **activity logging**, making it ideal for teams and individuals managing projects of any size.

---

## 👨‍💻 About the Developer
Hi, I'm **Milind Daraniya**, a passionate developer dedicated to creating efficient and user-friendly solutions.  

📧 Email: [mkdaraniya@gmail.com](mailto:mkdaraniya@gmail.com)  
🌐 Website: [milinddaraniya.com](https://milinddaraniya.com)  
🐙 GitHub: [github.com/mkdaraniya](https://github.com/mkdaraniya)  

If you find **TaskManager** useful, please ⭐ star this repository or share your feedback. Contributions are always welcome! 🎉

---

## ✨ Features

- 🗂 **Kanban Board** – Drag-and-drop task management (To Do, In Progress, Done)  
- 🔄 **Real-Time Updates** – Powered by Laravel Reverb for instant collaboration  
- 📅 **Calendar Integration** – Task scheduling via FullCalendar with filters  
- 📜 **Activity Logging** – Detailed logs for tasks, teams, subtasks, and time logs  
- 👥 **Team Management** – Create teams, assign roles, and manage members  
- ✅ **Task Management** – Support for subtasks, priorities, and due dates  
- 🧑‍💻 **User Profiles** – Avatar upload, bio, and secure authentication  
- ⚡ **AJAX-Powered UI** – Smooth updates without page reloads  
- 📊 **Dynamic Dashboard** – Real-time stats and charts for insights  
- 🔐 **Role-Based Access** – Using `spatie/laravel-permission`  
- 🔔 **Notifications** – Alerts for task assignments and updates  
- 📤 **Export Options** – Generate reports in **PDF** and **CSV**  
- 📱 **Responsive Design** – Works seamlessly on mobile and desktop  

---

## 🛠 Prerequisites

Make sure you have:

- PHP **>= 8.1**  
- Composer  
- MySQL (or another supported database)  
- Laravel 11 compatible server (Valet, Homestead, or XAMPP)  
- Redis *(optional, for Laravel Reverb)*  

---

## ⚙️ Installation

```bash
# Clone the repository
git clone https://github.com/mkdaraniya/task-manager.git
cd task-manager

# Install dependencies
composer install

# Copy .env file and update configuration
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Set up storage
php artisan storage:link
mkdir -p storage/app/public/avatars
chmod -R 775 storage

# Serve the app
php artisan serve
