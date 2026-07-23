# SpendSync API

Backend RESTful API for SpendSync, a personal/family budget and expense tracking application (MVP).

This project serves as the data engine for a frontend user interface (e.g., Angular), providing secure and efficient endpoints to register transactions, categorize expenses, and manage who makes each movement.

## Tech Stack

*   **Framework:** Symfony 7 (PHP 8+)
*   **Database:** MariaDB
*   **ORM:** Doctrine
*   **Infrastructure:** Docker & Docker Compose
*   **Security:** LexikJWTAuthenticationBundle (JSON Web Tokens)

## Key Features

*   **Stateless Authentication:** Secure login system using JWT.
*   **Security by Design:** Use of UUIDs instead of auto-incremental numerical IDs to prevent sequential data exposure.
*   **Full CRUD:** Endpoints to manage Transactions, Categories, and Persons.
*   **Integrated Pagination:** Optimized transaction list with pagination metadata for frontend consumption.
*   **Clean Architecture:** Clear separation between API controllers, relational entities, and database configuration.

## Local Setup

Although the environment is containerized, the following commands are required to initialize the database and generate the security keys for the JWT authentication.

```bash

# 1. Clone the repository and start containers
git clone [https://github.com/your-username/SpendSync-API.git](https://github.com/your-username/SpendSync-API.git)
cd SpendSync-API
docker compose up -d

# 2. Install PHP dependencies
docker exec -it spendsync-php composer install

# 3. Prepare the Database
docker exec -it spendsync-php php bin/console doctrine:database:create
docker exec -it spendsync-php php bin/console doctrine:migrations:migrate

# 4. Generate JWT keys (Crucial for Login)
docker exec -it spendsync-php php bin/console lexik:jwt:generate-keypair

The API will be available at http://127.0.0.1:8015/api.