Project Title: 
TEAM-13 Ecommerce Backend Service (Loft&Living)
Table of Contents
1.	Project Description, Purpose, and Tech Stack
2.	Project structure:
    Folder structure
    Table dependency chain
3.	Deployment
4.	Contributors
Project Description, Purpose, and Tech Stack:
What We Created...
This repository hosts the centralized backend service for the Loft&Living Ecommerce Website. It provides the core business logic, user authentication, catalog management, and transactional processing (basket, orders, payments, and returns) required to power the frontend application.

Purpose and Why
The primary purpose is to serve as a robust, scalable API for an e-commerce platform specializing in homeware (sofas, rugs, blankets, etc.). The design and data models were prioritized to facilitate efficient filtering based on styles and pricing, directly addressing the needs of our target audience: first-time buyers aged 21-30.

Backend Technology Stack
The backend is implemented using a LAMP/WAMP/MAMP-compatible stack:
•	Technology Stack: PHP / Apache (Handling routing and server-side scripting)
•	Database: MySQL / phpMyAdmin (Used for data persistence and administration)
•	Deployment Target: Webmin (Used for server management and deployment

• Project Structure: 
The core PHP logic resides within the backend folder, structured as follows:
•	config/: Database connection and general application settings.
•	controllers/: Request handling logic.
•	routes/: API endpoint definitions.
•	models/: Data models and database interaction.
•	services/: Business logic processing.
Database Setup: Table Dependency Chain
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
Deployment
This service is deployed and managed using Webmin.
•	Staging Environment: The entire codebase is placed in the dedicated Draft folder on Webmin
Backend Developers.

Dimitri Khair-Cabalan
(240124715)	Categories, Products, Returns, and related Models, Controllers, and routes
Amatullah Stevenson
(160158221)	Users, Basket, Payments, and related Models, Controllers, Routes, and Services
Omar Fareh
(230087675)	Orders, Order Items, Reviews, and related Models, Controllers, and routes.







