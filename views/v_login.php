<style>
    @import url(https://fonts.googleapis.com/css?family=Roboto:300);

    .login-page {
        width: 360px;
        padding: 8% 0 0;
        margin: auto;
    }
    .form {
        position: relative;
        z-index: 1;
        background: #FFFFFF;
        max-width: 360px;
        margin: 0 auto 100px;
        padding: 40px;
        text-align: center;
        box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
    }
    .form input {
        font-family: "Roboto", sans-serif;
        outline: 0;
        background: #f2f2f2;
        width: 100%;
        border: 0;
        margin: 0 0 15px;
        padding: 15px;
        box-sizing: border-box;
        font-size: 14px;
    }
    .form button {
        font-family: "Roboto", sans-serif;
        text-transform: uppercase;
        outline: 0;
        background: #4CAF50;
        width: 100%;
        border: 0;
        padding: 15px;
        color: #FFFFFF;
        font-size: 14px;
        -webkit-transition: all 0.3 ease;
        transition: all 0.3 ease;
        cursor: pointer;
    }
    .form button:hover,.form button:active,.form button:focus {
        background: #43A047;
    }
    .form .message {
        margin: 15px 0 0;
        color: #b3b3b3;
        font-size: 12px;
    }
    .form .message a {
        color: #4CAF50;
        text-decoration: none;
    }
    .form .register-form {
        display: none;
    }
    .container {
        position: relative;
        z-index: 1;
        max-width: 300px;
        margin: 0 auto;
    }
    .container:before, .container:after {
        content: "";
        display: block;
        clear: both;
    }
    .container .info {
        margin: 50px auto;
        text-align: center;
    }
    .container .info h1 {
        margin: 0 0 15px;
        padding: 0;
        font-size: 36px;
        font-weight: 300;
        color: #1a1a1a;
    }
    .container .info span {
        color: #4d4d4d;
        font-size: 12px;
    }
    .container .info span a {
        color: #000000;
        text-decoration: none;
    }
    .container .info span .fa {
        color: #EF3B3A;
    }
    body {
        background: #005ebb; /* fallback for old browsers */
        background: -webkit-linear-gradient(right, #005ebb, #8DC26F);
        background: -moz-linear-gradient(right, #005ebb, #8DC26F);
        background: -o-linear-gradient(right, #005ebb, #8DC26F);
        background: linear-gradient(to left, #005ebb, #8DC26F);
        font-family: "Roboto", sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;      
    }

    .logo-rs-login {
        text-align: center;
    }

    #username.invalid,
    #password.invalid {
        border: 1px solid red;
        background-color: #f3d1d1;
    }

    @media only screen and (max-width: 400px) {
        .form {
            position:fixed;
            top: 20%;
        }
    }

    .info-login {
        margin: 10px;
        background-color: #8480ec;
        padding: 15px;
        font-family: Arial;
        color: white;
        font-weight: 700;
        font-size: 14px;
        text-align : center;
    }

    @media only screen and (min-width: 500px) {
        .form {
            margin-bottom : 20px;
        }

        .info-login {
            font-size: 12px;
        }
    }


</style>
<div class="login-page">
    <div class="form">
        <!-- <form class="register-form">
            <input type="text" placeholder="name"/>
            <input type="password" placeholder="password"/>
            <input type="text" placeholder="email address"/>
            <button>create</button>
            <p class="message">Already registered? <a href="#">Sign In</a></p>
        </form> -->
        <form id="login-form" class="login-form" method="POST" action="<?php echo BASE_URL; ?>/controllers/C_login.php?action=login">
            <!-- <img src="<?php // echo BASE_URL; ?>/assets/img/RSHAJI.PNG" alt="Rshaji-Jakarta-telemedicine" width="100px" heigh="50px"> -->
            <p><b>Inventory <font style="color: green;">Duta Karya</font></b></p>
            <input type="text" id="username" name="username" placeholder="username"/>
            <input type="password" id="password" name="password" placeholder="password"/>
            <button>login</button>
            <!-- <p class="message">Not registered? <a href="#">Create an account</a></p> -->
        </form>
    </div>
</div>
<script>
    $('.message a').click(function(){
        $('form').animate({height: "toggle", opacity: "toggle"}, "slow");
    });

    $(function(){
        $("input").on("keyup", function () {
            if($(this).val() !== '') {
                $("input").removeClass("invalid")
            }
        });

        $("#login-form").submit(function(){
            $.ajax({
                url:$(this).attr("action"),
                data:$(this).serialize(),
                type:$(this).attr("method"),
                dataType: 'html',
                success:function(hasil) {
                    if(hasil == 200) {
                        window.location.href='<?php echo BASE_URL ?>/controllers/C_Login';
                    } else {
                        $("#username").val('');
                        $("#password").val('');
                        $("#username").addClass("invalid");
                        $("#password").addClass("invalid");
                    }
                }
            })
            return false;
        });
    });
</script>