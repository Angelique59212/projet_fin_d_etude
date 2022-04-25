const close = document.getElementById('close');
const message = document.querySelector('.message');

if (close) {
    close.addEventListener("click", ()=> {
        close.style.display = 'none';
        message.remove();
    })
}
