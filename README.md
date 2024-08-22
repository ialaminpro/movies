# Movies Project

This project is a web application for managing a movie database, developed using the Symfony framework. It integrates MongoDB, MySQL, and Elasticsearch to provide robust data management and search capabilities.

## Features

- **Movie Management**: Add, update, delete, and view movies in the database.
- **Advanced Search**: Utilize Elasticsearch for advanced movie searching capabilities.
- **Data Storage**: Leverages both MongoDB and MySQL for different aspects of data storage and retrieval.

## Technologies Used

- **Symfony**: PHP framework used for building the web application.
- **MongoDB**: NoSQL database used for storing certain types of data, like movie metadata.
- **MySQL**: Relational database used for storing structured data.
- **Elasticsearch**: Search engine used for implementing advanced search functionality.

## Project Structure

```plaintext
movies-project/
├── config/               # Configuration files for Symfony
├── src/                  # Source code of the application
│   ├── Controller/       # Symfony controllers
│   ├── Entity/           # Doctrine entities (MySQL)
│   ├── Document/         # MongoDB documents
│   ├── Repository/       # Repositories for database interactions
│   ├── Service/          # Business logic and services
│   └── Command/          # Symfony commands
├── templates/            # Twig templates for views
├── public/               # Publicly accessible files (e.g., index.php)
├── migrations/           # Database migrations
├── tests/                # Automated tests
├── var/                  # Symfony-generated files (cache, logs, etc.)
├── vendor/               # Composer dependencies
└── composer.json         # Composer configuration file
```

## Installation

### Prerequisites

- PHP 7.4 or higher
- Composer
- Symfony CLI (optional but recommended)
- MongoDB
- MySQL
- Elasticsearch

### Steps

1. **Clone the repository**:
   ```bash
   git clone https://github.com/ialaminpro/movies.git
   cd movies
   ```

2. **Install dependencies**:
   ```bash
   composer install
   ```

3. **Set up environment variables**:
   Copy `.env` to `.env.local` and configure your database and other environment-specific settings:
   ```bash
   cp .env .env.local
   ```

4. **Configure Databases**:
   - Set up MongoDB and MySQL connection strings in the `.env.local` file.
   - Create MySQL database:
     ```bash
     php bin/console doctrine:database:create
     php bin/console doctrine:migrations:migrate
     ```

5. **Start Services**:
   - Ensure MongoDB, MySQL, and Elasticsearch services are running.
   - Run the Symfony server:
     ```bash
     symfony server:start
     ```

6. **Index Data with Elasticsearch**:
   Run the command to index data into Elasticsearch:
   ```bash
   php bin/console app:index-movies
   ```

## Usage

- Access the application in your browser at `http://localhost:8000`.
- Use the web interface to manage movies and utilize the search functionality.

## Testing

To run the tests, execute:
```bash
php bin/phpunit
```

## Contributing

Contributions are welcome! Please submit a Pull Request or open an issue to discuss potential improvements.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contact

For any questions or inquiries, please reach out via [GitHub issues](https://github.com/ialaminpro/movies/issues).
