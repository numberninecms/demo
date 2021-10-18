import '../scss/app.scss';

(() => {
    const timeout = setInterval(() => {
        const timeLeft = Math.floor((15 - (new Date().getTime() / 60000 + 15) % 15) * 60);
        const span = document.getElementById('reset-countdown');
        span.innerText = `${String(Math.floor(timeLeft / 60)).padStart(2, '0')}:${String(timeLeft % 60).padStart(2, '0')}`;

        if (timeLeft <= 0) {
            clearTimeout(timeout);
        }
    }, 1000);
})();
