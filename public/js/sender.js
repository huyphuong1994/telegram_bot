let from = document.querySelector('#from');

from.addEventListener("submit", e => {
    e.preventDefault();

    let tokenBotA = document.querySelector("#token_bot_a");
    let tokenBotB = document.querySelector("#token_bot_b");

    fetch(`${window.location.href}create-config`, {
        method: "POST",
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            token_a: tokenBotA,
            token_b: tokenBotB
        })
    }).then((res) => {
        return res.json();
    }).then((resJson) => {
        alert('Thành công');
    })
});
