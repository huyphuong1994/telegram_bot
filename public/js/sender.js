let from = document.querySelector('#from');

from.addEventListener("submit", e => {
    e.preventDefault();

    let tokenBotA = document.querySelector("#token_bot_a");
    let idGroupA = document.querySelector("#id_group_a");

    let tokenBotB = document.querySelector("#token_bot_b");
    let idTopic1 = document.querySelector("#id_topic_1");

    localStorage.setItem('tokenBotA', tokenBotA.value);
    localStorage.setItem('idGroupA', idGroupA.value);
    localStorage.setItem('tokenBotB', tokenBotB.value);
    localStorage.setItem('idTopic1', idTopic1.value);

    intervalCall()
    console.log('123', tokenBotA, idGroupA, tokenBotB, idTopic1)
})

function intervalCall() {
    setInterval(() => {
        fetch(`https://api.telegram.org/bot${localStorage.getItem('tokenBotB')}/getUpdates`, {
            method: "GET"
        }).then((res) => {
            return res.json();
        }).then((resJson) => {
            console.log(resJson)
            // if (!localStorage.getItem('countMes')) {
            //     localStorage.setItem('countMes', resJson.result.length + "")
            // }
            // if (resJson.result) {
            //     if (+localStorage.getItem('countMes') < resJson.result.length) {
            //         localStorage.setItem('countMes', resJson.result.length + "")
            //
            //         let message = resJson.result.reverse()[0];
            //
            //         // if (message && message.message && message.message.text) {
            //         //     sendMessageNew(message.message.text)
            //         // }
            //
            //         console.log('123', message && message.message && message.message.message_thread_id == 4)
            //         if (message && message.message && message.message.message_thread_id == 4) {
            //             deleteMessage(message.message.message_id)
            //         }
            //     }
            // }
        })
    }, 2000)
}

// function sendMessageNew(text) {
//     fetch(`https://api.telegram.org/bot${localStorage.getItem('tokenBotB')}/sendMessage?chat_id=${localStorage.getItem('idTopic1')}&text=${text}`).then((res) => {
//
//     })
// }
//
// function blockSendMessage() {
//     fetch(`https://api.telegram.org/bot${localStorage.getItem('tokenBotB')}/sendMessage?chat_id=${localStorage.getItem('idTopic1')}&text=${text}`).then((res) => {
//         console.log('delete', res)
//     })
// }
//
// function deleteMessage(id) {
//     fetch(`https://api.telegram.org/bot${localStorage.getItem('tokenBotB')}/deleteMessage?chat_id=${localStorage.getItem('idTopic1')}&message_id=${id}`)
// }
