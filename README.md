# Assessment Reports System

A CLI-based reporting tool for student assessments, built with PHP. Generates Diagnostic, Progress, and Feedback reports from JSON data.

## How to Run

### Local (Without Docker)

- Install dependencies: `composer install`
- Run App: `composer run app` (Enter Student ID and Report Type)
- Run Tests: `composer run test`

### Docker (Recommended for Consistency)

- Ensure Docker is installed and running (`sudo systemctl start docker`).
- Run App: `docker compose run php-app` (Interactive prompts)
- Run Tests: `docker compose run php-test`
- Note: First run installs Composer (~3-5 mins); subsequent runs are fast.

## Assumptions

- Total questions per assessment: Derived from responses array count (e.g., 16).
- Strands: Hardcoded based on questions.json (Number and Algebra, etc.).
- Only completed assessments (with 'completed' date) are considered.
- PHP 8.1+ required for PHPUnit compatibility.
- Data loaded in-memory from provided JSON files (no DB).

## CI/CD

GitHub Actions automatically runs tests on push/PR to main branch. See [Actions tab](https://github.com/LakshithaSankalpa/e1be67be-feb3-49ee-952e-d2ba56965982/actions) for details.

## Coding Challenge

- **Name:** Lakshitha Sankalpa
- **Email:** lakshithasankalpa356@gmail.com
- **Repository:** [UUID-based GitHub Repo](https://github.com/LakshithaSankalpa/e1be67be-feb3-49ee-952e-d2ba56965982) (Public)
- **Tech Stack:** PHP 8.1, Composer, PHPUnit, GitHub Actions, Docker Compose.
- **Key Features:** OOP design (ReportGenerator class), automated tests (5+ coverage), Docker for portability.

For questions or extensions, contact via email.
