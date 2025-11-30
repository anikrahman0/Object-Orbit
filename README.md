# ObjectOrbit

**ObjectOrbit** is a Laravel 12 application designed to simplify managing cloud storage services such as AWS S3, DigitalOcean Spaces, and other S3-compatible providers. Users can securely connect multiple storage accounts, browse folders and files, create and delete directories, and perform batch operations — all through a clean, responsive dashboard built with Tailwind CSS.

---

## Features

- Multi-user support with isolated storage connections  
- Connect any S3-compatible storage by providing credentials  
- Browse folders and files with seamless navigation  
- Create and delete folders easily  
- Batch delete with selection and confirmation dialogs  
- Secure user authentication and credential management  
- Responsive UI powered by Tailwind CSS  

---

## Technology Stack

- **Backend:** Laravel 12  
- **Frontend:** Tailwind CSS  
- **Storage:** Flysystem (League) with dynamic disks  
- **Cloud SDK:** AWS SDK for PHP (S3 compatibility)  

---

## Installation

## 1. Clone the repo:  
   git clone https://github.com/anikrahman0/Object-Orbit.git
   cd objectorbit
## 2. Install Dependencies

Install backend dependencies with Composer:

composer install

## 3. Environment Setup

Copy the example environment file and generate the application key:

cp .env.example .env
php artisan key:generate


## 4. Database Migration

Run the following command to migrate the database:


php artisan migrate

## 5. Login & Register

Register a new user or log in with your credentials to access the dashboard.

## 6. Connect S3-Compatible Storage

After login, navigate to the **Storage Connections** section and add your S3-compatible credentials. This includes support for AWS S3, DigitalOcean Spaces, and similar providers.

**Required Fields:**

- Access Key  
- Secret Key  
- Region  
- Bucket  
- Endpoint  

## 7. Browse Files & Folders

Once a connection is successful, you can:

- Browse folders
- View images and files
- Navigate through directories like a file manager

## 8. Folder & File Management

- Create new folders inside any path
- Navigate via breadcrumbs
- Delete folders/files with confirmation
- (Coming soon) Select multiple items for batch operations

## 9. Contact

For support or suggestions, please reach out to:

**Md. Anik Rahman**  
[Email: a7604366@gmail.com](mailto:a7604366@gmail.com)

