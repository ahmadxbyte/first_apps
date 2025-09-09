document.addEventListener('DOMContentLoaded', function() {
    // Real-time clock
    const clockElement = document.querySelector('.real-time-clock');

    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        if (clockElement) {
            clockElement.textContent = `${hours}:${minutes}:${seconds}`;
        }
    }

    setInterval(updateClock, 1000);
    updateClock(); // Initial call

    // Profile dropdown
    const profile = document.querySelector('.profile');
    const dropdownMenu = document.querySelector('.dropdown-menu');

    if (profile && dropdownMenu) {
        profile.addEventListener('click', function(event) {
            event.stopPropagation();
            dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
        });

        document.addEventListener('click', function() {
            dropdownMenu.style.display = 'none';
        });
    }
});
