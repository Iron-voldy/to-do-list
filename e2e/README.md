# End-to-End Tests

This directory contains end-to-end tests for the To-Do application.

## Running E2E Tests

Make sure the application is running first:

```bash
# From the project root
docker-compose up -d
```

Then run the E2E tests:

```bash
# Make the script executable
chmod +x e2e/test.sh

# Run the tests
./e2e/test.sh
```

## What the Tests Cover

The E2E test script validates the following scenarios:

1. API health check
2. Frontend accessibility
3. Fetching initial tasks
4. Creating a new task
5. Verifying the task appears in the list
6. Completing a task
7. Verifying the completed task is removed from the list
8. Error handling (creating task without required fields)

## Custom Configuration

You can customize the API and Frontend URLs:

```bash
API_URL=http://custom-api:8080 FRONTEND_URL=http://custom-frontend:3000 ./e2e/test.sh
```
