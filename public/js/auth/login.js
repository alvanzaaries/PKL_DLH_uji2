(function () {
    const checkbox = document.getElementById('captcha_check');
    const submitBtn = document.getElementById('loginSubmit');
    if (!checkbox || !submitBtn) return;

    const syncState = () => {
        submitBtn.disabled = !checkbox.checked;
    };

    syncState();
    checkbox.addEventListener('change', syncState);
})();
