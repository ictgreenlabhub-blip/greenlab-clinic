<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>GreenLab Auth</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #dff5f2, #f4f6f9);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* CARD */
.container-box {
    width: 850px;
    height: 480px;
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    display: flex;
    overflow: hidden;
    position: relative;
}

/* FORMS */
.form-box {
    width: 50%;
    padding: 50px;
    z-index: 1;
}

/* INPUT STYLE */
.input-group {
    position: relative;
    margin-bottom: 20px;
}

.input-group input {
    width: 100%;
    padding: 12px 40px 12px 12px;
    border-radius: 10px;
    border: 1px solid #ccc;
}

.input-group i {
    position: absolute;
    right: 10px;
    top: 12px;
    color: #999;
    cursor: pointer;
}

/* BUTTON */
.btn-green {
    background: linear-gradient(135deg, #4ad2fc, #4ad2fc);
    color: #fff;
    border: none;
}

.btn-green:hover {
    opacity: 0.9;
}

/* SLIDER */
.overlay {
    position: absolute;
    width: 50%;
    height: 100%;
    background: linear-gradient(135deg, #15b879, #2ff793);
    top: 0;
    left: 50%;
    transition: 0.6s ease-in-out;
    border-radius: 40px 0 0 40px;
    z-index: 2;

    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: #fff;
    padding: 40px;
}

.container-box.active .overlay {
    left: 0;
    border-radius: 0 40px 40px 0;
}

h3 {
    font-weight: bold;
}

.small-text {
    font-size: 14px;
    opacity: 0.9;
}
</style>
</head>

<body>

<div class="container-box" id="container">

    <!-- LOGIN -->
    <div class="form-box">
        <h3>Login</h3>

        <form method="POST" action="/login">
            @csrf

            <div class="input-group">
                <input type="email" name="email" placeholder="Email">
                <i class="bi bi-envelope"></i>
            </div>

            <div class="input-group">
                <input type="password" name="password" id="loginPass" placeholder="Password">
                <i onclick="togglePassword('loginPass')" class="bi bi-eye"></i>
            </div>

            <button class="btn btn-green w-100">Login</button>
        </form>
    </div>

    <!-- REGISTER -->
    <div class="form-box">
        <h3>Register</h3>

        <form method="POST" action="/register">
            @csrf

            <div class="input-group">
                <input type="text" name="name" placeholder="Full Name">
            </div>

            <div class="input-group">
                <input type="email" name="email" placeholder="Email">
            </div>

            <div class="input-group">
                <input type="password" name="password" id="regPass" placeholder="Password">
                <i onclick="togglePassword('regPass')" class="bi bi-eye"></i>
            </div>

            <button class="btn btn-green w-100">Register</button>
        </form>
    </div>

    <!-- SLIDER -->
    <div class="overlay">
        <div>
            <h3 id="title">Hello, Welcome!</h3>
            <p id="text" class="small-text">Don't have an account?</p>

            <button class="btn btn-light mt-3" onclick="toggleForm()" id="btnSwitch">
                Register
            </button>
        </div>
    </div>

</div>

<!-- ICONS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<script>
function toggleForm() {
    const container = document.getElementById('container');
    const title = document.getElementById('title');
    const text = document.getElementById('text');
    const btn = document.getElementById('btnSwitch');

    container.classList.toggle('active');

    if (container.classList.contains('active')) {
        title.innerText = "Welcome Back!";
        text.innerText = "Already have an account?";
        btn.innerText = "Login";
    } else {
        title.innerText = "Hello, Welcome!";
        text.innerText = "Don't have an account?";
        btn.innerText = "Register";
    }
}

/* SHOW/HIDE PASSWORD */
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === "password" ? "text" : "password";
}
</script>

</body>
</html>