# Blog Project Setup Guide

## Prerequisites

Before running the project, ensure you have the following:

- **PHP** (version 8.x or higher)
- **Composer**
- **Node.js** (version 12.x or higher)
- **npm**

## Project Setup

Follow the steps below to get the blog project up and running:

1. **Clone the Repository**  
   - If you haven't already, clone the repository to your local machine:
     ```bash
     git clone <repository_url>
     cd <project_directory>
     ```

2. **Environment Configuration**  
   - Copy the `.env.example` file to `.env`:
     ```bash
     cp .env.example .env
     ```
   - Add your database settings in the `.env` file.
   - Set the API server URL by adding the following line to the `.env` file:
     ```bash
     VITE_API_URL="http://localhost/api/"
     ```

3. **Install PHP Dependencies**  
   - Run the following command to install PHP dependencies:
     ```bash
     composer install
     ```

4. **Generate Application Key**  
   - Run the following command to generate the application key:
     ```bash
     php artisan key:generate
     ```

5. **Set JWT Secret**  
   - Set the JWT secret by running:
     ```bash
     php artisan jwt:secret
     ```

6. **Run Migrations and Seeding**  
   - Run the migrations and seed the database (if needed):
     ```bash
     php artisan migrate --seed
     ```

7. **JWT Authentication**  
   - Ensure JWT authentication is set up and running for the application. If you are using a package like `tymon/jwt-auth`, follow the official documentation to configure it.

8. **Storage Link**  
   - Create a symbolic link for storage:
     ```bash
     php artisan storage:link
     ```

9. **Install Node.js Dependencies**  
   - Install the required frontend dependencies using npm:
     ```bash
     npm install
     ```

10. **Build Frontend**  
    - Build the Vue.js frontend assets:
      ```bash
      npm run build
      ```
    - Alternatively, for development, you can run:
      ```bash
      npm run dev
      ```

11. **Server Configuration**  
    - **Option 1: Configure with Nginx or Apache**  
      Configure the server to point to the `public` directory of your Laravel project.
    
    - **Option 2: Use Laravel’s Built-In Server**  
      Run the following command to start the built-in PHP development server:
      ```bash
      php artisan serve
      ```
      The application will be accessible at `http://localhost:8000`.

## Conclusion

Once you have completed the setup steps, your blog application should be running locally. For production, configure your Nginx or Apache server accordingly.
