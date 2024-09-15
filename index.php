<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Bootstrap links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <style>
        body {
            width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #loginDiv {
            width: 50%;
            height: 50%;
        }
    </style>
</head>

<body>
    <div id="loginDiv">
        <form>
            <div class="form-group">
                <label for="exampleInputEmail1">Email address</label>
                <input type="email" class="form-control" id="loginId" aria-describedby="emailHelp"
                    placeholder="Enter email">
            </div>
            <br>
            <div class="form-group">
                <label for="exampleInputPassword1">Password</label>
                <input type="password" class="form-control" id="loginPass" placeholder="Password">
            </div>
            <br>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="showPass">
                <label class="form-check-label" for="showPass" onclick="showPass()">Show Password</label>
            </div>
            <br>
            <button type="button" class="btn btn-primary" onclick="validate()">Submit</button>
        </form>
    </div>

    <script>
        async function validate() {
            const username = document.getElementById("loginId").value;
            const password = document.getElementById("loginPass").value;

            if (username === "" || password === "") {
                alert("Both fields are required!");
                return;
            }

            // Send login data using POST method to PHP
            const response = await fetch('server/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ username, password }) // send as JSON
            });

            const data = await response.json();

            if (data.success) {
                window.location.href = "./view/home.php";
                // alert('Login successful');
                // Redirect or take further actions
            } else {
                alert('Invalid login credentials');
            }
        }

        function showPass() {
            const passwordField = document.getElementById("loginPass");
            const showPassword = document.getElementById("showPass");

            if (!showPassword.checked) {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
</body>

</html>