# Blog Post Management API with Elasticsearch

This is a Laravel-based API for managing blog posts with Elasticsearch integration for efficient searching.

## Features
- RESTful API for managing blog posts (Create, Read, Update, Delete)
- Elasticsearch integration for fast and efficient search
- Custom Artisan command to reindex all posts

## Requirements
- PHP 8.1+
- Laravel 10+
- Elasticsearch 8+
- Composer
- MySQL or another supported database
- Docker (optional for running Elasticsearch)

## Installation

### **1. Clone the Repository**
```sh
git clone https://github.com/MahdiPurhosseini/blog-api.git
cd blog-api
```

### **2. Start the Docker Containers**
```sh
docker compose -f docker-compose-dev.yml up -d --build
```

### **3. Configure Environment Variables**
Create a `.env` file in the project root and add the following configurations:

```ini
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=blog_api
DB_USERNAME=root
DB_PASSWORD=secret

# Elasticsearch Configuration
ELASTICSEARCH_HOST=https://localhost:9200
ELASTICSEARCH_SCHEME=https
ELASTICSEARCH_USER=root
ELASTICSEARCH_PASS=secret
ELASTICSEARCH_CLOUD_ID= # <-- Set if you are using Elasticsearch Cloud
ELASTICSEARCH_API_KEY= # <-- Set if you're using API key for authentication
ELASTICSEARCH_SSL_VERIFICATION=true
ELASTICSEARCH_INDEX_PREFIX= # <-- Optional: Set prefix for Elasticsearch index
ELASTICSEARCH_TIMEOUT=60
ELASTICSEARCH_CONNECT_TIMEOUT=10
ELASTICSEARCH_RETRIES=2
ELASTICSEARCH_POOL_SIZE=50
```

### **4. Install Dependencies & Run Migrations**
Inside the `app` container, run the following commands:
```sh
docker exec -it blog-api-app bash
composer install
php artisan migrate --seed
```

### **5. API Requests**
You can now send requests to the project using tools like Postman. A Postman collection is available for easy testing.

### **6. Reindex Posts in Elasticsearch**
Run the following command to set up and reindex blog posts in Elasticsearch:
```sh
php artisan elasticsearch:setup-posts \
                          {--refresh : Drop and recreate the index} \
                          {--sync : Sync all posts after creating index}
```

This will create the necessary index in Elasticsearch and optionally sync all existing posts into it.

## Usage
- Use the API endpoints to manage blog posts.
- Perform searches efficiently with Elasticsearch.
- Keep the index updated by reindexing as needed.

---
This API is designed for scalability and efficiency, leveraging Laravel and Elasticsearch to provide a seamless experience.

