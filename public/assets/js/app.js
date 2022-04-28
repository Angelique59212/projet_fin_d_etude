const close = document.getElementById('close');
const message = document.querySelector('.message');

if (close) {
    close.addEventListener("click", ()=> {
        close.style.display = 'none';
        message.remove();
    })
}

//Video dysphasie//

let player = $('.player__video').first();

//create button pause and alternate between play and pause
$("button").click(function (event) {
    if($(this).is(".toggle")) {
        player.trigger("play");
        $(this).removeClass("toggle");
        $(this).text("⏸")
    }
    else {
        player.trigger("pause");
        $(this).text("►");
        $(this).addClass("toggle");
    }
})
