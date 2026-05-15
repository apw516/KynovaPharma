<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>FALIH FARMA | Silahkan Login </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Montserrat:400,800');

        * {
            box-sizing: border-box;
        }

        body {
            background: #f6f5f7;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            font-family: 'Montserrat', sans-serif;
            height: 100vh;
            margin: -20px 0 50px;
        }

        h1 {
            font-weight: bold;
            margin: 0;
        }

        h2 {
            text-align: center;
        }

        p {
            font-size: 14px;
            font-weight: 100;
            line-height: 20px;
            letter-spacing: 0.5px;
            margin: 20px 0 30px;
        }

        span {
            font-size: 12px;
        }

        a {
            color: #333;
            font-size: 14px;
            text-decoration: none;
            margin: 15px 0;
        }

        button {
            border-radius: 20px;
            border: 1px solid rgb(91, 209, 123);
            background-color: rgb(91, 209, 123);
            color: #FFFFFF;
            font-size: 12px;
            font-weight: bold;
            padding: 12px 45px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 80ms ease-in;
        }

        button:active {
            transform: scale(0.95);
        }

        button:focus {
            outline: none;
        }

        button.ghost {
            background-color: transparent;
            border-color: #FFFFFF;
        }

        form {
            background-color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 50px;
            height: 100%;
            text-align: center;
        }

        input {
            background-color: #eee;
            border: none;
            padding: 12px 15px;
            margin: 8px 0;
            width: 100%;
        }

        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25),
                0 10px 10px rgba(0, 0, 0, 0.22);
            position: relative;
            overflow: hidden;
            width: 768px;
            max-width: 100%;
            min-height: 480px;
        }

        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }

        .sign-in-container {
            left: 0;
            width: 50%;
            z-index: 2;
        }

        .container.right-panel-active .sign-in-container {
            transform: translateX(100%);
        }

        .sign-up-container {
            left: 0;
            width: 50%;
            opacity: 0;
            z-index: 1;
        }

        .container.right-panel-active .sign-up-container {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
            animation: show 0.6s;
        }

        @keyframes show {

            0%,
            49.99% {
                opacity: 0;
                z-index: 1;
            }

            50%,
            100% {
                opacity: 1;
                z-index: 5;
            }
        }

        .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.6s ease-in-out;
            z-index: 100;
        }

        .container.right-panel-active .overlay-container {
            transform: translateX(-100%);
        }

        .overlay {
            background: rgb(112, 49, 114);
            background: -webkit-linear-gradient(to right, rgb(91, 209, 123), rgb(91, 209, 123));
            background: linear-gradient(to right, rgb(91, 209, 123), rgb(91, 209, 123));
            background-repeat: no-repeat;
            background-size: cover;
            background-position: 0 0;
            color: #FFFFFF;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .container.right-panel-active .overlay {
            transform: translateX(50%);
        }

        .overlay-panel {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 40px;
            text-align: center;
            top: 0;
            height: 100%;
            width: 50%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .overlay-left {
            transform: translateX(-20%);
        }

        .container.right-panel-active .overlay-left {
            transform: translateX(0);
        }

        .overlay-right {
            right: 0;
            transform: translateX(0);
        }

        .container.right-panel-active .overlay-right {
            transform: translateX(20%);
        }

        .social-container {
            margin: 20px 0;
        }

        .social-container a {
            border: 1px solid #DDDDDD;
            border-radius: 50%;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin: 0 5px;
            height: 40px;
            width: 40px;
        }

        footer {
            background-color: #222;
            color: #fff;
            font-size: 14px;
            bottom: 0;
            position: fixed;
            left: 0;
            right: 0;
            text-align: center;
            z-index: 999;
        }

        footer p {
            margin: 10px 0;
        }

        footer i {
            color: red;
        }

        footer a {
            color: #3c97bf;
            text-decoration: none;
        }
    </style>
</head>

<body>
    {{-- <h2>Selamat Datang</h2> --}}
    <div class="container" id="container">
        <div class="form-container sign-up-container">
            <form action="{{ route('register') }}" method="post">
                @csrf
                <h1>Buat Akun</h1>
                <input type="text" name="nama" id="nama" placeholder="Masukan Nama Lengkap" />
                <input type="text" name="username" id="username" placeholder="Masukan username" />
                <input type="password" name="password" id="password" placeholder="Masukan Password" />
                <input type="password" name="password_confirmation" id="password_confirmation"
                    placeholder="Ketik Ulang Password" />
                <button>Registrasi</button>
            </form>
        </div>
        <div class="form-container sign-in-container">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('login') }}" method="post">
                @csrf
                <h1>Login</h1>
                @if (session()->has('loginError'))
                    <div class="alert alert-danger mt-2" role="alert">
                        Login gagal !
                    </div>
                @elseif(session()->has('regisgagal'))
                    <div class="alert alert-danger mt-2" role="alert">
                        Registrasi Gagal
                    </div>
                @endif
                @if (session()->has('Sukses'))
                    <div class="alert alert-success mt-2" role="alert">
                        Registrasi berhasil , Silahkan login !
                    </div>
                @endif
                <input required type="text" placeholder="Masukan username anda ..." name="username" id="username" />
                <input required type="password" placeholder="Password" name="password" id="password" />
                <a href="#">Lupa password?</a>
                <button>Login</button>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <img src="{{ asset('/public/img/KynovaPharma.png') }}" alt="" width="50%"
                        style="margin-bottom:20px">
                    <h1>Welcome Back!</h1>
                    <p>Untuk tetap terhubung dengan kami, silakan masuk dengan akun Anda</p>
                    <button class="ghost" id="signIn">Login</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <img src="{{ asset('/public/img/KynovaPharma.png') }}" alt="" width="50%"
                        style="margin-bottom:20px">
                    <h1>Hello, Friend!</h1>
                    <p>Masukan data anda dan dapatkan akun akun baru ...</p>
                    <button class="ghost" id="signUp">Registrasi</button>
                </div>
            </div>
        </div>
    </div>
    <footer>
      <div class="footer-text">
    <p>
        &copy; {{ date('Y') }} <strong>KynovaPharma</strong>. 
        <span>Smart Solution for Modern Pharmacy Management.</span>
    </p>
    <small>Developed with <i class="fa fa-heart text-danger"></i> by <a href="#" class="text-decoration-none">APW LAB</a></small>
</div>
    </footer>
</body>
<script>
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('container');

    signUpButton.addEventListener('click', () => {
        container.classList.add("right-panel-active");
    });

    signInButton.addEventListener('click', () => {
        container.classList.remove("right-panel-active");
    });
</script>

</html>
