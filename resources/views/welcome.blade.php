<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        main {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        main .centered {
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.164);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', serif
        }

        main .centered h1 {
            text-align: center;
            margin-top: 0 !important;
            margin-bottom: 20px;
            font-size: 25px;
        }

        main .centered .item {
            margin-bottom: 20px;
            margin-right: 12px;
        }

        main .centered .item label {
            width: 100%;
            display: block;
            margin-bottom: 4px;
        }

        main .centered .item input {
            width: 100%;
            height: 40px;
            border: solid 1px;
            outline: none;
            padding: 5px;
            font-size: 18px;
            border-radius: 5px;
        }

        main .centered button {
            background: cornflowerblue;
            color: #fff;
            margin-top: 15px;
            padding: 16px;
            cursor: pointer;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.164);
        }

    </style>
</head>
<body class="antialiased">
<main>
    <div class="centered">
        @if($listConfig->count())
        <div style="margin-bottom: 24px">
            <h1>Danh sách cấu hình</h1>
            @foreach($listConfig as $config)
                <div style="display: flex; align-items: center">

                    <div style="margin-right: 12px; display: flex">
                        <div class="item">
                            <label>Token Bot A</label>
                            <label for="token_bot_a"></label>
                            <input value="{{$config->token_a}}" type="text">
                        </div>

                        <div class="item">
                            <label>Chat ID A</label>
                            <label for="chat_id_a"></label>
                            <input type="text" value="{{$config->chat_id_a}}">
                        </div>
                    </div>

                    <div style="margin-left: 12px; display: flex">
                        <div class="item">
                            <label>Token Bot B</label>
                            <label for="token_bot_b"></label><input value="{{$config->token_b}}" type="text">
                        </div>

                        <div class="item">
                            <label>Chat ID B</label>
                            <label for="chat_id_b"></label><input value="{{$config->chat_id_b}}" type="text">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @endif
        <form id="from">
            <h1>Thêm cấu hình</h1>

            <div style="display: flex; align-items: center">

                <div style="margin-right: 12px; display: flex">
                    <div class="item">
                        <label>Token Bot A</label>
                        <label for="token_bot_a"></label><input type="text" id="token_bot_a">
                    </div>

                    <div class="item">
                        <label>Chat ID A</label>
                        <label for="chat_id_a"></label><input type="text" id="chat_id_a">
                    </div>
                </div>

                <div style="margin-left: 12px; display: flex">
                    <div class="item">
                        <label>Token Bot B</label>
                        <label for="token_bot_b"></label><input type="text" id="token_bot_b">
                    </div>

                    <div class="item">
                        <label>Chat ID B</label>
                        <label for="chat_id_b"></label><input type="text" id="chat_id_b">
                    </div>
                </div>
            </div>

            <button type="submit">
                Bắt Đầu
            </button>
        </form>
    </div>
    <script>
        let from = document.querySelector('#from');

        from.addEventListener("submit", e => {
            e.preventDefault();

            let tokenBotA = document.querySelector("#token_bot_a");
            let chatIdA = document.querySelector("#chat_id_a");
            let tokenBotB = document.querySelector("#token_bot_b");
            let chatIdB = document.querySelector("#chat_id_b");

            fetch(`${window.location.href}create-config`, {
                method: "POST",
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    token_a: tokenBotA.value,
                    token_b: tokenBotB.value,
                    chat_id_a: chatIdA.value,
                    chat_id_b: chatIdB.value
                })
            }).then((res) => {
                return res.json();
            }).then((resJson) => {
                location.reload();
                console.log('234', resJson);
                alert('Thành công');
            })
        });

    </script>
</main>
</body>
</html>
