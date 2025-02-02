<?php
include 'middleware/is_logged_in.php';
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Welcome to RuralLink</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;400;500;700&display=swap" rel="stylesheet">
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body Styling */
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background: linear-gradient(to bottom right, #e3f2fd, #bbdefb);
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            min-height: 100vh;
        }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 20px 40px;
            background-color: #2196F3;
            color: white;
            position: fixed;
            top: 0;
            z-index: 1000;
        }

        .navbar .logo {
            font-size: 1.5rem;
            font-weight: 500;
        }

        .navbar .links a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-size: 1rem;
            font-weight: 400;
            transition: color 0.3s;
        }

        .navbar .links a:hover {
            color: #bbdefb;
        }

        .hero {
            text-align: center;
            margin-top: 100px;
            max-width: 800px;
            padding: 20px;
        }

        .hero h1 {
            font-size: 3rem;
            color: #0D47A1;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            color: #333;
            font-weight: 400;
        }

        .btn-group {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .btn {
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 500;
            /* Medium weight */
            border: none;
            background-color: #1E88E5;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #1565C0;
        }


        .image-placeholder {
            margin-top: 50px;
            width: 100%;
            height: 400px;
            background: url('download.jpeg') no-repeat center center / cover;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            background-color: #2196F3;
            color: white;
            width: 100%;
            margin-top: 20px;
        }

        .image-placeholder {
            margin-top: 50px;
            width: 100%;
            height: 400px;
            background: url(public/assets/images/hero-image.png) no-repeat center;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="logo">RuralLink</div>
        <div class="links">
            <a href="login.php">Login</a>
            <a href="register.php">Sign Up</a>
        </div>
    </div>

    <div class="hero">
        <h1>Connecting Rural Talent with Opportunities</h1>
        <p>Discover jobs, hire skilled workers, or manage projects effortlessly. RuralLink brings everyone together in
            one
            seamless platform.</p>
        <div class="btn-group">
            <button class="btn" onclick="window.location.href='login.php'">Login</button>
            <button class="btn" onclick="window.location.href='register.php'">Sign Up</button>
        </div>
    </div>

    <div class="image-placeholder">

    </div>

    <div class="footer">
        <footer class="page-footer" style="background-color: #424242; padding: 20px;">
            <div class="container" style="max-width: 1200px; margin: 0 auto;">
                <div class="row" style="display: flex; justify-content: space-between;">
                    <div class="col" style="flex: 0 0 60%; padding: 10px;">
                        <h2 style="color:  rgb(255, 221, 0);">About RuralLink</h2>
                        <p style="color: #bdbdbd;">
                            RuralLink is a dynamic job finding platform designed to connect clients and job-seekers
                            seamlessly. It
                            allows buyers and sellers, or clients and daily workers, to communicate by agent after
                            logging in. The
                            platform supports multi-way messaging, enabling clients to reach multiple job-seekers and
                            vice versa. With
                            a focus on efficient and clear communication, RuralLink simplifies the job-seeking process
                            while fostering
                            professional connections between clients and worker .
                        </p>

                    </div>

                    <div class="col" style="flex: 0 0 30%; padding: 10px;">
                        <h2 style="color: rgb(255, 221, 0);">Connect</h2>
                        <ul style="list-style-type: none; padding: 0;">
                            <li><a href="#" style="color: #bdbdbd; text-decoration: none;">Facebook</a></li>
                            <li><a href="#" style="color: #bdbdbd; text-decoration: none;">Twitter</a></li>
                            <li><a href="#" style="color: #bdbdbd; text-decoration: none;">LinkedIn</a></li>
                            <li><a href="#" style="color: #bdbdbd; text-decoration: none;">Instagram</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="footer-copyright" style="background-color: #212121; padding: 10px;">
                <div class="container" style="text-align: center; color: white;">&copy;2024 HustlUp</div>
            </div>
        </footer />
    </div>
</body>

</html>