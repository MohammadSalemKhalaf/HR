#!/usr/bin/env bash

# EMS End-to-End Testing Script
# This script tests the complete EMS workflow

BASE_URL="http://127.0.0.1:8001/api"
ADMIN_EMAIL="admin@ems.local"
ADMIN_PASS="Admin@123"
SEEKER_EMAIL="seeker@example.local"
SEEKER_PASS="Seeker@123"
EMPLOYEE_EMAIL="employee@acme.local"
EMPLOYEE_PASS="Employee@123"
MANAGER_EMAIL="manager@acme.local"
MANAGER_PASS="Manager@123"

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test counter
TESTS_PASSED=0
TESTS_FAILED=0

# Helper function to make API calls
call_api() {
    local method=$1
    local endpoint=$2
    local data=$3
    local token=$4

    if [ -z "$token" ]; then
        curl -s -X "$method" "$BASE_URL$endpoint" \
            -H "Content-Type: application/json" \
            -d "$data"
    else
        curl -s -X "$method" "$BASE_URL$endpoint" \
            -H "Content-Type: application/json" \
            -H "Authorization: Bearer $token" \
            -d "$data"
    fi
}

# Helper function to extract field from JSON response
extract_field() {
    local json=$1
    local field=$2
    echo "$json" | grep -o "\"$field\":\"[^\"]*\"" | cut -d'"' -f4
}

# Test function
test_endpoint() {
    local test_name=$1
    local method=$2
    local endpoint=$3
    local data=$4
    local token=$5
    local expected_code=$6

    echo -n "Testing: $test_name ... "

    response=$(call_api "$method" "$endpoint" "$data" "$token")

    # Check if response is valid JSON and contains success indicator
    if echo "$response" | grep -q '"success":true'; then
        echo -e "${GREEN}PASS${NC}"
        ((TESTS_PASSED++))
        echo "$response"
    else
        echo -e "${RED}FAIL${NC}"
        ((TESTS_FAILED++))
        echo "Response: $response" >&2
    fi

    echo ""
}

echo "================================"
echo "EMS End-to-End Testing"
echo "================================"
echo ""

# Test 1: Admin Login
echo -e "${YELLOW}[1] Testing Authentication${NC}"
admin_response=$(call_api "POST" "/auth/login" "{\"email\":\"$ADMIN_EMAIL\",\"password\":\"$ADMIN_PASS\"}")
ADMIN_TOKEN=$(extract_field "$admin_response" "token")
echo "Admin Token: ${ADMIN_TOKEN:0:20}..."
test_endpoint "Admin Login" "POST" "/auth/login" "{\"email\":\"$ADMIN_EMAIL\",\"password\":\"$ADMIN_PASS\"}" "" 200
echo ""

# Test 2: Job Seeker Registration & Login
echo -e "${YELLOW}[2] Testing Job Seeker Flow${NC}"
SEEKER_EMAIL="seeker_$(date +%s)@example.local"
seeker_register=$(call_api "POST" "/auth/register" "{\"name\":\"New Seeker\",\"email\":\"$SEEKER_EMAIL\",\"password\":\"Password@123\",\"role\":\"job_seeker\"}")
SEEKER_TOKEN=$(extract_field "$seeker_register" "token")
test_endpoint "Job Seeker Registration" "POST" "/auth/register" "{\"name\":\"Seeker User\",\"email\":\"$SEEKER_EMAIL\",\"password\":\"Password@123\",\"role\":\"job_seeker\"}" "" 200
echo ""

# Test 3: Employee Management
echo -e "${YELLOW}[3] Testing Employee Management${NC}"
test_endpoint "List Employees" "GET" "/employees" "" "$ADMIN_TOKEN" 200
test_endpoint "Get Current User Info" "GET" "/me" "" "$ADMIN_TOKEN" 200
echo ""

# Test 4: Company Management
echo -e "${YELLOW}[4] Testing Company Management${NC}"
test_endpoint "List Companies" "GET" "/companies" "" "$ADMIN_TOKEN" 200
echo ""

# Test 5: Department Management
echo -e "${YELLOW}[5] Testing Department Management${NC}"
test_endpoint "List Departments" "GET" "/departments" "" "$ADMIN_TOKEN" 200
echo ""

# Test 6: Employee Login & Self-Service
echo -e "${YELLOW}[6] Testing Employee Self-Service${NC}"
emp_response=$(call_api "POST" "/auth/login" "{\"email\":\"$EMPLOYEE_EMAIL\",\"password\":\"$EMPLOYEE_PASS\"}")
EMP_TOKEN=$(extract_field "$emp_response" "token")
test_endpoint "Employee Login" "POST" "/auth/login" "{\"email\":\"$EMPLOYEE_EMAIL\",\"password\":\"$EMPLOYEE_PASS\"}" "" 200

# Extract employee ID from response
EMP_ID=$(echo "$emp_response" | grep -o '"employee_id":"[^"]*"' | cut -d'"' -f4)
echo "Employee ID: $EMP_ID"
echo ""

# Test 7: Attendance Tracking
echo -e "${YELLOW}[7] Testing Attendance Tracking${NC}"
if [ -n "$EMP_ID" ]; then
    test_endpoint "Check In" "POST" "/employees/$EMP_ID/check-in" "" "$EMP_TOKEN" 201
    test_endpoint "Check Out" "POST" "/employees/$EMP_ID/check-out" "" "$EMP_TOKEN" 200
    test_endpoint "List Attendance" "GET" "/attendance?employee_id=$EMP_ID" "" "$EMP_TOKEN" 200
else
    echo -e "${RED}Skipping attendance tests - could not extract employee ID${NC}"
fi
echo ""

# Test 8: Leave Management
echo -e "${YELLOW}[8] Testing Leave Management${NC}"
if [ -n "$EMP_ID" ]; then
    leave_data="{\"employee_id\":\"$EMP_ID\",\"leave_type\":\"annual\",\"start_date\":\"2026-05-10\",\"end_date\":\"2026-05-15\",\"days_count\":6}"
    test_endpoint "Request Leave" "POST" "/leave/apply" "$leave_data" "$EMP_TOKEN" 201
    test_endpoint "List Leaves" "GET" "/leaves" "" "$EMP_TOKEN" 200
else
    echo -e "${RED}Skipping leave tests - could not extract employee ID${NC}"
fi
echo ""

# Summary
echo "================================"
echo "Test Results Summary"
echo "================================"
echo -e "${GREEN}Passed: $TESTS_PASSED${NC}"
echo -e "${RED}Failed: $TESTS_FAILED${NC}"
echo ""

if [ $TESTS_FAILED -eq 0 ]; then
    echo -e "${GREEN}✓ All tests passed!${NC}"
    exit 0
else
    echo -e "${RED}✗ Some tests failed${NC}"
    exit 1
fi
