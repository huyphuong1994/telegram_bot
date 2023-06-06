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
            margin: 10px 12px;
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

        main .centered .add-config {
            background: cornflowerblue;
            color: #fff;
            margin-top: 15px;
            padding: 16px;
            cursor: pointer;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.164);
        }

        .config-info {
            margin-right: 12px;
            display: grid;
            grid-template-columns: 50% 50%;
        }

        .delete-config button {
            background: rgba(255, 120, 120, 0.93);
            color: #ffffff;
            padding: 8px;
            cursor: pointer;
            border-radius: 8px;
            border: none;
            box-shadow: 0 0 15px rgba(255, 130, 130, 0.16);
        }

    </style>
</head>
<body class="antialiased">
<main>
    <div class="centered">
        @if($listConfig->count())
            <div id="info" style="margin-bottom: 24px">
                <h1>Danh sách cấu hình</h1>
                @foreach($listConfig as $config)
                    <div style="display: flex; align-items: center">

                        <div class="config-info">
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
                                <label for="token_bot_b"></label>
                                <input value="{{$config->token_b}}" type="text">
                            </div>

                            <div class="item">
                                <label>Chat ID B</label>
                                <label for="chat_id_b"></label>
                                <input value="{{$config->chat_id_b}}" type="text">
                            </div>
                        </div>

                        <div class="delete-config">
                            <button onclick="deleteConfig({{$config->id}})" value="{{$config->id}}">Xóa</button>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
        <form id="from">
            <h1>Thêm cấu hình</h1>

            <div class="config-info">

                <div style="padding-right: 24px; border-right: 1px solid">
                    <div class="item">
                        <label>Tên group A - Tên bot</label>
                        <label for="name_group_a"></label><input type="text" id="name_group_a">
                    </div>

                    <div class="item">
                        <label>Token Bot A</label>
                        <label for="token_bot_a"></label><input type="text" id="token_bot_a">
                    </div>

                    <div class="item">
                        <label>Chat ID A</label>
                        <label for="chat_id_a"></label><input type="text" id="chat_id_a">
                    </div>
                </div>

                <div style="padding-left: 24px;">
                    <div class="item">
                        <label>Tên group B - Tên bot</label>
                        <label for="name_group_b"></label><input type="text" id="name_group_a">
                    </div>

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

            <button class="add-config" type="submit">
                Thêm
            </button>
        </form>
    </div>
    <script>
        let from = document.querySelector('#from');

        from.addEventListener("submit", e => {
            e.preventDefault();

            let nameGroupA = document.querySelector("#name_group_a");
            let nameGroupB = document.querySelector("#name_group_b");
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
                    name_group_a: nameGroupA.value,
                    name_group_b: nameGroupB.value,
                    token_a: tokenBotA.value,
                    token_b: tokenBotB.value,
                    chat_id_a: chatIdA.value,
                    chat_id_b: chatIdB.value
                })
            }).then((resJson) => {
                location.reload();
                console.log('234', resJson);
                alert('Thành công');
            })
        });

        function deleteConfig(id) {
            fetch(`${window.location.href}destroy/${id}`).then((res) => {
                location.reload();
                alert('Xóa thành công')
            }).catch(err => {
                alert('Xóa thất bại')
            })
        }

    </script>
</main>
</body>
</html>
