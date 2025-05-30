<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo link_tag('/assets/css/login.css'); ?>
    <script defer src="/assets/js/v2/login.js"></script>
    <title>Login</title>
</head>

<body>
    <div class="wrapper">


        <div>
            <label for="id">ID:</label>
            <input type="text" name="id" id="id">
        </div>
        <hr>
        <div>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password">
        </div>
        <hr>
        <div id="fail_message"></div>
        <button id="login_btx" onclick="return login()" type="submit">
            로그인
        </button>
        <button id="create_account_btx" onclick="return open_create_account()" type="button">
            회원가입
        </button>
        <button type="button" onclick="location.href='/board'">뒤로 가기</button>
        <div id="create_account_box" hidden>
            <div>
                <label for="newID">ID:</label>
                <input type="text" name="newID" id="newID">
            </div>
            <div>
                <label for="newPassword">Password:</label>
                <input type="password" name="newPassword" id="newPassword">
            </div>
            <div>
                <label for="username">username:</label>
                <input type="username" name="username" id="username">
            </div>
            <button id="request_create_account" type="button">
                회원가입
            </button>
        </div>
    </div>
</body>

</html>