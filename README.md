# Project Title: 
TEAM-13 Ecommerce Backend Service (Loft&Living)

## Table of Contents
1.	Project Description and Purpose, 
2.  Frontend Technology Stack
3.  Design Workflow
4.  Backend Technology Stack
5.	Project structure:
    Folder structure
6.  Frontend-Backend Interaction
7.  Database Setup: Table Dependency Chain
8.	Deployment
9.	Contributors

## Project Description, Purpose, and Tech Stack:

### What We Created...
This repository hosts the centralized backend service for the Loft&Living Ecommerce Website. It provides the core business logic, user authentication, catalog management, and transactional processing (basket, orders, payments, and returns) required to power the frontend application.

### Purpose and Why

The primary purpose is to serve as a robust, scalable API for an e-commerce platform specializing in homeware (sofas, rugs, blankets, etc.). The design and data models were prioritized to facilitate efficient filtering based on styles and pricing, directly addressing the needs of our target audience: first-time buyers aged 21-30.

## Frontend Technology Stack

The frontend is built using:
•	**HTML5** for page structure
•	**CSS3** for layout and visual design
•	**Javascript** for dynamic UI interaction (client-side interactions and form handling)
•	**Figma** - UI/UX wireframing and prototyping

## Design Workflow

Before devlopement began, we used **Figma** to create wireframes and early UI prototypes for the Loft & Living website. These wireframes defined the layout, nabigation flow and key user interactions, ensuring a consistent design across all pages.

Using Figma allowed us to:
•	Plan the user journey and page structure in advance
•	Align frontend and backend requirements
•	Identify missing components early (e.g forms, basket/checkout layout etc)
•	Maintain a cohesive visual style throughout the website

The Figma wireframe served as the visual reference for all frontend development and ensures the final implementation matches the intended user experience.

## Backend Technology Stack

The backend is implemented using a LAMP/WAMP/MAMP-compatible stack:
•	Technology Stack: **PHP / Apache** (Handling routing and server-side scripting)
•	Database: **MySQL / phpMyAdmin** (Used for data persistence and administration)
•	Deployment Target: **Webmin** (Used for server management and deployment)

## Project Structure: 

The project is divided into frontend and backend components to maintain a clear separation of concerns.

All frontend files are stored within the Draft folder as follows:
•	css/: Stylesheets and layout design.
•	html/:  HTML pages.
•	images/: Images, icons, and visual assets.
•	javascript/: Client-side scripts for interactions and requests.

The core PHP logic resides within the backend folder, structured as follows:
•	config/: Database connection and general application settings.
•	controllers/: Request handling logic.
•	routes/: API endpoint definitions.
•	models/: Data models and database interaction.
•	services/: Business logic processing.

This ensures a modular development folder structure where presentation and application logic are separated, making the project is easier to maintain, test and scale.

## Frontend-Backend Interaction

The Loft & Living frontend communicates with the backend via dedicated PHP route endpoints to enable secure and efficient handling of user actions and data requests.

Interaction flows include:
•	User Authentication: Login and signup forms send POST requests to /routes/userRoutes.php to validate input and manage user sessions.
•	Product Display: Product pages and Admin-accessible pages request data from /routes/productRoutes.php to dynaically retrieve details, categories and images.
•	Basket Management: The basket page requests /routes/basketRoutes.php to add, remove or update items in the basket, reflecting database changes.
•	Orders & Payments: Order and payment submission are handled via their respected endpoints to ensure efficient transactional processing.
•	Returns & Reviews: Returns and reviews are also managed through returnsRoutes.php and reviewsRoutes.php

**Note:** Full interaction implementation is still under development. Whilst the backend rouutes exist, not all frontend forms and pages are fully connected to the backend at this stage.

## Database Setup: Table Dependency Chain

The database must be initialized in the following order to satisfy foreign key constraints.
1.	Completely Independent Tables (Level 0):
o	users
o	categories
2.	Level 1 Dependencies:
o	products (Requires categories)
o	basket (Requires users)
o	basket_items (Requires basket and products)
3.	Level 2 Dependencies:
o	orders (Requires users)
o	order_items (Requires orders and products)
o	payments (Requires orders and users)
4.	Level 3 Dependencies:
o	returns (Requires order_items and users)
o	return_items (Requires returns and products)
o	reviews (Requires products and users)

## Deployment

This service is deployed and managed using Webmin.
•	Staging Environment: The entire codebase is placed in the dedicated Draft folder on Webmin
Backend Developers.

## Contributors
Team 13 - CS2TP 2025

### Backend
Dimitri Khair-Cabalan
(240124715)	Categories, Products, Returns, and related Models, Controllers, and Routes.
Amatullah Stevenson
(160158221)	Users, Basket, Payments, and related Models, Controllers, Routes, and Services.
Omar Fareh
(230087675)	Orders, Order Items, Reviews, and related Models, Controllers, and Routes.

### Frontend
Bibi Alaradi
(240006097) Homepage, Basket, Order Confirmation pages and related CSS stylesheets.
Manaal Aouttah
(240124977) About, Contact, Categories, Checkout pages and related CSS stylesheets.
Navdeep Malhi
(230042537) Navigation (Header & Footer), Products page and related CSS stylesheets.
Ayoub Abdisalam
(240240392) Sign-in & Sign-up pages and related CSS stylesheets.





