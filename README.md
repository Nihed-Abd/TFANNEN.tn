# Tfannen.tn 🎨
Tfannen.tn is an e-commerce platform tailored for custom-designed T-shirts, enabling users to create, customize, and purchase their own designs with ease. The platform features secure authentication, role-based access, product management, event handling, and a unique 3D Studio powered by Three.js for immersive T-shirt customization.

## Features
Google Authentication: Quick and easy sign-in with Google.
Secure Authentication: Role-based access with distinct Designer and Client roles.
Product Management: Seamless product catalog management.
Event Management: Organize and manage events directly from the admin panel.
Data Visualization: Integrated charts for business insights.
Order Management: Comprehensive control over customer orders.
Complaint Management: Efficient handling of user complaints and support requests.
Admin Control: Full control over the platform’s functionalities.
Payment Integration: Secure online payments for purchases.
Product Filtering and Sorting: Advanced store functionality with filters and sorting options.
3D T-Shirt Designer: Interactive 3D studio for designing T-shirts, powered by Three.js.


### Getting Started
Follow these steps to set up and run the project on your local machine.

Prerequisites

Make sure you have the following installed:

PHP (v8.0+)
Composer (for managing Symfony dependencies)
Symfony CLI (optional, for easier project management)
Node.js and npm (for frontend assets)
MySQL (or another compatible database)

Installation

1 * Clone the Repository

git clone https://github.com/YourUsername/tfannen.tn.git
cd tfannen.tn


2 * Install Dependencies

Use Composer to install backend dependencies and npm for frontend dependencies.

composer install
npm install


3 * Set Up Environment Variables

Duplicate the .env.example file and rename it to .env. Update the following values with your database credentials and any other required environment variables:


DATABASE_URL="mysql://username:password@127.0.0.1:3306/tfannen"

4 * Database Setup

Run the following commands to create the database and apply migrations:

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
Run Symfony Server

5 * Start the Symfony development server:


symfony serve

6 * Compile Studio

Use the following command to compile studio :

cd studio
npm install
npm run dev

#### Usage

Once the server is running, open http://127.0.0.1:8000 in your browser to access Tfannen.tn.

Project Structure
This project is organized with Symfony's MVC architecture. The main directories include:

src/ - Core logic, including controllers and services.
public/ - Web assets and entry point for the application.
templates/ - Twig templates for rendering views.
assets/ - Frontend assets (CSS, JavaScript).
Contributing
Feel free to submit issues, fork the repository, and send pull requests! Contributions are always welcome.

License
This project is licensed under the MIT License.
