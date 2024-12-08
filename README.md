# Take-Home Test: Task Management System

## Instructions
1.	Set up a fresh Laravel project:
	•	Included are a Dockerfile and docker-compose.yml to help you get started.
	•	Use the following commands to build and run the Docker environment:
	•	`docker-compose build` – to build the Docker image.
	•	`docker-compose up` – to create and start the containers.
	•	Note: You are not required to use Docker. The provided setup is optional and intended to simplify the process. You are free to use your preferred environment (e.g., local setup, Vagrant, etc.).
2. Complete the following tasks. Ensure your code follows Laravel best practices, is well-commented, and uses modern PHP and Laravel conventions.
3. Document any assumptions made and provide clear instructions to run your project.
4. Submit your completed project as a zipped file or push it to a private repository and share access.

---

## Important Notes
- This task is **purely backend-focused**. You are only required to build the **API** for the system.
- There is no need to implement or worry about frontend functionality.

---

## Scenario
You are tasked with building the backend for a **Task Management System**. The system will manage tasks and comments, and users can collaborate in projects with assigned roles. The following constraints apply to add complexity and encourage critical thinking.

---

## Core Features
1. **Projects Management**:
    - A user can create a project and become its **Owner**.
    - A project can have multiple **Members**, and each member has a role: `Viewer` or `Editor`.
    - Only `Editors` and the `Owner` can create tasks within the project.
    - Implement an API endpoint to invite users to a project (generate a unique token for the invitation).

2. **Tasks Management**:
    - A task has a **title**, **description**, **status** (`todo`, `in_progress`, `done`), and **priority** (`low`, `medium`, `high`).
    - Tasks can be assigned to project members (but not to `Viewers`).
    - Only the **Owner** can delete tasks.

3. **Commenting System**:
    - Comments can be added to tasks by any project member.
    - Comments cannot be edited or deleted after **10 minutes** from creation (enforced in the backend).

4. **Task Analytics**:
    - Create an API endpoint: `GET /projects/{project}/analytics` to return analytics about the tasks in a project.
    - The analytics should include:
        - Total number of tasks per **status** (`todo`, `in_progress`, `done`).
        - Total number of tasks per **priority** (`low`, `medium`, `high`).
        - Total tasks assigned to each **member**.
    - Example Output:
      ```json
      {
        "status_summary": {
          "todo": 5,
          "in_progress": 3,
          "done": 7
        },
        "priority_summary": {
          "low": 4,
          "medium": 6,
          "high": 5
        },
        "member_task_summary": {
          "user_1": 6,
          "user_2": 3,
          "user_3": 6
        }
      }
      ```
    - The logic for calculating these summaries must be implemented in **PHP using arrays**, not database queries (e.g., no `GROUP BY`).

---

## Constraints
1. **Database Design**:
    - Design a schema that ensures projects, tasks, and comments are correctly related, considering ownership and roles.
    - Include a pivot table for project memberships with roles.

2. **Security**:
    - Use Laravel’s built-in features to secure endpoints. Implement middleware to enforce permissions based on project roles.
    - Ensure the invite token for project invitations expires after 24 hours.

3. **Performance**:
    - Optimize queries to ensure scalability, especially for retrieving a project’s tasks or audit logs.

4. **Testing** (Optional):
    - Write unit tests and feature tests for:
        - Task creation and role enforcement.
        - Comment editing within the time limit.
        - Task analytics functionality.

---

## Deliverables
1. API routes with explanations of their usage.
2. Database migrations and models.
3. Laravel policies or middleware for role enforcement.
4. Any additional design considerations or improvements you would make to the system, documented in a `README.md` file.

---

## Bonus Challenges (Optional but encouraged)
1. Implement **soft deletes** for projects, ensuring all associated data is also recoverable.
2. Add a **search filter** for tasks within a project by **priority**, **status**, or **assignee**.
3. Refactor the **Task Analytics** endpoint to use caching for faster responses when analytics data doesn't change frequently.