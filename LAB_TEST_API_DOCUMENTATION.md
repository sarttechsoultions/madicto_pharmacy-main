# Medicto Pharmacy – Lab Test Management API Documentation

This document provides complete technical specifications for integrating the **Lab Test Management System** into the Medicto Pharmacy Mobile Application (React Native / Flutter / Android / iOS).

---

## 1. General Architecture & Standards

- **Base URL**: `http://<your-server-domain-or-ip>/api` (e.g. `http://192.168.1.100:8000/api` or `https://pharmacy.yourdomain.com/api`)
- **Format**: All requests and responses use standard `application/json`.
- **Status Filter**: All public/mobile APIs automatically return **only active laboratories** and **only active lab tests**. Inactive tests or tests belonging to inactive laboratories are filtered out at the database level.
- **Dynamic Pricing**: The mobile application must **never hardcode** test names, laboratories, or prices. Always fetch dynamic data using these endpoints.

### Standard Response Envelope
```json
{
  "success": true,
  "message": "Human readable status message",
  "data": { ... }
}
```

### Standard Error Response Envelope
```json
{
  "success": false,
  "message": "Error description message",
  "errors": { ... }
}
```

---

## 2. API Endpoints Overview

| # | Endpoint | Method | Auth Required | Description |
|---|---|---|---|---|
| 1 | `/api/labs` | `GET` | No | Get all active laboratories |
| 2 | `/api/lab-test-categories` | `GET` | No | Get all active test categories |
| 3 | `/api/lab-tests` | `GET` | No | Get all active lab tests (with pagination & filters) |
| 4 | `/api/labs/{id}/tests` | `GET` | No | Get all active tests for a specific laboratory |
| 5 | `/api/lab-tests/{id}` | `GET` | No | Get full details of a specific lab test |
| 6 | `/api/lab-tests/{id}/price` | `GET` | No | Fetch real-time live pricing and discount calculation |
| 7 | `/api/lab-tests?search={query}` | `GET` | No | Search tests by name, code or keyword |
| 8 | `/api/lab-tests?category_id={id}`| `GET` | No | Filter lab tests by category |
| 9 | `/api/lab-tests?lab_id={id}` | `GET` | No | Filter lab tests by laboratory |

---

## 3. Detailed Endpoint Documentation

### API 1: Get All Active Laboratories
Retrieve the list of all active partner diagnostic centers.

- **URL**: `/api/labs`
- **Method**: `GET`
- **Headers**:
  ```http
  Accept: application/json
  ```
- **Query Parameters**:
  - `search` *(optional, string)*: Filter labs by name, city, or address.
  - `city` *(optional, string)*: Filter labs by specific city.

#### Sample Request (cURL):
```bash
curl -X GET "http://127.0.0.1:8000/api/labs" -H "Accept: application/json"
```

#### Sample Response (`200 OK`):
```json
{
  "success": true,
  "message": "Active laboratories fetched successfully",
  "data": [
    {
      "id": 1,
      "name": "City Diagnostic Lab",
      "logo": "http://127.0.0.1:8000/uploads/labs/city_lab.png",
      "description": "NABL Accredited premium pathology lab with 24/7 emergency testing.",
      "contact_number": "+91 98765 43210",
      "email": "info@citydiagnosticlab.com",
      "address": "Shop 12-14, Health Plaza, M.G. Road",
      "city": "Mumbai",
      "state": "Maharashtra",
      "pincode": "400001",
      "status": "active",
      "active_tests_count": 8
    },
    {
      "id": 2,
      "name": "ABC Laboratory",
      "logo": null,
      "description": "Advanced molecular diagnostics and clinical biochemistry center.",
      "contact_number": "+91 98220 12345",
      "email": "support@abclabs.in",
      "address": "45 Park View Avenue, Near Metro Station",
      "city": "Pune",
      "state": "Maharashtra",
      "pincode": "411001",
      "status": "active",
      "active_tests_count": 6
    }
  ]
}
```

---

### API 2: Get All Active Lab Test Categories
Retrieve all active diagnostic categories (e.g. Blood Test, Thyroid, Lipid Profile, etc.).

- **URL**: `/api/lab-test-categories`
- **Method**: `GET`
- **Headers**:
  ```http
  Accept: application/json
  ```

#### Sample Request (cURL):
```bash
curl -X GET "http://127.0.0.1:8000/api/lab-test-categories" -H "Accept: application/json"
```

#### Sample Response (`200 OK`):
```json
{
  "success": true,
  "message": "Active lab test categories fetched successfully",
  "data": [
    {
      "id": 1,
      "name": "Blood Tests",
      "icon": "http://127.0.0.1:8000/uploads/lab_categories/blood.png",
      "description": "Routine blood counts, hematology, and cellular health profiles.",
      "status": "active",
      "active_tests_count": 5
    },
    {
      "id": 2,
      "name": "Endocrine & Thyroid",
      "icon": null,
      "description": "Thyroid hormones (T3, T4, TSH) and hormonal health assessments.",
      "status": "active",
      "active_tests_count": 3
    }
  ]
}
```

---

### API 3: Get All Active Lab Tests (Search & Filter)
Retrieve all active tests with laboratory details, category details, live pricing, and instructions.

- **URL**: `/api/lab-tests`
- **Method**: `GET`
- **Headers**:
  ```http
  Accept: application/json
  ```
- **Query Parameters**:
  - `search` *(optional, string)*: Search by test name, test code, or description (e.g., `?search=cbc` or `?search=CBC001`).
  - `category_id` *(optional, integer)*: Filter tests belonging to a specific category (e.g., `?category_id=1`).
  - `lab_id` *(optional, integer)*: Filter tests conducted by a specific laboratory (e.g., `?lab_id=2`).
  - `page` *(optional, integer)*: Page number for pagination (defaults to 1).
  - `per_page` *(optional, integer)*: Items per page (defaults to 15).

#### Sample Request (cURL):
```bash
curl -X GET "http://127.0.0.1:8000/api/lab-tests?search=cbc" -H "Accept: application/json"
```

#### Sample Response (`200 OK`):
```json
{
  "success": true,
  "message": "Active lab tests fetched successfully",
  "data": [
    {
      "id": 1,
      "test_name": "Complete Blood Count (CBC)",
      "test_code": "CBC001",
      "lab_id": 1,
      "lab_name": "City Diagnostic Lab",
      "category_id": 1,
      "category_name": "Blood Tests",
      "description": "Measures red blood cells, white blood cells, platelets, and hemoglobin.",
      "price": 350.0,
      "discount_price": 300.0,
      "sample_type": "Blood (EDTA)",
      "report_delivery_time": "24 Hours",
      "preparation_instructions": "No special preparation required. Overnight fasting is optional.",
      "image": null,
      "status": "active"
    }
  ],
  "pagination": {
    "total": 1,
    "per_page": 15,
    "current_page": 1,
    "last_page": 1,
    "has_more_pages": false
  }
}
```

---

### API 4: Get Lab Tests by Lab ID
Retrieve all active tests provided by a specific laboratory.

- **URL**: `/api/labs/{id}/tests`
- **Method**: `GET`
- **URL Parameters**:
  - `id` *(required, integer)*: ID of the laboratory.
- **Query Parameters**:
  - `category_id` *(optional, integer)*: Filter within this laboratory by category.
  - `search` *(optional, string)*: Search within this laboratory's catalog.
  - `page` *(optional, integer)*: Page number.

#### Sample Request:
```bash
curl -X GET "http://127.0.0.1:8000/api/labs/1/tests" -H "Accept: application/json"
```

#### Sample Response (`200 OK`):
```json
{
  "success": true,
  "message": "Tests for City Diagnostic Lab fetched successfully",
  "laboratory": {
    "id": 1,
    "name": "City Diagnostic Lab",
    "logo": null,
    "city": "Mumbai"
  },
  "data": [
    {
      "id": 1,
      "test_name": "Complete Blood Count (CBC)",
      "test_code": "CBC001",
      "lab_id": 1,
      "lab_name": "City Diagnostic Lab",
      "category_id": 1,
      "category_name": "Blood Tests",
      "description": "Complete blood count evaluation.",
      "price": 350.0,
      "discount_price": 300.0,
      "sample_type": "Blood (EDTA)",
      "report_delivery_time": "24 Hours",
      "preparation_instructions": "No special preparation required",
      "image": null,
      "status": "active"
    }
  ],
  "pagination": {
    "total": 1,
    "per_page": 15,
    "current_page": 1,
    "last_page": 1,
    "has_more_pages": false
  }
}
```

---

### API 5: Get Lab Test Details
Fetch full clinical profile, preparation guidelines, and laboratory info for a single test.

- **URL**: `/api/lab-tests/{id}`
- **Method**: `GET`
- **URL Parameters**:
  - `id` *(required, integer)*: Lab Test ID.

#### Sample Request:
```bash
curl -X GET "http://127.0.0.1:8000/api/lab-tests/1" -H "Accept: application/json"
```

#### Sample Response (`200 OK`):
```json
{
  "success": true,
  "message": "Lab test details fetched successfully",
  "data": {
    "id": 1,
    "test_name": "Complete Blood Count (CBC)",
    "test_code": "CBC001",
    "lab_id": 1,
    "lab_name": "City Diagnostic Lab",
    "category_id": 1,
    "category_name": "Blood Tests",
    "description": "Complete blood count evaluation with 24 individual parameters.",
    "price": 350.0,
    "discount_price": 300.0,
    "sample_type": "Blood (EDTA)",
    "report_delivery_time": "24 Hours",
    "preparation_instructions": "No special preparation required. Water intake allowed.",
    "image": null,
    "status": "active",
    "laboratory": {
      "id": 1,
      "name": "City Diagnostic Lab",
      "logo": null,
      "contact_number": "+91 98765 43210",
      "email": "info@citydiagnosticlab.com",
      "address": "Shop 12-14, Health Plaza",
      "city": "Mumbai",
      "state": "Maharashtra",
      "pincode": "400001"
    },
    "category": {
      "id": 1,
      "name": "Blood Tests",
      "icon": null
    }
  }
}
```

#### Error Response when Test is Not Found or Inactive (`404 Not Found`):
```json
{
  "success": false,
  "message": "Lab test not found or is currently inactive."
}
```

---

### API 6: Get Lab Test Pricing
Retrieve real-time price calculation for checkout screens.

- **URL**: `/api/lab-tests/{id}/price`
- **Method**: `GET`
- **URL Parameters**:
  - `id` *(required, integer)*: Lab Test ID.

#### Sample Request:
```bash
curl -X GET "http://127.0.0.1:8000/api/lab-tests/1/price" -H "Accept: application/json"
```

#### Sample Response (`200 OK`):
```json
{
  "success": true,
  "message": "Live price fetched successfully",
  "data": {
    "test_id": 1,
    "test_name": "Complete Blood Count (CBC)",
    "lab_name": "City Diagnostic Lab",
    "price": 350.0,
    "discount_price": 300.0,
    "has_discount": true,
    "effective_price": 300.0,
    "savings_amount": 50.0,
    "currency": "INR",
    "currency_symbol": "₹"
  }
}
```

---

## 4. React Native Integration Examples

### Fetching Lab Tests with Search & Category Filter
```typescript
import axios from 'axios';

const API_BASE = 'http://127.0.0.1:8000/api';

export const fetchLabTests = async (params: {
  search?: string;
  category_id?: number;
  lab_id?: number;
  page?: number;
}) => {
  try {
    const response = await axios.get(`${API_BASE}/lab-tests`, {
      headers: {
        Accept: 'application/json',
      },
      params,
    });
    if (response.data.success) {
      return response.data.data; // Array of test objects
    }
    return [];
  } catch (error) {
    console.error('Failed to load lab tests:', error);
    throw error;
  }
};
```

### Fetching Dynamic Test Pricing
```typescript
export const getLiveTestPrice = async (testId: number) => {
  const response = await axios.get(`${API_BASE}/lab-tests/${testId}/price`);
  if (response.data.success) {
    return response.data.data; // { effective_price, savings_amount, has_discount }
  }
  throw new Error(response.data.message);
};
```

---

## 5. Summary of Built-in Sample Records

| Test Name | Test Code | Lab Name | Category | Regular Price | Deal Price | Sample Type | Delivery |
|---|---|---|---|---:|---:|---|---|
| Complete Blood Count (CBC) | CBC001 | City Diagnostic Lab | Blood Tests | ₹350 | ₹300 | Blood (EDTA) | 24 Hours |
| Thyroid Profile (T3, T4, TSH) | THY002 | City Diagnostic Lab | Endocrine & Thyroid | ₹600 | ₹550 | Blood (Serum) | 24 Hours |
| Vitamin D (25-Hydroxy) | VITD003 | ABC Laboratory | Vitamins & Minerals | ₹900 | ₹799 | Blood (Serum) | 48 Hours |
| Lipid Profile | LIP004 | ABC Laboratory | Cardiovascular | ₹500 | ₹450 | Blood (Serum) | 24 Hours |
| Blood Sugar (Fasting / PP) | GLU005 | City Diagnostic Lab | Diabetes & Metabolic | ₹150 | — | Blood (Sodium Fluoride) | 12 Hours |
| Liver Function Test (LFT) | LFT006 | MetroPath Diagnostics | Liver & Renal | ₹750 | ₹650 | Blood (Serum) | 24 Hours |
| Kidney Function Test (KFT) | KFT007 | MetroPath Diagnostics | Liver & Renal | ₹700 | ₹600 | Blood (Serum) | 24 Hours |
| Urine Routine & Microscopy | URN008 | HealthFirst Clinical Lab | Pathology | ₹200 | — | Fresh Urine | Same Day |
