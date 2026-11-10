document.addEventListener("DOMContentLoaded", function () {
    const historyList = document.getElementById('historyList');
    const clearHistoryBtn = document.getElementById('clearHistory');

    // Display current page title in the UI info box
    document.getElementById('currentTitle').textContent = document.title;

    // 1. Fetch existing history from localStorage or create an empty list
    let browsingHistory = JSON.parse(localStorage.getItem('siteBrowsingHistory')) || [];

    // 2. Capture data about the current page visit
    const newVisit = {
        title: document.title,
        url: window.location.href,
        timestamp: new Date().toLocaleString()
    };

    // 3. Automatically add this visit to history and update localStorage
    browsingHistory.push(newVisit);
    localStorage.setItem('siteBrowsingHistory', JSON.stringify(browsingHistory));

    // 4. Function to render the updated history list (Newest First)
    function renderHistory() {
        historyList.innerHTML = '';

        if (browsingHistory.length === 0) {
            historyList.innerHTML = '<li style="color: #888; font-style: italic; padding: 10px;">No browsing history recorded.</li>';
            return;
        }

        // Reverse array slice to display the newest visits at the top
        browsingHistory.slice().reverse().forEach(item => {
            const li = document.createElement('li');
            li.className = 'history-item';
            li.innerHTML = `
                    <div>
                        <span class="page-title">${item.title}</span>
                        <span class="page-url">${item.url}</span>
                    </div>
                    <span class="timestamp">${item.timestamp}</span>
                `;
            historyList.appendChild(li);
        });
    }

    // 5. Clear history event listener
    clearHistoryBtn.addEventListener('click', function () {
        browsingHistory = [];
        localStorage.removeItem('siteBrowsingHistory');
        renderHistory();
    });

    // Run the initial UI render on page load
    renderHistory();
});