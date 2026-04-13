# Technical Assignment
---

## Tech Stack

### Backend
- Laravel 8 (PHP Framework)
- MySQL
- REST API

### Frontend
- Vue 3
- Vuetify 3
- Axios

---

## Features
- Import customers via CSV file
- Implement pagination
- Handle user input appropriately

---

## Installation

### 1. Clone project and checkout to `develop` branch

```
git clone https://github.com/mscabrera/be-dev-test-marican-bernardo.git
cd be-dev-test-marican-bernardo

git checkout develop
```

### 2. Install backend and frontend dependencies
```
composer install
npm install
```

### 3. Setup environment
```
cp .env.example .env
```

### 4. Run migrations
```
php artisan migrate
```

### 5. Run project

```
php artisan serve
npm run dev
```

### 6. Finally, visit `http://127.0.0.1:8000`