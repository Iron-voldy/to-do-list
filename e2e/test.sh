#!/bin/bash

# End-to-end test script for To-Do Application
# This script tests the full application flow

set -e

API_URL="${API_URL:-http://localhost:8080}"
FRONTEND_URL="${FRONTEND_URL:-http://localhost:3000}"

echo "Starting End-to-End Tests..."
echo "API URL: $API_URL"
echo "Frontend URL: $FRONTEND_URL"
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Test counter
TESTS_PASSED=0
TESTS_FAILED=0

# Helper function to run tests
run_test() {
    local test_name=$1
    local command=$2

    echo -n "Testing: $test_name... "

    if eval "$command" > /dev/null 2>&1; then
        echo -e "${GREEN}PASSED${NC}"
        ((TESTS_PASSED++))
        return 0
    else
        echo -e "${RED}FAILED${NC}"
        ((TESTS_FAILED++))
        return 1
    fi
}

# Wait for services to be ready
echo "Waiting for services to be ready..."
sleep 5

# Test 1: Check if API is healthy
run_test "API Health Check" "curl -f $API_URL/health"

# Test 2: Check if frontend is accessible
run_test "Frontend Accessibility" "curl -f $FRONTEND_URL"

# Test 3: Get initial tasks
echo -n "Testing: Get initial tasks... "
RESPONSE=$(curl -s -X GET $API_URL/tasks)
if echo "$RESPONSE" | grep -q "success"; then
    echo -e "${GREEN}PASSED${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}FAILED${NC}"
    ((TESTS_FAILED++))
fi

# Test 4: Create a new task
echo -n "Testing: Create new task... "
CREATE_RESPONSE=$(curl -s -X POST $API_URL/tasks \
    -H "Content-Type: application/json" \
    -d '{"title":"E2E Test Task","description":"This is a test task"}')

if echo "$CREATE_RESPONSE" | grep -q "success"; then
    echo -e "${GREEN}PASSED${NC}"
    ((TESTS_PASSED++))
    TASK_ID=$(echo "$CREATE_RESPONSE" | grep -o '"id":[0-9]*' | grep -o '[0-9]*')
    echo "  Created task with ID: $TASK_ID"
else
    echo -e "${RED}FAILED${NC}"
    ((TESTS_FAILED++))
fi

# Test 5: Verify task was created
echo -n "Testing: Verify task in list... "
LIST_RESPONSE=$(curl -s -X GET $API_URL/tasks)
if echo "$LIST_RESPONSE" | grep -q "E2E Test Task"; then
    echo -e "${GREEN}PASSED${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}FAILED${NC}"
    ((TESTS_FAILED++))
fi

# Test 6: Complete the task
if [ ! -z "$TASK_ID" ]; then
    echo -n "Testing: Complete task... "
    COMPLETE_RESPONSE=$(curl -s -X PUT $API_URL/tasks/$TASK_ID/complete)
    if echo "$COMPLETE_RESPONSE" | grep -q "success"; then
        echo -e "${GREEN}PASSED${NC}"
        ((TESTS_PASSED++))
    else
        echo -e "${RED}FAILED${NC}"
        ((TESTS_FAILED++))
    fi

    # Test 7: Verify task is no longer in list
    echo -n "Testing: Verify task removed from list... "
    FINAL_LIST=$(curl -s -X GET $API_URL/tasks)
    if echo "$FINAL_LIST" | grep -q "E2E Test Task"; then
        echo -e "${RED}FAILED${NC}"
        echo "  Task should not be in list after completion"
        ((TESTS_FAILED++))
    else
        echo -e "${GREEN}PASSED${NC}"
        ((TESTS_PASSED++))
    fi
fi

# Test 8: Test error handling - create task without title
echo -n "Testing: Error handling (missing title)... "
ERROR_RESPONSE=$(curl -s -X POST $API_URL/tasks \
    -H "Content-Type: application/json" \
    -d '{"description":"No title"}')

if echo "$ERROR_RESPONSE" | grep -q "false"; then
    echo -e "${GREEN}PASSED${NC}"
    ((TESTS_PASSED++))
else
    echo -e "${RED}FAILED${NC}"
    ((TESTS_FAILED++))
fi

# Summary
echo ""
echo "================================"
echo "E2E Test Summary"
echo "================================"
echo -e "Tests Passed: ${GREEN}$TESTS_PASSED${NC}"
echo -e "Tests Failed: ${RED}$TESTS_FAILED${NC}"
echo "================================"

if [ $TESTS_FAILED -eq 0 ]; then
    echo -e "${GREEN}All tests passed!${NC}"
    exit 0
else
    echo -e "${RED}Some tests failed!${NC}"
    exit 1
fi
