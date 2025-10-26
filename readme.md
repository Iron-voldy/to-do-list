# CoverageX Full Stack Todo Task Application

This project is a small, full-stack to-do task web application built as a technical assessment for the Full Stack Engineer/Intern position at CoverageX LLC.

It adheres to the assignment requirements, including a **3-component architecture (DB, Backend API, Frontend UI)**, full containerization with **Docker/Docker Compose**, and specific task management logic.

---

## 🚀 Getting Started

These instructions will get you a copy of the project up and running on your local machine.

### Prerequisites

You only need **Docker** and **Docker Compose** installed on your system.
*(Note: A Linux-like environment with Bash is assumed, as per the assignment's technical assumptions.)*

### Running the Application

1.  **Clone the repository:**
    ```bash
    git clone [YOUR_GITHUB_REPO_LINK]
    cd [YOUR_REPO_NAME]
    ```

2.  **Start the containers:**
    The provided `docker-compose.yml` file handles the setup, building, and linking of all three components (Database, Backend API, and Frontend UI).

    ```bash
    docker-compose up --build -d
    ```
    *The `--build` flag ensures that the backend and frontend components are built into fresh images before starting.*

3.  **Access the application:**
    Once all services are running (this may take a minute or two for the initial build), you can access the To-Do application at:
    **Frontend UI:** `http://localhost:[FRONTEND_PORT]` (e.g., `http://localhost:3000`)

---

## 🛠️ Architecture and Tech Stack

The system follows a standard three-tier architecture, completely containerized for easy deployment.

| Component | Tech Stack Chosen | Description |
| :--- | :--- | :--- |
| **Database (DB)** | **[DBMS_NAME]** (e.g., PostgreSQL) | Stores the task data in the `task` table. |
| **Backend API** | **[LANGUAGE]** with **[FRAMEWORK]** (e.g., Node.js with Express) | A **RESTful API** serving as the core business logic. It handles all database interactions. |
| **Frontend UI** | **[FRAMEWORK/LIBRARY]** (e.g., React/Vue/Vanilla JS) | A **Single Page Application (SPA)** that consumes the backend API and presents the user interface. |

### Database Design (`task` Table)

| Column Name | Data Type | Description |
| :--- | :--- | :--- |
| `id` | SERIAL/INT PRIMARY KEY | Unique identifier for the task. |
| `title` | VARCHAR(255) | The title of the to-do task. |
| `description` | TEXT | The detailed description of the task. |
| `created_at` | TIMESTAMP | Timestamp of when the task was created (used for ordering). |
| `is_completed` | BOOLEAN | **Status flag**: `FALSE` for active, `TRUE` for completed. |

### REST API Endpoints

| Method | Path | Description |
| :--- | :--- | :--- |
| `POST` | `/api/tasks` | **Create Task**: Creates a new task with a title and description. |
| `GET` | `/api/tasks/recent` | **Get Tasks**: Retrieves the **5 most recently created and uncompleted** tasks. |
| `PUT` | `/api/tasks/{id}/complete` | **Mark Done**: Updates the specified task's `is_completed` status to `TRUE`. |

---

## ✅ Core Functionality Implemented

The application successfully meets all the user requirements:

1.  **Task Creation:** Users can create tasks via the UI by providing a title and description.
2.  **Recent Tasks Listing:** The UI only displays the **5 most recent** tasks that are *not* completed, ordered by creation time.
3.  **Task Completion:** Clicking the "Done" button marks the task as completed (`is_completed = TRUE`), and it is immediately removed from the active task list.

---

## 🧪 Testing and Quality

### Backend

| Test Type | Location | Coverage Details |
| :--- | :--- | :--- |
| **Unit Tests** | `[backend/test/unit]` | Tests for individual functions and classes (e.g., data validation, utility methods). |
| **Integration Tests** | `[backend/test/integration]` | Tests the full flow of API endpoints, including database interaction (e.g., create task, fetch tasks, mark complete). |

> **To run Backend Tests:**
> ```bash
> docker-compose exec backend [COMMAND_TO_RUN_TESTS]
> ```
> *The test coverage report is located at `[path/to/coverage/report]` after running.*

### Frontend

| Test Type | Location | Details |
| :--- | :--- | :--- |
| **Unit Tests** | `[frontend/src/tests]` | Tests for individual components and state management logic using [Testing Library/Jest]. |
| **End-to-End (E2E) Tests (Extra Marks)** | `[e2e/tests]` | Comprehensive tests covering user flows (create task -> view list -> mark done) using [Cypress/Playwright]. |

### Code Quality

The codebase prioritizes **clean code principles** and, where applicable, adheres to **SOLID principles** (e.g., Single Responsibility Principle in API handlers, Dependency Inversion in service layers) for maintainability and scalability.