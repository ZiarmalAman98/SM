<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Portal | Aman Private School</title>
    <style>
        :root {
            --ink: #132238;
            --muted: #607086;
            --line: #dfe7ef;
            --brand: #0d9488;
            --sky: #0284c7;
            --gold: #f59e0b;
            --violet: #7c3aed;
            --soft: #f6f9fb;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--ink);
            background:
                linear-gradient(120deg, rgba(246, 249, 251, 0.94), rgba(255, 255, 255, 0.9)),
                url("/afghanistan_school.png") center / cover no-repeat;
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
        }

        a { color: inherit; text-decoration: none; }

        .shell {
            width: min(1080px, calc(100% - 32px));
            margin: 0 auto;
            padding: 34px 0 60px;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            margin-bottom: 64px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 850;
        }

        .brand img {
            width: 52px;
            height: 52px;
            object-fit: contain;
        }

        .back {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 16px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: white;
            font-weight: 750;
        }

        .hero {
            max-width: 740px;
            margin-bottom: 30px;
        }

        .hero p:first-child {
            margin: 0 0 8px;
            color: var(--brand);
            font-size: 12px;
            font-weight: 850;
            text-transform: uppercase;
        }

        h1 {
            margin: 0 0 12px;
            font-size: clamp(34px, 5vw, 58px);
            line-height: 1.04;
            letter-spacing: 0;
        }

        .lead {
            margin: 0;
            color: var(--muted);
            font-size: 18px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .card {
            min-height: 250px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 24px;
            border-radius: 8px;
            background: white;
            border: 1px solid var(--line);
            box-shadow: 0 20px 48px rgba(19, 34, 56, 0.1);
            transition: transform 160ms ease, box-shadow 160ms ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 26px 62px rgba(19, 34, 56, 0.16);
        }

        .icon {
            width: 54px;
            height: 54px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            color: white;
            font-size: 24px;
            font-weight: 900;
            margin-bottom: 18px;
        }

        .admin .icon { background: var(--brand); }
        .parent .icon { background: var(--sky); }
        .teacher .icon { background: var(--gold); }
        .student .icon { background: var(--violet); }

        .card h2 {
            margin: 0 0 8px;
            font-size: 24px;
        }

        .card p {
            margin: 0;
            color: var(--muted);
        }

        .open {
            margin-top: 24px;
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: white;
            background: #132238;
            font-weight: 850;
        }

        @media (max-width: 980px) {
            .cards { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 560px) {
            .top {
                align-items: flex-start;
                flex-direction: column;
                margin-bottom: 44px;
            }

            .cards { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <main class="shell">
        <div class="top">
            <a href="/" class="brand">
                <img src="/aman-logo.svg" alt="Aman Private School logo">
                <span>Aman Private School</span>
            </a>
            <a class="back" href="/">Back to Website</a>
        </div>

        <section class="hero">
            <p>Secure Login Portal</p>
            <h1>Choose your portal</h1>
            <p class="lead">Select the correct login area for administration, parents, teachers, or students.</p>
        </section>

        <section class="cards" aria-label="Login options">
            <a class="card admin" href="/admin/login">
                <div>
                    <div class="icon">A</div>
                    <h2>Admin</h2>
                    <p>Manage students, staff, finance, reports, attendance, exams, and school settings.</p>
                </div>
                <span class="open">Admin Login</span>
            </a>

            <a class="card parent" href="/parent/login">
                <div>
                    <div class="icon">P</div>
                    <h2>Parent</h2>
                    <p>View child attendance, payments, discipline notes, assignments, and notifications.</p>
                </div>
                <span class="open">Parent Login</span>
            </a>

            <a class="card teacher" href="/teacher/login">
                <div>
                    <div class="icon">T</div>
                    <h2>Teacher</h2>
                    <p>Handle attendance, assignments, teacher plans, leaves, and class activities.</p>
                </div>
                <span class="open">Teacher Login</span>
            </a>

            <a class="card student" href="/student/login">
                <div>
                    <div class="icon">S</div>
                    <h2>Student</h2>
                    <p>Check classes, homework, payments, attendance, exam results, and timetable.</p>
                </div>
                <span class="open">Student Login</span>
            </a>
        </section>
    </main>
</body>
</html>
