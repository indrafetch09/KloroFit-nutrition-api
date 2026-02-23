# KloroFit API

A comprehensive nutrition and activity tracking API built with Laravel. KloroFit helps users manage their daily nutrition goals, track food intake, and maintain their fitness journey.

## Overview

The API is organized into logical endpoint groups for authentication, nutrition tracking, goal management, and user profile management. All protected endpoints require authentication using Laravel Sanctum tokens.

## API Endpoints

All endpoints are prefixed with `/api/v1`

### Authentication

#### Register

- **POST** `/register`
- **Description:** Create a new user account
- **Body:** User registration credentials

#### Login

- **POST** `/login`
- **Description:** Authenticate user and receive access token

#### Change Password

- **POST** `/change-password`
- **Description:** Update user password
- **Auth:** Required

#### Logout

- **POST** `/logout`
- **Description:** Invalidate current session token
- **Auth:** Required

#### Logout All

- **POST** `/logout-all`
- **Description:** Invalidate all active session tokens
- **Auth:** Required

#### Refresh Token

- **POST** `/refresh-token`
- **Description:** Generate new access token
- **Auth:** Required

### Nutrition Library

#### Search Foods

- **GET** `/nutrition-libraries/search`
- **Description:** Find foods by name
- **Auth:** Required
- **Query Parameters:** Search term

#### Get Food Details

- **GET** `/nutrition-libraries/{id}`
- **Description:** View detailed nutrition information for a specific food
- **Auth:** Required

### Goals

#### Get Goal

- **GET** `/goals/{date}`
- **Description:** Retrieve daily nutrition goal for a specific date
- **Auth:** Required

#### Set/Update Goal

- **POST** `/goals/set`
- **Description:** Create or update daily nutrition goals
- **Auth:** Required

#### Delete Goal

- **DELETE** `/goals/{date}`
- **Description:** Remove goal for a specific date
- **Auth:** Required

### Food Tracking

#### List Foods

- **GET** `/foods`
- **GET** `/foods/{date}`
- **Description:** View logged foods for today or a specific date
- **Auth:** Required

#### Get Food Entry

- **GET** `/foods/{id}`
- **Description:** View details of a specific logged food entry
- **Auth:** Required

#### Log Food

- **POST** `/foods`
- **Description:** Add a new food entry
- **Auth:** Required

#### Log Multiple Foods

- **POST** `/foods/select`
- **Description:** Add multiple food entries at once
- **Auth:** Required

#### Update Food Entry

- **PUT** `/foods/{id}`
- **Description:** Modify a logged food entry
- **Auth:** Required

#### Delete Food Entry

- **DELETE** `/foods/{id}`
- **Description:** Remove a logged food entry
- **Auth:** Required

### Dashboard

#### Get Dashboard Data

- **GET** `/dashboard`
- **Description:** Retrieve daily summary and statistics
- **Auth:** Required

### User Profile

#### Get Profile

- **GET** `/profile`
- **Description:** View current user profile information
- **Auth:** Required

#### Update Profile

- **PUT** `/profile`
- **Description:** Update user profile details
- **Auth:** Required

#### Get Settings

- **GET** `/settings`
- **Description:** Retrieve user settings
- **Auth:** Required

#### Update Settings

- **PUT** `/settings`
- **Description:** Update user preferences
- **Auth:** Required

#### Change Password

- **PUT** `/password`
- **Description:** Update password
- **Auth:** Required

## Authentication

The API uses Laravel Sanctum for token-based authentication. Include the token in request headers:

```
Authorization: Bearer {token}
```

## Health Check

- **GET** `/api/v1/`
- **Description:** Verify API is running
